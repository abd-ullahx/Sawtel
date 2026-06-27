<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class LeaveController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.leave.list', compact('assets'));
    }

    public function get_table_data() {
        $leaves = Leave::select('leaves.*')
            ->with('staff');

        return Datatables::eloquent($leaves)
            ->editColumn('leave_duration', function ($leave) {
                return $leave->leave_duration == 'full_day' ? _lang('Full Day') : _lang('Half Day');
            })
            ->editColumn('total_days', function ($leave) {
                return $leave->total_days.' '._lang('days');
            })
            ->editColumn('status', function ($leave) {
                return '<div class="text-center">'.leave_status($leave->status).'</div>';
            })
            ->addColumn('action', function ($leave) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('leaves.edit', $leave['id']) . '" data-title="' . _lang('Update Leave') . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('leaves.show', $leave['id']) . '" data-title="' . _lang('Leave Details') . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('leaves.destroy', $leave['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($leave) {
                return "row_" . $leave->id;
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
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.leave.modal.create');
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
            'employee_id'    => 'required',
            'leave_type'     => 'required',
            'leave_duration' => 'required',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'status'         => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('leaves.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $leave                 = new Leave();
        $leave->employee_id    = $request->input('employee_id');
        $leave->leave_type     = $request->input('leave_type');
        $leave->leave_duration = $request->input('leave_duration');
        $leave->start_date     = $request->input('start_date');
        $leave->end_date       = $request->input('end_date');
        $leave->total_days     = $request->input('total_days');
        $leave->description    = $request->input('description');
        $leave->status         = $request->input('status');

        $leave->save();

        if (!$request->ajax()) {
            return redirect()->route('leaves.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $leave, 'table' => '#leaves_table']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $leave = Leave::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.leave.modal.view', compact('leave', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $leave = Leave::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.leave.modal.edit', compact('leave', 'id'));
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
            'employee_id'    => 'required',
            'leave_type'     => 'required',
            'leave_duration' => 'required',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'status'         => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('leaves.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $leave                 = Leave::find($id);
        $leave->employee_id    = $request->input('employee_id');
        $leave->leave_type     = $request->input('leave_type');
        $leave->leave_duration = $request->input('leave_duration');
        $leave->start_date     = $request->input('start_date');
        $leave->end_date       = $request->input('end_date');
        $leave->total_days     = $request->input('total_days');
        $leave->description    = $request->input('description');
        $leave->status         = $request->input('status');
        $leave->business_id    = $request->input('business_id');

        $leave->save();

        if (!$request->ajax()) {
            return redirect()->route('leaves.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $leave, 'table' => '#leaves_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $leave = Leave::find($id);
        $leave->delete();
        return redirect()->route('leaves.index')->with('success', _lang('Deleted Successfully'));
    }
}