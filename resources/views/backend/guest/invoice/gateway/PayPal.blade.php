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
                        <div class="form-group">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
    </div>
</div>
@endsection

@section('js-script')
<!--PayPal Pay Now Button-->
<script src="https://www.paypal.com/sdk/js?client-id={{ $gateway->client_id }}&currency={{ $invoice->business->currency }}&disable-funding=credit,card"></script>

<div id="paypal-button-container"></div>

<script>
  paypal.Buttons({
	createOrder: function(data, actions) {
	  	return actions.order.create({
			purchase_units: [{
			  amount: {
				value: '{{ ($invoice->grand_total - $invoice->paid) }}'
			  }
			}]
	 	});
	},
	onApprove: function(data, actions) {
		window.location.href = "{{ $data->callback_url }}?order_id=" + data.orderID + "&invoice_id={{ $invoice->id }}&slug={{ $slug }}";
	},
	onCancel: function (data) {
		alert("{{ _lang('Payment Cancelled') }}");
	}
  }).render('#paypal-button-container');

</script>

@endsection