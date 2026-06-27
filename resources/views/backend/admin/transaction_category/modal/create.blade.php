<link href="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('transaction_categories.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
	    <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			</div>
		</div>

		@if(isset($_GET['type']))
		<input type="hidden" name="type" value="{{ $_GET['type'] }}" required>
		@else
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Type') }}</label>						
				<select class="form-control auto-select" name="type" data-selected="{{ old('name', 'income') }}" required>
					<option value="income">{{ _lang('Income') }}</option>
					<option value="expense">{{ _lang('Expense') }}</option>
				</select>
			</div>
		</div>
		@endif

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>						
				<textarea class="form-control" name="description">{{ old('description') }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save') }}</button>
		    </div>
		</div>
	</div>
</form>

<script src="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.js') }}"></script>
