@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Quotations') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('quotations.create') }}"><i class="ti-plus mr-1"></i>{{ _lang('New Quotation') }}</a>
			</div>
			<div class="card-body">
				<table id="quotations_table" class="table">
					<thead>
					    <tr>
							<th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Expired At') }}</th>
							<th>{{ _lang('Quotation Number') }}</th>
						    <th>{{ _lang('Customer') }}</th>
						    <th>{{ _lang('Status') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
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
	var quotation_table = $('#quotations_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url: _url + '/admin/quotations/get_table_data',
			method: "POST",
			data: function (d) {
				d._token =  $('meta[name="csrf-token"]').attr('content');			
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		}),
		"columns" : [
			{ data : 'quotation_date', name : 'quotation_date' },
			{ data : 'expired_date', name : 'expired_date' },
			{ data : 'quotation_number', name : 'quotation_number', orderable: false  },
			{ data : 'customer.name', name : 'customer.name', orderable: false },
			{ data : 'status', name : 'status', orderable: false },
			{ data : 'grand_total', name : 'grand_total', orderable: false },
			{ data : 'action', name : 'action', orderable: false },
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

})(jQuery);
</script>
@endsection