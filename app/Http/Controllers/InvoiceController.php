<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BusinessSetting;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Tax;
use App\Models\Transaction;
use App\Notifications\SendInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller {

    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.invoice.list', compact('assets'));
    }

    public function get_table_data(Request $request) {
        $invoices = Invoice::select('invoices.*')
            ->with('customer')
            ->where('is_recurring', 0)
            ->orderBy("invoices.id", "desc");

        return Datatables::eloquent($invoices)
            ->editColumn('invoice_number', function ($invoice) {
                $invoice_number = $invoice->invoice_number;
                if ($invoice->parent_id != null) {
                    $invoice_number .= '<div class="text-height-0 font-italic"><small>' . _lang('Recurring') . '</small></div>';
                }
                return $invoice_number;
            })
            ->editColumn('grand_total', function ($invoice) {
                if ($invoice->customer->currency != request()->activeBusiness->currency) {
                    return '<div class="text-right">' . formatAmount($invoice->grand_total, currency_symbol(request()->activeBusiness->currency)) . '<br>'
                    . formatAmount($invoice->converted_total, currency_symbol($invoice->customer->currency)) . '</div>';
                }
                return '<div class="text-right">' . formatAmount($invoice->grand_total, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->addColumn('amount_due', function ($invoice) {
                return '<div class="text-right">' . formatAmount($invoice->grand_total - $invoice->paid, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('status', function ($invoice) {
                return '<div class="text-center">' . invoice_status($invoice) . '</div>';
            })
            ->addColumn('action', function ($invoice) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ' . ($invoice->status == 2 || $invoice->status == 99 ? "disabled" : "") . '" href="' . ($invoice->status != 2 ? route('invoices.edit', $invoice['id']) : '') . '"><i class="far fa-edit mr-2"></i>' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('invoices.show', $invoice['id']) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                . '<a class="dropdown-item ' . ($invoice->status == 99 ? "disabled" : "ajax-modal") . '" href="' . route('invoices.duplicate', $invoice['id']) . '"><i class="far fa-copy mr-2"></i>' . _lang('Duplicate') . '</a>'
                . ' <div class="dropdown-divider"></div>'
                . '<a class="dropdown-item" href="' . route('invoices.export_pdf', $invoice['id']) . '"><i class="far fa-file-pdf mr-2"></i>' . _lang('Export as PDF') . '</a>'
                . '<a class="dropdown-item ' . ($invoice->status == 0 || $invoice->status == 99 ? "disabled" : "ajax-modal") . '" data-title="' . _lang('Add Invoice Payment') . '" href="' . route('invoices.add_payment', $invoice->id) . '"><i class="far fa-credit-card mr-2"></i>' . _lang('Add Payment') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('invoices.get_invoice_link', $invoice->id) . '"  data-title="' . _lang('Get share link') . '"><i class="fas fa-share-alt mr-2"></i>' . _lang('Share Invoice') . '</a>'
                . '<form action="' . route('invoices.destroy', $invoice['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . ' <div class="dropdown-divider"></div>'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-minus-circle mr-2"></i>' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('invoice_number')) {
                    $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
                }

                if ($request->has('customer_id')) {
                    $query->where('customer_id', $request->customer_id);
                }

                if ($request->has('status')) {
                    $query->whereIn('status', json_decode($request->status));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->date_range);
                    $query->whereBetween('invoice_date', [$date_range[0], $date_range[1]]);
                }
            })
            ->setRowId(function ($invoice) {
                return "row_" . $invoice->id;
            })
            ->rawColumns(['invoice_number', 'grand_total', 'amount_due', 'status', 'action'])
            ->make(true);
    }

    public function create(Request $request) {
        return view('backend.admin.invoice.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'customer_id'    => 'required',
            'title'          => 'required',
            'invoice_number' => 'required',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|after_or_equal:invoice_date',
            'product_id'     => 'required',
            'template'       => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('invoices.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $invoice                  = new Invoice();
        $invoice->customer_id     = $request->input('customer_id');
        $invoice->title           = $request->input('title');
        $invoice->invoice_number  = $request->input('invoice_number');
        $invoice->order_number    = $request->input('order_number');
        $invoice->invoice_date    = $request->input('invoice_date');
        $invoice->due_date        = $request->input('due_date');
        $invoice->sub_total       = $summary['subTotal'];
        $invoice->grand_total     = $summary['grandTotal'];
        $invoice->converted_total = convert_currency($request->activeBusiness->currency, $invoice->customer->currency, $invoice->grand_total);
        $invoice->paid            = 0;
        $invoice->discount        = $summary['discountAmount'];
        $invoice->discount_type   = $request->input('discount_type');
        $invoice->discount_value  = $request->input('discount_value');
        $invoice->template_type   = is_numeric($request->template) ? 1 : 0;
        $invoice->template        = $request->input('template');
        $invoice->note            = $request->input('note');
        $invoice->footer          = $request->input('footer');
        $invoice->short_code      = rand(100000, 9999999) . uniqid();

        $invoice->save();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $invoiceItem = $invoice->items()->save(new InvoiceItem([
                'invoice_id'   => $invoice->id,
                'product_id'   => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description'  => $request->description[$i],
                'quantity'     => $request->quantity[$i],
                'unit_cost'    => $request->unit_cost[$i],
                'sub_total'    => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$invoiceItem->product_id])) {
                foreach ($request->taxes[$invoiceItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $invoiceItem->taxes()->save(new InvoiceItemTax([
                        'invoice_id' => $invoice->id,
                        'tax_id'     => $taxId,
                        'name'       => $tax->name . ' ' . $tax->rate . ' %',
                        'amount'     => ($invoiceItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }

            $product = $invoiceItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                if ($product->stock < $request->quantity[$i]) {
                    DB::rollBack();
                    return back()->with('error', $product->name . ' ' . _lang('Stock is not available!'));
                }

                $product->stock = $product->stock - $request->quantity[$i];
                $product->save();
            }
        }

        BusinessSetting::where('name', 'invoice_number')->increment('value');

        DB::commit();

        if ($invoice->id > 0) {
            return redirect()->route('invoices.show', $invoice->id)->with('success', _lang('Saved Successfully'));
        } else {
            return back()->with('error', _lang('Something going wrong, Please try again'));
        }

    }

    /**
     * Preview Private Invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $assets    = ['summernote'];
        $alert_col = 'col-lg-8 offset-lg-2';
        $invoice   = Invoice::with(['business', 'items'])->find($id);
        return view('backend.admin.invoice.view', compact('invoice', 'id', 'alert_col', 'assets'));
    }

    public function get_invoice_link(Request $request, $id) {
        $invoice = Invoice::find($id);
        if ($request->ajax()) {
            return view('backend.admin.invoice.modal.share-link', compact('invoice', 'id'));
        }
        return back();
    }

    public function send_email(Request $request, $id) {
        if (!$request->ajax()) {
            return back();
        }
        if ($request->isMethod('get')) {
            $email_templates = EmailTemplate::whereIn('slug', ['NEW_INVOICE_CREATED', 'INVOICE_PAYMENT_REMINDER'])
                ->where('email_status', 1)->get();
            $invoice = Invoice::find($id);
            return view('backend.admin.invoice.modal.send_email', compact('invoice', 'id', 'email_templates'));
        } else {
            $validator = Validator::make($request->all(), [
                'email'   => 'required',
                'subject' => 'required',
                'message' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            }

            $customMessage = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $invoice         = Invoice::find($id);
            $customer        = $invoice->customer;
            $customer->email = $request->email;

            try {
                Notification::send($customer, new SendInvoice($invoice, $customMessage, $request->template));
                $invoice->email_send    = 1;
                $invoice->email_send_at = now();
                $invoice->save();
                return response()->json(['result' => 'success', 'message' => _lang('Email has been sent')]);
            } catch (\Exception $e) {
                return response()->json(['result' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    public function show_public_invoice($short_code, $export = 'preview') {
        $alert_col = 'col-lg-10 offset-lg-1';
        $invoice   = Invoice::withoutGlobalScopes()->with(['customer', 'business', 'items', 'taxes'])
            ->where('short_code', $short_code)
            ->first();

        if ($export == 'pdf') {
            $pdf = Pdf::loadView('backend.admin.invoice.pdf', compact('invoice'));
            return $pdf->download('invoice#-' . $invoice->invoice_number . '.pdf');
        }

        return view('backend.guest.invoice.view', compact('invoice', 'alert_col'));
    }

    public function export_pdf(Request $request, $id) {
        $invoice = Invoice::with(['business', 'items'])->find($id);
        $pdf     = Pdf::loadView('backend.admin.invoice.pdf', compact('invoice', 'id'));
        return $pdf->download('invoice#-' . $invoice->invoice_number . '.pdf');
    }

    public function approve($id) {
        $invoice = Invoice::where('id', $id)
            ->where('is_recurring', 0)
            ->first();
        if ($invoice->status == 0) {
            $invoice->status = 1;
            $invoice->save();
            return back()->with('success', _lang('Invoice has been approved'));
        }
        return back();
    }

    public function edit(Request $request, $id) {
        $invoice = Invoice::with('items')
            ->where('id', $id)
            ->where('status', '!=', 2)
            ->where('status', '!=', 99)
            ->where('is_recurring', 0)
            ->first();
        return view('backend.admin.invoice.edit', compact('invoice', 'id'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'customer_id'    => 'required',
            'title'          => 'required',
            'invoice_number' => 'required',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|date|after_or_equal:invoice_date',
            'product_id'     => 'required',
            'template'       => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('invoices.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $invoice = Invoice::where('id', $id)
            ->where('status', '!=', 2)
            ->where('status', '!=', 99)
            ->where('is_recurring', 0)
            ->first();
        $invoice->customer_id     = $request->input('customer_id');
        $invoice->title           = $request->input('title');
        $invoice->invoice_number  = $request->input('invoice_number');
        $invoice->order_number    = $request->input('order_number');
        $invoice->invoice_date    = $request->input('invoice_date');
        $invoice->due_date        = $request->input('due_date');
        $invoice->sub_total       = $summary['subTotal'];
        $invoice->grand_total     = $summary['grandTotal'];
        $invoice->converted_total = convert_currency($request->activeBusiness->currency, $invoice->customer->currency, $invoice->grand_total);
        $invoice->discount        = $summary['discountAmount'];
        $invoice->discount_type   = $request->input('discount_type');
        $invoice->discount_value  = $request->input('discount_value');
        $invoice->template_type   = $invoice->template_type   = is_numeric($request->template) ? 1 : 0;
        $invoice->template        = $request->input('template');
        $invoice->note            = $request->input('note');
        $invoice->footer          = $request->input('footer');

        $invoice->save();

        foreach ($invoice->items as $invoice_item) {
            $product = $invoice_item->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                $product->stock = $product->stock + $invoice_item->quantity;
                $product->save();
            }
            $invoice_item->delete();
        }

        for ($i = 0; $i < count($request->product_id); $i++) {
            $invoiceItem = $invoice->items()->save(new InvoiceItem([
                'invoice_id'   => $invoice->id,
                'product_id'   => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description'  => $request->description[$i],
                'quantity'     => $request->quantity[$i],
                'unit_cost'    => $request->unit_cost[$i],
                'sub_total'    => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$invoiceItem->product_id])) {
                $invoiceItem->taxes()->delete();
                foreach ($request->taxes[$invoiceItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $invoiceItem->taxes()->save(new InvoiceItemTax([
                        'invoice_id' => $invoice->id,
                        'tax_id'     => $taxId,
                        'name'       => $tax->name . ' ' . $tax->rate . ' %',
                        'amount'     => ($invoiceItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }

            $product = $invoiceItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                if ($product->stock < $request->quantity[$i]) {
                    DB::rollBack();
                    return back()->with('error', $product->name . ' ' . _lang('Stock is not available!'));
                }

                $product->stock = $product->stock - $request->quantity[$i];
                $product->save();
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('invoices.show', $invoice->id)->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $invoice, 'table' => '#invoices_table']);
        }
    }

    public function duplicate($id) {
        DB::beginTransaction();
        $invoice = Invoice::where('id', $id)
            ->where('status', '!=', 99)
            ->first();
        $newInvoice                 = $invoice->replicate();
        $newInvoice->status         = 0;
        $newInvoice->paid           = 0;
        $newInvoice->invoice_number = get_business_option('invoice_number', rand());
        $newInvoice->short_code     = rand(100000, 9999999) . uniqid();
        $newInvoice->save();

        foreach ($invoice->items as $invoiceItem) {
            $newInvoiceItem             = $invoiceItem->replicate();
            $newInvoiceItem->invoice_id = $newInvoice->id;
            $newInvoiceItem->save();

            foreach ($invoiceItem->taxes as $InvoiceItemTax) {
                $newInvoiceItemTax                  = $InvoiceItemTax->replicate();
                $newInvoiceItemTax->invoice_id      = $newInvoice->id;
                $newInvoiceItemTax->invoice_item_id = $newInvoiceItem->id;
                $newInvoiceItemTax->save();
            }

            $product = $invoiceItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                //Check Available Stock Quantity
                if ($product->stock < $newInvoiceItem->quantity) {
                    DB::rollBack();
                    return back()->with('error', $product->name . ' ' . _lang('Stock is not available!'));
                }

                $product->stock = $product->stock - $newInvoiceItem->quantity;
                $product->save();
            }
        }

        BusinessSetting::where('name', 'invoice_number')->increment('value');

        DB::commit();

        return redirect()->route('invoices.edit', $newInvoice->id);
    }

    public function add_payment(Request $request, $id) {
        if (!$request->ajax()) {
            return back();
        }
        if ($request->isMethod('get')) {
            $invoice = Invoice::where('id', $id)
                ->where('status', '!=', 0)
                ->where('status', '!=', 99)
                ->first();
            return view('backend.admin.invoice.modal.add-payment', compact('invoice'));
        } else if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'trans_date' => 'required',
                'account_id' => 'required',
                'method'     => 'required',
                'amount'     => 'required|numeric',
                'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                }
            }

            DB::beginTransaction();

            $invoice = Invoice::find($id);
            $account = Account::find($request->account_id);

            $refAmount = convert_currency($account->currency, $request->activeBusiness->currency, $request->amount);

            if ($refAmount > ($invoice->grand_total - $invoice->paid)) {
                return response()->json(['result' => 'error', 'message' => _lang('Amount must be equal or less than due amount')]);
            }

            $attachment = '';
            if ($request->hasfile('attachment')) {
                $file       = $request->file('attachment');
                $attachment = rand() . time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/media/", $attachment);
            }

            $transaction              = new Transaction();
            $transaction->trans_date  = $request->input('trans_date');
            $transaction->account_id  = $request->input('account_id');
            $transaction->method      = $request->method();
            $transaction->dr_cr       = 'cr';
            $transaction->type        = 'income';
            $transaction->amount      = $request->input('amount');
            $transaction->ref_amount  = $refAmount;
            $transaction->reference   = $request->input('reference');
            $transaction->description = _lang('Invoice Payment') . ' #' . $invoice->invoice_number;
            $transaction->attachment  = $attachment;
            $transaction->ref_id      = $invoice->id;
            $transaction->ref_type    = 'invoice';
            $transaction->customer_id = $invoice->customer_id;

            $transaction->save();

            $invoice->paid   = $invoice->paid + $transaction->ref_amount;
            $invoice->status = 3; //Partially Paid
            if ($invoice->paid >= $invoice->grand_total) {
                $invoice->status = 2; //Paid
            }
            $invoice->save();

            DB::commit();

            if ($transaction->id > 0) {
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment made successfully'), 'data' => $transaction]);
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Error occured, please try again')]);
            }
        }
    }

    public function mark_as_cancelled($id) {
        DB::beginTransaction();

        $invoice = Invoice::where('id', $id)
            ->where('status', '!=', 99)
            ->first();

        foreach ($invoice->items as $item) {
            if ($item->product->type == 'product' && $item->product->stock_management == 1) {
                $item->product->increment('stock', $item->quantity);
            }
        }
        $invoice->transactions()->delete();

        $invoice->status = 99;
        $invoice->paid   = 0;
        $invoice->save();

        DB::commit();

        return back()->with('success', _lang('Invoice Cancelled'));
    }

    public function destroy($id) {

        DB::beginTransaction();
        $invoice = Invoice::find($id);
        $invoice->transactions()->delete();
        $invoice->delete();
        DB::commit();
        
        return redirect()->route('invoices.index')->with('success', _lang('Deleted Successfully'));
    }

    private function calculateTotal(Request $request) {
        $subTotal       = 0;
        $taxAmount      = 0;
        $discountAmount = 0;
        $grandTotal     = 0;

        for ($i = 0; $i < count($request->product_id); $i++) {
            $line_qnt       = $request->quantity[$i];
            $line_unit_cost = $request->unit_cost[$i];
            $line_total     = ($line_qnt * $line_unit_cost);

            $subTotal = ($subTotal + $line_total);

            if (isset($request->taxes[$request->product_id[$i]])) {
                for ($j = 0; $j < count($request->taxes[$request->product_id[$i]]); $j++) {
                    $taxId       = $request->taxes[$request->product_id[$i]][$j];
                    $tax         = Tax::find($taxId);
                    $product_tax = ($line_total / 100) * $tax->rate;
                    $taxAmount += $product_tax;
                }
            }

            if ($request->discount_type == '0') {
                $discountAmount = ($subTotal / 100) * $request->discount_value;
            } else if ($request->discount_type == '1') {
                $discountAmount = $request->discount_value;
            }

        }

        $grandTotal = ($subTotal + $taxAmount) - $discountAmount;

        return array(
            'subTotal'       => $subTotal,
            'taxAmount'      => $taxAmount,
            'discountAmount' => $discountAmount,
            'grandTotal'     => $grandTotal,
        );

    }
}