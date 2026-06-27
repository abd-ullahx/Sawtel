@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Transactions') }}</span>
				<div>
					<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('New Income') }}" href="{{ route('transactions.create') }}?type=income"><i class="fas fa-plus-circle mr-1"></i>{{ _lang('New Income') }}</a>
					<a class="btn btn-danger btn-xs ajax-modal" data-title="{{ _lang('New Expense') }}" href="{{ route('transactions.create') }}?type=expense"><i class="fas fa-minus-circle mr-1"></i>{{ _lang('New Expense') }}</a>
					<a class="btn btn-info btn-xs ajax-modal" data-title="{{ _lang('Transfer Between Accounts') }}" href="{{ route('transactions.transfer') }}"><i class="far fa-paper-plane mr-1"></i>{{ _lang('Transfer Money') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="transactions_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Account') }}</th>
							<th>{{ _lang('Category') }}</th>
							<th>{{ _lang('Reference') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
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

	var transactions_table = $('#transactions_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/transactions/get_table_data',
		"columns" : [
			{ data : 'trans_date', name : 'trans_date' },
			{ data : 'account.account_name', name : 'account.account_name' },
			{ data : 'category.name', name : 'category.name' },
			{ data : 'reference', name : 'reference' },
			{ data : 'amount', name : 'amount' },
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
		},
		drawCallback: function () {
			$(".dataTables_paginate > .pagination").addClass("pagination-bordered");
		},

	});

	$(document).on("ajax-screen-submit", function () {
		transactions_table.draw();
	});
})(jQuery);
</script>
@endsection