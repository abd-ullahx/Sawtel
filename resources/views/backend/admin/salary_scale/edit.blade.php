@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('salary_scales.update', $id) }}" enctype="multipart/form-data">
	@csrf
	<input name="_method" type="hidden" value="PATCH">
	<div class="row">
		<div class="col-lg-10 offset-lg-1">
			<div class="card">
				<div class="card-header">
					<span class="panel-title">{{ _lang('Update Salary Scale') }}</span>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Department') }}</label>
								<select class="form-control select2 auto-select" data-selected="{{ $salaryscale->department_id }}" name="department_id" id="department_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(App\Models\Department::all() as $department)
									<option value="{{ $department->id }}">{{ $department->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Designation') }}</label>
								<select class="form-control auto-select" data-selected="{{ $salaryscale->designation_id }}" name="designation_id" id="designation_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\Designation::where('department_id', $salaryscale->department_id)->get() as $designation)
									<option value="{{ $designation->id }}">{{ $designation->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Grade Number') }}</label>
								<input type="number" class="form-control" name="grade_number" value="{{ $salaryscale->grade_number }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Basic Salary') }}</label>
								<input type="text" class="form-control float-field" name="basic_salary" value="{{ $salaryscale->basic_salary }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Full Day Absence Fine') }}</label>
								<input type="text" class="form-control float-field" name="full_day_absence_fine" value="{{ $salaryscale->full_day_absence_fine }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Half Day Absence Fine') }}</label>
								<input type="text" class="form-control float-field" name="half_day_absence_fine" value="{{ $salaryscale->half_day_absence_fine }}" required>
							</div>
						</div>


						<div class="col-lg-6 mt-4">
							<div class="card">
								<div class="card-header d-flex justify-content-between">
									<span class="panel-title text-success font-weight-bold">{{ _lang('Allowances') }}</span>
									<button type="button" class="btn btn-outline-success btn-xs" id="add-allowances"><i class="fas fa-plus"></i></button>
								</div>
								<div class="card-body">
									<table class="table table-bordered" id="allowances">
										<thead class="bg-white">
											<th class="text-dark">{{ _lang('Name') }}</th>
											<th class="text-dark">{{ _lang('Amount') }}</th>
											<th class="text-dark text-center">{{ _lang('Action') }}</th>
										</thead>
										<tbody>
											@foreach($salaryscale->salary_benefits()->where('type','add')->get() as $allowances)
											<tr>
												<td>
													<input type="hidden" name="allowances[salary_benefit_id][]" value="{{ $allowances->id }}">
													<input type="text" class="form-control" name="allowances[name][]" placeholder="{{ _lang('Name') }}" value="{{ $allowances->name }}" required>
												</td>
												<td><input type="text" class="form-control float-amount" name="allowances[amount][]" placeholder="{{ _lang('Amount') }}" value="{{ $allowances->amount }}" required></td>
												<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="col-lg-6 mt-4">
							<div class="card">
								<div class="card-header d-flex justify-content-between">
									<span class="panel-title text-danger font-weight-bold">{{ _lang('Deductions') }}</span>
									<button type="button" class="btn btn-outline-danger btn-xs" id="add-deductions"><i class="fas fa-plus"></i></button>
								</div>
								<div class="card-body">
									<table class="table table-bordered" id="deductions">
										<thead class="bg-white">
											<th class="text-dark">{{ _lang('Name') }}</th>
											<th class="text-dark">{{ _lang('Amount') }}</th>
											<th class="text-dark text-center">{{ _lang('Action') }}</th>
										</thead>
										<tbody>
											@foreach($salaryscale->salary_benefits()->where('type','deduct')->get() as $deductions)
											<tr>
												<td>
													<input type="hidden" name="deductions[salary_benefit_id][]" value="{{ $deductions->id }}">
													<input type="text" class="form-control" name="deductions[name][]" placeholder="{{ _lang('Name') }}" value="{{ $deductions->name }}" required>
												</td>
												<td><input type="text" class="form-control float-amount" name="deductions[amount][]" placeholder="{{ _lang('Amount') }}" value="{{ $deductions->amount }}" required></td>
												<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>


						<div class="col-lg-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
							</div>
						</div>
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
	$(document).on('click', '#add-allowances', function(){
		$("#allowances tbody").append(`<tr>
										<td>
											<input type="text" class="form-control" name="allowances[name][]" placeholder="{{ _lang('Name') }}" required>
										</td>
										<td><input type="text" class="form-control float-amount" name="allowances[amount][]" placeholder="{{ _lang('Amount') }}" required></td>
										<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
									</tr>`);
	});

	$(document).on('click', '#add-deductions', function(){
		$("#deductions tbody").append(`<tr>
										<td>
											<input type="text" class="form-control" name="deductions[name][]" placeholder="{{ _lang('Name') }}" required>
										</td>
										<td><input type="text" class="form-control float-amount" name="deductions[amount][]" placeholder="{{ _lang('Amount') }}" required></td>
										<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
									</tr>`);
	});

	$(document).on('click', '.remove-item', function(){
		$(this).parent().parent().remove();
	});

	$(document).on('change','#department_id', function(){
		var department_id = $(this).val();
		$.ajax({
			url: _url + "/admin/designations/get_designations/" + department_id,
			beforeSend: function(){
				$("#preloader").fadeIn();
			},success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				$('#designation_id option:not(:first)').remove();
				$(json).each(function( index, element ) {
					$('#designation_id').append(new Option(element['name'], element['id']));
				});
				$("#preloader").fadeOut();
			}
		});
	});

})(jQuery);
</script>
@endsection


