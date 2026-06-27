<?php

function option_fields(){
	$options = array(
		'invoice_item_table_header_background' => array(
			'label' 	=> _lang("Header Background"),
			'type'  	=> 'text',
			'value' 	=> '#34BCF9',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.base_color',
						   'action'  => 'css_background-color',
						),
		),
		'invoice_item_table_header_color' => array(
			'label' 	=> _lang("Header Color"),
			'type'  	=> 'text',
			'value' 	=> '#FFFFFF',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.base_color',
						   'action'  => 'css_color',
						),
		),
		'invoice_item_table_background' => array(
			'label' 	=> _lang("Table Background"),
			'type'  	=> 'text',
			'value' 	=> '#FFFFFF',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.table',
						   'action'  => 'css_background-color',
						),
		),
		'invoice_item_table_text_color' => array(
			'label' 	=> _lang("Table Text Color"),
			'type'  	=> 'text',
			'value' 	=> '#4a4a4a',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.table',
						   'action'  => 'css_color',
						),
		),
		'invoice_item_table_style' => array(
			'label' 	=> _lang("Table Style"),
			'type'     => 'select',
			'options'  => array(
			                'table-bordered'  => _lang('Table Bordered'),
							'table-striped'   => _lang('Table Striped'),
							'table-condensed' => _lang('Table Condensed'),
							'no-style' 		  => _lang('No Style'),
						),
			'value' 	=> 'no-style',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.table',
						   'action'  => 'addClass',
						),
		),
		'invoice_item_table_custom_class' => array(
			'label'     => _lang("Custom Class"),
			'type'      => 'text',
			'value'     => '',
			'column'    => 'col-md-6',
			'required'  => false,
			'change'    => array(
			               'class'   => '.table',
						   'action'  => 'addClass',
						),
		),

	);
	
	return $options;
}

function element(){
	return '<div class="element-invoice-item-table">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
				
				<div class="table-responsive">
					<table class="table" id="invoice-item-table">
						 <thead class="base_color">
							 <tr>
								 <th>'. _lang("Name") .' </th>
								 <th class="text-center wp-100">'. _lang("Quantity") .' </th>
								 <th class="text-right">'. _lang("Price") .' </th>
								 <th class="text-right">'. _lang("Amount") .' </th>
							 </tr>
						 </thead>
						 <tbody>
							<!--START_INVOICE_LOOP-->
								 <tr>
									 <td>Item 1</td>
									 <td class="text-center wp-100">1</td>
									 <td class="text-right">$ 999.00</td>
									 <td class="text-right">$ 999.00</td>
								 </tr>
								 <tr>
									 <td>Item 2</td>
									 <td class="text-center wp-100">1</td>
									 <td class="text-right">$ 1099.00</td>
									 <td class="text-right">$ 1099.00</td>
								 </tr>
							<!--END_INVOICE_LOOP-->
						 </tbody>
					</table>
				</div>	
			</div>';
}

