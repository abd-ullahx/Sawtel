@extends('layouts.guest')

@section('content')
<div class="row">
	<div class="col-lg-6 offset-lg-3">
		<div class="card">
			<div class="card-header">
				<h4 class="header-title text-center">{{ _lang('Payment Confirm') }}</h4>
			</div>
			<div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-center">
						<h4>{{ _lang('Total Amount').': '.formatAmount($invoice->grand_total - $invoice->paid, currency_symbol($invoice->business->currency), $invoice->business_id) }}</h4>
                    </div>

                    <div class="col-md-12 mt-4">
                        <form action="{{ $data->callback_url }}?invoice_id={{ $invoice->id }}&slug={{ $slug }}" method="POST">
                            @csrf
                            <script
                            src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="{{ $gateway->razorpay_key_id }}"
                            data-amount="{{ (($invoice->grand_total - $invoice->paid) * 100) }}"
                            data-currency="{{ $invoice->business->currency }}"
                            data-name="{{ _lang('Invoice Payment') }}"
                            data-image="{{ asset('public/uploads/media/' . $invoice->business->logo) }}"
                            data-description="{{ _lang('Invoice Payment') . ' #' . $invoice->invoice_number }}"
                            data-prefill.name="{{ $invoice->customer->name }}"
                            data-prefill.email="{{ $invoice->customer->email  }}"
                            data-prefill.contact="{{ $invoice->customer->mobile  }}"
                            data-notes.shopping_order_id="{{ $invoice->id }}">
                            </script>
                        </form>
                    </div>
                </div>
			</div>
		</div>
    </div>
</div>
@endsection