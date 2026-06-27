<?php

function option_fields(){
	$options = array(
		'column_width' => array(
			'label'    => _lang('Column Width'),
			'type'     => 'select',
			'options'  => array(
			                'col-12' => _lang('col-12'),
							'col-11' => _lang('col-11'),
							'col-10' => _lang('col-10'),
							'col-9'  => _lang('col-9'),
							'col-8'  => _lang('col-8'),
							'col-7'  => _lang('col-7'),
							'col-6'  => _lang('col-6'),
							'col-5'  => _lang('col-5'),
							'col-4'  => _lang('col-4'),
							'col-3'  => _lang('col-3'),
							'col-2'  => _lang('col-2'),
							'col-1'  => _lang('col-1'),
						),
			'value'    => 'col-12',
			'required' => true,
			'change'   => array(
			               'class'   => '',
						   'action'  => 'addClass',
						),
		),
		'column_custom_class' => array(
			'label'    => _lang('Custom Class'),
			'type'     => 'text',
			'value'    => '',
			'required' => false,
			'change'   => array(
			               'class'   => '',
						   'action'  => 'addClass',
						),
		),
	);
	
	return $options;
}

function element(){
	return '<div class="col-12 dot-element" data-sort="true">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
			</div>';
}

