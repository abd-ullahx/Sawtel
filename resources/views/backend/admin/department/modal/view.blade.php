<div class="row px-2">
	<div class="col-md-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Name') }}</td><td>{{ $department->name }}</td></tr>
			<tr><td>{{ _lang('Descriptions') }}</td><td>{{ $department->descriptions }}</td></tr>
			<tr>
				<td>{{ _lang('Designations') }}</td>
				<td>
					<ol class="pl-3">
					@foreach($department->designations as $designation)
						<li>{{ $designation->name }}</li>
					@endforeach
					</ol>
				</td>
			</tr>
		</table>
	</div>
</div>

