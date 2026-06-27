@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('quotations.store') }}" enctype="multipart/form-data">
	@csrf
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
										<input type="text" class="form-control form-control-lg" name="title" value="{{ get_business_option('quotation_title', 'Quotation') }}" placeholder="{{ _lang('Quotation Title') }}" required>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<input type="text" class="form-control" name="quotation_number" value="{{ get_business_option('quotation_number', old('quotation_number')) }}" placeholder="{{ _lang('Quotation Number') }}" required>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<input type="text" class="form-control" name="po_so_number" value="{{ old('po_so_number') }}" placeholder="{{ _lang('PO / SO Number') }}">
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
								<select class="form-control" data-selected="{{ old('customer_id') }}" name="customer_id" data-value="id" data-display="name"
										data-href="{{ route('clients.create') }}" data-title="{{ _lang('Create New Client') }}" data-table="customers"
										data-where="3" data-placeholder="{{ _lang('Choose Customer') }}" required>
									<option value="">{{ _lang('Select Customer') }}</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6 offset-lg-2">
							<div class="form-group row">
								<label class="col-xl-4 col-form-label">{{ _lang('Quotation Date') }}</label>
								<div class="col-xl-8">
									<input type="text" class="form-control datepicker" name="quotation_date" value="{{ old('quotation_date') }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-4 col-form-label">{{ _lang('Expired Date') }}</label>
								<div class="col-xl-8">
									<input type="text" class="form-control datepicker" name="expired_date" value="{{ old('expired_date') }}" required>
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
										<!-- Invoice Items -->
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
									<input type="text" class="form-control text-md-right" name="sub_total" id="sub_total" value="{{ old('sub_total') }}" readonly>
								</div>
							</div>

							<div class="form-group row" id="after-tax">
								<label class="col-md-6 col-form-label">{{ _lang('Discount Amount') }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right" name="discount" id="discount" value="{{ old('discount') }}" readonly>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-6 col-form-label">{{ _lang('Grand Total') }}</label>
								<div class="col-md-6">
									<input type="text" class="form-control text-md-right" name="grand_total" id="grand_total" value="{{ old('grand_total') }}" readonly>
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
								<textarea class="form-control" name="note">{{ old('note') }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Footer') }}</label>
								<textarea class="form-control" name="footer">{!! xss_clean(get_business_option('quotation_footer', old('footer'))) !!}</textarea>
							</div>
						</div>

						<div class="col-md-12 mt-4">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-1"></i>{{ _lang('Save Quotation') }}</button>
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
								<select class="form-control auto-select" data-selected="{{ old('template', 'default') }}" name="template" required>
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
										<select class="form-control discount_type" id="discount_type" name="discount_type" value="{{ old('discount_type') }}">
											<option value="0">%</option>
											<option value="1">{{ currency_symbol(request()->activeBusiness->currency) }}</option>
										</select>
									</div>
									<input type="number" class="form-control" name="discount_value" id="discount_value" min="0" value="{{ old('discount_value',0) }}">
								</div>
							</div>
						</div>

						<div class="col">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box mr-1"></i>{{ _lang('Save Quotation') }}</button>
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


