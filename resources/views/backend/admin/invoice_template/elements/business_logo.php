<?php

function option_fields(){
	$options = array(
		'company_logo_width' => array(
			'label' => _lang('Logo Witdh'),
			'type'  => 'text',
			'value' => '100px',
			'required' => true,
			'change'   => array(
			               'class'   => '.element-logo',
						   'action'  => 'css_width',
						),
		),
		'company_logo_custom_class' => array(
			'label' => _lang('Custom Class'),
			'type'  => 'text',
			'value' => 'wp-100',
			'required' => false,
			'change'   => array(
			               'class'   => '.element-logo',
						   'action'  => 'addClass',
						),
		),
	);
	
	return $options;
}

function element(){
	return '<div class="d-inline-block">
		<i class="far fa-trash-alt"></i>
		<i class="far fa-edit"></i>
		<i class="fas fa-clone"></i>
		<img src="'. asset('public/uploads/media/' . request()->activeBusiness->logo) .'" class="element-logo wp-100">
	</div>';
}

