<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class DashboardController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $data             = array();
        $data['assets']   = ['datatable'];
        $data['business'] = auth('client')->user()->business;
        $data['invoice']  = Invoice::selectRaw('COUNT(id) as total_invoice, SUM(grand_total) as total_amount, sum(paid) as total_paid')
            ->where('customer_id', auth('client')->id())
            ->where('is_recurring', 0)
            ->where('status', '!=', 0)
            ->first();
        $data['recent_invoices'] = Invoice::where('customer_id', auth('client')->id())
            ->where('is_recurring', 0)
            ->orderBy('invoice_date', 'desc')
            ->limit(10)
            ->get();

        return view('backend.client.dashboard', $data);
    }

}
