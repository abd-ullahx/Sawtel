<?php

namespace App\Http\Controllers;

use App\Models\Award;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class AwardController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.award.list', compact('assets'));
    }

    public function get_table_data() {
        $awards = Award::select('awards.*')->with('staff');

        return Datatables::eloquent($awards)
            ->addColumn('action', function ($award) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('awards.edit', $award['id']) . '" data-title="' . _lang('Update Award') . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('awards.show', $award['id']) . '" data-title="' . _lang('Award Details') . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('awards.destroy', $award['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($award) {
                return "row_" . $award->id;
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
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.award.modal.create');
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
            'employee_id' => 'required',
            'award_date'  => 'required',
            'award_name'  => 'required',
            'award'       => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('awards.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $award              = new Award();
        $award->employee_id = $request->input('employee_id');
        $award->award_date  = $request->input('award_date');
        $award->award_name  = $request->input('award_name');
        $award->award       = $request->input('award');
        $award->details     = $request->input('details');

        $award->save();

        if (!$request->ajax()) {
            return redirect()->route('awards.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $award, 'table' => '#awards_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $award = Award::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.award.modal.view', compact('award', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $award = Award::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.award.modal.edit', compact('award', 'id'));
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
            'employee_id' => 'required',
            'award_date'  => 'required',
            'award_name'  => 'required',
            'award'       => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('awards.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $award              = Award::find($id);
        $award->employee_id = $request->input('employee_id');
        $award->award_date  = $request->input('award_date');
        $award->award_name  = $request->input('award_name');
        $award->award       = $request->input('award');
        $award->details     = $request->input('details');

        $award->save();

        if (!$request->ajax()) {
            return redirect()->route('awards.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $award, 'table' => '#awards_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $award = Award::find($id);
        $award->delete();
        return redirect()->route('awards.index')->with('success', _lang('Deleted Successfully'));
    }
}