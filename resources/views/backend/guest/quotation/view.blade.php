@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/invoice.css?v=1.0') }}">
@include('layouts.others.invoice-css')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header bg-light d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Quotation').' #'.$quotation->quotation_number }}</span>
				<div class="dropdown">
					<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
						<i class="fas fa-cog mr-1"></i>{{ _lang('Actions') }}
					</button>
					<div class="dropdown-menu">
						<a href="#" class="dropdown-item print" data-print="invoice">{{ _lang('Print Quotation') }}</a>
						<a href="{{ route('quotations.show_public_quotation', [$quotation->short_code,'pdf']) }}" class="dropdown-item">{{ _lang('Export PDF') }}</a>
					</div>
				</div>
			</div>
			<div>
			@include('backend.admin.quotation.template.loader')
			</div>
		</div>
	</div>
</div>
@endsection