<?php

namespace App\Models;

use App\Notifications\ClientResetPasswordNotification;
use App\Notifications\ClientVerificationEmail;
use App\Traits\MultiTenant;
use App\Utilities\Overrider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable implements MustVerifyEmail {
	use Notifiable, MultiTenant;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'customers';

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function scopeActive($query) {
		return $query->where('status', 1);
	}

	public function invoices() {
		return $this->hasMany(Invoice::class, 'customer_id')->withoutGlobalScopes();
	}

	public function quotations() {
		return $this->hasMany(Quotation::class, 'customer_id')->withoutGlobalScopes();
	}

	public function business() {
		return $this->belongsTo(Business::class, 'business_id')->withDefault();
	}

	public function sendEmailVerificationNotification() {
		if (get_option('email_verification') == 0) {
			return;
		}
		Overrider::load("Settings");
		$this->notify(new ClientVerificationEmail);
	}

	public function sendPasswordResetNotification($token) {
		$this->notify(new ClientResetPasswordNotification($token));
	}

    public function customerProductPrices(): HasMany
    {
        return $this->hasMany(CustomerProductPrice::class, 'customer_id');
    }
}