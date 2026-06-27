@extends('layouts.app')

@section('content')
<link href="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs business-settings-tabs" role="tablist">
			 <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general_settings"><i class="fas fa-tools mr-2"></i><span>{{ _lang('General Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#currency_settings"><i class="fas fa-pound-sign mr-2"></i><span>{{ _lang('Currency Settings') }}</span></a></li>
{{--			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoice_settings"><i class="fas fa-receipt mr-2"></i><span>{{ _lang('Invoice Settings') }}</span></a></li>--}}
{{--			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#payment_gateways"><i class="fab fa-paypal mr-2"></i><span>{{ _lang('Payment Gateways') }}</span></a></li>--}}
			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#email"><i class="fas fa-at mr-2"></i><span>{{ _lang('Email Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" href="{{ route('business.edit', request()->activeBusiness->id) }}"><i class="far fa-edit mr-2"></i><span>{{ _lang('Update Business') }}</span></a></li>
		</ul>

		<div class="tab-content settings-tab-content">
			<div id="general_settings" class="tab-pane active">
				<div class="card">

					<div class="card-body">
						<form action="{{ route('business.store_general_settings', $id) }}" class="settings-submit" autocomplete="off" method="post" enctype="multipart/form-data">
							@csrf
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Timezone') }}</label>
								<div class="col-xl-9">
									<select class="form-control select2 auto-select" data-selected="{{ get_setting($business->systemSettings, 'timezone','',$id) }}" name="timezone" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ create_timezone_option() }}
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Language') }}</label>
								<div class="col-xl-9">
									<select class="form-control select2 auto-select" name="language" data-selected="{{ get_setting($business->systemSettings, 'language','',$id) }}" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ load_language() }}
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Backend Direction') }}</label>
								<div class="col-xl-9">
									<select class="form-control auto-select" name="backend_direction" data-selected="{{ get_setting($business->systemSettings, 'backend_direction', 'ltr', $id) }}" required>
										<option value="ltr">{{ _lang('LTR') }}</option>
										<option value="rtl">{{ _lang('RTL') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Date Format') }}</label>
								<div class="col-xl-9">
									<select class="form-control auto-select" name="date_format" data-selected="{{ get_setting($business->systemSettings, 'date_format', 'Y-m-d', $id) }}" required>
										<option value="Y-m-d">{{ date('Y-m-d') }}</option>
										<option value="d-m-Y">{{ date('d-m-Y') }}</option>
										<option value="d/m/Y">{{ date('d/m/Y') }}</option>
										<option value="m-d-Y">{{ date('m-d-Y') }}</option>
										<option value="m.d.Y">{{ date('m.d.Y') }}</option>
										<option value="m/d/Y">{{ date('m/d/Y') }}</option>
										<option value="d.m.Y">{{ date('d.m.Y') }}</option>
										<option value="d/M/Y">{{ date('d/M/Y') }}</option>
										<option value="d/M/Y">{{ date('M/d/Y') }}</option>
										<option value="d M, Y">{{ date('d M, Y') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Time Format') }}</label>
								<div class="col-xl-9">
									<select class="form-control auto-select" name="time_format" data-selected="{{ get_setting($business->systemSettings, 'time_format', 24, $id) }}" required>
										<option value="24">{{ _lang('24 Hours') }}</option>
										<option value="12">{{ _lang('12 Hours') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row mt-2">
								<div class="col-xl-9 offset-lg-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div id="currency_settings" class="tab-pane">
				<div class="card">
					<div class="card-body">
						<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_currency_settings', $id) }}" enctype="multipart/form-data">
							@csrf													
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Currency Position') }}</label>						
								<div class="col-xl-9">
									<select class="form-control auto-select" data-selected="{{ get_setting($business->systemSettings, 'currency_position', 'left', $id) }}" name="currency_position" required>
										<option value="left">{{ _lang('Left') }}</option>
										<option value="right">{{ _lang('Right') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Thousand Seperator') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="thousand_sep" value="{{ get_setting($business->systemSettings, 'thousand_sep', ',', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Decimal Seperator') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="decimal_sep" value="{{ get_setting($business->systemSettings, 'decimal_sep', '.', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Decimal Places') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="decimal_places" value="{{ get_setting($business->systemSettings, 'decimal_places', 2, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
								</div>
							</div>								
						</form>
					</div>
				</div>
			</div>

			<div id="invoice_settings" class="tab-pane">
				<div class="card">

					<div class="card-body">
						<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_invoice_settings', $id) }}" enctype="multipart/form-data">
							@csrf	
							
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Title') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="invoice_title" value="{{ get_setting($business->systemSettings, 'invoice_title', 'Invoice', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Auto Increment') }}</label>	
								<div class="col-xl-9">
									<input type="number" class="form-control" name="invoice_number" value="{{ get_setting($business->systemSettings, 'invoice_number', 100001, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Primary Color') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control colorpicker" name="invoice_primary_color" value="{{ get_setting($business->systemSettings, 'invoice_primary_color', '#023e85', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Footer') }} ({{ _lang('HTML Allowed') }})</label>	
								<div class="col-xl-9">
									<textarea class="form-control" name="invoice_footer">{{ get_setting($business->systemSettings, 'invoice_footer', '', $id) }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Column Settings') }}</label>	
								<div class="col-xl-9">
									@php $invoiceColumns = json_decode(get_setting($business->systemSettings, 'invoice_column', null, $id)); @endphp
									
									<div class="table-responsove">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>{{ _lang('Column Name') }}</th>
													<th>{{ _lang('Label') }}</th>
													<th class="text-center">{{ _lang('Visible') }}</th>
													<th class="text-center">{{ _lang('Hidden') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>{{ _lang('Name') }}</td>
													<td>
														<input type="text" name="invoice_column[name][label]" value="{{ isset($invoiceColumns->name->label) ? $invoiceColumns->name->label : _lang('Name') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[name][status]" value="1" {{ isset($invoiceColumns->name->status) && $invoiceColumns->name->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[name][status]" value="0" {{ isset($invoiceColumns->name->status) && $invoiceColumns->name->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Descriptions') }}</td>
													<td></td>
													<td class="text-center">
														<input type="radio" name="invoice_column[description][status]" value="1" {{ isset($invoiceColumns->description->status) && $invoiceColumns->description->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[description][status]" value="0" {{ isset($invoiceColumns->description->status) && $invoiceColumns->description->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Quantity') }}</td>
													<td>
														<input type="text" name="invoice_column[quantity][label]" value="{{ isset($invoiceColumns->quantity->label) ? $invoiceColumns->quantity->label : _lang('Quantity') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[quantity][status]" value="1" {{ isset($invoiceColumns->quantity->status) && $invoiceColumns->quantity->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[quantity][status]" value="0" {{ isset($invoiceColumns->quantity->status) && $invoiceColumns->quantity->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Price') }}</td>
													<td>
														<input type="text" name="invoice_column[price][label]" value="{{ isset($invoiceColumns->price->label) ? $invoiceColumns->price->label : _lang('Price') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[price][status]" value="1" {{ isset($invoiceColumns->price->status) && $invoiceColumns->price->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[price][status]" value="0" {{ isset($invoiceColumns->price->status) && $invoiceColumns->price->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Amount') }}</td>
													<td>
														<input type="text" name="invoice_column[amount][label]" value="{{ isset($invoiceColumns->amount->label) ? $invoiceColumns->amount->label : _lang('Amount') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[amount][status]" value="1" {{ isset($invoiceColumns->amount->status) && $invoiceColumns->amount->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="invoice_column[amount][status]" value="0" {{ isset($invoiceColumns->amount->status) && $invoiceColumns->amount->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<hr>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Quotation Title') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="quotation_title" value="{{ get_setting($business->systemSettings, 'quotation_title', 'Quotation', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Quotation Auto Increment') }}</label>	
								<div class="col-xl-9">
									<input type="number" class="form-control" name="quotation_number" value="{{ get_setting($business->systemSettings, 'quotation_number', 100001, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Quotation Footer') }} ({{ _lang('HTML Allowed') }})</label>	
								<div class="col-xl-9">
									<textarea class="form-control" name="quotation_footer">{{ get_setting($business->systemSettings, 'quotation_footer', '', $id) }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Quotation Column Settings') }}</label>	
								<div class="col-xl-9">
									@php $quotationColumns = json_decode(get_setting($business->systemSettings, 'quotation_column', null, $id)); @endphp
									
									<div class="table-responsove">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>{{ _lang('Column Name') }}</th>
													<th>{{ _lang('Label') }}</th>
													<th class="text-center">{{ _lang('Visible') }}</th>
													<th class="text-center">{{ _lang('Hidden') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>{{ _lang('Name') }}</td>
													<td>
														<input type="text" name="quotation_column[name][label]" value="{{ isset($quotationColumns->name->label) ? $quotationColumns->name->label : _lang('Name') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[name][status]" value="1" {{ isset($quotationColumns->name->status) && $quotationColumns->name->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[name][status]" value="0" {{ isset($quotationColumns->name->status) && $quotationColumns->name->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Descriptions') }}</td>
													<td></td>
													<td class="text-center">
														<input type="radio" name="quotation_column[description][status]" value="1" {{ isset($quotationColumns->description->status) && $quotationColumns->description->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[description][status]" value="0" {{ isset($quotationColumns->description->status) && $quotationColumns->description->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Quantity') }}</td>
													<td>
														<input type="text" name="quotation_column[quantity][label]" value="{{ isset($quotationColumns->quantity->label) ? $quotationColumns->quantity->label : _lang('Quantity') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[quantity][status]" value="1" {{ isset($quotationColumns->quantity->status) && $quotationColumns->quantity->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[quantity][status]" value="0" {{ isset($quotationColumns->quantity->status) && $quotationColumns->quantity->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Price') }}</td>
													<td>
														<input type="text" name="quotation_column[price][label]" value="{{ isset($quotationColumns->price->label) ? $quotationColumns->price->label : _lang('Price') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[price][status]" value="1" {{ isset($quotationColumns->price->status) && $quotationColumns->price->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[price][status]" value="0" {{ isset($quotationColumns->price->status) && $quotationColumns->price->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Amount') }}</td>
													<td>
														<input type="text" name="quotation_column[amount][label]" value="{{ isset($quotationColumns->amount->label) ? $quotationColumns->amount->label : _lang('Amount') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[amount][status]" value="1" {{ isset($quotationColumns->amount->status) && $quotationColumns->amount->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="quotation_column[amount][status]" value="0" {{ isset($quotationColumns->amount->status) && $quotationColumns->amount->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<hr>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Purchase Title') }}</label>	
								<div class="col-xl-9">
									<input type="text" class="form-control" name="purchase_title" value="{{ get_setting($business->systemSettings, 'purchase_title', 'Purchase Order', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Purchase Footer') }} ({{ _lang('HTML Allowed') }})</label>	
								<div class="col-xl-9">
									<textarea class="form-control" name="purchase_footer">{{ get_setting($business->systemSettings, 'purchase_footer', '', $id) }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Purchase Column Settings') }}</label>	
								<div class="col-xl-9">
									@php $purchaseColumns = json_decode(get_setting($business->systemSettings, 'purchase_column', null, $id)); @endphp
									
									<div class="table-responsove">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>{{ _lang('Column Name') }}</th>
													<th>{{ _lang('Label') }}</th>
													<th class="text-center">{{ _lang('Visible') }}</th>
													<th class="text-center">{{ _lang('Hidden') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>{{ _lang('Name') }}</td>
													<td>
														<input type="text" name="purchase_column[name][label]" value="{{ isset($purchaseColumns->name->label) ? $purchaseColumns->name->label : _lang('Name') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[name][status]" value="1" {{ isset($purchaseColumns->name->status) && $purchaseColumns->name->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[name][status]" value="0" {{ isset($purchaseColumns->name->status) && $purchaseColumns->name->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Descriptions') }}</td>
													<td></td>
													<td class="text-center">
														<input type="radio" name="purchase_column[description][status]" value="1" {{ isset($purchaseColumns->description->status) && $purchaseColumns->description->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[description][status]" value="0" {{ isset($purchaseColumns->description->status) && $purchaseColumns->description->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Quantity') }}</td>
													<td>
														<input type="text" name="purchase_column[quantity][label]" value="{{ isset($purchaseColumns->quantity->label) ? $purchaseColumns->quantity->label : _lang('Quantity') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[quantity][status]" value="1" {{ isset($purchaseColumns->quantity->status) && $purchaseColumns->quantity->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[quantity][status]" value="0" {{ isset($purchaseColumns->quantity->status) && $purchaseColumns->quantity->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Price') }}</td>
													<td>
														<input type="text" name="purchase_column[price][label]" value="{{ isset($purchaseColumns->price->label) ? $purchaseColumns->price->label : _lang('Price') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[price][status]" value="1" {{ isset($purchaseColumns->price->status) && $purchaseColumns->price->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[price][status]" value="0" {{ isset($purchaseColumns->price->status) && $purchaseColumns->price->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td>{{ _lang('Amount') }}</td>
													<td>
														<input type="text" name="purchase_column[amount][label]" value="{{ isset($purchaseColumns->amount->label) ? $purchaseColumns->amount->label : _lang('Amount') }}" class="form-control" required>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[amount][status]" value="1" {{ isset($purchaseColumns->amount->status) && $purchaseColumns->amount->status == '0' ? '' : 'checked' }}>
													</td>
													<td class="text-center">
														<input type="radio" name="purchase_column[amount][status]" value="0" {{ isset($purchaseColumns->amount->status) && $purchaseColumns->amount->status == '0' ? 'checked' : '' }}>
													</td>
												</tr>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
								</div>
							</div>								
						</form>
					</div>
				</div>
			</div>

			<div id="payment_gateways" class="tab-pane">
				<div class="card">

					<div class="card-body">
						<div class="accordion" id="payment_gateways">
							
							@foreach(\App\Models\PaymentGateway::all() as $paymentgateway)
							@php $params = json_decode(get_setting($business->systemSettings, $paymentgateway->slug, null, $id)); @endphp

							<div class="card">
								<div class="card-header bg-light" data-toggle="collapse" data-target="#{{ $paymentgateway->slug }}" aria-expanded="true" aria-controls="{{ $paymentgateway->slug }}">
								  <span class="panel-title"><img class="thumb-xs rounded-circle img-thumbnail mr-2" src="{{ asset('public/backend/images/gateways/'.$paymentgateway->image) }}"/>{{ $paymentgateway->name }}</span>
								</div>

								<div id="{{ $paymentgateway->slug }}" class="collapse" aria-labelledby="heading{{ $paymentgateway->slug }}" data-parent="#payment_gateways">
									<div class="card-body">
									   <form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_payment_gateway_settings', $id) }}" enctype="multipart/form-data">
											@csrf
											<div class="row">
												<div class="col-lg-10">
													<div class="form-group row">
														<label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
														<div class="col-xl-9">
															<select class="form-control auto-select" data-selected="{{ isset($params->status) ? $params->status : 0 }}" name="{{ $paymentgateway->slug }}[status]" required>
																<option value="0">{{ _lang('Disabled') }}</option>
																<option value="1">{{ _lang('Active') }}</option>
															</select>
														</div>
													</div>
													<input type="hidden" name="slug" value="{{ $paymentgateway->slug }}">

													@foreach($paymentgateway->parameters as $key => $value)
														@if($key != 'environment')
															<div class="form-group row">
																<label class="col-xl-3 col-form-label">{{ strtoupper(str_replace('_',' ',$key)) }}</label>
																<div class="col-xl-9">
																	<input type="text" class="form-control" value="{{ isset($params->$key) ? $params->$key : '' }}" name="{{ $paymentgateway->slug }}[{{$key}}]">
																</div>
															</div>
														@else
															<div class="form-group row">
																<label class="col-xl-3 col-form-label">{{ strtoupper(str_replace('_',' ',$key)) }}</label>
																<div class="col-xl-9">
																	<select class="form-control auto-select" data-selected="{{ isset($params->$key) ? $params->$key : 'sandbox' }}" name="{{ $paymentgateway->slug }}[{{$key}}]">
																		<option value="sandbox">{{ _lang('Sandbox') }}</option>
																		<option value="live">{{ _lang('Live') }}</option>
																	</select>
																</div>
															</div>
														@endif
													@endforeach

													<div class="form-group row">
														<label class="col-xl-3 col-form-label">{{ _lang('Credit Account') }}</label>
														<div class="col-xl-9">
															<select class="form-control auto-select" data-selected="{{ isset($params->account) ? $params->account : '' }}" name="{{ $paymentgateway->slug }}[account]">
																<option value="">{{ _lang('Select One') }}</option>
																@foreach(\App\Models\Account::all() as $account)
																<option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->currency }}</option>
																@endforeach
															</select>
														</div>
													</div>

													<div class="form-group row mt-2">
														<div class="col-xl-9 offset-xl-3">
															<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							@endforeach

						</div><!-- End Accordion -->
					</div>
				</div>
			</div>

			<div id="email" class="tab-pane">
				<div class="row">
					<div class="col-lg-8 mb-md-4">
						<div class="card">
							<div class="card-header">
								<span>{{ _lang('Email Configuration') }}</span>
							</div>
							<div class="card-body">
								<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_email_settings', $id) }}" enctype="multipart/form-data">
									@csrf
									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('Mail Type') }}</label>
										<div class="col-xl-9">
											<select class="form-control auto-select" data-selected="{{ get_setting($business->systemSettings, 'mail_type', '', $id) }}" name="mail_type" id="mail_type">
												<option value="">{{ _lang('None') }}</option>
												<option value="smtp">{{ _lang('SMTP') }}</option>
												<option value="sendmail">{{ _lang('Sendmail') }}</option>
											</select>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('From Email') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control" name="from_email" value="{{ get_setting($business->systemSettings, 'from_email', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('From Name') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control" name="from_name" value="{{ get_setting($business->systemSettings, 'from_name', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Host') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" name="smtp_host" value="{{ get_setting($business->systemSettings, 'smtp_host', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Port') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" name="smtp_port" value="{{ get_setting($business->systemSettings, 'smtp_port', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Username') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" autocomplete="off" name="smtp_username" value="{{ get_setting($business->systemSettings, 'smtp_username', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Password') }}</label>
										<div class="col-xl-9">
											<input type="password" class="form-control smtp" autocomplete="off" name="smtp_password" value="{{ get_setting($business->systemSettings, 'smtp_password', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Encryption') }}</label>
										<div class="col-xl-9">
											<select class="form-control smtp auto-select" data-selected="{{ get_setting($business->systemSettings, 'smtp_encryption', '', $id) }}" name="smtp_encryption">
												<option value="">{{ _lang('None') }}</option>
												<option value="ssl">{{ _lang('SSL') }}</option>
												<option value="tls">{{ _lang('TLS') }}</option>
											</select>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-xl-9 offset-xl-3">
											<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
										</div>
									</div>	
								</form>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card">
							<div class="card-header">
								<span>{{ _lang('Send Test Email') }}</span>
							</div>
							<div class="card-body">
								<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.send_test_email', $id) }}">
									@csrf
									<div class="form-group">
										<label class="control-label">{{ _lang('Recipient Email') }}</label>
										<input type="email" class="form-control" name="recipient_email">
									</div>

									<div class="form-group">
										<label class="control-label">{{ _lang('Message') }}</label>
										<textarea class="form-control" name="message"></textarea>
									</div>

									<div class="form-group">
										<button type="submit" class="btn btn-primary btn-block"><i class="far fa-paper-plane mr-2"></i>{{ _lang('Send Test Email') }}</button>
									</div>	
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.js') }}"></script>
<script>
(function ($) {
	"use strict";	
	if($('.colorpicker').length){
		$('.colorpicker').colorpicker();
	}
})(jQuery);	
</script>
@endsection


