<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller {

    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    public function index() {
        $alert_col = 'col-lg-8 offset-lg-2';
        $profile   = Auth::User('client');
        return view('backend.client.profile.profile_view', compact('profile', 'alert_col'));
    }

    public function show_notification($id) {
        $notification = auth('client')->user()->notifications()->find($id);
        if ($notification && request()->ajax()) {
            $notification->markAsRead();

            $response = '<div class="p-3 border rounded">' . $notification->data['message'];
            $response .= isset($notification->data['url']) ?
            '<p><a href="'. url($notification->data['url']) .'" class="btn btn-primary btn-xs mt-2">' . $notification->data['button_text'] . '</a></p>'
            : '';
            $response .= '</div>';

            return new Response($response);
        }
        return back();
    }

    public function notification_mark_as_read($id) {
        $notification = auth('client')->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function edit() {
        $alert_col = 'col-lg-8 offset-lg-2';
        $profile   = Auth::User('client');
        return view('backend.client.profile.profile_edit', compact('profile', 'alert_col'));
    }

    public function update(Request $request) {
        $this->validate($request, [
            'name'            => 'required',
            'email'           => [
                'required',
                Rule::unique('customers')->ignore(Auth::id()),
            ],
            'profile_picture' => 'nullable|image|max:5120',
        ]);

        DB::beginTransaction();

        $profile               = Auth::user('client');
        $profile->name         = $request->name;
        $profile->company_name = $request->company_name;
        $profile->email        = $request->email;

        if ($request->hasFile('profile_picture')) {
            $image     = $request->file('profile_picture');
            $file_name = "profile_" . time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->crop(300, 300)->save(base_path('public/uploads/profile/') . $file_name);
            $profile->profile_picture = $file_name;
        }

        $profile->mobile  = $request->input('mobile');
        $profile->city    = $request->input('city');
        $profile->state   = $request->input('state');
        $profile->zip     = $request->input('zip');
        $profile->country = $request->input('country');
        $profile->address = $request->input('address');

        $profile->save();

        DB::commit();

        return redirect()->route('client.profile.index')->with('success', _lang('Updated successfully'));
    }

    /**
     * Show the form for change_password the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change_password() {
        $alert_col = 'col-lg-6 offset-lg-3';
        $user_type = auth()->user()->user_type;
        return view('backend.client.profile.change_password', compact('alert_col'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request) {
        $user = auth()->user();

        $this->validate($request, [
            'oldpassword' => $user->password != null ? 'required' : 'nullable',
            'password'    => 'required|string|min:6|confirmed',
        ]);

        if ($user->password == null) {
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('client.dashboard.index')->with('success', _lang('Password has been changed'));
        }

        if (Hash::check($request->oldpassword, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        } else {
            return back()->with('error', _lang('Old Password did not match !'));
        }

        return back()->with('success', _lang('Password has been changed'));
    }

}
