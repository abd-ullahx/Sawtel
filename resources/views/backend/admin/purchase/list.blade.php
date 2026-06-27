@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Purchase Orders') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('purchases.create') }}"><i class="ti-plus mr-1"></i>{{ _lang('New Purchase') }}</a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-3 mb-2">
						<div class="input-group">
							<input type="text" class="form-control" name="bill_no" id="bill_no" placeholder="{{ _lang('Bill No') }}">
							<div class="input-group-append">
								<button type="button" class="btn btn-outline-secondary" id="submit_bill_no"><i class="fas fa-search"></i></button>
							</div>	
						</div>	
                    </div>	
					
					<div class="col-lg-3 mb-2">
						<select class="form-control select2 select-filter" name="vendor_id">
                            <option value="">{{ _lang('All Vendors') }}</option>
							@foreach(\App\Models\Vendor::all() as $vendor)
							<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
							@endforeach
                     	</select>
                    </div>	
					
                    <div class="col-lg-3 mb-2">
                     	<select class="form-control multi-selector select-filter" data-placeholder="{{ _lang('All Statuses') }}" name="status" multiple>
							<option value="0">{{ _lang('Unpaid') }}</option>
							<option value="1">{{ _lang('Partially Paid') }}</option>
							<option value="2">{{ _lang('Paid') }}</option>
                     	</select>
                    </div>	

                    <div class="col-lg-3">
                     	<input type="text" class="form-control select-filter" id="date_range" autocomplete="off" placeholder="{{ _lang('Date Range') }}" name="date_range">
                    </div>	
                </div>
			</div>
		</div>
		

		<div class="card">
			<div class="card-body pt-0 mt-0">
				<table id="purchases_table" class="table rounded-table-header mt-0">
					<thead>
					    <tr>
							<th>{{ _lang('Purchasee Date') }}</th>
							<th>{{ _lang('Due Date') }}</th>
							<th>{{ _lang('Bill Number') }}</th>
						    <th>{{ _lang('Vendor') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-right">{{ _lang('Amount Due') }}</th>
							<th class="text-center">{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Order Status') }}</th>
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

	let final_url = "{{ (request('type') == 'purchase_alert') ? '/admin/purchases/get_table_data?type=purchase_alert' : '/admin/purchases/get_table_data' }}";

	var purchase_table = $('#purchases_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url: _url + final_url,
			method: "POST",
			data: function (d) {

				d._token =  $('meta[name="csrf-token"]').attr('content');
				
				if($('input[name=bill_no]').val() != ''){
					d.bill_no = $('input[name=bill_no]').val();
				}

				if($('select[name=vendor_id]').val() != ''){
					d.vendor_id = $('select[name=vendor_id]').val();
				}
				
				if($('select[name=status]').val().length > 0){
					d.status = JSON.stringify($('select[name=status]').val());
				}
			
				if($('input[name=date_range]').val() != ''){
					d.date_range = $('input[name=date_range]').val();
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		}),
		"columns" : [
			{ data : 'purchase_date', name : 'purchase_date' },
			{ data : 'due_date', name : 'due_date' },
			{ data : 'bill_no', name : 'bill_no', orderable: false  },
			{ data : 'vendor.name', name : 'vendor.name', orderable: false },
			{ data : 'grand_total', name : 'grand_total', orderable: false },
			{ data : 'amount_due', name : 'amount_due', orderable: false },
			{ data : 'status', name : 'status', orderable: false },
			{ data : 'order_status', name : 'order_status', orderable: false },
			{ data : 'action', name : 'action', orderable: false },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": true,
		"dom": "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'>>" +
					"<'row mt-0'<'col-sm-12'tr>>" +
					"<'row'<'col-sm-12 col-md-5 mt-2'l><'col-sm-12 col-md-7 mt-2'p>>",
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

	$('#submit_bill_no').on('click', function(e) {
		purchase_table.draw();
	});
	
	$('.select-filter').on('change', function(e) {
		purchase_table.draw();
	});
	
	$('#date_range').daterangepicker({
		autoUpdateInput: false,
		locale: {
			format: 'YYYY-MM-DD',
			cancelLabel: 'Clear'
		}
	});

	$('#date_range').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
		purchase_table.draw();
	});

	$('#date_range').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
		purchase_table.draw();
	});

	$(document).on("ajax-screen-submit", function () {
		purchase_table.draw();
	});

})(jQuery);
</script>
@endsection