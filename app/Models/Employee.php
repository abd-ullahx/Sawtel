<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
	use MultiTenant;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'employees';

	public function scopeActive($query) {
		$date = date('Y-m-d');
		return $query->whereRaw("employees.end_date is NULL OR employees.end_date > $date");
	}

	public function department() {
		return $this->belongsTo(Department::class, 'department_id')->withDefault();
	}

	public function designation() {
		return $this->belongsTo(Designation::class, 'designation_id')->withDefault();
	}

	public function salary_scale() {
		return $this->belongsTo(SalaryScale::class, 'salary_scale_id')->withDefault();
	}

	public function payslips() {
		return $this->hasMany(Payroll::class, 'employee_id');
	}

	public function attendance() {
		return $this->hasMany(Attendance::class, 'employee_id');
	}

	public function documents() {
		return $this->hasMany(EmployeeDocument::class, 'employee_id');
	}

	public function department_history() {
		return $this->hasMany(EmployeeDepartmentHistory::class, 'employee_id');
	}

	public function getNameAttribute() {
		return $this->first_name . ' ' . $this->last_name;
	}

	protected function dateOfBirth(): Attribute {
		$date_format = get_date_format();
		return Attribute::make(
			get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format"),
		);
	}

	protected function joiningDate(): Attribute {
		$date_format = get_date_format();
		return Attribute::make(
			get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format"),
		);
	}

	protected function endDate(): Attribute {
		$date_format = get_date_format();
		return Attribute::make(
			get: fn($value) => $value != null?\Carbon\Carbon::parse($value)->format("$date_format") : '',
		);
	}

}