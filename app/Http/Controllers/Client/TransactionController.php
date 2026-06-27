<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;

class TransactionController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.client.transaction.list', compact('assets'));
    }

    public function get_table_data() {
        $transactions = Transaction::select('transactions.*')
            ->with('category', 'account')
            ->where('customer_id', auth('client')->id())
            ->orderBy("transactions.id", "desc");

        $business = auth('client')->user()->business;

        return Datatables::eloquent($transactions, $business)
            ->editColumn('category.name', function ($transaction) {
                if ($transaction->ref_id != null && $transaction->ref_type == 'invoice') {
                    return '<div class="rounded-circle color-circle mr-1" style="background:' . $transaction->category->color . '"></div>' . $transaction->category->name . ' #' . $transaction->invoice->invoice_number
                    . '<br><a href="' . route('invoices.show_public_invoice', $transaction->invoice->short_code) . '" target="_blank"><i class="far fa-eye mr-1"></i>' . _lang('View Invoice') . '</a>';
                }
                return '<div class="rounded-circle color-circle mr-1" style="background:' . $transaction->category->color . '"></div>' . $transaction->category->name;
            })
            ->editColumn('amount', function ($transaction) use ($business) {
                if ($transaction->dr_cr == 'dr') {
                    return '<div class="dropdown text-right text-danger text-nowrap font-weight-bold">- ' . formatAmount($transaction->amount, currency_symbol($transaction->account->currency), $business->id) . '</div>';
                } else {
                    return '<div class="dropdown text-right text-success text-nowrap font-weight-bold">+ ' . formatAmount($transaction->amount, currency_symbol($transaction->account->currency), $business->id) . '</div>';
                }
            })
            ->addColumn('action', function ($transaction) {
                return '<div class="text-center">'
                . '<a class="btn btn-xs btn-outline-primary ajax-modal" data-title="'. _lang('Transaction Details') .'" href="' . route('client.transactions.show', $transaction->id) . '"><i class="far fa-eye mr-2"></i>' . _lang('Details') . '</a>'
                    . '</div>';
            })
            ->setRowId(function ($transaction) {
                return "row_" . $transaction->id;
            })
            ->rawColumns(['category.name', 'amount', 'action'])
            ->make(true);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        if (!$request->ajax()) {
            return back();
        } else {
            $transaction = Transaction::where('id', $id)->where('customer_id', auth('client')->id())->first();
            return view('backend.client.transaction.modal.view', compact('transaction', 'id'));
        }
    }

}