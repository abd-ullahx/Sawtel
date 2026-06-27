<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class HolidayController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.holiday.list', compact('assets'));
    }

    public function get_table_data() {
        $holidays = Holiday::select('holidays.*')
            ->whereRaw('YEAR(date)=?', [date('Y')]);

        return Datatables::eloquent($holidays)
            ->addColumn('action', function ($holiday) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('holidays.edit', $holiday['id']) . '" data-title="' . _lang('Update Holiday') . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<form action="' . route('holidays.destroy', $holiday['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($holiday) {
                return "row_" . $holiday->id;
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
            return view('backend.admin.holiday.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $customMessages = [];
        $data           = [];
        foreach ($request->date as $key => $value) {
            $customMessages["date.$key.distinct"] = 'This date (:input) should be unique';
            $customMessages["date.$key.unique"]   = 'This date (:input) should be unique';
            array_push($data, [
                'title'       => $request->title[$key],
                'date'        => $value,
                'business_id' => business_id(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title'   => 'required|array|min:1',
            'title.*' => 'required|distinct',
            'date'    => 'required|array|min:1',
            'date.*'  => 'required|distinct|unique:holidays,date|date',
        ], $customMessages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('holidays.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        Holiday::insert($data);

        if (!$request->ajax()) {
            return redirect()->route('holidays.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully')]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $holiday = Holiday::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.holiday.modal.edit', compact('holiday', 'id'));
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
            'title' => 'required',
            'date'  => [
                'required',
                'date',
                Rule::unique('holidays')->ignore($id),
            ],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('holidays.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $holiday        = Holiday::find($id);
        $holiday->title = $request->input('title');
        $holiday->date  = $request->input('date');

        $holiday->save();

        if (!$request->ajax()) {
            return redirect()->route('holidays.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $holiday, 'table' => '#holidays_table']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function weekends(Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            if ($request->isMethod('get')) {
                $weekends = json_decode(get_business_option('weekends', '[]', business_id()));
                return view('backend.admin.holiday.modal.weekends', compact('weekends'));
            } else {
                update_business_option('weekends', json_encode($request->weekends), business_id());
                if (!$request->ajax()) {
                    return redirect()->route('holidays.index')->with('success', _lang('Updated Successfully'));
                } else {
                    return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully')]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $holiday = Holiday::find($id);
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', _lang('Deleted Successfully'));
    }
}