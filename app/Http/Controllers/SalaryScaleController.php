<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SalaryBenefit;
use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SalaryScaleController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($department_id = '') {
        $assets       = ['datatable'];
        $salaryscales = SalaryScale::when($department_id, function ($query, $department_id) {
            return $query->where('department_id', $department_id);
        })
            ->orderBy("department_id")
            ->orderBy("designation_id")
            ->get();
        $departments = Department::all();
        return view('backend.admin.salary_scale.list', compact('salaryscales', 'assets', 'department_id', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-10 offset-lg-1';
        return view('backend.admin.salary_scale.create', compact('alert_col'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'department_id'         => 'required',
            'designation_id'        => 'required',
            'grade_number'          => 'required|integer',
            'basic_salary'          => 'required|numeric',
            'full_day_absence_fine' => 'required|numeric',
            'half_day_absence_fine' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('salary_scales.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();
        $salaryscale                        = new SalaryScale();
        $salaryscale->department_id         = $request->input('department_id');
        $salaryscale->designation_id        = $request->input('designation_id');
        $salaryscale->grade_number          = $request->input('grade_number');
        $salaryscale->basic_salary          = $request->input('basic_salary');
        $salaryscale->full_day_absence_fine = $request->input('full_day_absence_fine');
        $salaryscale->half_day_absence_fine = $request->input('half_day_absence_fine');

        $salaryscale->save();

        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $salaryscale->salary_benefits()->save(new SalaryBenefit([
                    'salary_scale_id' => $salaryscale->id,
                    'name'            => $request->allowances['name'][$i],
                    'amount'          => $request->allowances['amount'][$i],
                    'type'            => 'add',
                ]));
            }
        }

        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $salaryscale->salary_benefits()->save(new SalaryBenefit([
                    'salary_scale_id' => $salaryscale->id,
                    'name'            => $request->deductions['name'][$i],
                    'amount'          => $request->deductions['amount'][$i],
                    'type'            => 'deduct',
                ]));
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('salary_scales.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $salaryscale, 'table' => '#salary_scales_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $salaryscale = SalaryScale::with('salary_benefits')->find($id);
        return view('backend.admin.salary_scale.view', compact('salaryscale', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col   = 'col-lg-10 offset-lg-1';
        $salaryscale = SalaryScale::with('salary_benefits')->find($id);
        return view('backend.admin.salary_scale.edit', compact('salaryscale', 'id', 'alert_col'));
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
            'department_id'  => 'required',
            'designation_id' => 'required',
            'grade_number'   => 'required|integer',
            'basic_salary'   => 'required|numeric',
            'full_day_absence_fine' => 'required|numeric',
            'half_day_absence_fine' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('salary_scales.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();
        $salaryscale                 = SalaryScale::find($id);
        $salaryscale->department_id  = $request->input('department_id');
        $salaryscale->designation_id = $request->input('designation_id');
        $salaryscale->grade_number   = $request->input('grade_number');
        $salaryscale->basic_salary   = $request->input('basic_salary');
        $salaryscale->full_day_absence_fine = $request->input('full_day_absence_fine');
        $salaryscale->half_day_absence_fine = $request->input('half_day_absence_fine');


        $salaryscale->save();

        $salaryscale->salary_benefits()->whereNotIn('id', isset($request->allowances['salary_benefit_id']) ? $request->allowances['salary_benefit_id'] : [])->delete();
        $salaryscale->salary_benefits()->whereNotIn('id', isset($request->deductions['salary_benefit_id']) ? $request->deductions['salary_benefit_id'] : [])->delete();

        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $salaryscale->salary_benefits()->save(SalaryBenefit::firstOrNew([
                    'id'              => isset($request->allowances['salary_benefit_id'][$i]) ? $request->allowances['salary_benefit_id'][$i] : null,
                    'salary_scale_id' => $salaryscale->id,
                    'type'            => 'add',
                ], [
                    'name'   => $request->allowances['name'][$i],
                    'amount' => $request->allowances['amount'][$i],
                ]));
            }
        }

        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $salaryscale->salary_benefits()->save(SalaryBenefit::firstOrNew([
                    'id'              => isset($request->deductions['salary_benefit_id'][$i]) ? $request->deductions['salary_benefit_id'][$i] : null,
                    'salary_scale_id' => $salaryscale->id,
                    'type'            => 'deduct',
                ], [
                    'salary_scale_id' => $salaryscale->id,
                    'name'            => $request->deductions['name'][$i],
                    'amount'          => $request->deductions['amount'][$i],
                ]));
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('salary_scales.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $salaryscale, 'table' => '#salary_scales_table']);
        }

    }

    public function get_salary_scales($designation_id) {
        $salaryScales = SalaryScale::where('designation_id', $designation_id)->get();
        return response()->json($salaryScales);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $salaryscale = SalaryScale::find($id);
        try {
            $salaryscale->delete();
            return redirect()->route('salary_scales.index')->with('success', _lang('Deleted Successfully'));
        } catch (\Exception $e) {
            return redirect()->route('salary_scales.index')->with('error', _lang('This items is already exists in other entity'));
        }
    }
}