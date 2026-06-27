@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-4 offset-lg-4">
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Generate Payslip') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('payslips.store') }}" enctype="multipart/form-data">
					@csrf
					<div class="row">

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Month') }}</label>						
								<select type="text" class="form-control auto-select" name="month" data-selected="{{ old('month', date('m')) }}" required>
									@for($m = 1; $m <=12; $m++)
									<option value="{{ date('m', mktime(0, 0, 0, $m, 10)) }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
									@endfor
								</select>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Year') }}</label>						
								<select type="text" class="form-control auto-select" name="year" data-selected="{{ old('year', date('Y')) }}" required>
									@for($y = 2020; $y <=date('Y'); $y++)
									<option value="{{ $y }}">{{ $y }}</option>
									@endfor
								</select>
							</div>
						</div>
			
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box mr-2"></i> {{ _lang('Generate Payslip') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
</div>
@endsection


