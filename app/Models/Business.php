<?php

namespace App\Models;

use App\Models\BusinessSetting;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Business extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    protected static function booted(): void {
        if (auth()->check()) {
            static::addGlobalScope('business_id', function (Builder $builder) {
                if (request()->has('activeBusiness')) {
                    $builder->whereIn('business.id', request()->businessList->pluck('id'));
                }
            });
        }
    }

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function systemSettings() {
        return $this->hasMany(BusinessSetting::class, 'business_id');
    }

    public function invoices() {
        return $this->hasMany(Invoice::class, 'business_id')->withoutGlobalScopes();
    }

    public function quotations() {
        return $this->hasMany(Quotation::class, 'business_id')->withoutGlobalScopes();
    }

    public function users() {
        return $this->belongsToMany(User::class, 'business_users')->withPivot('role_id')->withTimestamps();
    }

    public function role() {
        return $this->belongsToMany(Role::class, 'business_users')->withTimestamps();
    }

    public static function createDefaultBusiness() {
        DB::beginTransaction();

        $business                   = new Business();
        $business->name             = 'Default Business';
        $business->logo             = 'default/default-company-logo.png';
        $business->status           = 1;
        $business->default          = 1;
        $business->country          = 'United States of America';
        $business->currency         = 'USD';
        $business->save();

        $business->users()->attach(auth()->id(), ['is_active' => 1]);

        DB::commit();

        return $business;

    }
}