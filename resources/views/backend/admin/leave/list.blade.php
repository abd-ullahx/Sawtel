@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Leave Management') }}</span>
				<div class="d-sm-flex align-items-center mt-2 mt-sm-0">
					<a class="btn btn-info btn-xs" href="{{ route('leave_types.index') }}">{{ _lang('Leave Types') }}</a>
					<a class="btn btn-primary btn-xs ajax-modal ml-0 ml-sm-1" data-title="{{ _lang('Add New Leave') }}" href="{{ route('leaves.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="leaves_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('ID') }}</th>
						    <th>{{ _lang('Employee ID') }}</th>
							<th>{{ _lang('Leave Type') }}</th>
							<th>{{ _lang('Leave Duration') }}</th>
							<th>{{ _lang('Start Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th>{{ _lang('Total') }}</th>
							<th class="text-center">{{ _lang('Status') }}</th>
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

	$('#leaves_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('admin/leaves/get_table_data') }}',
		"columns" : [
			{ data : 'id', name : 'id' },
			{ data : 'staff.employee_id', name : 'staff.employee_id' },
			{ data : 'leave_type', name : 'leave_type' },
			{ data : 'leave_duration', name : 'leave_duration' },
			{ data : 'start_date', name : 'start_date' },
			{ data : 'end_date', name : 'end_date' },
			{ data : 'total_days', name : 'total_days' },
			{ data : 'status', name : 'status' },
			{ data : "action", name : "action" },
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

	$(document).on('change', 'input[name=start_date], input[name=end_date]', function(){
		var startDate = $('input[name=start_date]').val();
		var endDate = $('input[name=end_date]').val();
		$('#total_days').val(0);
		if(startDate != '' && endDate != ''){
			var start_date = new Date(startDate);
			var end_date = new Date(endDate);
			var total_days = (end_date - start_date) / 1000 / 60 / 60 / 24 + 1;
			if(total_days > 0){
				$('#total_days').val(total_days);
			}
		}
	});

})(jQuery);
</script>
@endsection