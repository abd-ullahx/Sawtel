@extends('layouts.client')

@section('content')
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 primary-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Total Invoices') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $invoice->total_invoice }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 warning-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Total Invoice Amount') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_amount, currency_symbol($business->currency), $business->id) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 success-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Total Paid') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_paid, currency_symbol($business->currency), $business->id) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 danger-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Due Amount') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_amount - $invoice->total_paid, currency_symbol($business->currency), $business->id) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Recent Invoices') }}</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table border-bottom">
						<thead>
							<tr>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Due Date') }}</th>
								<th>{{ _lang('Invoice Number') }}</th>
								<th class="text-right">{{ _lang('Grand Total') }}</th>
								<th class="text-right">{{ _lang('Amount Due') }}</th>
								<th class="text-center">{{ _lang('Status') }}</th>
								<th class="text-center">{{ _lang('Action') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($recent_invoices as $invoice)
								<tr>
									<td>{{ $invoice->invoice_date }}</td>
									<td>{{ $invoice->due_date }}</td>
									<td>{{ $invoice->invoice_number }}</td>
									<td class="text-right">{{ formatAmount($invoice->grand_total, currency_symbol($business->currency), $business->id) }}</td>
									<td class="text-right">{{ formatAmount($invoice->grand_total - $invoice->paid, currency_symbol($business->currency), $business->id) }}</td>
									<td class="text-center">{!! xss_clean(invoice_status($invoice)) !!}</td>
									<td class="text-center">
										<a class="btn btn-xs btn-outline-primary" target="_blank" href="{{ route('invoices.show_public_invoice', $invoice->short_code) }}"><i class="far fa-eye mr-1"></i>{{ _lang('Preview') }}</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
