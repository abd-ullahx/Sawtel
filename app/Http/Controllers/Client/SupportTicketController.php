<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Notifications\NewSupportMessage;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SupportTicketController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.client.support_ticket.list', compact('assets'));
    }

    public function get_table_data() {
        $supporttickets = SupportTicket::select('support_tickets.*')
            ->where('customer_id', auth('client')->id())
            ->orderBy("support_tickets.id", "desc");

        return Datatables::eloquent($supporttickets)
            ->addColumn('status', function ($supportticket) {
                return ticket_status($supportticket->status);
            })
            ->addColumn('priority', function ($supportticket) {
                $priorityList = [_lang('Low'), _lang('Medium'), _lang('High'), _lang('Critical')];
                return $priorityList[$supportticket->priority] ?? _lang('N/A');
            })
            ->addColumn('is_resolved', function ($supportticket) {
                return $supportticket->is_resolved == 1 ? show_status(_lang('Yes'), 'success') : show_status(_lang('No'), 'danger');
            })
            ->addColumn('action', function ($supportticket) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('client.support_tickets.show', $supportticket['uuid']) . '"><i class="fas fa-eye"></i> ' . _lang('Ticket Details') . '</a>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($supportticket) {
                return "row_" . $supportticket->id;
            })
            ->rawColumns(['status', 'is_resolved', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-8 offset-lg-2';
        return view('backend.client.support_ticket.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'subject'     => 'required',
            'description' => 'required',
            'attachment'  => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('client.support_tickets.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = null;
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        DB::beginTransaction();

        $supportticket              = new SupportTicket();
        $supportticket->uuid        = Str::uuid();
        $supportticket->customer_id = auth('client')->id();
        $supportticket->subject     = $request->input('subject');
        $supportticket->priority    = $request->input('priority');
        $supportticket->business_id = auth('client')->user()->business_id;

        $supportticket->save();

        $message              = new SupportTicketMessage();
        $message->message     = $request->description;
        $message->sender_id   = $supportticket->customer_id;
        $message->sender_type = get_class(auth('client')->user());
        $message->attachment  = $attachment;

        $supportticket->messages()->save($message);

        DB::commit();

        return redirect()->route('client.support_tickets.show', $supportticket->uuid)->with('success', _lang('New Ticket Created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid) {
        $alert_col     = 'col-lg-10 offset-lg-1';
        $supportticket = SupportTicket::where('uuid', $uuid)
            ->where('customer_id', auth('client')->id())
            ->first();
        $priorityList = [_lang('Low'), _lang('Medium'), _lang('High'), _lang('Critical')];

        $unreadMessages = $supportticket->messages()
            ->where('sender_type', 'App\Models\User')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('backend.client.support_ticket.view', compact('supportticket', 'alert_col', 'priorityList'));
    }

    public function reply(Request $request, $uuid) {
        $validator = Validator::make($request->all(), [
            'message'    => 'required',
            'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supportticket = SupportTicket::where('uuid', $uuid)
            ->where('status', 1)
            ->where('customer_id', auth('client')->id())
            ->first();

        if ($supportticket->status == 0) {
            $supportticket->status = 1;
            $supportticket->save();
        }

        $attachment = null;
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . md5(uniqid()) . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $message              = new SupportTicketMessage();
        $message->message     = $request->message;
        $message->sender_id   = auth('client')->id();
        $message->sender_type = get_class(auth('client')->user());
        $message->attachment  = $attachment;

        $supportticket->messages()->save($message);

        //Send Notification
        try {
            $supportticket->operator->notify(new NewSupportMessage($supportticket, $message));
        } catch (\Exception $e) {}

        return back()->with('success', _lang('Ticket Replied'));
    }

    public function mark_as_closed(Request $request, $uuid) {
        $supportticket = SupportTicket::where('uuid', $uuid)
            ->where('status', 1)
            ->where('customer_id', auth('client')->id())
            ->first();
        $supportticket->status           = 2;
        $supportticket->closed_user_id   = auth()->id();
        $supportticket->closed_user_type = get_class(auth()->user());
        $supportticket->save();

        return back()->with('success', _lang('Ticket Closed'));
    }

    public function mark_as_resolved(Request $request, $uuid) {
        $supportticket = SupportTicket::where('uuid', $uuid)
            ->where('status', 2)
            ->where('customer_id', auth('client')->id())
            ->first();
        $supportticket->is_resolved = 1;
        $supportticket->save();

        return back()->with('success', _lang('Ticket marked as resolved'));
    }

}
