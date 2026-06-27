<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Validator;

class LeaveTypeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets     = ['datatable'];
        $leavetypes = LeaveType::all()->sortByDesc("id");
        return view('backend.admin.leave_types.list', compact('leavetypes', 'assets'));
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
            return view('backend.admin.leave_types.modal.create');
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
            'title' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('leave_types.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $leavetype        = new LeaveType();
        $leavetype->title = $request->input('title');

        $leavetype->save();

        if (!$request->ajax()) {
            return redirect()->route('leave_types.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $leavetype, 'table' => '#leave_types_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $leavetype = LeaveType::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.leave_types.modal.edit', compact('leavetype', 'id'));
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
            'title' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('leave_types.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $leavetype        = LeaveType::find($id);
        $leavetype->title = $request->input('title');

        $leavetype->save();

        if (!$request->ajax()) {
            return redirect()->route('leave_types.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $leavetype, 'table' => '#leave_types_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $leavetype = LeaveType::find($id);
        $leavetype->delete();
        return redirect()->route('leave_types.index')->with('success', _lang('Deleted Successfully'));
    }
}