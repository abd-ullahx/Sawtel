@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Clients') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" data-title="{{ _lang('Add New Client') }}" href="{{ route('clients.create') }}"><i class="ti-plus mr-1"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="customers_table" class="table">
					<thead>
					    <tr>
							<th>{{ _lang('Picture') }}</th>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Company Name') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('Mobile') }}</th>
							<th>{{ _lang('Currency') }}</th>
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

	$('#customers_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/clients/get_table_data',
		"columns" : [
			{ data : 'profile_picture', name : 'profile_picture' },
			{ data : 'name', name : 'name' },
			{ data : 'company_name', name : 'company_name' },
			{ data : 'email', name : 'email' },
			{ data : 'mobile', name : 'mobile' },
			{ data : 'currency', name : 'currency' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		//"ordering": false,
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