<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use DataTables;
use Illuminate\Http\Request;

class QuotationController extends Controller {

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
        return view('backend.client.quotation.list', compact('assets'));
    }

    public function get_table_data(Request $request) {
        $quotations = Quotation::select('quotations.*')
            ->where('customer_id', auth('client')->id())
            ->orderBy("quotations.id", "desc");

        $business = auth('client')->user()->business;

        return Datatables::eloquent($quotations, $business)
            ->editColumn('grand_total', function ($quotation) use ($business)  {
                if ($quotation->customer->currency != $business->currency) {
                    return '<div class="text-right">' . formatAmount($quotation->grand_total, currency_symbol($business->currency), $business->id) . '<br>'
                    . formatAmount($quotation->converted_total, currency_symbol($quotation->customer->currency), $business->id) . '</div>';
                }
                return '<div class="text-right">' . formatAmount($quotation->grand_total, currency_symbol($business->currency), $business->id) . '</div>';
            })
            ->addColumn('status', function ($quotation) {
                return quotation_status($quotation);
            })
            ->addColumn('action', function ($quotation) {
                return '<div class="text-center">'
                . '<a class="btn btn-xs btn-outline-primary" target="_blank" href="' . route('quotations.show_public_quotation', $quotation->short_code) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                    . '</div>';
            })
            ->setRowId(function ($quotation) {
                return "row_" . $quotation->id;
            })
            ->rawColumns(['status', 'grand_total', 'action'])
            ->make(true);

    }

}