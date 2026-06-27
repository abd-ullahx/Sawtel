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
                        <button type="button" class="pay-now-btn" onclick="payWithPaystack()"> {{ _lang('Pay Now') }}</button>
                    </div>
                </div>
            </div>
	    </div>
    </div>
</div>
@endsection

@section('js-script')
<script src="https://js.paystack.co/v1/inline.js"></script>

<script type="text/javascript">

function payWithPaystack(e) {
  let handler = PaystackPop.setup({
    key: '{{ $gateway->paystack_public_key }}',
    email: '{{ $invoice->customer->email }}',
    amount: {{ (($invoice->grand_total - $invoice->paid) * 100) }},
    currency: '{{ $invoice->business->currency }}',
    firstname: '{{ $invoice->customer->name }}',
    ref: '{{ uniqid().time() }}',
    callback: function(response){
    	window.location = "{{ $data->callback_url }}?reference=" + response.reference + "&invoice_id={{ $invoice->id }}&slug={{ $slug }}";
    }
  });
  handler.openIframe();
}

</script>
@endsection