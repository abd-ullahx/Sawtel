<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('products.store') }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-3">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Item Code') }}</label>
				<input type="text" class="form-control" name="item_code" value="{{ old('item_code') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Bar Code') }}</label>
				<input type="text" class="form-control" name="bar_code" id="bar_code"
					   value="{{ old('bar_code') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Type') }}</label>						
				<select class="form-control auto-select" data-selected="{{ old('type', 'product') }}" name="type" required>
					<option value="product">{{ _lang('Product') }}</option>
					<option value="service">{{ _lang('Service') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Product Unit') }}</label>						
				<select class="form-control select2-ajax" data-selected="{{ old('product_unit_id') }}" name="product_unit_id" data-value="id" data-display="unit" data-table="product_units" data-title="{{ _lang('New Product Unit') }}" data-href="{{ route('product_units.create') }}" data-where="3">
					<option value="">{{ _lang('Select One') }}</option>							
				</select>
			</div>
		</div>

{{--		@if(isset($_GET) && $_GET['type'] == 'sell')--}}
			<input type="hidden" name="allow_for_selling" value="1">
			<input type="hidden" name="allow_for_purchasing" value="1">

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">{{ _lang('Selling Price').' ('.currency_symbol(request()->activeBusiness->currency).')' }} <span class="required"> *</span></label>						
					<input type="text" class="form-control float-field no-msg" name="selling_price" value="{{ old('selling_price') }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">{{ _lang('Income Category') }} <span class="required"> *</span></label>						
					<select class="form-control select2-ajax no-msg" data-selected="{{ old('income_category_id') }}" name="income_category_id" data-value="id" data-display="name" data-table="transaction_categories" data-title="{{ _lang('New Category') }}" data-href="{{ route('transaction_categories.create') }}?type=income" data-where="4">
						<option value="">{{ _lang('Select One') }}</option>								
					</select>
				</div>
			</div>

{{--		@else--}}
{{--			<input type="hidden" name="allow_for_purchasing" value="1">--}}
{{--			<input type="hidden" name="allow_for_selling" value="0">--}}

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">{{ _lang('Purchase Cost').' ('.currency_symbol(request()->activeBusiness->currency).')' }} <span class="required"> *</span></label>						
					<input type="text" class="form-control float-field no-msg" name="purchase_cost" value="{{ old('purchase_cost') }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">{{ _lang('Expense Category') }} <span class="required"> *</span></label>						
					<select class="form-control select2-ajax no-msg" data-selected="{{ old('expense_category_id') }}" name="expense_category_id" data-value="id" data-display="name" data-table="transaction_categories" data-title="{{ _lang('New Category') }}" data-href="{{ route('transaction_categories.create') }}?type=expense" data-where="5">
						<option value="">{{ _lang('Select One') }}</option>								
					</select>
				</div>
			</div>
{{--		@endif--}}

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Image') }}</label>						
				<input type="file" class="form-control dropify" name="image">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Descriptions') }}</label>						
				<textarea class="form-control" name="descriptions">{{ old('descriptions') }}</textarea>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label">{{ _lang('Status') }}</label>						
				<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status" required>
					<option value="1">{{ _lang('Active') }}</option>
					<option value="0">{{ _lang('Disabled') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label">{{ _lang('Stock Management') }}</label>	
				<select class="form-control auto-select" data-selected="{{ old('stock_management', 0) }}" name="stock_management" required>
					<option value="1">{{ _lang('Yes') }}</option>
					<option value="0">{{ _lang('No') }}</option>
				</select>
				<small class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ _lang('Works for product only!') }}</small>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label">{{ _lang('Initial Stock') }}</label>						
				<input type="text" class="form-control" name="stock" value="{{ old('stock', 0) }}" required>
				<small class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>{{ _lang('Works for product only!') }}</small>
			</div>
		</div>
			
		<div class="col-md-12 mt-2">
			<div class="form-group">
				<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-1"></i>{{ _lang('Save Changes') }}</button>
			</div>
		</div>
	</div>
</form>


