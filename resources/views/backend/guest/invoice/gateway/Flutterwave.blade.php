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
                <form>
                    <script src="https://checkout.flutterwave.com/v3.js"></script>
                    <button type="button" class="btn btn-primary btn-block" onClick="makePayment()">{{ _lang('Pay Now') }}</button>
                </form>
            </div>
        </div>
			</div>
		</div>
  </div>
</div>
@endsection

@section('js-script')
<script>
  function makePayment() {
    FlutterwaveCheckout({
      public_key: "{{ $gateway->public_key }}",
      tx_ref: "{{ uniqid().time() }}",
      amount: {{ ($invoice->grand_total - $invoice->paid) }},
      currency: "{{ $invoice->business->currency }}",
      customer: {
        email: "{{ $invoice->customer->email }}",
        phone_number: "{{ $invoice->customer->mobile }}",
        name: "{{ $invoice->customer->name }}",
      },
      callback: function (data) {
        if(data.status == 'successful'){
            window.location.href = "{{ $data->callback_url }}?transaction_id=" + data.transaction_id + "&invoice_id={{ $invoice->id }}&slug={{ $slug }}";
        }else{
            window.location.href = "{{ $data->callback_url }}?invoice_id={{ $invoice->id }}";
        }
      },
      onclose: function() {},
      customizations: {
        title: "{{ _lang('Invoice Payment') }}",
        description: "{{ _lang('Invoice Payment') . ' #' . $invoice->invoice_number }}",
        logo: "{{ asset('public/uploads/media/' . $invoice->business->logo) }}",
      },
    });
  }
</script>
@endsection