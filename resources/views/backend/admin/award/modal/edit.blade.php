<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('awards.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Employee ID') }}</label>						
				<select class="form-control auto-select select2" data-selected="{{ $award->employee_id }}" name="employee_id"  required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\Employee::active()->get() as $employee)
					<option value="{{ $employee->id }}">{{ $employee->employee_id }} ({{ $employee->name }})</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Award Date') }}</label>						
				<input type="text" class="form-control datepicker" name="award_date" value="{{ $award->getRawOriginal('award_date') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Award Name') }}</label>						
				<input type="text" class="form-control" name="award_name" value="{{ $award->award_name }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Award Gift / Cash / Others') }}</label>						
				<input type="text" class="form-control" name="award" value="{{ $award->award }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Details') }}</label>						
				<textarea class="form-control" name="details">{{ $award->details }}</textarea>
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

