@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/plugins/jquery-ui/jquery-ui.min.css') }}">
<style type="text/css" id="custom-css-code">
</style>
		
<div class="row">
	<div class="col-lg-9">
		<div class="alert alert-info">
			<strong><i class="ti-info-alt mr-2"></i>{{ _lang('You need basic HTML and CSS knowledge for creating/updating invoice template') }}</strong>
		</div>
		<div class="card">
			<div class="card-header d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('New Invoice Template') }}</span>
				<div>
					<button id="undo-btn" class="btn btn-primary btn-xs"><i class="fas fa-undo"></i> {{ _lang('Undo ') }}</button>
					<button id="redo-btn" class="btn btn-info btn-xs"><i class="fas fa-redo"></i> {{ _lang('Redo') }}</button>
					<button id="btn-preview" class="btn btn-dark btn-xs"><i class="fas fa-eye"></i> {{ _lang('Preview Mode') }}</button>
					<button id="btn-editor" class="btn btn-primary btn-xs d-none"><i class="fas fa-sliders-h"></i> {{ _lang('Editor Mode') }}</button>	
				</div>
			</div>
			<div class="card-body">
			  	<form method="post" autocomplete="off" id="action" action="{{ route('invoice_templates.store') }}" enctype="multipart/form-data">
					@csrf
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Template Name') }}</label>						
								<input type="text" class="form-control" name="name" id="template_name" value="{{ old('name') }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Template Type') }}</label>						
								<select class="form-control auto-select" name="template_type" id="template_type" data-selected="{{ old('type', 'invoice') }}" required>
									<option value="invoice">{{ _lang('Invoice') }}</option>
									<option value="quotation">{{ _lang('Quotation') }}</option>
								</select>
							</div>
						</div>
						
						<div class="col-md-12">
							<div id="invoice-canvas" class="dot-element">
							</div>
						</div>
	
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" id="store_invoice_template" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Template') }}</button>
							</div>
						</div>
					</div>			
			  	</form>
			</div>
	    </div>

		<div class="card mt-4">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Custom CSS') }}</span>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">					
							<textarea class="form-control" rows="8" name="custom_css" id="custom-css">{{ old('custom_css') }}</textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    
    <div class="col-lg-3">    
		<div class="card sticky-card">
			<div class="card-body">
				<ul class="nav nav-tabs">
					<li class="nav-item flex-grow-1 text-center">
						<a class="nav-link active" data-toggle="tab" href="#components">{{ _lang('UI Element') }}</a>
					</li>
					<li class="nav-item flex-grow-1 text-center">
						<a class="nav-link" data-toggle="tab" href="#data">{{ _lang('Invoice Data') }}</a>
					</li>
				</ul>
				
				<div class="tab-content">	
					<div class="tab-pane active" id="components">			
			
					  <ul class="custom-invoice-element mt-4">
					     <li data-element="box">{{ _lang('Box') }}</li>
					     <li data-element="box_2">{{ _lang('Box 1 / 2') }}</li>
					     <li data-element="box_3">{{ _lang('Box 1 / 3') }}</li>
					     <li data-element="box_4">{{ _lang('Box 1 / 4') }}</li>
						 <li data-element="row">{{ _lang('BS4 Row') }}</li>
						 <li data-element="column">{{ _lang('BS4 Column') }}</li>
						 <li data-element="business_logo">{{ _lang('Business Logo') }}</li>
						 <li data-element="heading">{{ _lang('Heading') }}</li>
						 <li data-element="paragraph">{{ _lang('Paragraph') }}</li>
						 <li data-element="span">{{ _lang('Span') }}</li>
						 <li class="invocie-field" data-element="invoice_item_table">{{ _lang('Invoice Item Table') }}</li>
						 <li class="invocie-field" data-element="invoice_summary_table">{{ _lang('Invoice Summary Table') }}</li>
						 <li class="invocie-field" data-element="payment_history_table">{{ _lang('Payment History Table') }}</li>
						 
						 <li class="quotation-field d-none" data-element="quotation_item_table">{{ _lang('Quotation Item Table') }}</li>
						 <li class="quotation-field d-none" data-element="quotation_summary_table">{{ _lang('Quotation Summary Table') }}</li>
						 <li data-element="raw_html">{{ _lang('Raw HTML') }}</li>
					  </ul>
					</div>
					
					<div class="tab-pane" id="data">
						<ul class="custom-invoice-element mt-4">					
							<li class="invocie-field" data-clipboard-text="$invoice_title" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Title') }}</li>
							<li class="quotation-field d-none" data-clipboard-text="$quotation_title" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Title') }}</li>
							<li data-clipboard-text="$business_name" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Name') }}</li>
							<li data-clipboard-text="$business_address" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Address') }}</li>
							<li data-clipboard-text="$business_phone" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Phone') }}</li>
							<li data-clipboard-text="$business_email" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Email') }}</li>
							<li data-clipboard-text="$business_country" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Country') }}</li>
							<li data-clipboard-text="$business_vat_id" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business VAT ID') }}</li>
							<li data-clipboard-text="$business_reg_no" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Business Reg No') }}</li>

							<li data-clipboard-text="$customer_name" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Name') }}</li>
							<li data-clipboard-text="$customer_email" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Email') }}</li>
							<li data-clipboard-text="$customer_mobile" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Mobile') }}</li>
							<li data-clipboard-text="$customer_company_name" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Company') }}</li>
							<li data-clipboard-text="$customer_vat_id" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer VAT ID') }}</li>
							<li data-clipboard-text="$customer_reg_no" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Reg NO') }}</li>
							<li data-clipboard-text="$customer_city" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer City') }}</li>
							<li data-clipboard-text="$customer_state" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer State') }}</li>
							<li data-clipboard-text="$customer_zip" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer ZIP') }}</li>
							<li data-clipboard-text="$customer_address" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Address') }}</li>
							<li data-clipboard-text="$customer_country" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Customer Country') }}</li>
							
							<li class="invocie-field" data-clipboard-text="$invoice_number" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Number') }}</li>
							<li class="invocie-field" data-clipboard-text="$sales_order_no" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Sales Order No') }}</li>
							<li class="invocie-field" data-clipboard-text="$invoice_date" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Date') }}</li>
							<li class="invocie-field" data-clipboard-text="$invoice_due_date" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Due Date') }}</li>	
							<li class="invocie-field" data-clipboard-text="$status" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Status') }}</li>
							
							<li class="quotation-field d-none" data-clipboard-text="$quotation_number" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Number') }}</li>
							<li class="quotation-field d-none" data-clipboard-text="$po_so_number" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('PO / SO Number') }}</li>
							<li class="quotation-field d-none" data-clipboard-text="$quotation_date" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Date') }}</li>
							<li class="quotation-field d-none" data-clipboard-text="$quotation_expired_date" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Due Date') }}</li>	
							
							<li data-clipboard-text="$sub_total" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Sub Total') }}</li>
							<li data-clipboard-text="$discount" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Discount') }}</li>
							<li data-clipboard-text="$tax_loop" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Tax @loop') }}</li>
							<li data-clipboard-text="$grand_total" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Grand Total') }}</li>

							<li class="invocie-field" data-clipboard-text="$total_paid" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Total Paid') }}</li>			
							<li class="invocie-field" data-clipboard-text="$amount_due" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Amount Due') }}</li>

							<li class="invocie-field" data-clipboard-text="$invoice_note" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Note') }}</li>
							<li class="invocie-field" data-clipboard-text="$invoice_footer_details" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Invoice Footer Details') }}</li>

							<li class="quotation-field d-none" data-clipboard-text="$quotation_note" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Note') }}</li>
							<li class="quotation-field d-none" data-clipboard-text="$quotation_footer_details" data-toggle="tooltip" title="{{ _lang('Click to Copy') }}">{{ _lang('Quotation Footer Details') }}</li>
						</ul>
			        </div>
			
				</div><!--End TAB-->
			</div>
		</div>
    </div>	

</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/clipboard.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/invoice-template.js') }}"></script>
@endsection


