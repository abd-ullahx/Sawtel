@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Holiday List') }}</span>

				<div>
					<a class="btn btn-info btn-xs ajax-modal" data-title="{{ _lang('Manage Weekends') }}" href="{{ route('holidays.weekends') }}"><i class="ti-calendar"></i> {{ _lang('Weekends') }}</a>
					<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Holiday') }}" href="{{ route('holidays.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="holidays_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Title') }}</th>
							<th>{{ _lang('Date') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";

	$('#holidays_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('admin/holidays/get_table_data') }}',
		"columns" : [
			{ data : 'title', name : 'title' },
			{ data : 'date', name : 'date' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": false,
		"language": {
		   "decimal":        "",
		   "emptyTable":     "{{ _lang('No Data Found') }}",
		   "info":           "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
		   "infoEmpty":      "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
		   "infoFiltered":   "(filtered from _MAX_ total entries)",
		   "infoPostFix":    "",
		   "thousands":      ",",
		   "lengthMenu":     "{{ _lang('Show') }} _MENU_ {{ _lang('Entries') }}",
		   "loadingRecords": "{{ _lang('Loading...') }}",
		   "processing":     "{{ _lang('Processing...') }}",
		   "search":         "{{ _lang('Search') }}",
		   "zeroRecords":    "{{ _lang('No matching records found') }}",
		   "paginate": {
			  "first":      "{{ _lang('First') }}",
			  "last":       "{{ _lang('Last') }}",
			  "previous":   "<i class='fas fa-angle-left'></i>",
			  "next":       "<i class='fas fa-angle-right'></i>"
		  }
		}
	});

	$(document).on('click', '#add-row', function(){
		$("#holidays-table").append(`<tr>
							<td>						
								<input type="text" class="form-control" name="title[]" placeholder="{{ _lang('Title') }}" required>
							</td>
							<td>						
								<input type="date" class="form-control" name="date[]" placeholder="{{ _lang('Date') }}" required>
							</td>
							<td class="text-center">						
								<button type="button" class="btn btn-danger btn-xs remove-row"><i class="ti-trash"></i></button>
							</td>
						</tr>`);
	});

	$(document).on('click', '.remove-row', function(){
		$(this).parent().parent().remove();
	});
})(jQuery);
</script>
@endsection