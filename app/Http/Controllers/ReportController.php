<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {}

	public function account_balances(Request $request) {
		$page_title = _lang('Account Balances');
		$assets = ['datatable'];
		$accounts = get_account_details();
		return view('backend.admin.reports.account_balances', compact('accounts', 'assets', 'page_title'));
	}

    public function saleProductsReport(Request $request)
    {
        $invoiceItems = InvoiceItem::query()
            ->with([
                'invoice.customer',
                'product',
            ])
            ->latest('id')
            ->paginate(20);

        return view('backend.admin.reports.sale_products_report', compact('invoiceItems'));
    }

	public function account_statement(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Account Statement');
			return view('backend.admin.reports.account_statement', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$account_id = isset($request->account_id) ? $request->account_id : '';

			$account = Account::find($account_id);
			$business_id = request()->activeBusiness->id;

			if (!$account) {
				return back()->with('error', _lang('Account not found'));
			}

			DB::select("SELECT ((SELECT IFNULL(SUM(amount),0) FROM transactions WHERE dr_cr = 'cr' AND business_id = $business_id AND account_id = $account->id AND created_at < '$date1') - (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE dr_cr = 'dr' AND business_id = $business_id AND account_id = $account->id AND created_at < '$date1')) into @openingBalance");

			$data['report_data'] = DB::select("SELECT '$date1' trans_date,'Opening Balance' as category, 'Opening Balance' as description, 0 as 'debit', 0 as 'credit', @openingBalance as 'balance'
            UNION ALL
            SELECT date(trans_date), category, description, debit, credit, @openingBalance := @openingBalance + (credit - debit) as balance FROM
            (SELECT date(transactions.trans_date) as trans_date, transaction_categories.name as category, transactions.description, IF(transactions.dr_cr='dr',transactions.amount,0) as debit, IF(transactions.dr_cr='cr',transactions.amount,0) as credit FROM `transactions` JOIN accounts ON account_id=accounts.id
            LEFT JOIN transaction_categories ON transaction_categories.id=transactions.transaction_category_id WHERE accounts.id = $account->id AND accounts.business_id = $business_id AND date(transactions.trans_date) >= '$date1' AND date(transactions.trans_date) <= '$date2')
            as all_transaction");

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['account_id'] = $request->account_id;
			$data['account'] = $account;
			$data['page_title'] = _lang('Account Statement');
			return view('backend.admin.reports.account_statement', $data);
		}
	}

	public function profit_and_loss(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Profit & Loss Report');
			return view('backend.admin.reports.profit_and_loss', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$report_type = $request->report_type;

			if ($report_type == 'paid_unpaid') {
				$invoices = Invoice::with(['items.product.income_category'])
					->whereRaw("date(invoice_date) >= '$date1' AND date(invoice_date) <= '$date2'")
					->whereRaw("invoices.status != 0 AND invoices.status != 99")
					->where('is_recurring', 0)
					->get();

				$data['invoices'] = array();
				$data['sales_discount'] = 0;
				foreach ($invoices as $invoice) {
					foreach ($invoice->items as $invoice_item) {
						if (isset($data['invoices'][$invoice_item->product->income_category_id])) {
							$existingAmount = $data['invoices'][$invoice_item->product->income_category_id]['amount'];
							$data['invoices'][$invoice_item->product->income_category_id] = ['category' => $invoice_item->product->income_category->name, 'amount' => $existingAmount + $invoice_item->sub_total];
						} else {
							$data['invoices'][$invoice_item->product->income_category_id] = ['category' => $invoice_item->product->income_category->name, 'amount' => $invoice_item->sub_total];
						}
					}
					$data['sales_discount'] += $invoice->discount;
				}

				$data['othersIncome'] = Transaction::with('category')
					->selectRaw('transaction_category_id, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
					->where('dr_cr', 'cr')
					->where('ref_id', null)
				//->where('transaction_category_id', '!=', null)
					->whereRaw("date(trans_date) >= '$date1' AND date(trans_date) <= '$date2'")
					->groupBy('transaction_category_id')
					->get();

				$purchases = Purchase::with(['items.product.expense_category'])
					->whereRaw("date(purchase_date) >= '$date1' AND date(purchase_date) <= '$date2'")
					->get();

				$data['purchases'] = array();
				$data['purchase_discount'] = 0;
				foreach ($purchases as $purchase) {
					foreach ($purchase->items as $purchase_item) {
						if (isset($data['purchases'][$purchase_item->product->expense_category_id])) {
							$existingAmount = $data['purchases'][$purchase_item->product->expense_category_id]['amount'];
							$data['purchases'][$purchase_item->product->expense_category_id] = ['category' => $purchase_item->product->expense_category->name, 'amount' => $existingAmount + $purchase_item->sub_total];
						} else {
							$data['purchases'][$purchase_item->product->expense_category_id] = ['category' => $purchase_item->product->expense_category->name, 'amount' => $purchase_item->sub_total];
						}
					}
					$data['purchase_discount'] += $purchase->discount;
				}

				$data['othersExpense'] = Transaction::with('category')
					->selectRaw('transaction_category_id, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
					->where('dr_cr', 'dr')
					->where('ref_id', null)
				//->where('transaction_category_id', '!=', null)
					->whereRaw("date(trans_date) >= '$date1' AND date(trans_date) <= '$date2'")
					->groupBy('transaction_category_id')
					->get();

			}

			if ($report_type == 'paid') {
				$invoices = Invoice::with(['items.product.income_category'])
					->whereRaw("date(invoice_date) >= '$date1' AND date(invoice_date) <= '$date2'")
					->whereRaw("invoices.status != 0 AND invoices.status != 99")
					->where('invoices.paid', '>', 0)
					->where('is_recurring', 0)
					->get();

				$data['invoices'] = array();
				$data['sales_discount'] = 0;
				foreach ($invoices as $invoice) {
					$percentage = (100 / $invoice->grand_total) * $invoice->paid;

					foreach ($invoice->items as $invoice_item) {
						if (isset($data['invoices'][$invoice_item->product->income_category_id])) {
							$existingAmount = $data['invoices'][$invoice_item->product->income_category_id]['amount'];
							$data['invoices'][$invoice_item->product->income_category_id] = ['category' => $invoice_item->product->income_category->name, 'amount' => $existingAmount + (($percentage / 100) * $invoice_item->sub_total)];
						} else {
							$data['invoices'][$invoice_item->product->income_category_id] = ['category' => $invoice_item->product->income_category->name, 'amount' => ($percentage / 100) * $invoice_item->sub_total];
						}
					}
					$data['sales_discount'] += ($percentage / 100) * $invoice->discount;
				}

				$data['othersIncome'] = Transaction::with('category')
					->selectRaw('transaction_category_id, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
					->where('dr_cr', 'cr')
					->where('ref_id', null)
				//->where('transaction_category_id', '!=', null)
					->whereRaw("date(trans_date) >= '$date1' AND date(trans_date) <= '$date2'")
					->groupBy('transaction_category_id')
					->get();

				$purchases = Purchase::with(['items.product.expense_category'])
					->whereRaw("date(purchase_date) >= '$date1' AND date(purchase_date) <= '$date2'")
					->where('purchases.paid', '>', 0)
					->get();

				$data['purchases'] = array();
				$data['purchase_discount'] = 0;
				foreach ($purchases as $purchase) {
					$percentage = (100 / $purchase->grand_total) * $purchase->paid;
					foreach ($purchase->items as $purchase_item) {
						if (isset($data['purchases'][$purchase_item->product->expense_category_id])) {
							$existingAmount = $data['purchases'][$purchase_item->product->expense_category_id]['amount'];
							$data['purchases'][$purchase_item->product->expense_category_id] = ['category' => $purchase_item->product->expense_category->name, 'amount' => $existingAmount + (($percentage / 100) * $purchase_item->sub_total)];
						} else {
							$data['purchases'][$purchase_item->product->expense_category_id] = ['category' => $purchase_item->product->expense_category->name, 'amount' => ($percentage / 100) * $purchase_item->sub_total];
						}
					}
					$data['purchase_discount'] += ($percentage / 100) * $purchase->discount;
				}

				$data['othersExpense'] = Transaction::with('category')
					->selectRaw('transaction_category_id, ROUND(IFNULL(SUM((transactions.amount/currency_rate) * 1),0),2) as amount')
					->where('dr_cr', 'dr')
					->where('ref_id', null)
				//->where('transaction_category_id', '!=', null)
					->whereRaw("date(trans_date) >= '$date1' AND date(trans_date) <= '$date2'")
					->groupBy('transaction_category_id')
					->get();

			}

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['report_type'] = $request->report_type;
			$data['report_data'] = true;
			$data['currency'] = request()->activeBusiness->currency;
			$data['page_title'] = _lang('Profit & Loss Report');
			return view('backend.admin.reports.profit_and_loss', $data);
		}

	}

	public function transactions_report(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Transactions Report');
			return view('backend.admin.reports.transactions_report', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$account_id = isset($request->account_id) ? $request->account_id : '';
			$transaction_type = isset($request->transaction_type) ? $request->transaction_type : '';

			$data['report_data'] = Transaction::select('transactions.*')
				->with(['account', 'category'])
				->when($transaction_type, function ($query, $transaction_type) {
					return $query->where('type', $transaction_type);
				})
				->when($account_id, function ($query, $account_id) {
					return $query->whereHas('account', function ($query) use ($account_id) {
						return $query->where('account_id', $account_id);
					});
				})
				->whereRaw("date(transactions.trans_date) >= '$date1' AND date(transactions.trans_date) <= '$date2'")
				->orderBy('transactions.trans_date', 'desc')
				->get();

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['status'] = $request->status;
			$data['account_id'] = $request->account_id;
			$data['transaction_type'] = $request->transaction_type;
			$data['page_title'] = _lang('Transactions Report');
			return view('backend.admin.reports.transactions_report', $data);
		}
	}

	public function income_by_customer(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Income By Customers');
			return view('backend.admin.reports.income_by_customer', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$customer_id = isset($request->customer_id) ? $request->customer_id : '';

			$data['report_data'] = Invoice::with('customer')
				->selectRaw('customer_id, SUM(grand_total) as total_income, sum(paid) as total_paid')
				->when($customer_id, function ($query, $customer_id) {
					return $query->where('customer_id', $customer_id);
				})
				->whereRaw("date(invoices.invoice_date) >= '$date1' AND date(invoices.invoice_date) <= '$date2'")
				->where('is_recurring', 0)
				->where('status', '!=', 0)
				->where('status', '!=', 99)
				->groupBy('customer_id')
				->get();

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['customer_id'] = $request->customer_id;
			$data['currency'] = request()->activeBusiness->currency;
			$data['page_title'] = _lang('Income By Customers');
			return view('backend.admin.reports.income_by_customer', $data);
		}
	}

	public function purchase_by_vendor(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Purchase By Vendors');
			return view('backend.admin.reports.purchase_by_vendor', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$vendor_id = isset($request->vendor_id) ? $request->vendor_id : '';

			$data['report_data'] = Purchase::with('vendor')
				->selectRaw('vendor_id, SUM(grand_total) as total_income, sum(paid) as total_paid')
				->when($vendor_id, function ($query, $vendor_id) {
					return $query->where('vendor_id', $vendor_id);
				})
				->whereRaw("date(purchase_date) >= '$date1' AND date(purchase_date) <= '$date2'")
				->groupBy('vendor_id')
				->get();

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['vendor_id'] = $request->vendor_id;
			$data['currency'] = request()->activeBusiness->currency;
			$data['page_title'] = _lang('Purchase By Vendors');
			return view('backend.admin.reports.purchase_by_vendor', $data);
		}
	}

	public function attendance_report(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Attendance Report');
			return view('backend.admin.reports.attendance_report', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);
			$data = array();
			$month = $request->month;
			$year = $request->year;

			$data['calendar'] = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$attendance_list = Attendance::select('attendance.*')
				->whereMonth('date', $month)
				->whereYear('date', $year)
				->orderBy('date', 'asc')
				->orderBy('employee_id', 'asc')
				->get();

			$holidays = Holiday::whereMonth('date', $month)
				->whereYear('date', $year)
				->orderBy('date', 'ASC')
				->pluck('date')
				->toArray();

			$data['employees'] = Employee::active()
				->orderBy('employees.id', 'asc')
				->get();

			$weekends = json_decode(get_business_option('weekends', '[]', business_id()));
			$report_data = [];

			for ($day = 1; $day <= $data['calendar']; $day++) {
				$date = date('Y-m-d', strtotime("$year-$month-$day"));
				$status = ['A', 'P', 'L', 'W', 'H'];

				foreach ($attendance_list as $attendance) {
					if (in_array($date, $holidays)) {
						$report_data[$attendance->employee_id][$day] = $status[4]; // Holiday
					} else {
						if ($date == $attendance->getRawOriginal('date')) {
							$report_data[$attendance->employee_id][$day] = $status[$attendance->status];
						} else {
							if (in_array(date('l', strtotime($date)), $weekends)) {
								$report_data[$attendance->employee_id][$day] = $status[3];
							}
						}
					}
				}

			}

			$data['month'] = $request->month;
			$data['year'] = $request->year;
			$data['page_title'] = _lang('Attendance Report');
			$data['report_data'] = $report_data;
			$data['attendance_list'] = $attendance_list;
			return view('backend.admin.reports.attendance_report', $data);
		}
	}

	public function payroll_report(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Payroll Report');
			return view('backend.admin.reports.payroll_report', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$month = $request->month;
			$year = $request->year;

			$data['report_data'] = Payroll::with('staff')
				->select('payslips.*')
				->where('month', $month)
				->where('year', $year)
				->get();

			$data['month'] = $request->month;
			$data['year'] = $request->year;
			$data['currency'] = request()->activeBusiness->currency;
			$data['page_title'] = _lang('Payroll Report');
			return view('backend.admin.reports.payroll_report', $data);
		}
	}

	public function tax_report(Request $request) {
		if ($request->isMethod('get')) {
			$page_title = _lang('Tax Report');
			return view('backend.admin.reports.tax_report', compact('page_title'));
		} else if ($request->isMethod('post')) {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0);

			$data = array();
			$date1 = $request->date1;
			$date2 = $request->date2;
			$report_type = $request->report_type;
			$business_id = request()->activeBusiness->id;

			if ($report_type == 'paid_unpaid') {
				$data['sales_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM(invoice_items.sub_total) as sales_amount,
                SUM(invoice_item_taxes.amount) as sales_tax FROM invoice_items LEFT JOIN invoice_item_taxes ON invoice_items.id=invoice_item_taxes.invoice_item_id
                AND invoice_items.business_id = $business_id JOIN invoices ON invoices.id=invoice_items.invoice_id AND invoices.status != 0 AND invoices.status != 99 AND invoices.is_recurring = 0
                AND invoices.invoice_date >= '$date1' AND invoices.invoice_date <= '$date2' RIGHT JOIN taxes ON taxes.id=invoice_item_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");

				$data['purchase_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM(purchase_items.sub_total) as purchase_amount,
                SUM(purchase_item_taxes.amount) as purchase_tax FROM purchase_items LEFT JOIN purchase_item_taxes ON purchase_items.id=purchase_item_taxes.purchase_item_id
                AND purchase_items.business_id = $business_id JOIN purchases ON purchases.id=purchase_items.purchase_id AND purchases.purchase_date >= '$date1' AND purchases.purchase_date <= '$date2'
                RIGHT JOIN taxes ON taxes.id=purchase_item_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");
			}

			if ($report_type == 'paid') {
				$data['sales_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM((((100 / invoices.grand_total) * invoices.paid) / 100) * invoice_items.sub_total) as sales_amount,
                SUM((((100 / invoices.grand_total) * invoices.paid) / 100) * invoice_item_taxes.amount) as sales_tax FROM invoice_items LEFT JOIN invoice_item_taxes ON invoice_items.id=invoice_item_taxes.invoice_item_id
                AND invoice_items.business_id = $business_id JOIN invoices ON invoices.id=invoice_items.invoice_id AND invoices.status != 0 AND invoices.status != 99 AND invoices.is_recurring = 0
                AND invoices.paid > 0 AND invoices.invoice_date >= '$date1' AND invoices.invoice_date <= '$date2' RIGHT JOIN taxes ON taxes.id=invoice_item_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");

				$data['purchase_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM((((100 / purchases.grand_total) * purchases.paid) / 100) * purchase_items.sub_total) as purchase_amount,
                SUM((((100 / purchases.grand_total) * purchases.paid) / 100) * purchase_item_taxes.amount) as purchase_tax FROM purchase_items LEFT JOIN purchase_item_taxes ON purchase_items.id=purchase_item_taxes.purchase_item_id
                AND purchase_items.business_id = $business_id JOIN purchases ON purchases.id=purchase_items.purchase_id AND purchases.paid > 0 AND purchases.purchase_date >= '$date1' AND purchases.purchase_date <= '$date2'
                RIGHT JOIN taxes ON taxes.id=purchase_item_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");
			}

			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['report_type'] = $request->report_type;
			$data['currency'] = request()->activeBusiness->currency;
			$data['page_title'] = _lang('Tax Report');
			$data['report_data'] = true;
			return view('backend.admin.reports.tax_report', $data);
		}
	}

}