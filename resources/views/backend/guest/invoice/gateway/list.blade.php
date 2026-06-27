@extends('layouts.guest')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header text-center bg-light">
				<span class="panel-title">{{ _lang('Available Payment Methods') }}</span>
			</div>
			<div class="card-body">
                <div class="row justify-content-center">
                @php $businessSettings = $invoice->business->systemSettings; @endphp
                @foreach(\App\Models\PaymentGateway::all() as $paymentgateway)
			    @php $params = json_decode(get_setting($businessSettings, $paymentgateway->slug, null, $invoice->business_id)); @endphp
                
                @if(isset($params->status) && $params->status == 1)
                    <div class="col-md-4 my-2">
						<div class="border rounded text-center">
							<div class="card-body">
								<img class="thumb-xl m-auto rounded-circle img-thumbnail" src="{{ asset('public/backend/images/gateways/'.$paymentgateway->image) }}"/>
								<h6 class="mt-3 mb-4">{{ $paymentgateway->name }}</h6>
								<a href="{{ route('invoices.make_payment', [$invoice->short_code, $paymentgateway->slug]) }}" class="btn btn-outline-primary btn-block"><i class="fas fa-long-arrow-alt-right mr-2"></i>{{ _lang('Select') }}</a>
							</div>
						</div>
					</div>
                @endif

                @endforeach
                </div>
            </div>
		</div>
	</div>
</div>
@endsection