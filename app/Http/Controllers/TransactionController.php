<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class TransactionController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.transaction.list', compact('assets'));
    }

    public function get_table_data() {
        $transactions = Transaction::select('transactions.*')
            ->with('category', 'account')
            ->orderBy("transactions.id", "desc");

        return Datatables::eloquent($transactions)
            ->editColumn('category.name', function ($transaction) {
                if ($transaction->ref_id != null && $transaction->ref_type == 'invoice') {
                    return '<div class="rounded-circle color-circle mr-1" style="background:' . $transaction->category->color . '"></div>' . $transaction->category->name . ' #' . $transaction->invoice->invoice_number
                    . '<br><a href="' . route('invoices.show', $transaction->ref_id) . '" target="_blank"><i class="far fa-eye mr-1"></i>' . _lang('View Invoice') . '</a>';
                }
                if ($transaction->ref_id != null && $transaction->ref_type == 'purchase') {
                    return '<div class="rounded-circle color-circle mr-1" style="background:' . $transaction->category->color . '"></div>' . $transaction->category->name . ' #' . $transaction->purchase->bill_no
                    . '<br><a href="' . route('purchases.show', $transaction->ref_id) . '" target="_blank"><i class="far fa-eye mr-1"></i>' . _lang('View Invoice') . '</a>';
                }
                return '<div class="rounded-circle color-circle mr-1" style="background:' . $transaction->category->color . '"></div>' . $transaction->category->name;
            })
            ->editColumn('amount', function ($transaction) {
                if ($transaction->dr_cr == 'dr') {
                    return '<div class="dropdown text-right text-danger text-nowrap font-weight-bold">- ' . formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) . '</div>';
                } else {
                    return '<div class="dropdown text-right text-success text-nowrap font-weight-bold">+ ' . formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) . '</div>';
                }
            })
            ->addColumn('action', function ($transaction) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '&nbsp;</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('transactions.edit', $transaction['id']) . '" data-title="' . _lang('Update Transaction') . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('transactions.show', $transaction['id']) . '" data-title="' . _lang('Transaction Details') . '"><i class="ti-eye"></i>  ' . _lang('Details') . '</a>'
                . '<form action="' . route('transactions.destroy', $transaction['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($transaction) {
                return "row_" . $transaction->id;
            })
            ->rawColumns(['category.name', 'amount', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.transaction.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'trans_date'              => 'required',
            'account_id'              => 'required',
            'method'                  => 'required',
            'type'                    => 'required',
            'transaction_category_id' => 'required',
            'amount'                  => 'required|numeric|min:0.1',
            'attachment'              => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('transactions.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = '';
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = rand() . time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $transaction                          = new Transaction();
        $transaction->trans_date              = $request->input('trans_date');
        $transaction->transaction_category_id = $request->input('transaction_category_id');
        $transaction->account_id              = $request->input('account_id');
        $transaction->method                  = $request->method;
        $transaction->dr_cr                   = $request->type == 'income' ? 'cr' : 'dr';
        $transaction->type                    = $request->type;
        $transaction->amount                  = $request->input('amount');
        $transaction->reference               = $request->input('reference');
        $transaction->description             = $request->input('description');
        $transaction->attachment              = $attachment;

        $transaction->save();

        if (!$request->ajax()) {
            return redirect()->route('transactions.create')->with('success', _lang('Transaction Created'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Transaction Created'), 'data' => $transaction, 'table' => '#transactions_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.transaction.modal.view', compact('transaction', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.transaction.modal.edit', compact('transaction', 'id'));
        }
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
            'trans_date'              => 'required',
            'transaction_category_id' => 'nullable',
            'account_id'              => 'required',
            'method'                  => 'required',
            'amount'                  => 'required|numeric|min:0.1',
            'attachment'              => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('transactions.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = rand() . time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        DB::beginTransaction();

        $transaction = Transaction::find($id);

        if ($transaction->ref_type == 'invoice') {
            $invoice       = Invoice::find($transaction->ref_id);
            $invoice->paid = $invoice->paid - $transaction->ref_amount;
        }

        if ($transaction->ref_type == 'purchase') {
            $purcahse       = Purchase::find($transaction->ref_id);
            $purcahse->paid = $purcahse->paid - $transaction->ref_amount;
        }

        $transaction->trans_date              = $request->input('trans_date');
        $transaction->transaction_category_id = $request->input('transaction_category_id');
        $transaction->account_id              = $request->input('account_id');
        $transaction->method                  = $request->method;
        $transaction->amount                  = $request->input('amount');
        if ($transaction->ref_id != null) {
            $transaction->ref_amount = convert_currency($transaction->account->currency, $request->activeBusiness->currency, $transaction->amount);
        }
        $transaction->reference   = $request->input('reference');
        $transaction->description = $request->input('description');
        if ($request->hasfile('attachment')) {
            $transaction->attachment = $attachment;
        }

        $transaction->save();

        if ($transaction->ref_type == 'invoice') {
            $invoice->paid   = $invoice->paid + $transaction->ref_amount;
            $invoice->status = 3; //Partially Paid
            if ($invoice->paid >= $invoice->grand_total) {
                $invoice->status = 2; //Paid
            }
            $invoice->save();
        }

        if ($transaction->ref_type == 'purchase') {
            $purcahse->paid   = $purcahse->paid + $transaction->ref_amount;
            $purcahse->status = 1; //Partially Paid
            if ($purcahse->paid >= $purcahse->grand_total) {
                $purcahse->status = 2; //Paid
            }
            $purcahse->save();
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('transactions.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $transaction, 'table' => '#transactions_table']);
        }

    }

    /**
     * Show the form for transfer money between accounts.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request) {
        if (!$request->ajax()) {
            return back();
        }

        if ($request->isMethod('get')) {
            return view('backend.admin.transaction.modal.transfer');
        } else {
            $validator = Validator::make($request->all(), [
                'trans_date'     => 'required',
                'debit_account'  => 'required',
                'credit_account' => 'required|different:debit_account',
                'amount'         => 'required|numeric',
                'attachment'     => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
            ], [
                'credit_account.different' => _lang('Debit and credit account must be different'),
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                }
            }

            if (get_account_balance($request->debit_account) < $request->amount) {
                return response()->json(['result' => 'error', 'message' => _lang('Insufficient account balance')]);
            }

            $attachment = '';
            if ($request->hasfile('attachment')) {
                $file       = $request->file('attachment');
                $attachment = rand() . time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/media/", $attachment);
            }

            DB::begintransaction();

            $debit             = new Transaction();
            $debit->trans_date = $request->input('trans_date');
            $debit->account_id = $request->input('debit_account');
            $debit->dr_cr      = 'dr';
            $debit->type       = 'Transfer';
            $debit->amount     = $request->input('amount');
            $debit->reference  = $request->input('reference');
            $debit->attachment = $attachment;
            $debit->ref_id     = null;
            $debit->ref_type   = "transfer";

            $debit->save();

            $credit              = new Transaction();
            $credit->trans_date  = $request->input('trans_date');
            $credit->account_id  = $request->input('credit_account');
            $credit->dr_cr       = 'cr';
            $credit->type        = 'Transfer';
            $credit->amount      = convert_currency($debit->account->currency, $credit->account->currency, $request->amount);
            $credit->reference   = $request->input('reference');
            $credit->attachment  = $attachment;
            $credit->ref_id      = $debit->id;
            $credit->ref_type    = "transfer";
            $credit->description = $request->description ?? _lang('Received Money from') . ' ' . $debit->account->account_name;

            $credit->save();

            //Set ref ID to debit transaction
            $debit->ref_id      = $credit->id;
            $debit->description = $request->description ?? _lang('Transfer Money to') . ' ' . $credit->account->account_name;
            $debit->save();

            DB::commit();

            if (!$request->ajax()) {
                return redirect()->route('transactions.index')->with('success', _lang('Transfered Successfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Transfered Successfully')]);
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::begintransaction();

        $transaction = Transaction::find($id);
        $transaction->delete();

        DB::commit();

        return redirect()->route('transactions.index')->with('success', _lang('Deleted Successfully'));
    }
}