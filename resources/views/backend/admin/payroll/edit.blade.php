@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('payslips.update', $id) }}" enctype="multipart/form-data">
	@csrf
	<input name="_method" type="hidden" value="PATCH">
	<div class="row">
		<div class="col-lg-10 offset-lg-1">
			<div class="card">
				<div class="card-header d-flex align-items-center justify-content-between">
					<span class="panel-title">{{ _lang('Update Payslip') }}</span>
					{!! xss_clean(payroll_status($payroll->status)) !!}
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Employee ID') }}</label>						
								<input type="text" class="form-control" name="employee_id" value="{{ $payroll->staff->employee_id }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Employee Name') }}</label>						
								<input type="text" class="form-control" name="employee_id" value="{{ $payroll->staff->name }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Month') }}</label>						
								<input type="text" class="form-control" name="month" value="{{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Year') }}</label>						
								<input type="text" class="form-control" name="year" value="{{ $payroll->year }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Basic Salary') }}</label>						
								<input type="text" class="form-control" name="current_salary" id="basic_salary" value="{{ $payroll->current_salary }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Expense Claim') }}</label>						
								<input type="text" class="form-control" name="expense" id="expense" value="{{ $payroll->expense }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Absence Fine') }}</label>						
								<input type="text" class="form-control" name="absence_fine" id="absence_fine" value="{{ $payroll->absence_fine }}" disabled>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Net Salary') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
								<input type="text" class="form-control" id="net_salary" name="net_salary" value="{{ $payroll->net_salary }}" disabled>
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
											@foreach($payroll->payroll_benefits()->where('type','add')->get() as $allowances)
											<tr>
												<td>
													<input type="hidden" name="allowances[payslip_id][]" value="{{ $allowances->id }}">
													<input type="text" class="form-control" name="allowances[name][]" placeholder="{{ _lang('Name') }}" value="{{ $allowances->name }}" required>
												</td>
												<td><input type="text" class="form-control float-amount add_amount" name="allowances[amount][]" placeholder="{{ _lang('Amount') }}" value="{{ $allowances->amount }}" required></td>
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
											@foreach($payroll->payroll_benefits()->where('type','deduct')->get() as $deductions)
											<tr>
												<td>
													<input type="hidden" name="deductions[payslip_id][]" value="{{ $deductions->id }}">
													<input type="text" class="form-control" name="deductions[name][]" placeholder="{{ _lang('Name') }}" value="{{ $deductions->name }}" required>
												</td>
												<td><input type="text" class="form-control float-amount deduct_amount" name="deductions[amount][]" placeholder="{{ _lang('Amount') }}" value="{{ $deductions->amount }}" required></td>
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
										<td><input type="text" class="form-control float-amount add_amount" name="allowances[amount][]" placeholder="{{ _lang('Amount') }}" required></td>
										<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
									</tr>`);
	});

	$(document).on('click', '#add-deductions', function(){
		$("#deductions tbody").append(`<tr>
										<td>
											<input type="text" class="form-control" name="deductions[name][]" placeholder="{{ _lang('Name') }}" required>
										</td>
										<td><input type="text" class="form-control float-amount deduct_amount" name="deductions[amount][]" placeholder="{{ _lang('Amount') }}" required></td>
										<td class="text-center"><button class="btn btn-danger btn-xs remove-item"><i class="far fa-trash-alt"></i></button></td>
									</tr>`);
	});

	$(document).on('click', '.remove-item', function(){
		$(this).parent().parent().remove();
		calculatingNetTotal();
	});

	$(document).on('change keyup blur','.add_amount,.deduct_amount', function(){
		calculatingNetTotal();
	});

	function calculatingNetTotal(){
		//Calculating Total Allowance
		var total_allowance = parseFloat($('#basic_salary').val()) + parseFloat($('#expense').val());
		$('.add_amount').each(function(){
			if($(this).val() != ''){
				total_allowance += parseFloat($(this).val());
			}
		});
		//$('#total_allowance').val(total_allowance.toFixed(2));

		//Calculating Total Deduction
		var total_deduction = 0;
		if(typeof($('#absence_fine').val()) != 'undefined'){
			total_deduction += parseFloat($('#absence_fine').val());
		}
		$('.deduct_amount').each(function(){
			if($(this).val() != ''){
				total_deduction += parseFloat($(this).val());
			}
		});
		//$('#total_deduction').val(total_deduction.toFixed(2));

		//Calculating Net Salary
		var net_salary = total_allowance - total_deduction;
		$('#net_salary').val(net_salary.toFixed(2));
	}

})(jQuery);
</script>
@endsection

