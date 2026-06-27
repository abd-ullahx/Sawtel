<?php

namespace App\Http\Controllers;

use App\Mail\GeneralMail;
use App\Models\Business;
use App\Utilities\Overrider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BusinessSettingsController extends Controller {

    private $ignoreRequests = ['_token', 'businessList', 'activeBusiness', 'isOwner', 'permissionList'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request, $id) {
        $business = Business::with('systemSettings')->find($id);
        return view('backend.admin.business.settings', compact('business', 'id'));
    }

    public function store_general_settings(Request $request, $businessId) {
        $settingsData = $request->except($this->ignoreRequests);

        foreach ($settingsData as $key => $value) {
            $value = is_array($value) ? json_encode($value) : $value;
            update_business_option($key, $value, $businessId);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Successfully')]);
        }
    }

    public function store_currency_settings(Request $request, $businessId) {
        $validator = Validator::make($request->all(), [
            'currency_position' => 'required',
            'decimal_places'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $settingsData = $request->except($this->ignoreRequests);

        foreach ($settingsData as $key => $value) {
            update_business_option($key, $value, $businessId);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Successfully')]);
        }
    }

    public function store_invoice_settings(Request $request, $businessId) {
        $validator = Validator::make($request->all(), [
            'invoice_title'    => 'required',
            'invoice_number'   => 'required|integer',
            'quotation_title'  => 'required',
            'quotation_number' => 'required|integer',
            'purchase_title'   => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $settingsData = $request->except($this->ignoreRequests);

        foreach ($settingsData as $key => $value) {
            $value = is_array($value) ? json_encode($value) : $value;
            update_business_option($key, $value, $businessId);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Successfully')]);
        }
    }

    public function store_payment_gateway_settings(Request $request, $businessId) {
        $slug     = $request->slug;
        $rules    = [$slug . '.status' => 'required'];
        $messages = [];

        foreach ($request->$slug as $key => $val) {
            if ($key == 'status') {continue;}
            $rules[$slug . '.' . $key]                     = "required_if:$slug.status,1";
            $messages[$slug . '.' . $key . '.required_if'] = ucwords(str_replace("_", " ", $key)) . ' ' . _lang("is required");
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $settingsData = $request->except($this->ignoreRequests);

        foreach ($settingsData as $key => $value) {
            $value = is_array($value) ? json_encode($value) : $value;
            update_business_option($key, $value, $businessId);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Successfully')]);
        }
    }

    public function store_email_settings(Request $request, $businessId) {
        $validator = Validator::make($request->all(), [
            'from_email'      => 'required_if:mail_type,smtp,sendmail',
            'from_name'       => 'required_if:mail_type,smtp,sendmail',
            'smtp_host'       => 'required_if:mail_type,smtp,sendmail',
            'smtp_port'       => 'required_if:mail_type,smtp,sendmail',
            'smtp_username'   => 'required_if:mail_type,smtp,sendmail',
            'smtp_password'   => 'required_if:mail_type,smtp,sendmail',
            'smtp_encryption' => 'required_if:mail_type,smtp,sendmail',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $settingsData = $request->except($this->ignoreRequests);

        foreach ($settingsData as $key => $value) {
            update_business_option($key, $value, $businessId);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Successfully')]);
        }
    }

    public function send_test_email(Request $request, $businessId) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        Overrider::load("BusinessSettings");

        $this->validate($request, [
            'recipient_email' => 'required|email',
            'message'         => 'required',
        ]);

        //Send Email
        $email   = $request->input("recipient_email");
        $message = $request->input("message");

        $mail          = new \stdClass();
        $mail->subject = "Email Configuration Testing";
        $mail->body    = $message;

        try {
            Mail::to($email)->send(new GeneralMail($mail));
            if (!$request->ajax()) {
                return back()->with('success', _lang('Test Message send sucessfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Test Message send sucessfully')]);
            }
        } catch (\Exception $e) {
            if (!$request->ajax()) {
                return back()->with('error', $e->getMessage());
            } else {
                return response()->json(['result' => 'error', 'action' => 'update', 'message' => $e->getMessage()]);
            }
        }
    }

}