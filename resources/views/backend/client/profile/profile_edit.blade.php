@extends('layouts.client')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Profile Settings') }}</span>
			</div>
			<div class="card-body">
				<form action="{{ route('client.profile.update') }}" autocomplete="off" class="form-horizontal form-group rows-bordered validate" enctype="multipart/form-data" method="post">
					@csrf
					<div class="row">
						<div class="col-lg-10">
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Name') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="name" value="{{ $profile->name }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Company Name') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="company_name" value="{{ $profile->company_name }}">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
								<div class="col-xl-9">
									<input type="email" class="form-control" name="email" value="{{ $profile->email }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Mobile') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="mobile" value="{{ $profile->mobile }}">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('City') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="city" value="{{ $profile->city }}">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('State') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="state" value="{{ $profile->state }}">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Post Code') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="zip" value="{{ $profile->zip }}">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Country') }}</label>						
								<div class="col-xl-9"> 
									<select class="form-control auto-select select2" name="country" data-selected="{{ $profile->country }}">
										<option value="">{{ _lang('Select One') }}</option>
										{{ get_country_list() }}
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Address') }}</label>
								<div class="col-xl-9">
									<textarea class="form-control" name="address">{{ $profile->address }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Image') }} (300 X 300)</label>
								<div class="col-xl-9">
									<input type="file" class="form-control dropify" data-default-file="{{ $profile->profile_picture != "" ? asset('public/uploads/profile/'.$profile->profile_picture) : '' }}" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
								</div>
							</div>

							<div class="form-group row mt-2">
								<div class="col-xl-9 offset-lg-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update Profile') }}</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection