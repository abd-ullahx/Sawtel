@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Attendance') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('attendance.create') }}"><i class="ti-plus"></i> {{ _lang('Manage Attendance') }}</a>
			</div>
			<div class="card-body">
				<table id="attendance_table" class="table">
					<thead>
					    <tr>
							<th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Present') }}</th>
							<th>{{ _lang('Absent') }}</th>
							<th>{{ _lang('Leave') }}</th>
							<th>{{ _lang('Total') }}</th>
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

	$('#attendance_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('admin/attendance/get_table_data') }}',
		"columns" : [
			{ data : 'date', name : 'date' },
			{ data : "present", name : "present" },
			{ data : "absent", name : "absent" },
			{ data : "leave", name : "leave" },
			{ data : 'total', name : 'total' },
			{ data : 'action', name : 'action' },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": true,
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
})(jQuery);
</script>
@endsection