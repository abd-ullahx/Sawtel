<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $user_type = $user->user_type;
        $data = array();
        $data['assets'] = ['datatable'];

        $month = date('m');
        $year = date('Y');

        $data['current_month_income'] = Transaction::selectRaw("IFNULL(SUM((transactions.amount/currency_rate) * 1),0) as total")
            ->where('dr_cr', 'cr')
            ->whereMonth("trans_date", $month)
            ->whereYear("trans_date", $year)
            ->first();

        $data['current_month_expense'] = Transaction::selectRaw("IFNULL(SUM((transactions.amount/currency_rate) * 1),0) as total")
            ->where('dr_cr', 'dr')
            ->whereMonth("trans_date", $month)
            ->whereYear("trans_date", $year)
            ->first();

        $data['invoice'] = Invoice::selectRaw('COUNT(id) as total_invoice, SUM(grand_total) as total_amount, sum(paid) as total_paid')
            ->where('is_recurring', 0)
            ->where('status', '!=', 0)
            ->where('status', '!=', 99)
            ->first();

        $data['purchase'] = Purchase::selectRaw('COUNT(id) as total_invoice, SUM(grand_total) as total_amount, sum(paid) as total_paid')
            ->where('status', '!=', 0)
            ->first();

        $data['accounts'] = get_account_details();

        $data['transactions'] = Transaction::limit(10)->orderBy('id', 'desc')->get();

        if ($user_type == 'admin') {
            return view("backend.admin.dashboard-admin", $data);
        } else if ($user_type == 'user') {
            return view("backend.admin.dashboard-user", $data);
        }
    }

    public function current_month_income_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function current_month_expense_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function due_invoice_amount_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function due_purchase_amount_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function cashflow_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function account_balance_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function income_by_category_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function expense_by_category_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function recent_transaction_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function json_income_by_category()
    {
        $transactions = Transaction::selectRaw('transaction_category_id, ref_id, ref_type, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
            ->with('category')
            ->where('dr_cr', 'cr')
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('transaction_category_id', 'ref_type')
            ->get();
        $category = array();
        $colors = array();
        $amounts = array();

        foreach ($transactions as $transaction) {
            array_push($category, $transaction->category->name);
            array_push($colors, $transaction->category->color);
            array_push($amounts, (double)$transaction->amount);
        }

        echo json_encode(array('amounts' => $amounts, 'category' => $category, 'colors' => $colors));
    }

    public function json_expense_by_category()
    {
        $transactions = Transaction::selectRaw('transaction_category_id, ref_id, ref_type, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
            ->with('category')
            ->where('dr_cr', 'dr')
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('transaction_category_id', 'ref_type')
            ->get();

        $category = array();
        $colors = array();
        $amounts = array();

        foreach ($transactions as $transaction) {
            array_push($category, $transaction->category->name);
            array_push($colors, $transaction->category->color);
            array_push($amounts, (double)$transaction->amount);
        }

        echo json_encode(array('amounts' => $amounts, 'category' => $category, 'colors' => $colors));
    }

    public function json_cashflow()
    {
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $transactions = Transaction::selectRaw('MONTH(trans_date) as td, dr_cr, type, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('td', 'type')
            ->get();

        $deposit = array();
        $withdraw = array();

        foreach ($transactions as $transaction) {
            if ($transaction->type == 'income') {
                $deposit[$transaction->td] = $transaction->amount;
            } else if ($transaction->type == 'expense') {
                $withdraw[$transaction->td] = $transaction->amount;
            }
        }

        $decimal_place = get_option('decimal_places', 2);

        echo json_encode(array('month' => $months, 'deposit' => $deposit, 'withdraw' => $withdraw, 'decimal_place' => $decimal_place));
    }

}
