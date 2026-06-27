<?php

function option_fields(){
	$options = array(
		'invoice_summary_table_background' => array(
			'label' 	=> _lang("Table Background"),
			'type'  	=> 'text',
			'value' 	=> '#FFFFF',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.table',
						   'action'  => 'css_background-color',
						),
		),
		'invoice_summary_table_text_color' => array(
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
		'invoice_summary_table_style' => array(
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
		'invoice_summary_table_custom_class' => array(
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
	return '<div class="element-invoice-summary-table">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
				<div class="table-responsive">
					<table class="table">
						<tbody>
								<tr>
									<td>'. _lang('Sub Total') .'</td>
									<td class="text-right">
										<span>$ 2,198.00</span>
									</td>
								</tr>
								<tr>
									<td>'. _lang('Taxes') .'</td>
									<td class="text-right">
										<span>$ 0.00</span>
									</td>
								</tr>
								<tr>
									<td>'. _lang('Discount') .'</td>
									<td class="text-right">
										<span>$ 0.00</span>
									</td>
								</tr>
								<tr>
									<td><b>'. _lang('Grand Total') .'</b></td>
									<td class="text-right">
										<b>$ 2,198.00</b>
									</td>
								</tr>
								<tr>
									<td>'. _lang('Amount Due') .'</td>
									<td class="text-right">
										<span>$ 2,198.00</span>
									</td>
								</tr>
						</tbody>
					</table>
				</div>
			</div>';
}

