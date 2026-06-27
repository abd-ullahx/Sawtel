<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseItemTax;
use App\Models\Tax;
use App\Models\Transaction;
use DataTables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class PurchaseController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function index()
    {
        $assets = ['datatable'];
        return view('backend.admin.purchase.list', compact('assets'));
    }

    public function get_table_data(Request $request)
    {
        $purchases = Purchase::select('purchases.*')
            ->with('vendor')
            ->when(request('type') == 'purchase_alert', function ($query) {
                $query->where('delivery_status', 'on_the_way')
                    ->orderBy("due_date");
            })
            ->when(!request('type'), function ($query) {
                $query->orderByDesc("id");
            });

        return Datatables::eloquent($purchases)
            ->editColumn('grand_total', function ($purchase) {
                if ($purchase->vendor->currency != request()->activeBusiness->currency) {
                    return '<div class="text-right">' . formatAmount($purchase->grand_total, currency_symbol(request()->activeBusiness->currency)) . '<br>'
                        . formatAmount($purchase->converted_total, currency_symbol($purchase->vendor->currency)) . '</div>';
                }
                return '<div class="text-right">' . formatAmount($purchase->grand_total, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->addColumn('amount_due', function ($purchase) {
                return '<div class="text-right">' . formatAmount($purchase->grand_total - $purchase->paid, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('status', function ($purchase) {
                return '<div class="text-center">' . purchase_status($purchase) . '</div>';
            })
            ->editColumn('order_status', function ($purchase) {
                return '<div class="text-center">' . purchase_order_status($purchase) . '</div>';
            })
            ->addColumn('action', function ($purchase) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item ' . ($purchase->status == 2 ? "disabled" : "") . '" href="' . ($purchase->status != 2 ? route('purchases.edit', $purchase['id']) : '') . '"><i class="far fa-edit mr-2"></i>' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('purchases.show', $purchase['id']) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                    . '<a class="dropdown-item" href="' . route('purchases.duplicate', $purchase['id']) . '"><i class="far fa-copy mr-2"></i>' . _lang('Duplicate') . '</a>'
                    . '<a class="dropdown-item ajax-modal" href="' . route('purchases.add_payment', $purchase['id']) . '" data-title="' . _lang('Add Payment') . '"><i class="far fa-credit-card mr-2"></i>' . _lang('Add Payment') . '</a>'
                    . '<form action="' . route('purchases.destroy', $purchase['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . ' <div class="dropdown-divider"></div>'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-minus-circle mr-2"></i>' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('bill_no')) {
                    $query->where('bill_no', 'like', "%{$request->bill_no}%")
                        ->orWhere('bar_code', 'like', "%{$request->bill_no}%");
                }

                if ($request->has('vendor_id')) {
                    $query->where('vendor_id', $request->vendor_id);
                }

                if ($request->has('status')) {
                    $query->whereIn('status', json_decode($request->status));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->date_range);
                    $query->whereBetween('invoice_date', [$date_range[0], $date_range[1]]);
                }
            })
            ->setRowId(function ($purchase) {
                return "row_" . $purchase->id;
            })
            ->rawColumns(['grand_total', 'amount_due', 'status', 'order_status', 'action'])
            ->make(true);
    }

    public function create(Request $request)
    {
        return view('backend.admin.purchase.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'title' => 'required',
            'bill_no' => 'required',
            'purchase_date' => 'required|date',
            'due_date' => 'required|after_or_equal:purchase_date',
            'product_id' => 'required',
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

        $purchase = new Purchase();
        $purchase->vendor_id = $request->input('vendor_id');
        $purchase->title = $request->input('title');
        $purchase->bill_no = $request->input('bill_no');
        $purchase->po_so_number = $request->input('po_so_number');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->due_date = $request->input('due_date');
        $purchase->sub_total = $summary['subTotal'];
        $purchase->grand_total = $summary['grandTotal'];
        $purchase->converted_total = convert_currency($request->activeBusiness->currency, $purchase->vendor->currency, $purchase->grand_total);
        $purchase->paid = 0;
        $purchase->discount = $summary['discountAmount'];
        $purchase->discount_type = $request->input('discount_type');
        $purchase->discount_value = $request->input('discount_value');
        $purchase->template_type = 0;
        $purchase->template = $request->input('template');
        $purchase->note = $request->input('note');
        $purchase->footer = $request->input('footer');
        $purchase->short_code = rand(100000, 9999999) . uniqid();
        $purchase->delivery_status = 'on_the_way';
        $purchase->bar_code = rand(111111111111, 999999999999);
        //$purchase->bar_code = '4666655562353';
        $purchase->save();

        if ($purchase->grand_total == 0) {
            $purchase->status = 2;
            $purchase->save();
        }

        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem = $purchase->items()->save(new PurchaseItem([
                'purchase_id' => $purchase->id,
                'product_id' => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description' => $request->description[$i],
                'quantity' => $request->quantity[$i],
                'unit_cost' => $request->unit_cost[$i],
                'sub_total' => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$purchaseItem->product_id])) {
                foreach ($request->taxes[$purchaseItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $purchaseItem->taxes()->save(new PurchaseItemTax([
                        'purchase_id' => $purchase->id,
                        'tax_id' => $taxId,
                        'name' => $tax->name . ' ' . $tax->rate . ' %',
                        'amount' => ($purchaseItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }

            //Update Stock
//            $product = $purchaseItem->product;
//            if ($product->type == 'product' && $product->stock_management == 1) {
//                $product->stock = $product->stock + $request->quantity[$i];
//                $product->save();
//            }

        }

        BusinessSetting::where('name', 'bill_no')->increment('value');

        DB::commit();

        if ($purchase->id > 0) {
            return redirect()->route('purchases.show', $purchase->id)->with('success', _lang('Saved Successfully'));
        } else {
            return back()->with('error', _lang('Something going wrong, Please try again'));
        }

    }

    public function show(Request $request, $id)
    {

//        $imageUrl = 'https://barcode.tec-it.com/barcode.ashx?data=115487965201%0A&translate-esc=on&code=Code128&translate-esc=on'; // URL of the image
//
//        // Get the image's content
//        $imageContent = file_get_contents($imageUrl);
//
//        // Get the image's MIME type
//        $mimeType = mime_content_type($imageUrl);
//
//        // Set the proper headers and force download
//        return Response::make($imageContent, 200, [
//            'Content-Type' => $mimeType,
//            'Content-Disposition' => 'attachment; filename="downloaded_image.jpg"'
//        ]);

        $alert_col = 'col-lg-8 offset-lg-2';
        $purchase = Purchase::with(['business', 'items'])->find($id);
        return view('backend.admin.purchase.view', compact('purchase', 'id', 'alert_col'));
    }

    public function edit(Request $request, $id)
    {
        $purchase = Purchase::with('items')
            ->where('id', $id)
            ->where('status', '!=', 2)
            ->first();
        return view('backend.admin.purchase.edit', compact('purchase', 'id'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'title' => 'required',
            'bill_no' => 'required',
            'purchase_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:purchase_date',
            'product_id' => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('purchases.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $purchase = Purchase::where('id', $id)
            ->where('status', '!=', 2)
            ->first();
        $purchase->vendor_id = $request->input('vendor_id');
        $purchase->title = $request->input('title');
        $purchase->bill_no = $request->input('bill_no');
        $purchase->po_so_number = $request->input('po_so_number');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->due_date = $request->input('due_date');
        $purchase->sub_total = $summary['subTotal'];
        $purchase->grand_total = $summary['grandTotal'];
        $purchase->converted_total = convert_currency($request->activeBusiness->currency, $purchase->vendor->currency, $purchase->grand_total);
        $purchase->discount = $summary['discountAmount'];
        $purchase->discount_type = $request->input('discount_type');
        $purchase->discount_value = $request->input('discount_value');
        $purchase->template_type = 0;
        $purchase->template = $request->input('template');
        $purchase->note = $request->input('note');
        $purchase->footer = $request->input('footer');

        $purchase->save();

        //Update Invoice item
        foreach ($purchase->items as $purchase_item) {
            $product = $purchase_item->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                $product->stock = $product->stock - $purchase_item->quantity;
                $product->save();
            }
            $purchase_item->delete();
        }

        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem = $purchase->items()->save(new PurchaseItem([
                'purchase_id' => $purchase->id,
                'product_id' => $request->product_id[$i],
                'product_name' => $request->product_name[$i],
                'description' => $request->description[$i],
                'quantity' => $request->quantity[$i],
                'unit_cost' => $request->unit_cost[$i],
                'sub_total' => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            if (isset($request->taxes[$purchaseItem->product_id])) {
                $purchaseItem->taxes()->delete();
                foreach ($request->taxes[$purchaseItem->product_id] as $taxId) {
                    $tax = Tax::find($taxId);

                    $purchaseItem->taxes()->save(new PurchaseItemTax([
                        'purchase_id' => $purchase->id,
                        'tax_id' => $taxId,
                        'name' => $tax->name . ' ' . $tax->rate . ' %',
                        'amount' => ($purchaseItem->sub_total / 100) * $tax->rate,
                    ]));
                }
            }

            //Update Stock
            $product = $purchaseItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                $product->stock = $product->stock + $request->quantity[$i];
                $product->save();
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            if ($purchase->status == 0) {
                return redirect()->route('purchases.show', $purchase->id)->with('success', _lang('Updated Successfully'));
            } else {
                return redirect()->route('purchases.index')->with('success', _lang('Updated Successfully'));
            }
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully')]);
        }

    }

    /** Duplicate Invoice */
    public function duplicate($id)
    {
        DB::beginTransaction();
        $purchase = Purchase::find($id);
        $newPurchase = $purchase->replicate();
        $newPurchase->status = 0;
        $newPurchase->paid = 0;
        $newPurchase->short_code = rand(100000, 9999999) . uniqid();
        $newPurchase->save();

        foreach ($purchase->items as $purchaseItem) {
            $newPurchaseItem = $purchaseItem->replicate();
            $newPurchaseItem->purchase_id = $newPurchase->id;
            $newPurchaseItem->save();

            foreach ($purchaseItem->taxes as $PurchaseItemTax) {
                $newPurchaseItemTax = $PurchaseItemTax->replicate();
                $newPurchaseItemTax->purchase_id = $newPurchase->id;
                $newPurchaseItemTax->purchase_item_id = $newPurchaseItem->id;
                $newPurchaseItemTax->save();
            }

            //Update Stock
            $product = $purchaseItem->product;
            if ($product->type == 'product' && $product->stock_management == 1) {
                $product->stock = $product->stock + $newPurchaseItem->quantity;
                $product->save();
            }
        }

        DB::commit();

        return redirect()->route('purchases.edit', $newPurchase->id);
    }

    public function add_payment(Request $request, $id)
    {
        if (!$request->ajax()) {
            return back();
        }
        if ($request->isMethod('get')) {
            $purchase = Purchase::find($id);
            return view('backend.admin.purchase.modal.add-payment', compact('purchase'));
        } else if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'trans_date' => 'required',
                'account_id' => 'required',
                'method' => 'required',
                'amount' => 'required|numeric',
                'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                }
            }

            DB::beginTransaction();

            $purchase = Purchase::find($id);
            $account = Account::find($request->account_id);

            $refAmount = convert_currency($account->currency, $request->activeBusiness->currency, $request->amount);
            if ($refAmount > ($purchase->grand_total - $purchase->paid)) {
                return response()->json(['result' => 'error', 'message' => _lang('Amount must be equal or less than due amount')]);
            }

            $attachment = '';
            if ($request->hasfile('attachment')) {
                $file = $request->file('attachment');
                $attachment = rand() . time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/media/", $attachment);
            }

            $transaction = new Transaction();
            $transaction->trans_date = $request->input('trans_date');
            $transaction->account_id = $request->input('account_id');
            $transaction->method = $request->input('method');
            $transaction->dr_cr = 'dr';
            $transaction->type = 'expense';
            $transaction->amount = $request->input('amount');
            $transaction->ref_amount = $refAmount;
            $transaction->reference = $request->input('reference');
            $transaction->description = _lang('Purchase / Bill') . ' #' . $purchase->bill_no;
            $transaction->attachment = $attachment;
            $transaction->ref_id = $purchase->id;
            $transaction->ref_type = 'purchase';

            $transaction->save();

            $purchase->paid = $purchase->paid + $transaction->ref_amount;
            $purchase->status = 1; //Partially Paid
            if ($purchase->paid >= $purchase->grand_total) {
                $purchase->status = 2; //Paid
            }
            $purchase->save();

            DB::commit();

            if ($transaction->id > 0) {
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment made successfully'), 'data' => $transaction]);
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Error occured, please try again')]);
            }
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        $purchase = Purchase::find($id);
        $purchase->transactions()->delete();
        $purchase->delete();
        DB::commit();
        return redirect()->route('purchases.index')->with('success', _lang('Deleted Successfully'));
    }

    private function calculateTotal(Request $request)
    {
        $subTotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;
        $grandTotal = 0;

        for ($i = 0; $i < count($request->product_id); $i++) {
            //Calculate Sub Total
            $line_qnt = $request->quantity[$i];
            $line_unit_cost = $request->unit_cost[$i];
            $line_total = ($line_qnt * $line_unit_cost);

            //Show Sub Total
            $subTotal = ($subTotal + $line_total);

            //Calculate Taxes
            if (isset($request->taxes[$request->product_id[$i]])) {
                for ($j = 0; $j < count($request->taxes[$request->product_id[$i]]); $j++) {
                    $taxId = $request->taxes[$request->product_id[$i]][$j];
                    $tax = Tax::find($taxId);
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
            'subTotal' => $subTotal,
            'taxAmount' => $taxAmount,
            'discountAmount' => $discountAmount,
            'grandTotal' => $grandTotal,
        );

    }

    public function orderReceived($id)
    {
        $purchase = Purchase::query()
            ->with('items')
            ->where('id', $id)
            ->firstOrFail();

        if ($purchase->items->count()) {
            foreach ($purchase->items as $item) {
                Product::query()
                    ->where('id', $item->product_id)
                    ->where('stock_management', true)
                    ->increment('stock', $item->quantity);
            }
        }

        $purchase->delivery_status = 'received';
        $purchase->save();
        return redirect()->route('purchases.show', $purchase->id)->with('success', 'Successfully Updated');
    }

    public function get_csv_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        try {
            $file = $request->file('csv_file');

            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $csvData = [];
                $header = null;

                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if (!$header) {
                        $header = $row;
                        continue;
                    }

                    $rowData = [];
                    foreach ($header as $index => $column) {
                        $rowData[$column] = $row[$index];
                    }

                    $csvData[] = $rowData;
                }

                fclose($handle);

                return response()->json(['success' => 'File uploaded successfully', 'data' => $csvData]);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Error reading the file.']);
        }

        return response()->json(['error' => 'Error reading the file.']);
    }

}