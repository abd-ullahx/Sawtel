@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Products & Services') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" data-title="{{ _lang('Add New Product') }}" href="{{ route('products.create') }}"><i class="ti-plus mr-1"></i>{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="products_table" class="table">
					<thead>
					    <tr>
{{--							<th>{{ _lang('Bar Code') }}</th>--}}
{{--							<th>{{ _lang('Image') }}</th>--}}
						    <th>{{ _lang('Name') }}</th>
						    <th>{{ _lang('Item Code') }}</th>
							<th>{{ _lang('Type') }}</th>
							<th class="text-right">{{ _lang('Purchase Cost') }}</th>
							<th class="text-right">{{ _lang('Selling Price') }}</th>	
							<th class="text-center">{{ _lang('Stock') }}</th>
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
	
	$('#products_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/products/get_table_data',
		"columns" : [
			// { data : 'bar_code', name : 'bar_code', orderable: false },
			// { data : 'image', name : 'image' },
			{ data : 'name', name : 'name' },
			{ data : 'item_code', name : 'item_code' },
			{ data : 'type', name : 'type', orderable: false },
			{ data : 'purchase_cost', name : 'purchase_cost', orderable: false },
			{ data : 'selling_price', name : 'selling_price', orderable: false },
			{ data : 'stock', name : 'stock', orderable: false },
			{ data : 'status', name : 'status', orderable: false },
			{ data : "action", name : 'action', orderable: false },
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