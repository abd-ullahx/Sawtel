<?php

function option_fields(){
	$options = array(
		'heading_text' => array(
			'label' => _lang('Heading Text'),
			'type'  => 'text',
			'value' => 'Heading 1',
			'required' => true,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'html',
						),
		),
		'heading_style' => array(
			'label'    => _lang('Font Size'),
			'type'     => 'text',
			'value'    => '2.5rem',
			'column'   => 'col-md-6',
			'required' => true,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'css_font-size',
						),
		),
		'heading_text_color' => array(
			'label'     => _lang('Text Color'),
			'type'      => 'text',
			'value'     => '#000',
			'column'    => 'col-md-6',
			'required'  => true,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'css_color',
						),
		),
		'heading_text_align' => array(
			'label'    => _lang('Text Align'),
			'type'     => 'select',
			'options'  => array(
			                'left'   => _lang('Left'),
							'center' => _lang('Center'),
							'right'  => _lang('Right'),
						),
			'value'    => 'left',
			'column'   => 'col-md-6',
			'required' => true,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'css_text-align',
						),
		),
		'heading_display_type' => array(
			'label'    => _lang('Display'),
			'type'     => 'select',
			'options'  => array(
			                'd-block'   => _lang('Block'),
							'd-inline-block' => _lang('Inline block'),
							'd-inline'  => _lang('Inline'),
						),
			'value'    => 'd-block',
			'column'   => 'col-md-6',
			'required' => true,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'addClass',
						),
		),
		'heading_font_weight' => array(
            'label'    => _lang('Font Weight'),
            'type'     => 'select',
            'options'  => array(
                '400' => '400',
                '500' => '500',
                '600' => '600',
                '700' => '700',
            ),
            'value'    => '500',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '.heading',
                'action' => 'css_font-weight',
            ),
        ),
		'heading_custom_class' => array(
			'label'     => _lang('Custom Class'),
			'type'      => 'text',
			'value'     => 'heading',
			'column'    => 'col-md-6',
			'required'  => false,
			'change'    => array(
			               'class'   => '.heading',
						   'action'  => 'addClass',
						),
		),

	);
	
	return $options;
}

function element(){
	return '<div class="element-heading">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
				<h1 class="heading">Heading 1</h1>
			</div>';
}

