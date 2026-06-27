<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Validator;

class DesignationController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets       = ['datatable'];
        $designations = Designation::all()->sortByDesc("id");
        return view('backend.admin.designation.list', compact('designations', 'assets'));
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
            return view('backend.admin.designation.modal.create');
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
            'name'          => 'required',
            'department_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('designations.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $designation                = new Designation();
        $designation->name          = $request->input('name');
        $designation->descriptions  = $request->input('descriptions');
        $designation->department_id = $request->input('department_id');

        $designation->save();
        $designation->department_id = $designation->department->name;

        if (!$request->ajax()) {
            return redirect()->route('designations.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $designation, 'table' => '#designations_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $designation = Designation::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.designation.modal.view', compact('designation', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $designation = Designation::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.designation.modal.edit', compact('designation', 'id'));
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
            'name'          => 'required',
            'department_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('designations.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $designation                = Designation::find($id);
        $designation->name          = $request->input('name');
        $designation->descriptions  = $request->input('descriptions');
        $designation->department_id = $request->input('department_id');

        $designation->save();
        $designation->department_id = $designation->department->name;

        if (!$request->ajax()) {
            return redirect()->route('designations.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $designation, 'table' => '#designations_table']);
        }

    }

    public function get_designations(Request $request, $department_id) {
        $designations = Designation::where('department_id', $department_id)->get();
        return response()->json($designations);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $designation = Designation::find($id);
        try {
            $designation->delete();
            return redirect()->route('designations.index')->with('success', _lang('Deleted Successfully'));
        } catch (\Exception $e) {
            return redirect()->route('designations.index')->with('error', _lang('This items is already exists in other entity'));
        }
    }
}