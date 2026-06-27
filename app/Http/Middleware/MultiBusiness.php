<?php

namespace App\Http\Middleware;

use app\Models\Business;
use Closure;

class MultiBusiness {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (auth()->check()) {
            $user = auth()->user();

            $business     = $user->business();
            $businessList = $business->withoutGlobalScopes()->get();

            if (!$businessList->isEmpty()) {
                $activeBusiness = $business->withoutGlobalScopes()->wherePivot('is_active', 1)->first();

                if ($activeBusiness == null) {
                    $activeBusiness = $user->business()->withoutGlobalScopes()->first();
                    $user->business()->updateExistingPivot($activeBusiness->id, ['is_active' => 1]);
                }

                $permissionList = $user->select('permissions.*')
                    ->join('business_users', 'users.id', 'business_users.user_id')
                    ->join('business', 'business_users.business_id', 'business.id')
                    ->join('permissions', 'business_users.role_id', 'permissions.role_id')
                    ->where('business.id', $activeBusiness->id)
                    ->where('users.id', $user->id)
                    ->get();

                date_default_timezone_set(get_business_option('timezone', 'Asia/Dhaka'));

                $request->merge([
                    'businessList'   => $businessList,
                    'activeBusiness' => $activeBusiness,
                    'permissionList' => $permissionList,
                ]);

            } else {
                if (!$request->has('businessList') && $user->user_type == 'admin') {
                    $business = Business::createDefaultBusiness();
                    return redirect()->route('business.edit', $business->id)->with('error', _lang("Please update your default business account"));
                }else{
                    auth()->logout();
                    return redirect()->route('login')->with('error', _lang("No business associated with your account!"));
                }
            }

        }

        return $next($request);
    }

}
