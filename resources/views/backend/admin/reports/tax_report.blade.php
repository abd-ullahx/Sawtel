@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Tax Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>
			<div class="card-body">
				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.tax_report') }}">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Report Type') }}</label>
									<select class="form-control auto-select" data-selected="{{ isset($report_type) ? $report_type : old('report_type', 'paid_unpaid') }}" name="report_type" required>
										<option value="paid_unpaid">{{ _lang('Paid & Unpaid') }}</option>
										<option value="paid">{{ _lang('Only Paid') }}</option>										
									</select>
								</div>
							</div>

							<div class="col-lg-3">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter"></i>&nbsp;{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp
				@php 
				$total_sales_tax = 0;
				$total_purcahse_tax = 0;
				@endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Tax Report') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Tax') }}</th>
								<th class="text-right">{{ _lang('Sales Subject to Tax') }}</th>
								<th class="text-right">{{ _lang('Tax Amount on Sales') }}</th>    
								<th class="text-right">{{ _lang('Purcahse Subject to Tax') }}</th>       
								<th class="text-right">{{ _lang('Tax Amount on Purcahse') }}</th>       
								<th class="text-right">{{ _lang('Net Tax Owing') }}</th>       
							</thead>
							<tbody>
								@if(isset($report_data))
								@foreach($sales_taxes as $sales_tax)
								<tr>
									<td>{{ $sales_tax->name }} ({{ $sales_tax->rate }} %)</td>
									<td class="text-right">{{ formatAmount($sales_tax->sales_amount, currency_symbol($currency)) }}</td>
									<td class="text-right">{{ formatAmount($sales_tax->sales_tax, currency_symbol($currency)) }}</td>
									<td class="text-right">{{ formatAmount($purchase_taxes[$loop->index]->purchase_amount, currency_symbol($currency)) }}</td>
									<td class="text-right">{{ formatAmount($purchase_taxes[$loop->index]->purchase_tax, currency_symbol($currency)) }}</td>
									<td class="text-right">{{ formatAmount($sales_tax->sales_tax - $purchase_taxes[$loop->index]->purchase_tax, currency_symbol($currency)) }}</td>
								</tr>
								@php $total_sales_tax += $sales_tax->sales_tax; @endphp
								@php $total_purcahse_tax += $purchase_taxes[$loop->index]->purchase_tax; @endphp
								@endforeach
								<tr>
									<td><b>{{ _lang('Total') }}</b></td>
									<td class="text-right"></td>
									<td class="text-right"><b>{{ formatAmount($total_sales_tax, currency_symbol($currency)) }}</b></td>  
									<td class="text-right"></td>  
									<td class="text-right"><b>{{ formatAmount($total_purcahse_tax, currency_symbol($currency)) }}</b</td> 
									<td class="text-right"><b>{{ formatAmount($total_sales_tax - $total_purcahse_tax, currency_symbol($currency)) }}</b</td> 
								</tr> 
								@endif
							</tbody>					
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection