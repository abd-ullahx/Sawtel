@php $type = isset($type) ? $type : 'preview'; @endphp
<!-- Default Invoice template -->
<div id="invoice" class="{{ $type }}">	
	<div class="default-invoice">			
		<div class="invoice-header">
			<div class="row">
				<div class="col-6 float-left left-header">
					@if($type == 'pdf')
					<img class="logo" src="{{ public_path('uploads/media/' . $quotation->business->logo) }}">
					@else
					<img class="logo" src="{{ asset('public/uploads/media/' . $quotation->business->logo) }}">
					@endif
					<h2 class="title">{{ $quotation->title }}</h2>
				</div>
				<div class="col-6 float-right right-header">
					<h4 class="company-name">{{ $quotation->business->name }}</h4>
					<p>{{ $quotation->business->address }}</p>
					<p>{{ $quotation->business->phone }}</p>
					<p>{{ $quotation->business->email }}</p>
					<p>{{ $quotation->business->country }}</p>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="invoice-details">
			<div class="row align-items-bottom">
				<div class="col-6 float-left">
					<h5 class="bill-to-heading">{{ _lang('BILLING DETAILS') }}</h5>

					<h4 class="bill-to">{{ $quotation->customer->name }}</h4>
					<p>{{ $quotation->customer->address }}</<p>
					<p>{{ $quotation->customer->city }}</<p>
					<p>{{ $quotation->customer->zip }}</<p>
					<p>{{ $quotation->customer->country }}</p>
				</div>
				<div class="col-6 text-right float-right">
					<h5 class="mb-2">{{ _lang('Quotation') }}#: {{ $quotation->quotation_number }}</h4>
					@if($quotation->po_so_number != '')
					<p>{{ _lang('PO / SO Number') }}: {{ $quotation->po_so_number }}</p>
					@endif
					<p>{{ _lang('Quotation Date') }}: {{ $quotation->quotation_date }}</p>
					<p class="mb-2">{{ _lang('Expired Date') }}: {{ $quotation->expired_date }}</p>
					<p><strong>{{ _lang('Grand Total') }}: {{ formatAmount($quotation->grand_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</strong></p>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		@php $quotationColumns = json_decode(get_business_option('quotation_column', null, $quotation->business_id)); @endphp
							
		<div class="invoice-body">
			<div class="table-responsive-sm">
				<table class="table">
					<thead>
						<tr>
							@if(isset($quotationColumns->name->status))
							@if($quotationColumns->name->status != '0')
							<th>{{ isset($quotationColumns->name->label) ? $quotationColumns->name->label : _lang('Name') }}</th>
							@endif
							@else
							<th>{{ _lang('Name') }}</th>
							@endif

							@if(isset($quotationColumns->quantity->status))
							@if($quotationColumns->quantity->status != '0')
							<th class="text-center">{{ isset($quotationColumns->quantity->label) ? $quotationColumns->quantity->label : _lang('Quantity') }}</th>
							@endif
							@else
							<th class="text-center">{{ _lang('Quantity') }}</th>
							@endif

							@if(isset($quotationColumns->price->status))
							@if($quotationColumns->price->status != '0')
							<th class="text-right">{{ isset($quotationColumns->price->label) ? $quotationColumns->price->label : _lang('Price') }}</th>
							@endif
							@else
							<th class="text-right">{{ _lang('Price') }}</th>
							@endif

							@if(isset($quotationColumns->amount->status))
							@if($quotationColumns->amount->status != '0')
							<th class="text-right">{{ isset($quotationColumns->amount->label) ? $quotationColumns->amount->label : _lang('Amount') }}</th>
							@endif
							@else
							<th class="text-right">{{ _lang('Amount') }}</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($quotation->items as $item)
						<tr>	
							<td class="product-name">
								@if(isset($quotationColumns->name->status))
									@if($quotationColumns->name->status != '0')
									<p>{{ $item->product_name }}</p>
									@endif
								@else
									<p>{{ $item->product_name }}</p>
								@endif

								@if(isset($quotationColumns->description->status))
									@if($quotationColumns->description->status != '0')
									<p>{{ $item->description }}</p>
									@endif
								@else
									<p>{{ $item->description }}</p>
								@endif
							</td>

							@if(isset($quotationColumns->quantity->status))
							@if($quotationColumns->quantity->status != '0')
							<td class="text-center">{{ $item->quantity.' '.$item->product->product_unit->unit }}</td>
							@endif
							@else
							<td class="text-center">{{ $item->quantity.' '.$item->product->product_unit->unit }}</td>
							@endif

							@if(isset($quotationColumns->price->status))
							@if($quotationColumns->price->status != '0')
							<td class="text-right text-nowrap">{{ formatAmount($item->unit_cost, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>	
							@endif
							@else
							<td class="text-right text-nowrap">{{ formatAmount($item->unit_cost, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>
							@endif
							
							@if(isset($quotationColumns->amount->status))
							@if($quotationColumns->amount->status != '0')
							<td class="text-right text-nowrap">{{ formatAmount($item->sub_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>	
							@endif
							@else
							<td class="text-right text-nowrap">{{ formatAmount($item->sub_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>	
							@endif
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="invoice-summary">
			<div class="row">
				<div class="col-xl-7 col-lg-6 float-left">
					<div class="invoice-note">
						<p><b>{{ _lang('Notes / Terms') }}:</b> {!! xss_clean($quotation->note) !!}</p>
					</div>
				</div>
				<div class="col-xl-5 col-lg-6 float-right">
					<table class="table text-right m-0">
						<tr>
							<td>{{ _lang('Sub Total') }}</td>
							<td class="text-nowrap">{{ formatAmount($quotation->sub_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>
						</tr>
						@foreach($quotation->taxes as $tax)
						<tr>
							<td>{{ $tax->name }}</td>						
							<td class="text-nowrap">+ {{ formatAmount($tax->amount, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>
						</tr>
						@endforeach	
						@if($quotation->discount > 0)
						<tr>
							<td>{{ _lang('Discount') }}</td>
							<td class="text-nowrap">- {{ formatAmount($quotation->discount, currency_symbol($quotation->business->currency), $quotation->business_id) }}</td>
						</tr>
						@endif
						<tr>
							<td><b>{{ _lang('Grand Total') }}</b></td>
							<td class="text-nowrap"><b>{{ formatAmount($quotation->grand_total, currency_symbol($quotation->business->currency), $quotation->business_id) }}</b></td>
						</tr>
						@if($quotation->grand_total != $quotation->converted_total)
						<tr>
							<td><b>{{ _lang('Converted Total') }}</b></td>
							<td class="text-nowrap"><b>{{ formatAmount($quotation->converted_total, currency_symbol($quotation->customer->currency), $quotation->business_id) }}</b></td>
						</tr>
						@endif
					</table>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<div class="invoice-footer">
		<p>{!! xss_clean($quotation->footer) !!}</p>
	</div>

</div>


