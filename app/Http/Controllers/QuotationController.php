<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Quotation;
use App\Models\quotationItem;
use App\Models\quotationItemTax;
use App\Models\Tax;
use App\Notifications\SendQuotation;
use Barryvdh\DomPDF\Facade\Pdf;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Validator;

class QuotationController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.quotation.list', compact('assets'));
    }

    public function get_table_data(Request $request) {
        $quotations = Quotation::select('quotations.*')
            ->with('customer')
            ->orderBy("quotations.id", "desc");

        return Datatables::eloquent($quotations)
            ->editColumn('grand_total', function ($quotation) {
                if ($quotation->customer->currency != request()->activeBusiness->currency) {
                    return '<div class="text-right">' . formatAmount($quotation->grand_total, currency_symbol(request()->activeBusiness->currency)) . '<br>'
                    . formatAmount($quotation->converted_total, currency_symbol($quotation->customer->currency)) . '</div>';
                }
                return '<div class="text-right">' . formatAmount($quotation->grand_total, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->addColumn('status', function ($quotation) {
                return quotation_status($quotation);
            })
            ->addColumn('action', function ($quotation) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('quotations.edit', $quotation['id']) . '"><i class="far fa-edit mr-2"></i>' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('quotations.show', $quotation['id']) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                . '<a class="dropdown-item" href="' . route('quotations.duplicate', $quotation['id']) . '"><i class="far fa-copy mr-2"></i>' . _lang('Duplicate') . '</a>'
                . ' <div class="dropdown-divider"></div>'
                . '<a class="dropdown-item" href="' . route('quotations.export_pdf', $quotation['id']) . '"><i class="far fa-file-pdf mr-2"></i>' . _lang('Export as PDF') . '</a>'
                . '<a class="dropdown-item" href="' . route('quotations.convert_to_invoice', $quotation['id']) . '"><i class="fas fa-recycle mr-2"></i>' . _lang('Convert to Invoice') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('quotations.get_quotation_link', $quotation->id) . '"  data-title="' . _lang('Get share link') . '"><i class="fas fa-share-alt mr-2"></i>' . _lang('Share Quotation') . '</a>'
                . '<form action="' . route('quotations.destroy', $quotation['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . ' <div class="dropdown-divider"></div>'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-minus-circle mr-2"></i>' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($quotation) {
                return "row_" . $quotation->id;
            })
            ->rawColumns(['status', 'grand_total', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return view('backend.admin.quotation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'customer_id'      => 'required',
            'title'            => 'required',
            'quotation_number' => 'required',
            'quotation_date'   => 'required',
            'expired_date'     => 'required',
            'product_id'       => 'required',
            'template'         => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('quotations.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $quotation                   = new Quotation();
        $quotation->customer_id      = $request->input('customer_id');
        $quotation->title            = $request->input('title');
        $quotation->quotation_number = $request->input('quotation_number');
        $quotation->po_so_number     = $request->input('po_so_number');
        $quotation->quotation_date   = $request->input('quotation_date');
        $quotation->expired_date     = $request->input('expired_date');
        $quotation->sub_total        = $summary['subTotal'];
        $quotation->grand_total      = $summary['grandTotal'];
        $quotation->converted_total  = convert_currency($request->activeBusiness->currency, $quotation->customer->currency, $quotation->grand_total);
        $quotation->discount         = $summary['discountAmount'];
        $quotation->discount_type    = $request->input('discount_type');
        $quotation->discount_value   = $request->input('discount_value');
        $quotation->template_type    = is_numeric($request->template) ? 1 : 0;
        $quotation->template         = $request->input('template');
        $quotation->note             = $request->input('note');
        $quotation->footer           = $request->input('footer');
        $quotation->short_code       = rand(100000, 9999999) . uniqid();

        $quotation->save();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $quotationItem = $quotation->items()->save(new quotationItem([
                'quotation_id' => $quotation->id,
                'product_id'   => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description'  => $request->description[$i],
                'quantity'     => $request->quantity[$i],
                'unit_cost'    => $request->unit_cost[$i],
                'sub_total'    => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$quotationItem->product_id])) {
                foreach ($request->taxes[$quotationItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $quotationItem->taxes()->save(new quotationItemTax([
                        'quotation_id' => $quotation->id,
                        'tax_id'       => $taxId,
                        'name'         => $tax->name . ' ' . $tax->rate . ' %',
                        'amount'       => ($quotationItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }
        }

        //Increment Quotation Number
        BusinessSetting::where('name', 'quotation_number')->increment('value');

        DB::commit();

        if ($quotation->id > 0) {
            return redirect()->route('quotations.show', $quotation->id)->with('success', _lang('Saved Successfully'));
        } else {
            return back()->with('error', _lang('Something going wrong, Please try again'));
        }

    }

    /**
     * Preview Private quotation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col = 'col-lg-8 offset-lg-2';
        $assets    = ['summernote'];
        $quotation = quotation::with(['business', 'items'])->find($id);
        return view('backend.admin.quotation.view', compact('quotation', 'id', 'alert_col', 'assets'));
    }

    public function get_quotation_link(Request $request, $id) {
        $quotation = quotation::find($id);
        if ($request->ajax()) {
            return view('backend.admin.quotation.modal.share-link', compact('quotation', 'id'));
        }
        return back();
    }

    public function send_email(Request $request, $id) {
        if (!$request->ajax()) {
            return back();
        }
        if ($request->isMethod('get')) {
            $email_templates = EmailTemplate::whereIn('slug', ['NEW_QUOTATION_CREATED'])
                ->where('email_status', 1)->get();
            $quotation = Quotation::find($id);
            return view('backend.admin.quotation.modal.send_email', compact('quotation', 'id', 'email_templates'));
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

            $quotation       = Quotation::find($id);
            $customer        = $quotation->customer;
            $customer->email = $request->email;

            try {
                Notification::send($customer, new SendQuotation($quotation, $customMessage, $request->template));
                return response()->json(['result' => 'success', 'message' => _lang('Email has been sent')]);
            } catch (\Exception$e) {
                return response()->json(['result' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    /**
     * Preview Public quotation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_public_quotation($short_code, $export = 'preview') {
        $alert_col = 'col-lg-8 offset-lg-2';
        $quotation = Quotation::withoutGlobalScopes()->with(['customer', 'business', 'items', 'taxes'])
            ->where('short_code', $short_code)
            ->first();
        if ($export == 'pdf') {
            $pdf = Pdf::loadView('backend.admin.quotation.pdf', compact('quotation'));
            return $pdf->download('quotation#-' . $quotation->quotation_number . '.pdf');
        }

        return view('backend.guest.quotation.view', compact('quotation', 'alert_col'));
    }

    public function export_pdf(Request $request, $id) {
        $quotation = Quotation::with(['business', 'items'])->find($id);
        $pdf = Pdf::loadView('backend.admin.quotation.pdf', compact('quotation', 'id'));
        return $pdf->download('quotation#-' . $quotation->quotation_number . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $quotation = Quotation::with('items')->find($id);
        return view('backend.admin.quotation.edit', compact('quotation', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'customer_id'      => 'required',
            'title'            => 'required',
            'quotation_number' => 'required',
            'quotation_date'   => 'required',
            'expired_date'     => 'required',
            'product_id'       => 'required',
            'template'         => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('quotations.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $quotation                   = Quotation::find($id);
        $quotation->customer_id      = $request->input('customer_id');
        $quotation->title            = $request->input('title');
        $quotation->quotation_number = $request->input('quotation_number');
        $quotation->po_so_number     = $request->input('po_so_number');
        $quotation->quotation_date   = $request->input('quotation_date');
        $quotation->expired_date     = $request->input('expired_date');
        $quotation->sub_total        = $summary['subTotal'];
        $quotation->grand_total      = $summary['grandTotal'];
        $quotation->converted_total  = convert_currency($request->activeBusiness->currency, $quotation->customer->currency, $quotation->grand_total);
        $quotation->discount         = $summary['discountAmount'];
        $quotation->discount_type    = $request->input('discount_type');
        $quotation->discount_value   = $request->input('discount_value');
        $quotation->template_type    = is_numeric($request->template) ? 1 : 0;
        $quotation->template         = $request->input('template');
        $quotation->note             = $request->input('note');
        $quotation->footer           = $request->input('footer');

        $quotation->save();

        $quotation->items()->delete();
        for ($i = 0; $i < count($request->product_id); $i++) {
            $quotationItem = $quotation->items()->save(new quotationItem([
                'quotation_id' => $quotation->id,
                'product_id'   => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description'  => $request->description[$i],
                'quantity'     => $request->quantity[$i],
                'unit_cost'    => $request->unit_cost[$i],
                'sub_total'    => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$quotationItem->product_id])) {
                $quotationItem->taxes()->delete();
                foreach ($request->taxes[$quotationItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $quotationItem->taxes()->save(new quotationItemTax([
                        'quotation_id' => $quotation->id,
                        'tax_id'       => $taxId,
                        'name'         => $tax->name . ' ' . $tax->rate . ' %',
                        'amount'       => ($quotationItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('quotations.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $quotation, 'table' => '#quotations_table']);
        }

    }

    /** Duplicate Invoice */
    public function duplicate($id) {
        DB::beginTransaction();
        $quotation                      = Quotation::find($id);
        $newQuotation                   = $quotation->replicate();
        $newQuotation->quotation_number = get_business_option('quotation_number', rand());
        $newQuotation->short_code       = rand(100000, 9999999) . uniqid();
        $newQuotation->save();

        foreach ($quotation->items as $quotationItem) {
            $newquotationItem               = $quotationItem->replicate();
            $newquotationItem->quotation_id = $newQuotation->id;
            $newquotationItem->save();

            foreach ($quotationItem->taxes as $QuotationItemTax) {
                $newQuotationItemTax                    = $QuotationItemTax->replicate();
                $newQuotationItemTax->quotation_id      = $newQuotation->id;
                $newQuotationItemTax->quotation_item_id = $newquotationItem->id;
                $newQuotationItemTax->save();
            }
        }

        //Increment Invoice Number
        BusinessSetting::where('name', 'quotation_number')->increment('value');

        DB::commit();

        return redirect()->route('quotations.edit', $newQuotation->id);
    }

    /** Convert to Invoice **/
    public function convert_to_invoice($id) {
        DB::beginTransaction();

        $quotation = Quotation::find($id);

        $invoice                  = new Invoice();
        $invoice->customer_id     = $quotation->customer_id;
        $invoice->title           = get_business_option('invoice_title', 'Invoice');
        $invoice->invoice_number  = get_business_option('invoice_number', '100001');
        $invoice->order_number    = $quotation->po_so_number;
        $invoice->invoice_date    = date('Y-m-d');
        $invoice->due_date        = date('Y-m-d');
        $invoice->sub_total       = $quotation->sub_total;
        $invoice->grand_total     = $quotation->grand_total;
        $invoice->converted_total = $quotation->converted_total;
        $invoice->paid            = 0;
        $invoice->discount        = $quotation->discount;
        $invoice->discount_type   = $quotation->discount_type;
        $invoice->discount_value  = $quotation->discount_value;
        $invoice->template_type   = $quotation->template_type;
        $invoice->template        = $quotation->template;
        $invoice->note            = $quotation->note;
        $invoice->footer          = $quotation->footer;
        $invoice->short_code      = rand(100000, 9999999) . uniqid();

        $invoice->save();

        foreach ($quotation->items as $quotationItem) {
            $invoiceItem = $invoice->items()->save(new InvoiceItem([
                'invoice_id'   => $invoice->id,
                'product_id'   => $quotationItem->product_id,
                'product_name' => $quotationItem->product_name,
                'description'  => $quotationItem->description,
                'quantity'     => $quotationItem->quantity,
                'unit_cost'    => $quotationItem->unit_cost,
                'sub_total'    => ($quotationItem->unit_cost * $quotationItem->quantity),
            ]));

            foreach ($quotationItem->taxes as $quotationItemTax) {
                $invoiceItem->taxes()->save(new InvoiceItemTax([
                    'invoice_id' => $invoice->id,
                    'tax_id'     => $quotationItemTax->tax_id,
                    'name'       => $quotationItemTax->name,
                    'amount'     => $quotationItemTax->amount,
                ]));
            }

            //Update Stock
            $product = $invoiceItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                //Check Available Stock Quantity
                if ($product->stock < $quotationItem->quantity) {
                    DB::rollBack();
                    return back()->with('error', $product->name . ' ' . _lang('Stock is not available!'));
                }

                $product->stock = $product->stock - $quotationItem->quantity;
                $product->save();
            }

        }

        //Increment Invoice Number
        BusinessSetting::where('name', 'invoice_number')->increment('value');

        DB::commit();

        return redirect()->route('invoices.edit', $invoice->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $quotation = Quotation::find($id);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', _lang('Deleted Successfully'));
    }

    private function calculateTotal(Request $request) {
        $subTotal       = 0;
        $taxAmount      = 0;
        $discountAmount = 0;
        $grandTotal     = 0;

        for ($i = 0; $i < count($request->product_id); $i++) {
            //Calculate Sub Total
            $line_qnt       = $request->quantity[$i];
            $line_unit_cost = $request->unit_cost[$i];
            $line_total     = ($line_qnt * $line_unit_cost);

            //Show Sub Total
            $subTotal = ($subTotal + $line_total);

            //Calculate Taxes
            if (isset($request->taxes[$request->product_id[$i]])) {
                for ($j = 0; $j < count($request->taxes[$request->product_id[$i]]); $j++) {
                    $taxId       = $request->taxes[$request->product_id[$i]][$j];
                    $tax         = Tax::find($taxId);
                    $product_tax = ($line_total / 100) * $tax->rate;
                    $taxAmount += $product_tax;
                }
            }

            //Calculate Discount
            if ($request->discount_type == '0') {
                $discountAmount = ($subTotal / 100) * $request->discount_value;
            } else if ($request->discount_type == '1') {
                $discountAmount = $request->discount_value;
            }
        }

        //Calculate Grand Total
        $grandTotal = ($subTotal + $taxAmount) - $discountAmount;

        return array(
            'subTotal'       => $subTotal,
            'taxAmount'      => $taxAmount,
            'discountAmount' => $discountAmount,
            'grandTotal'     => $grandTotal,
        );

    }
}