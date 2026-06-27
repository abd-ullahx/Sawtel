<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollBenefit;
use App\Models\Transaction;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class PayrollController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.payroll.list', compact('assets'));
    }

    public function get_table_data() {
        $payrolls = Payroll::with('staff')->select('payslips.*');

        return Datatables::eloquent($payrolls)
            ->editColumn('staff.first_name', function ($payroll) {
                return $payroll->staff->name;
            })
            ->editColumn('month', function ($payroll) {
                return date('F', mktime(0, 0, 0, $payroll->month, 10));
            })
            ->editColumn('net_salary', function ($payroll) {
                return formatAmount($payroll->net_salary, currency_symbol(request()->activeBusiness->currency));
            })
            ->editColumn('status', function ($payroll) {
                return payroll_status($payroll->status);
            })
            ->addColumn('action', function ($payroll) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('payslips.edit', $payroll['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('payslips.show', $payroll['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('payslips.destroy', $payroll['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->filterColumn('staff.first_name', function ($query, $keyword) {
                $query->whereHas('staff', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($payroll) {
                return "row_" . $payroll->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-4 offset-lg-4';
        return view('backend.admin.payroll.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('payslips.create')
                ->withErrors($validator)
                ->withInput();
        }

        $month = $request->month;
        $year  = $request->year;

        DB::beginTransaction();

        $employees = Employee::active()
            ->with('salary_scale.salary_benefits')
            ->whereDoesntHave('payslips', function (Builder $query) use ($month, $year) {
                $query->whereRaw("payslips.month = $month AND payslips.year = $year");
            })
            ->get();

        if ($employees->count() == 0) {
            return back()->with('error', _lang('Payslip is already generated for the selected period !'));
        }

        foreach ($employees as $employee) {
            //It requires if user spend money from his own pocket
            $expense = 0;

            //Get Absence Fine
            $absence_fine = 0;
            $full_day     = $employee->salary_scale->full_day_absence_fine;
            $half_day     = $employee->salary_scale->half_day_absence_fine;

            $absence_fine = Attendance::select([
                DB::raw("IFNULL(SUM(CASE WHEN leave_duration = 'half_day' THEN $half_day ELSE $full_day END),0) AS absence_fine"),
            ])
                ->where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('attendance.status', 0)
                ->first()
                ->absence_fine;

            $benefits        = $employee->salary_scale->salary_benefits()->where('type', 'add')->get();
            $deductions      = $employee->salary_scale->salary_benefits()->where('type', 'deduct')->get();
            $total_benefits  = $employee->salary_scale->basic_salary + $expense + $benefits->sum('amount');
            $total_deduction = $absence_fine + $deductions->sum('amount');

            $payroll                 = new Payroll();
            $payroll->employee_id    = $employee->id;
            $payroll->month          = $month;
            $payroll->year           = $year;
            $payroll->current_salary = $employee->salary_scale->basic_salary;
            $payroll->expense        = $expense;
            $payroll->absence_fine   = $absence_fine;
            $payroll->net_salary     = ($total_benefits - $total_deduction);
            $payroll->status         = 0;

            $payroll->save();

            foreach ($benefits as $benefit) {
                $payroll->payroll_benefits()->save(new PayrollBenefit([
                    'payslip_id' => $payroll->id,
                    'type'       => 'add',
                    'name'       => $benefit->name,
                    'amount'     => $benefit->amount,
                ]));
            }

            foreach ($deductions as $deduction) {
                $payroll->payroll_benefits()->save(new PayrollBenefit([
                    'payslip_id' => $payroll->id,
                    'type'       => 'deduct',
                    'name'       => $deduction->name,
                    'amount'     => $deduction->amount,
                ]));
            }
        }

        DB::commit();

        if ($payroll->id > 0) {
            return redirect()->route('payslips.index')->with('success', _lang('Payslip Generated Successfully'));
        } else {
            return redirect()->route('payslips.index')->with('error', _lang('Error Occured, Please try again !'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $payroll      = Payroll::with('staff', 'payroll_benefits')->find($id);
        $working_days = Attendance::whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->groupBy('date')->get()->count();
        $absence = Attendance::where('employee_id', $payroll->employee_id)
            ->selectRaw("SUM(CASE WHEN attendance.leave_duration = 'half_day' THEN 0.5 ELSE 1 END) as absence")
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->where('attendance.status', 0)
            ->first()
            ->absence;
        $currency_symbol = currency_symbol($request->activeBusiness->currency);
        return view('backend.admin.payroll.view', compact('payroll', 'id', 'currency_symbol', 'working_days', 'absence'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $payroll = Payroll::with('staff', 'payroll_benefits')->find($id);
        return view('backend.admin.payroll.edit', compact('payroll', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();

        $payroll = Payroll::find($id);
        if ($payroll->status != 0) {
            return back()->with('error', _lang('Sorry, Only unpaid payslip can be modify !'));
        }

        $payroll->payroll_benefits()->whereNotIn('id', isset($request->allowances['payslip_id']) ? $request->allowances['payslip_id'] : [])->delete();
        $payroll->payroll_benefits()->whereNotIn('id', isset($request->deductions['payslip_id']) ? $request->deductions['payslip_id'] : [])->delete();

        $benefits = 0;
        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $payroll->payroll_benefits()->save(PayrollBenefit::firstOrNew([
                    'id'         => isset($request->allowances['payslip_id'][$i]) ? $request->allowances['payslip_id'][$i] : null,
                    'payslip_id' => $payroll->id,
                    'type'       => 'add',
                ], [
                    'name'   => $request->allowances['name'][$i],
                    'amount' => $request->allowances['amount'][$i],
                ]));
                $benefits += $request->allowances['amount'][$i];
            }
        }

        $deductions = 0;
        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $payroll->payroll_benefits()->save(PayrollBenefit::firstOrNew([
                    'id'         => isset($request->deductions['payslip_id'][$i]) ? $request->deductions['payslip_id'][$i] : null,
                    'payslip_id' => $payroll->id,
                    'type'       => 'deduct',
                ], [
                    'name'   => $request->deductions['name'][$i],
                    'amount' => $request->deductions['amount'][$i],
                ]));
                $deductions += $request->deductions['amount'][$i];
            }
        }

        $total_benefits  = $payroll->current_salary + $payroll->expense + $benefits;
        $total_deduction = $payroll->absence_fine + $deductions;

        $payroll->net_salary = ($total_benefits - $total_deduction);
        $payroll->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('payslips.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $payroll, 'table' => '#payslips_table']);
        }

    }

    public function make_payment(Request $request) {
        if ($request->isMethod('get')) {
            $alert_col = 'col-lg-4 offset-lg-4';
            return view('backend.admin.payroll.make_payment', compact('alert_col'));
        } else {
            $validator = Validator::make($request->all(), [
                'month'                   => 'required',
                'year'                    => 'required',
                'account_id'              => 'required',
                'transaction_category_id' => 'required',
                'method'                  => 'required',
            ], [
                'account_id.required'              => 'You must select debit account',
                'transaction_category_id.required' => 'Expense category is required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $payslips = Payroll::with('staff')
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('status', 0)
                ->get();
            $currency_symbol         = currency_symbol($request->activeBusiness->currency);
            $account_id              = $request->account_id;
            $transaction_category_id = $request->transaction_category_id;
            $method                  = $request->method;
            $alert_col               = 'col-lg-10 offset-lg-1';
            return view('backend.admin.payroll.make_payment', compact('payslips', 'currency_symbol', 'alert_col', 'account_id', 'transaction_category_id', 'method'));
        }
    }

    public function store_payment(Request $request) {
        if (empty($request->payslip_ids)) {
            return back()->with('error', _lang('You must select at least one employee'))->withInput();
        }

        $validator = Validator::make($request->all(), [
            'account_id'              => 'required',
            'transaction_category_id' => 'required',
        ], [
            'account_id.required'              => 'You must select debit account',
            'transaction_category_id.required' => 'Expense category is required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $account = Account::find($request->account_id);

        if (!$account) {
            return back()->with('error', _lang('Sorry, No account found'))->withInput();
        }

        if (request()->activeBusiness->currency != $account->currency) {
            return back()->with('error', _lang('Account currency and business currency must be same'))->withInput();
        }

        DB::beginTransaction();

        $payslips = Payroll::whereIn('id', $request->payslip_ids)
            ->where('status', 0)
            ->get();

        //Check Account Balance
        if (get_account_balance($request->account_id) < $payslips->sum('net_salary')) {
            return back()->with('error', _lang('Insufficient account balance'))->withInput();
        }

        $transaction                          = new Transaction();
        $transaction->trans_date              = now();
        $transaction->account_id              = $request->account_id;
        $transaction->transaction_category_id = $request->transaction_category_id;
        $transaction->method                  = $request->method;
        $transaction->dr_cr                   = 'dr';
        $transaction->type                    = 'expense';
        $transaction->amount                  = $payslips->sum('net_salary');
        $transaction->description             = _lang('Staff Salary');
        $transaction->save();

        foreach ($payslips as $payslip) {
            $payslip->status         = 1;
            $payslip->transaction_id = $transaction->id;
            $payslip->save();
        }

        DB::commit();

        return redirect()->route('payslips.index')->with('success', _lang('Payment made successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $payroll = Payroll::find($id);
        $payroll->delete();
        return redirect()->route('payslips.index')->with('success', _lang('Deleted Successfully'));
    }
}