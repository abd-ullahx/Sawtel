<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('holidays.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
		<div class="col-lg-12">
			<table class="table table-bordered" id="holidays-table">
				<thead class="bg-white">
					<th class="text-dark">{{ _lang('Title') }}</th>
					<th class="text-dark">{{ _lang('Date') }}</th>
					<th class="text-dark text-center">{{ _lang('Remove') }}</th>
				</thead>
				<tbody>
					<tr>
						<td>						
							<input type="text" class="form-control" name="title[]" placeholder="{{ _lang('Title') }}" required>
						</td>
						<td>						
							<input type="date" class="form-control" name="date[]" placeholder="{{ _lang('Date') }}" required>
						</td>
						<td class="text-center">						
							<button type="button" class="btn btn-danger btn-xs disabled"><i class="ti-trash"></i></button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	
		<div class="col-12 d-flex justify-content-between mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save') }}</button>
		    </div>

		    <div class="form-group">
			    <button type="button" class="btn btn-outline-danger" id="add-row"><i class="ti-plus mr-2"></i>{{ _lang('Add Row') }}</button>
		    </div>
		</div>
	</div>
</form>

