@extends('layouts.app')

@section('content')

<form method="post" class="validate" autocomplete="off" action="{{ route('staffs.store') }}" enctype="multipart/form-data">
	@csrf
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
										<input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Last Name') }}</label>						
										<input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Fathers Name') }}</label>						
										<input type="text" class="form-control" name="fathers_name" value="{{ old('fathers_name') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Mothers Name') }}</label>						
										<input type="text" class="form-control" name="mothers_name" value="{{ old('mothers_name') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Date Of Birth') }}</label>						
										<input type="text" class="form-control datepicker" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Email') }}</label>						
										<input type="text" class="form-control" name="email" value="{{ old('email') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Phone') }}</label>						
										<input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('City') }}</label>						
										<input type="text" class="form-control" name="city" value="{{ old('city') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('State') }}</label>						
										<input type="text" class="form-control" name="state" value="{{ old('state') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Zip') }}</label>						
										<input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Country') }}</label>						
										<select class="form-control auto-select select2" data-selected="{{ old('country') }}" name="country">
											<option value="">{{ _lang('Select One') }}</option>
											{{ get_country_list(old('country')) }}
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Remarks') }}</label>						
										<textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="card">
						<div class="card-header">
							<span class="panel-title">{{ _lang('Company Details') }}</span>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Employee ID') }}</label>						
										<input type="text" class="form-control" name="employee_id" value="{{ old('employee_id') }}" required>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Department') }}</label>						
										<select class="form-control auto-select" data-selected="{{ old('department_id') }}" name="department_id" id="department_id" required>
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
										<select class="form-control auto-select" data-selected="{{ old('designation_id') }}" name="designation_id" id="designation_id" required>
											<option value="">{{ _lang('Select One') }}</option>
										</select>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Salary Scale') }}</label>						
										<select class="form-control auto-select" data-selected="{{ old('salary_scale_id') }}" name="salary_scale_id" id="salary_scale_id" required>
											<option value="">{{ _lang('Select One') }}</option>
											
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('Joining Date') }}</label>						
										<input type="text" class="form-control datepicker" name="joining_date" value="{{ old('joining_date') }}" required>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">{{ _lang('End Date') }}</label>						
										<input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
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
										<input type="text" class="form-control" name="bank_name" value="{{ old('bank_name') }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Branch Name') }}</label>						
										<input type="text" class="form-control" name="branch_name" value="{{ old('branch_name') }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Account Name') }}</label>						
										<input type="text" class="form-control" name="account_name" value="{{ old('account_name') }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Account Number') }}</label>						
										<input type="text" class="form-control" name="account_number" value="{{ old('account_number') }}">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">{{ _lang('Swift Code') }}</label>						
										<input type="text" class="form-control" name="swift_code" value="{{ old('swift_code') }}">
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
	

})(jQuery);
</script>
@endsection


