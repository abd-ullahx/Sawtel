@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<form method="post" class="validate" autocomplete="off" action="{{ route('clients.update', $id) }}" enctype="multipart/form-data">
			@csrf
			<div class="card">
				<div class="card-header d-flex align-items-center justify-content-between">
					<span class="panel-title">{{ _lang('Update Client') }}</span>
				</div>
				<div class="card-body">
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $customer->name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Company Name') }}</label>						
								<input type="text" class="form-control" name="company_name" value="{{ $customer->company_name }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>						
								<input type="text" class="form-control" name="email" value="{{ $customer->email }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Country') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $customer->country }}" name="country">
									<option value="">{{ _lang('Select One') }}</option>
									{{ get_country_list() }}
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Currency') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $customer->currency }}" name="currency" disabled>
									<option value="">{{ _lang('Select One') }}</option>
									{!! xss_clean(get_currency_list()) !!}
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Mobile') }}</label>						
								<input type="text" class="form-control" name="mobile" value="{{ $customer->mobile }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Vat ID') }}</label>						
								<input type="text" class="form-control" name="vat_id" value="{{ $customer->vat_id }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Reg No') }}</label>						
								<input type="text" class="form-control" name="reg_no" value="{{ $customer->reg_no }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('City') }}</label>						
								<input type="text" class="form-control" name="city" value="{{ $customer->city }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('State') }}</label>						
								<input type="text" class="form-control" name="state" value="{{ $customer->state }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('ZIP') }}</label>						
								<input type="text" class="form-control" name="zip" value="{{ $customer->zip }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>						
								<textarea class="form-control" name="address">{{ $customer->address }}</textarea>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Remarks') }}</label>						
								<textarea class="form-control" name="remarks">{{ $customer->remarks }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Profile Picture') }}</label>						
								<input type="file" class="form-control dropify" name="profile_picture" data-default-file="{{ asset('public/uploads/profile/'.$customer->profile_picture) }}" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
							</div>
						</div>
							
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>	
	</div>
</div>
@endsection


