@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Payslips') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('payslips.create') }}"><i class="fas fa-receipt"></i> {{ _lang('Generate Payslip') }}</a>
			</div>
			<div class="card-body">
				<table id="payslips_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Employee ID') }}</th>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Month') }}</th>
							<th>{{ _lang('Year') }}</th>
							<th>{{ _lang('Net Salary') }}</th>
							<th>{{ _lang('Status') }}</th>
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

	$('#payslips_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('admin/payslips/get_table_data') }}',
		"columns" : [
			{ data : 'staff.employee_id', name : 'staff.employee_id' },
			{ data : 'staff.first_name', name : 'staff.first_name' },
			{ data : 'month', name : 'month' },
			{ data : 'year', name : 'year' },
			{ data : 'net_salary', name : 'net_salary' },
			{ data : 'status', name : 'status' },
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
			  "next":       "{{ _lang('Next') }}",
			  "previous":   "{{ _lang('Previous') }}"
		  }
		}
	});
})(jQuery);
</script>
@endsection