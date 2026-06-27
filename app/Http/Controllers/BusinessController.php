<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Validator;
use App\Models\Role;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{

    public function index()
    {
        $assets = ['datatable'];
        $businesss = Business::with('role')->get()->sortByDesc("id");
        return view('backend.admin.business.list', compact('businesss', 'assets'));
    }

    public function create(Request $request)
    {
        return to_route('business.index');
//        $alert_col = 'col-lg-8 offset-lg-2';
//        return view('backend.admin.business.create', compact('alert_col'));
    }

    public function store(Request $request)
    {
        return to_route('business.index');
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'country' => 'required',
//            'currency' => 'required',
//            'logo' => 'nullable|image|max:2048',
//            'status' => 'required',
//            'default' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            if ($request->ajax()) {
//                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
//            } else {
//                return redirect()->route('business.create')
//                    ->withErrors($validator)
//                    ->withInput();
//            }
//        }
//
//        $logo = 'default/default-company-logo.png';
//        if ($request->hasfile('logo')) {
//            $file = $request->file('logo');
//            $logo = time() . $file->getClientOriginalName();
//            $file->move(public_path() . "/uploads/media/", $logo);
//        }
//
//        DB::beginTransaction();
//
//        $business = new Business();
//        $business->name = $request->input('name');
//        $business->reg_no = $request->input('reg_no');
//        $business->vat_id = $request->input('vat_id');
//        $business->email = $request->input('email');
//        $business->phone = $request->input('phone');
//        $business->country = $request->input('country');
//        $business->currency = $request->input('currency');
//        $business->address = $request->input('address');
//        $business->logo = $logo;
//        $business->status = $request->input('status');
//        if ($request->default == 1) {
//            Business::where('default', 1)->update(['default' => 0]);
//            $business->default = $request->default;
//        }
//
//        $business->save();
//
//        $business->users()->attach(auth()->id(), ['is_active' => count($request->businessList) == 0 ? 1 : 0]);
//
//        //Import Invoice Templates
//        $sql = file_get_contents('public/uploads/invoice_templates.sql');
//        $sql = str_replace("%businessID%", $business->id, $sql);
//        DB::unprepared($sql);
//
//        DB::commit();
//
//        if ($business->id > 0) {
//            return redirect()->route('business.index')->with('success', _lang('Saved Successfully'));
//        }
    }

    public function users(Request $request, $id)
    {
        $assets = ['datatable'];
        $business = Business::with('users')->find($id);
        return view('backend.admin.business.system_users', compact('business', 'id', 'assets'));
    }

    public function edit(Request $request, $id)
    {
        $alert_col = 'col-lg-8 offset-lg-2';
        $business = Business::find($id);
        return view('backend.admin.business.edit', compact('business', 'id', 'alert_col'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required',
            'default' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('business.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('logo')) {
            $file = $request->file('logo');
            $logo = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $logo);
        }

        DB::beginTransaction();

        $business = Business::find($id);

        $business->name = $request->input('name');
        $business->reg_no = $request->input('reg_no');
        $business->vat_id = $request->input('vat_id');
        $business->email = $request->input('email');
        $business->phone = $request->input('phone');
        $business->country = $request->input('country');

        if ($business->invoices->count() == 0 || $business->quotations->count() == 0) {
            $business->currency = $request->input('currency');
        }

        $business->address = $request->input('address');

        if ($request->hasfile('logo')) {
            $business->logo = $logo;
        }

        $business->status = $request->input('status');

        if ($request->default == true) {
            Business::where('default', true)->update(['default' => 0]);
            $business->default = true;
        }

        $business->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('business.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $business, 'table' => '#business_table']);
        }

    }

    public function destroy($id)
    {
        $business = Business::find($id);
        if ($business->default == 1) {
            return redirect()->route('business.index')->with('error', _lang('Sorry, You will not be able to delete default business!'));
        }
        $business->delete();
        return redirect()->route('business.index')->with('success', _lang('Deleted Successfully'));
    }

    public function invite(Request $request, $businessId)
    {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.business.modal.create', compact('businessId'));
        }
    }

    public function send_invitation(Request $request)
    {
        if (!$request->ajax()) {
            return back();
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'business_id' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $business = Business::find($request->business_id);
        if ($business->users->where('id', $request->user_id)->first()) {
            return response()->json(['result' => 'error', 'message' => _lang('User is already assigned this business')]);
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            $business->users()->detach($request->user_id);
            $business->users()->attach($request->user_id, [
                'role_id' => $request->role_id,
                'is_active' => 1,
            ]);
        } else {
            return response()->json(['result' => 'error', 'message' => _lang('No user found')]);
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('New user assigned successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('New user assigned successfully'), 'data' => $user, 'table' => '#users_table']);
        }

    }

    public function change_role(Request $request, $userId, $businessId)
    {
        if (!$request->ajax()) {
            return back();
        }

        if ($request->isMethod('get')) {
            $business = Business::find($businessId);
            $user = $business->users->find($userId);
            return view('backend.admin.business.modal.edit', compact('user', 'business'));
        } else {
            $validator = Validator::make($request->all(), [
                'business_id' => 'required',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            }

            $business = Business::find($request->business_id);
            $role = Role::find($request->role_id);

            if (!$business) {
                return response()->json(['result' => 'error', 'message' => _lang('No business found')]);
            }

            $business->users()->detach($userId);
            $business->users()->attach($userId, [
                'role_id' => $role->id,
                'is_active' => 1,
            ]);

            return response()->json(['result' => 'success', 'message' => _lang('Role Updated Successfully')]);
        }

    }

    public function destroy_user($id)
    {
        $user = User::find($id);
        $user->business()->detach();
        return back()->with('success', _lang('Deleted Successfully'));
    }

    public function switch_business(Request $request, $id)
    {
        $user = auth()->user();

        $business = $user->business()->where('business.id', $id)->first();

        if (!$business) {
            return back()->with('error', _lang('Permission denied !'));
        }

        $user->business()->updateExistingPivot($request->activeBusiness->id, ['is_active' => 0]);

        $user->business()->updateExistingPivot($id, ['is_active' => 1]);

        return redirect()->route('dashboard.index')->with('success', _lang('Business switched to') . ' ' . $business->name);
    }

}