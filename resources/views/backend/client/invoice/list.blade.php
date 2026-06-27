@extends('layouts.client')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Invoices') }}</span>
			</div>
			<div class="card-body">
				<div class="row">	
					<div class="col-lg-3 mb-2">
						<div class="input-group">
							<input type="text" class="form-control" name="invoice_number" id="invoice_number" placeholder="{{ _lang('Invoice Number') }}">
							<div class="input-group-append">
								<button type="button" class="btn btn-outline-secondary" id="submit_invoice_number"><i class="fas fa-search"></i></button>
							</div>	
						</div>	
                    </div>	
					
                    <div class="col-lg-3 mb-2">
                     	<select class="form-control multi-selector select-filter" data-placeholder="{{ _lang('All Statuses') }}" name="status" multiple>
							<option value="0">{{ _lang('Draft') }}</option>
							<option value="1">{{ _lang('Approved') }}</option>
							<option value="2">{{ _lang('Paid') }}</option>
							<option value="3">{{ _lang('Partially Paid') }}</option>
							<option value="4">{{ _lang('Canceled') }}</option>
                     	</select>
                    </div>	

                    <div class="col-lg-3">
                     	<input type="text" class="form-control select-filter" id="date_range" autocomplete="off" placeholder="{{ _lang('Date Range') }}" name="date_range">
                    </div>	

					<div class="col-lg-3">
                     	<input type="text" class="form-control select-filter" id="due_date_range" autocomplete="off" placeholder="{{ _lang('Due Date Range') }}" name="due_date_range">
                    </div>	
                </div>
			</div>
		</div>
		

		<div class="card">
			<div class="card-body pt-0 mt-0">
				<table id="invoices_table" class="table rounded-table-header mt-0">
					<thead>
					    <tr>
							<th>{{ _lang('Invoice Number') }}</th>
							<th>{{ _lang('Invoice Date') }}</th>
							<th>{{ _lang('Due Date') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-right">{{ _lang('Amount Due') }}</th>
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
	var invoice_table = $('#invoices_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url: _url + '/client/invoices/get_table_data',
			method: "POST",
			data: function (d) {

				d._token =  $('meta[name="csrf-token"]').attr('content');
				
				if($('input[name=invoice_number]').val() != ''){
					d.invoice_number = $('input[name=invoice_number]').val();
				}

				if($('select[name=status]').val().length > 0){
					d.status = JSON.stringify($('select[name=status]').val());
				}
			
				if($('input[name=date_range]').val() != ''){
					d.date_range = $('input[name=date_range]').val();
				}

				if($('input[name=due_date_range]').val() != ''){
					d.due_date_range = $('input[name=due_date_range]').val();
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		}),
		"columns" : [
			{ data : 'invoice_number', name : 'invoice_number', orderable: false },
			{ data : 'invoice_date', name : 'invoice_date' },
			{ data : 'due_date', name : 'due_date' },
			{ data : 'grand_total', name : 'grand_total', orderable: false },
			{ data : 'amount_due', name : 'amount_due', orderable: false },
			{ data : 'status', name : 'status', orderable: false },
			{ data : 'action', name : 'action', orderable: false },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": false,
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

	$('#submit_invoice_number').on('click', function(e) {
		invoice_table.draw();
	});
	
	$('.select-filter').on('change', function(e) {
		invoice_table.draw();
	});
	
	$('#date_range, #due_date_range').daterangepicker({
		autoUpdateInput: false,
		locale: {
			format: 'YYYY-MM-DD',
			cancelLabel: 'Clear'
		}
	});

	$('#date_range, #due_date_range').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
		invoice_table.draw();
	});

	$('#date_range, #due_date_range').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
		invoice_table.draw();
	});

	$(document).on("ajax-screen-submit", function () {
		invoice_table.draw();
	});

})(jQuery);
</script>
@endsection