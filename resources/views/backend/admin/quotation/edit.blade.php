@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('quotations.update', $id) }}" enctype="multipart/form-data">
	@csrf
	<input name="_method" type="hidden" value="PATCH">

	<div class="row">
		<div class="col-xl-9 col-lg-8">
			<div class="card invoice-form">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4">
							<div class="invoice-logo">
								<img src="{{ asset('public/uploads/media/' . request()->activeBusiness->logo) }}" alt="logo">
							</div>
						</div>

						<div class="col-lg-4 offset-lg-4">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<input type="text" class="form-control form-control-lg" name="title" value="{{ $quotation->title }}" placeholder="{{ _lang('Invoice Title') }}" required>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<input type="text" class="form-control" name="quotation_number" value="{{ $quotation->quotation_number }}" placeholder="{{ _lang('Invoice Number') }}" required>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<input type="text" class="form-control" name="po_so_number" value="{{ $quotation->po_so_number }}" placeholder="{{ _lang('PO / SO Number') }}">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row my-4">
						<div class="col-12">
							<div class="divider"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4">
							<div class="form-group select-customer">
								<select class="form-control auto-select" data-selected="{{ $quotation->customer_id }}" name="customer_id" data-value="id" data-display="name"
										data-href="{{ route('clients.create') }}" data-title="{{ _lang('Create New Client') }}" data-table="customers"
										data-where="3" data-placeholder="{{ _lang('Choose Customer') }}" required>
									<option value="">{{ _lang('Select Customer') }}</option>
									@foreach(\App\Models\Customer::where('id', $quotation->customer_id)->get() as $customer)
									<option value="{{ $customer->id }}">{{ $customer->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6 offset-lg-2">
							<div class="form-group row">
								<label class="col-xl-4 col-form-label">{{ _lang('Quotation Date') }}</label>
								<div class="col-xl-8">
									<input type="text" class="form-control datepicker" name="quotation_date" value="{{ $quotation->getRawOriginal('quotation_date') }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-4 col-form-label">{{ _lang('Expired Date') }}</label>
								<div class="col-xl-8">
									<input type="text" class="form-control datepicker" name="expired_date" value="{{ $quotation->getRawOriginal('expired_date') }}" required>
								</div>
							</div>
						</div>
					</div>

					<div class="row mt-2">
						<div class="col-12">
							<div class="form-group">
								<select class="form-control select2-ajax" id="products" data-type="sell" data-value="id" data-display="name"
									data-href="{{ route('products.create') }}?type=sell" data-title="{{ _lang('Add New Item') }}"
									data-placeholder="{{ _lang('Select an Item') }}" data-modal="ajax-modal" data-table="products" data-where="6">
								</select>
							</div>
						</div>
					</div>

					<div class="row mt-2">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table" id="invoice-table">
									<thead>
										<tr>
											<th class="input-lg">{{ _lang('Name') }}</th>
											<th class="input-md">{{ _lang('Item Taxes') }}</th>
											<th class="input-xs text-center">{{ _lang('Quantity') }}</th>
											<th class="input-sm text-right">{{ _lang('Price') }}</th>
											<th class="input-sm text-right">{{ _lang('Amount') }}</th>
											<th class="text-center"><i class="fas fa-minus-circle"></i></th>
										</tr>
									</thead>
									<tbody>
										@foreach($quotation->items as $item)
										<tr class="line-item">
											<td class="input-lg">
												<input type="hidden" class="product_id" name="product_id[]" value="{{ $item->product_id }}">
												<input type="text" class="form-control product_name" name="product_name[]" value="{{ $item->product_name }}"><br>
												<textarea class="form-control description" name="description[]" placeholder="{{ _lang('Descriptions') }}">{{ $item->description }}</textarea>
											</td>
											<td class="input-md">
												<select name="taxes[{{ $item->product_id }}][]" class="multi-selector product_taxes auto-multiple-select" data-selected="{{ $item->taxes->pluck('tax_id') }}" data-placeholder="{{ _lang('Select Taxes') }}" multiple>
													@foreach(\App\Models\Tax::all() as $tax)
													<option value="{{ $tax->id }}" data-tax-rate="{{ $tax->rate }}" data-tax-name="{{ $tax->name }} {{ $tax->rate }} %">{{ $tax->name }} {{ $tax->rate }} %</option>
													@endforeach
												</select>
											</td>
											<td class="input-xs text-center"><input type="number" class="form-control quantity" name="quantity[]" value="{{ $item->quantity }}" min="1"></td>
											<td class="input-sm"><input type="text" class="form-control text-right unit_cost" name="unit_cost[]" value="{{ $item->unit_cost }}"></td>
											<td class="input-sm"><input type="text" class="form-control text-right sub_total" name="sub_total[]" value="{{ $item->sub_total }}" readonly></td>
											<td class="input-xxs text-center"><button type="button" class="btn btn-outline-danger btn-xs mt-1 btn-remove-row"><i class="fas fa-minus-circle"></i></button></td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="row my-4">
						<div class="col-12">
							<div class="divider"></div>
						</div>
					</div>

					<div class="row text-md-right">
						<div class="col-xl-6 offset-xl-6">
							<div class="form-group row" id="before-tax">
								<label class="col-md-6 col-form-label">{{ _lang('Sub Total') }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right" name="sub_total" id="sub_total" value="{{ $quotation->sub_total }}" readonly>
								</div>
							</div>

							<!-- Taxes -->
							@foreach($quotation->taxes as $tax)
							<div class="form-group row tax-field">
								<label class="col-md-6 col-form-label">{{ $tax->name }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right tax-input-field" name="tax_amount[{{ $tax->tax_id }}]" id="tax-{{ $tax->tax_id }}" value="{{ $tax->amount }}" readonly>
								</div>
							</div>
							@endforeach

							<div class="form-group row" id="after-tax">
								<label class="col-md-6 col-form-label">{{ _lang('Discount Amount') }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right" name="discount" id="discount" value="{{ $quotation->discount }}" readonly>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-6 col-form-label">{{ _lang('Grand Total') }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right" name="grand_total" id="grand_total" value="{{ $quotation->grand_total }}" readonly>
								</div>
							</div>
						</div>
					</div>

					<div class="row my-4">
						<div class="col-12">
							<div class="divider"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Notes') }}</label>
								<textarea class="form-control" name="note">{{ $quotation->note }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Footer') }}</label>
								<textarea class="form-control" name="footer">{{ $quotation->footer }}</textarea>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-1"></i>{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-lg-4">
			<div class="card sticky-card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Quotation Template') }}</label>
								<select class="form-control auto-select" data-selected="{{ $quotation->template }}" name="template" required>
									<option value="default">{{ _lang('Default') }}</option>
									@foreach(\App\Models\InvoiceTemplate::where('type', 'quotation')->get() as $template)
									<option value="{{ $template->id }}">{{ $template->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Discount Value') }}</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<select class="form-control discount_type" id="discount_type" name="discount_type" value="{{ $quotation->discount_type }}">
											<option value="0">%</option>
											<option value="1">{{ currency_symbol(request()->activeBusiness->currency) }}</option>
										</select>
									</div>
									<input type="number" class="form-control" name="discount_value" id="discount_value" min="0" value="{{ $quotation->discount_value }}">
								</div>
							</div>
						</div>

						<div class="col">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box mr-1"></i>{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>


<table class="d-none">
<tr class="line-item" id="copy-line">
	<td class="input-lg">
		<input type="hidden" class="product_id" name="product_id[]">
		<input type="text" class="form-control product_name" name="product_name[]"><br>
		<textarea class="form-control description" name="description[]" placeholder="{{ _lang('Descriptions') }}"></textarea>
	</td>
	<td class="input-md">
		<select name="taxes[][]" class="multi-selector product_taxes" data-placeholder="{{ _lang('Select Taxes') }}" multiple>
			@foreach(\App\Models\Tax::all() as $tax)
			<option value="{{ $tax->id }}" data-tax-rate="{{ $tax->rate }}" data-tax-name="{{ $tax->name }} {{ $tax->rate }} %">{{ $tax->name }} {{ $tax->rate }} %</option>
			@endforeach
		</select>
	</td>
	<td class="input-xs text-center"><input type="number" class="form-control quantity" name="quantity[]" min="1"></td>
	<td class="input-sm"><input type="text" class="form-control text-right unit_cost" name="unit_cost[]"></td>
	<td class="input-sm"><input type="text" class="form-control text-right sub_total" name="sub_total[]" readonly></td>
	<td class="input-xxs text-center"><button type="button" class="btn btn-outline-danger btn-xs mt-1 btn-remove-row"><i class="fas fa-minus-circle"></i></button></td>
</tr>
</table>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/invoice.js?v=1.2') }}"></script>
@endsection