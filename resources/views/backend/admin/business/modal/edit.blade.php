<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('system_users.change_role', [$user->id, $business->id]) }}" enctype="multipart/form-data">
	{{ csrf_field()}}

	<div class="row p-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>
				<input type="text" class="form-control" name="email" value="{{ $user->name }}" readonly>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Email') }}</label>
				<input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Business') }}</label>
				<input type="text" class="form-control" name="business" value="{{ $business->name }}" readonly>
				<input type="hidden" name="business_id" value="{{ $business->id }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('User Role') }}</label>
				<select class="form-control select2-ajax" data-href="{{ route('roles.create') }}" data-title="{{ _lang('Add New Role') }}" data-value="id" data-display="name" data-table="roles" id="role_id" name="role_id" required>
					@foreach(\App\Models\Role::all() as $role)
					<option value="{{ $role->id }}" {{ $user->pivot->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
					@endforeach
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