@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/invoice.css?v=1.0') }}">
@include('layouts.others.invoice-css')

<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header bg-light d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Invoice').' #'.$invoice->invoice_number }}</span>
				<div class="dropdown">
					<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
						<i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
					</button>
					<div class="dropdown-menu">
						<a href="#" class="dropdown-item print" data-print="invoice">{{ _lang('Print Invoice') }}</a>
						<a href="{{ route('invoices.show_public_invoice', [$invoice->short_code,'pdf']) }}" class="dropdown-item">{{ _lang('Export PDF') }}</a>
						@if($invoice->status != 2 && $invoice->status != 0  && $invoice->status != 99)
						<a href="{{ route('invoices.payment_methods', $invoice->short_code) }}" class="dropdown-item">{{ _lang('Make Payment') }}</a>
						@endif
					</div>
				</div>
			</div>
			<div>
				@include('backend.admin.invoice.template.loader')
			</div>
		</div>
	</div>
</div>
@endsection