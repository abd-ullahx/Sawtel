<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use DataTables;
use Illuminate\Http\Request;

class InvoiceController extends Controller {

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
        return view('backend.client.invoice.list', compact('assets'));
    }

    public function get_table_data(Request $request) {
        $invoices = Invoice::select('invoices.*')
            ->where('is_recurring', 0)
            ->where('customer_id', auth('client')->id())
            ->orderBy("invoices.id", "desc");

        $business = auth('client')->user()->business;

        return Datatables::eloquent($invoices, $business)
            ->editColumn('invoice_number', function ($invoice) {
                $invoice_number = $invoice->invoice_number;
                if ($invoice->parent_id != null) {
                    $invoice_number .= '<div class="text-height-0 font-italic"><small>' . _lang('Recurring') . '</small></div>';
                }
                return $invoice_number;
            })
            ->editColumn('grand_total', function ($invoice) use ($business) {
                if ($invoice->customer->currency != $business->currency) {
                    return '<div class="text-right">' . formatAmount($invoice->grand_total, currency_symbol($business->currency), $business->id) . '<br>'
                    . formatAmount($invoice->converted_total, currency_symbol($invoice->customer->currency), $business->id) . '</div>';
                }
                return '<div class="text-right">' . formatAmount($invoice->grand_total, currency_symbol($business->currency), $business->id) . '</div>';
            })
            ->addColumn('amount_due', function ($invoice) use ($business) {
                return '<div class="text-right">' . formatAmount($invoice->grand_total - $invoice->paid, currency_symbol($business->currency), $business->id) . '</div>';
            })
            ->editColumn('status', function ($invoice) {
                return '<div class="text-center">' . invoice_status($invoice) . '</div>';
            })
            ->addColumn('action', function ($invoice) {
                return '<div class="text-center">'
                . '<a class="btn btn-xs btn-outline-primary" target="_blank" href="' . route('invoices.show_public_invoice', $invoice->short_code) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                . '</div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('invoice_number')) {
                    $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
                }

                if ($request->has('status')) {
                    $query->whereIn('status', json_decode($request->status));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->date_range);
                    $query->whereBetween('invoice_date', [$date_range[0], $date_range[1]]);
                }

                if ($request->has('due_date_range')) {
                    $due_date_range = explode(" - ", $request->due_date_range);
                    $query->whereBetween('due_date', [$due_date_range[0], $due_date_range[1]]);
                }
            })
            ->setRowId(function ($invoice) {
                return "row_" . $invoice->id;
            })
            ->rawColumns(['invoice_number', 'grand_total', 'amount_due', 'status', 'action'])
            ->make(true);
    }

}