<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('holidays.weekends') }}">
	@csrf
	<div class="row px-2">
		<div class="col-md-12">
			<table class="table table-bordered">
				<thead>
					<th>{{ _lang('Day') }}</th>
					<th class="text-center">{{ _lang('Weekends') }}</th>
				</thead>
				<tbody>
					<tr>
						<td>{{ _lang('Sunday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Sunday" {{ in_array('Sunday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Monday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Monday" {{ in_array('Monday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Tuesday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Tuesday" {{ in_array('Tuesday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Wednesday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Wednesday" {{ in_array('Wednesday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Thursday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Thursday" {{ in_array('Thursday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Friday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Friday" {{ in_array('Friday', $weekends) ? 'checked' : '' }}></td>
					</tr>
					<tr>
						<td>{{ _lang('Saturday') }}</td>
						<td class="text-center"><input type="checkbox" name="weekends[]" value="Saturday" {{ in_array('Saturday', $weekends) ? 'checked' : '' }}></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save Changes') }}</button>
		    </div>
		</div>
	</div>
</form>

