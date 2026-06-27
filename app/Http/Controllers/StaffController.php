<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDepartmentHistory;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class StaffController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.staff.list', compact('assets'));
    }

    public function get_table_data() {
        $employees = Employee::with('department', 'designation', 'salary_scale')
            ->select('employees.*');

        return Datatables::eloquent($employees)
            ->addColumn('salary_scale.basic_salary', function ($employee) {
                return formatAmount($employee->salary_scale->basic_salary, currency_symbol(request()->activeBusiness->currency));
            })
            ->addColumn('action', function ($employee) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('staffs.edit', $employee['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('staff_documents.index', $employee['id']) . '"><i class="fas fa-folder-open"></i> ' . _lang('Documents') . '</a>'
                . '<a class="dropdown-item" href="' . route('staffs.show', $employee['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('staffs.destroy', $employee['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($employee) {
                return "row_" . $employee->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-10 offset-lg-1';
        return view('backend.admin.staff.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'employee_id'     => 'required|unique:employees',
            'first_name'      => 'required|max:50',
            'last_name'       => 'required|max:50',
            'date_of_birth'   => 'required',
            'email'           => 'nullable|email|unique:employees|max:191',
            'phone'           => 'nullable|max:30',
            'department_id'   => 'required',
            'designation_id'  => 'required',
            'salary_scale_id' => 'required',
            'joining_date'    => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('staffs.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        $employee                  = new Employee();
        $employee->employee_id     = $request->input('employee_id');
        $employee->first_name      = $request->input('first_name');
        $employee->last_name       = $request->input('last_name');
        $employee->fathers_name    = $request->input('fathers_name');
        $employee->mothers_name    = $request->input('mothers_name');
        $employee->date_of_birth   = $request->input('date_of_birth');
        $employee->email           = $request->input('email');
        $employee->phone           = $request->input('phone');
        $employee->city            = $request->input('city');
        $employee->state           = $request->input('state');
        $employee->zip             = $request->input('zip');
        $employee->country         = $request->input('country');
        $employee->department_id   = $request->input('department_id');
        $employee->designation_id  = $request->input('designation_id');
        $employee->salary_scale_id = $request->input('salary_scale_id');
        $employee->joining_date    = $request->input('joining_date');
        $employee->end_date        = $request->input('end_date');
        $employee->bank_name       = $request->input('bank_name');
        $employee->branch_name     = $request->input('branch_name');
        $employee->account_name    = $request->input('account_name');
        $employee->account_number  = $request->input('account_number');
        $employee->swift_code      = $request->input('swift_code');
        $employee->remarks         = $request->input('remarks');

        $employee->save();

        //Update Employee History
        $history              = new EmployeeDepartmentHistory();
        $history->employee_id = $employee->id;
        $history->details     = json_encode(array(
            'employee_id'  => $employee->employee_id,
            'department'   => $employee->department,
            'designation'  => $employee->designation,
            'salary_scale' => $employee->salary_scale,
            'joining_date' => $employee->joining_date,
            'end_date'     => $employee->end_date,
        ));
        $history->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('staffs.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $employee, 'table' => '#employees_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $employee  = Employee::with('department', 'designation', 'salary_scale')->find($id);
        return view('backend.admin.staff.view', compact('employee', 'id', 'alert_col'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $employee  = Employee::find($id);
        return view('backend.admin.staff.edit', compact('employee', 'id', 'alert_col'));
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
            'first_name'             => 'required|max:50',
            'last_name'              => 'required|max:50',
            'date_of_birth'          => 'required',
            'email'                  => [
                'nullable',
                'email',
                Rule::unique('employees')->ignore($id),
            ],
            'phone'                  => 'nullable|max:30',
            'update_company_details' => 'required',
            'employee_id'            => [
                'required_if:update_company_details,1',
                Rule::unique('employees')->ignore($id),
            ],
            'department_id'          => 'required_if:update_company_details,1',
            'designation_id'         => 'required_if:update_company_details,1',
            'salary_scale_id'        => 'required_if:update_company_details,1',
            'joining_date'           => 'required_if:update_company_details,1',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('staffs.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();
        $employee                = Employee::find($id);
        $employee->first_name    = $request->input('first_name');
        $employee->last_name     = $request->input('last_name');
        $employee->fathers_name  = $request->input('fathers_name');
        $employee->mothers_name  = $request->input('mothers_name');
        $employee->date_of_birth = $request->input('date_of_birth');
        $employee->email         = $request->input('email');
        $employee->phone         = $request->input('phone');
        $employee->city          = $request->input('city');
        $employee->state         = $request->input('state');
        $employee->zip           = $request->input('zip');
        $employee->country       = $request->input('country');

        if ($request->update_company_details == 1) {
            $employee->employee_id     = $request->input('employee_id');
            $employee->department_id   = $request->input('department_id');
            $employee->designation_id  = $request->input('designation_id');
            $employee->salary_scale_id = $request->input('salary_scale_id');
            $employee->joining_date    = $request->input('joining_date');
            $employee->end_date        = $request->input('end_date');
            //Update Employee History
            $history              = new EmployeeDepartmentHistory();
            $history->employee_id = $employee->id;
            $history->details     = json_encode(array(
                'employee_id'  => $employee->employee_id,
                'department'   => $employee->department,
                'designation'  => $employee->designation,
                'salary_scale' => $employee->salary_scale,
                'joining_date' => $employee->joining_date,
                'end_date'     => $employee->end_date,
            ));
            $history->save();

        }
        $employee->bank_name      = $request->input('bank_name');
        $employee->branch_name    = $request->input('branch_name');
        $employee->account_name   = $request->input('account_name');
        $employee->account_number = $request->input('account_number');
        $employee->swift_code     = $request->input('swift_code');
        $employee->remarks        = $request->input('remarks');

        $employee->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('staffs.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $employee, 'table' => '#employees_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $employee = Employee::find($id);
        $employee->delete();
        return redirect()->route('staffs.index')->with('success', _lang('Deleted Successfully'));
    }
}