<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('designations.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Department') }}</label>						
				<select class="form-control select2 auto-select" data-selected="{{ old('department_id') }}" name="department_id"  required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(App\Models\Department::all() as $department)
					<option value="{{ $department->id }}">{{ $department->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Designation Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Descriptions') }}</label>						
				<textarea class="form-control" name="descriptions">{{ old('descriptions') }}</textarea>
			</div>
		</div>

		<div class="col-md-12 mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
		    </div>
		</div>
	</div>
</form>
