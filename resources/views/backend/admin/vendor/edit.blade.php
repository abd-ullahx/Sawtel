@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Update Vendor') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ route('vendors.update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $vendor->name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Company Name') }}</label>						
								<input type="text" class="form-control" name="company_name" value="{{ $vendor->company_name }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>						
								<input type="text" class="form-control" name="email" value="{{ $vendor->email }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Registration No') }}</label>						
								<input type="text" class="form-control" name="registration_no" value="{{ $vendor->registration_no }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Vat ID') }}</label>						
								<input type="text" class="form-control" name="vat_id" value="{{ $vendor->vat_id }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Mobile') }}</label>						
								<input type="text" class="form-control" name="mobile" value="{{ $vendor->mobile }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Country') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $vendor->country }}" name="country">
									<option value="">{{ _lang('Select One') }}</option>
									{{ get_country_list() }}
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Currency') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $vendor->currency }}" name="currency" disabled>
									<option value="">{{ _lang('Select One') }}</option>
									{!! xss_clean(get_currency_list()) !!}
								</select>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">{{ _lang('City') }}</label>						
								<input type="text" class="form-control" name="city" value="{{ $vendor->city }}">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">{{ _lang('State') }}</label>						
								<input type="text" class="form-control" name="state" value="{{ $vendor->state }}">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">{{ _lang('ZIP') }}</label>						
								<input type="text" class="form-control" name="zip" value="{{ $vendor->zip }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>						
								<textarea class="form-control" name="address">{{ $vendor->address }}</textarea>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Remarks') }}</label>						
								<textarea class="form-control" name="remarks">{{ $vendor->remarks }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Profile Picture') }}</label>						
								<input type="file" class="form-control dropify" name="profile_picture" data-default-file="{{ asset('public/uploads/profile/'.$vendor->profile_picture) }}" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
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


