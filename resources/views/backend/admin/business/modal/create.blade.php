<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('system_users.send_invitation') }}" enctype="multipart/form-data">
	{{ csrf_field() }}

	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('User') }}</label>
				<select class="form-control select2 auto-select" data-selected="{{ old('user_id') }}" name="user_id" required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\User::active()->where('user_type', 'user')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
				</select>
			</div>
		</div>

        <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Business') }}</label>
				<select class="form-control auto-select" data-selected="{{ old('business_id', $businessId) }}" name="business_id" required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\Business::active()->get() as $business)
                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                    @endforeach
				</select>
			</div>
		</div>

        <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('User Role') }}</label>
				<select class="form-control select2-ajax" data-href="{{ route('roles.create') }}" data-title="{{ _lang('Add New Role') }}" data-value="id" data-display="name" data-table="roles" id="role_id" name="role_id" required>
				</select>
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane mr-2"></i>{{ _lang('Assign') }}</button>
			</div>
		</div>
	</div>
</form>