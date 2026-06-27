@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('staffs.update', $id) }}" enctype="multipart/form-data">
	@csrf
	<input name="_method" type="hidden" value="PATCH">
	<div class="row">
		<div class="col-lg-10 offset-lg-1">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<span class="panel-title">{{ _lang('Personal Details') }}</span>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('First Name') }}</label>						
										<input type="text" class="form-control" name="first_name" value="{{ $employee->first_name }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Last Name') }}</label>						
										<input type="text" class="form-control" name="last_name" value="{{ $employee->last_name }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Fathers Name') }}</label>						
										<input type="text" class="form-control" name="fathers_name" value="{{ $employee->fathers_name }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Mothers Name') }}</label>						
										<input type="text" class="form-control" name="mothers_name" value="{{ $employee->mothers_name }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Date Of Birth') }}</label>						
										<input type="text" class="form-control datepicker" name="date_of_birth" value="{{ $employee->getRawOriginal('date_of_birth') }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Email') }}</label>						
										<input type="text" class="form-control" name="email" value="{{ $employee->email }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Phone') }}</label>						
										<input type="text" class="form-control" name="phone" value="{{ $employee->phone }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('City') }}</label>						
										<input type="text" class="form-control" name="city" value="{{ $employee->city }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('State') }}</label>						
										<input type="text" class="form-control" name="state" value="{{ $employee->state }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Zip') }}</label>						
										<input type="text" class="form-control" name="zip" value="{{ $employee->zip }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Country') }}</label>						
										<select class="form-control auto-select select2" data-selected="{{ $employee->country }}" name="country">
											<option value="">{{ _lang('Select One') }}</option>
											{{ get_country_list(old('country')) }}
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Remarks') }}</label>						
										<textarea class="form-control" name="remarks">{{ $employee->remarks }}</textarea>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6" id="company-details">
					<div class="card">
						<div class="card-header">
							<span class="panel-title">{{ _lang('Company Details') }}</span>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label text-danger">{{ _lang('Update Company Details') }}?</label>						
										<select class="form-control" name="update_company_details" id="update_company_details">
											<option value="0">{{ _lang('No') }}</option>
											<option value="1">{{ _lang('Yes') }}</option>
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Employee ID') }}</label>						
										<input type="text" class="form-control" name="employee_id" value="{{ $employee->employee_id }}" disabled required>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Department') }}</label>						
										<select class="form-control auto-select" data-selected="{{ $employee->department_id }}" name="department_id" id="department_id" disabled required>
											<option value="">{{ _lang('Select One') }}</option>
											@foreach(App\Models\Department::all() as $department)
											<option value="{{ $department->id }}">{{ $department->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Designation') }}</label>
										<select class="form-control auto-select" data-selected="{{ $employee->designation_id }}" name="designation_id" id="designation_id" disabled required>
											<option value="">{{ _lang('Select One') }}</option>
											@foreach(\App\Models\Designation::where('department_id', $employee->department_id)->get() as $designation)
											<option value="{{ $designation->id }}">{{ $designation->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Salary Scale') }}</label>						
										<select class="form-control auto-select" data-selected="{{ $employee->salary_scale_id }}" name="salary_scale_id" id="salary_scale_id" disabled required>
											<option value="">{{ _lang('Select One') }}</option>
											@foreach(\App\Models\SalaryScale::where('department_id', $employee->department_id)->where('designation_id', $employee->designation_id)->get() as $salaryScale)
											<option value="{{ $salaryScale->id }}">{{ $salaryScale->salary_grade }} ({{ formatAmount($salaryScale->basic_salary, currency_symbol(request()->activeBusiness->currency)) }})</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Joining Date') }}</label>						
										<input type="text" class="form-control datepicker" name="joining_date" value="{{ $employee->getRawOriginal('joining_date') }}" disabled required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('End Date') }}</label>						
										<input type="date" class="form-control" name="end_date" value="{{ $employee->getRawOriginal('end_date') }}" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="card">
						<div class="card-header">
							<span class="panel-title">{{ _lang('Bank Details') }}</span>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Bank Name') }}</label>						
										<input type="text" class="form-control" name="bank_name" value="{{ $employee->bank_name }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Branch Name') }}</label>						
										<input type="text" class="form-control" name="branch_name" value="{{ $employee->branch_name }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Account Name') }}</label>						
										<input type="text" class="form-control" name="account_name" value="{{ $employee->account_name }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Account Number') }}</label>						
										<input type="text" class="form-control" name="account_number" value="{{ $employee->account_number }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Swift Code') }}</label>						
										<input type="text" class="form-control" name="swift_code" value="{{ $employee->swift_code }}">
									</div>
								</div>					
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-12 mt-2">
					<div class="form-group">
						<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save Changes') }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection


@section('js-script')
<script>
(function($) {
    "use strict";
	
	$(document).on('change','#department_id', function(){
		var department_id = $(this).val();
		$.ajax({
			url: _url + "/admin/designations/get_designations/" + department_id,
			beforeSend: function(){
				$("#preloader").fadeIn();
			},success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				$('#designation_id option:not(:first)').remove();
				$('#salary_scale_id option:not(:first)').remove();
				$(json).each(function( index, element ) {
					$('#designation_id').append(new Option(element['name'], element['id']));
				});
				$("#preloader").fadeOut();
			}
		});
	});

	$(document).on('change','#designation_id', function(){
		var designation_id = $(this).val();
		$.ajax({
			url: _url + "/admin/salary_scales/get_salary_scales/" + designation_id,
			beforeSend: function(){
				$("#preloader").fadeIn();
			},success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				$('#salary_scale_id option:not(:first)').remove();
				$(json).each(function( index, element ) {
					$('#salary_scale_id').append(new Option(element['salary_grade'] + '  (' + _currency_symbol + element['basic_salary'] + ')', element['id']));
				});
				$("#preloader").fadeOut();
			}
		});
	});

	$(document).on('change','#update_company_details', function(){
		if($(this).val() == 1){
			$("#company-details input, #company-details select").prop('disabled', false);
		}else{
			$("#company-details input, #company-details select").prop('disabled', true);
		}
	});
	

})(jQuery);
</script>
@endsection


