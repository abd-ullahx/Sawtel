@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Update Business') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ route('business.update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $business->name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Country') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $business->country }}" name="country" required>
									<option value="">{{ _lang('Select One') }}</option>
									{{ get_country_list() }}
								</select>
							</div>
						</div>

						@if ($business->invoices->count() == 0 || $business->quotations->count() == 0)
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Currency') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $business->currency }}" name="currency" required>
									<option value="">{{ _lang('Select One') }}</option>
									{!! xss_clean(get_currency_list()) !!}
								</select>
								<small class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ _lang('You can change this currency until you create any invoice or quotations') }}</small>
							</div>							
						</div>
						@else
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Currency') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $business->currency }}" name="currency" disabled>
									<option value="">{{ _lang('Select One') }}</option>
									{!! xss_clean(get_currency_list()) !!}
								</select>
							</div>							
						</div>
						@endif

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Reg No') }}</label>						
								<input type="text" class="form-control" name="reg_no" value="{{ $business->reg_no }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Vat ID') }}</label>						
								<input type="text" class="form-control" name="vat_id" value="{{ $business->vat_id }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>						
								<input type="text" class="form-control" name="email" value="{{ $business->email }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Phone') }}</label>						
								<input type="text" class="form-control" name="phone" value="{{ $business->phone }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>						
								<textarea class="form-control" name="address">{{ $business->address }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Logo') }}</label>						
								<input type="file" class="form-control dropify" name="logo">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $business->status }}" name="status">
									<option value="">{{ _lang('Select One') }}</option>
									<option value="1">{{ _lang('Active') }}</option>
									<option value="1">{{ _lang('Disabled') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Is Default') }} ?</label>						
								<select class="form-control auto-select" data-selected="{{ $business->default }}" name="default">
									<option value="">{{ _lang('Select One') }}</option>
									<option value="1">{{ _lang('Yes') }}</option>
									<option value="0">{{ _lang('No') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection


