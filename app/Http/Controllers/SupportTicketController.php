<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Notifications\NewSupportMessage;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $assets = ['datatable'];
        if ($request->status == 'active') {
            $status = 1;
            $title  = _lang('Active Tickets');
        } else if ($request->status == 'pending') {
            $status = 0;
            $title  = _lang('Pending Tickets');
        } else {
            $status = 2;
            $title  = _lang('Closed Tickets');
        }
        return view('backend.admin.support_ticket.list', compact('assets', 'status', 'title'));
    }

    public function get_table_data($status = 1) {
        $supporttickets = SupportTicket::select('support_tickets.*')
            ->with('customer')
            ->where('business_id', request()->activeBusiness->id)
            ->where('status', $status)
            ->orderBy("support_tickets.id", "desc");

        return Datatables::eloquent($supporttickets)
            ->editColumn('customer.name', function ($supportticket) {
                return '<div class="d-flex align-items-center">'
                . '<img src="' . profile_picture($supportticket->customer->profile_picture) . '" class="thumb-sm img-thumbnail rounded-circle mr-3">'
                . '<div><span class="d-block text-height-0"><b>' . $supportticket->customer->name . '</b></span><span class="d-block">' . $supportticket->customer->email . '</span></div>'
                    . '</div>';
            })
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
                . '<a class="dropdown-item" href="' . route('support_tickets.show', $supportticket['uuid']) . '"><i class="fas fa-eye"></i> ' . _lang('Ticket Details') . '</a>'
                    . '</div>'
                    . '</div>';
            })
            ->filterColumn('customer.name', function ($query, $keyword) {
                $query->whereHas('customer', function ($query) use ($keyword) {
                    return $query->whereRaw("name like ? OR email like ?", ["%{$keyword}%", "%{$keyword}%"]);
                });
            })
            ->setRowId(function ($supportticket) {
                return "row_" . $supportticket->id;
            })
            ->rawColumns(['customer.name', 'status', 'is_resolved', 'action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid) {
        $alert_col     = 'col-lg-10 offset-lg-1';
        $priorityList  = [_lang('Low'), _lang('Medium'), _lang('High'), _lang('Critical')];
        $supportticket = SupportTicket::where('uuid', $uuid)->first();

        $unreadMessages = $supportticket->messages()
            ->where('sender_type', 'App\Models\Customer')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('backend.admin.support_ticket.view', compact('supportticket', 'priorityList', 'alert_col'));
    }

    public function accept(Request $request, $uuid) {
        $supportticket              = SupportTicket::where('uuid', $uuid)->where('status', 0)->first();
        $supportticket->status      = 1;
        $supportticket->operator_id = auth()->id();
        $supportticket->save();

        return back()->with('success', _lang('Ticket Accepted'));
    }

    public function reply(Request $request, $uuid) {
        $validator = Validator::make($request->all(), [
            'message'    => 'required',
            'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supportticket = SupportTicket::where('uuid', $uuid)->where('status', 1)->first();

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
        $message->sender_id   = auth()->id();
        $message->sender_type = get_class(auth()->user());
        $message->attachment  = $attachment;

        $supportticket->messages()->save($message);

        //Send Notification
        try {
            $supportticket->customer->notify(new NewSupportMessage($supportticket, $message));
        } catch (\Exception $e) {}

        return back()->with('success', _lang('Ticket Replied'));
    }

    public function mark_as_closed(Request $request, $uuid) {
        $supportticket                   = SupportTicket::where('uuid', $uuid)->where('status', 1)->first();
        $supportticket->status           = 2;
        $supportticket->closed_user_id   = auth()->id();
        $supportticket->closed_user_type = get_class(auth()->user());
        $supportticket->save();

        return redirect()->route('support_tickets.index', ['status' => 'closed'])->with('success', _lang('Ticket Closed'));
    }

    public function mark_as_resolved(Request $request, $uuid) {
        $supportticket              = SupportTicket::where('uuid', $uuid)->where('status', 2)->first();
        $supportticket->is_resolved = 1;
        $supportticket->save();

        return back()->with('success', _lang('Ticket marked as resolved'));
    }

}
