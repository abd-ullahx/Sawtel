<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AttendanceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.attendance.list', compact('assets'));
    }

    public function get_table_data() {
        $attendances = Attendance::select([
            'attendance.date',
            DB::raw("COUNT(attendance.status) AS total_attendance"),
            DB::raw("SUM(CASE WHEN attendance.status = '0' THEN 1 ELSE 0 END) as absent"),
            DB::raw("SUM(CASE WHEN attendance.status = '1' THEN 1 ELSE 0 END) as present"),
            DB::raw("SUM(CASE WHEN attendance.status = '2' THEN 1 ELSE 0 END) as leaves"),
        ])
            ->groupBy('attendance.date');

        return Datatables::eloquent($attendances)
            ->addColumn('total', function ($attendance) {
                return $attendance->total_attendance;
            })
            ->addColumn('present', function ($attendance) {
                return $attendance->present;
            })
            ->addColumn('absent', function ($attendance) {
                return $attendance->absent;
            })
            ->addColumn('leave', function ($attendance) {
                return $attendance->leaves;
            })
            ->setRowId(function ($attendance) {
                return "row_" . $attendance->id;
            })
            ->addColumn('action', function ($attendance) {
                return '<div class="dropdown text-center">'
                . '<form action="' . route('attendance.create', $attendance['id']) . '" method="post">'
                . csrf_field()
                . '<input name="date" type="hidden" value="' . $attendance->getRawOriginal('date') . '">'
                . '<button class="btn btn-primary btn-xs" type="submit"><i class="fas fa-pencil-alt"></i> ' . _lang('Manage') . '</button>'
                    . '</form>'
                    . '</div>';
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
        if ($request->isMethod('get')) {
            $alert_col = 'col-lg-4 offset-lg-4';
            return view('backend.admin.attendance.create', compact('alert_col'));
        } else if ($request->isMethod('post')) {
            $alert_col = 'col-lg-10 offset-lg-1';
            $validator = Validator::make($request->all(), [
                'date' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->route('attendance.create')->withErrors($validator)->withInput();
            }

            $date     = $request->date;
            $weekends = json_decode(get_business_option('weekends', '[]', business_id()));
            $message  = null;
            if (in_array(date('l', strtotime($date)), $weekends)) {
                $message = _lang('The date you selected which is a weekend !');
            }

            $holiday = Holiday::where('date', $date)->first();
            if ($holiday) {
                $message = _lang('The date you selected which is a holiday !');
            }

            $employees = Employee::active()->select('employees.*', DB::raw('IFNULL(attendance.status, 1) as attendance_status'), 'attendance.remarks as attendance_remarks', 'attendance.leave_duration as attendance_leave_duration', 'leaves.leave_type', 'leaves.leave_duration', 'leaves.description as leave_description')
                ->leftJoin('attendance', function ($join) use ($date) {
                    $join->on('attendance.employee_id', 'employees.id')
                        ->where('attendance.date', $date);
                })
                ->leftJoin('leaves', function ($join) use ($date) {
                    $join->on('leaves.employee_id', 'employees.id')
                        ->where('leaves.status', 1)
                        ->whereRaw("date(leaves.start_date) <= '$date' AND date(leaves.end_date) >= '$date'");
                })
                ->orderBy('employees.id', 'ASC')
                ->get();

            return view('backend.admin.attendance.create', compact('employees', 'message', 'alert_col', 'date'));
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
            'employee_id.*' => 'required',
            'date'          => 'required',
            'status.*'      => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('attendance.create')
                ->withErrors($validator)
                ->withInput();
        }

        if (empty($request->employee_id)) {
            return back()->with('error', _lang('You must select at least one employee'))->withInput();
        }

        $data = [];
        foreach ($request->employee_id as $key => $employee_id) {
            $leave_duration = $request->leave_duration[$key];
            if ($request->status[$key] != 1 && $leave_duration == '') {
                $leave_duration = 'full_day';
            }
            array_push($data, [
                'employee_id'    => $employee_id,
                'date'           => date('Y-m-d', strtotime($request->date)),
                'status'         => $request->status[$key],
                'leave_type'     => $request->leave_type[$key],
                'leave_duration' => $leave_duration,
                'remarks'        => $request->remarks[$key],
                'business_id'    => business_id(),
            ]);
        }

        Attendance::upsert($data, ['employee_id', 'date', 'business_id', 'user_id']);

        if (!$request->ajax()) {
            return redirect()->route('attendance.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $attendance, 'table' => '#attendance_table']);
        }

    }

}