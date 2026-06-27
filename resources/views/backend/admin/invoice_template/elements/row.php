<?php


function option_fields(){
	$options = array(
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
	return '<div class="row" data-sort="true">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
			</div>';
}


?>



