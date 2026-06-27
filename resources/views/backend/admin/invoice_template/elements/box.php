<?php

function option_fields() {
    $options = array(
        'box_background'   => array(
            'label'    => _lang('Background Color'),
            'type'     => 'text',
            'value'    => 'transparent',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_background',
            ),
        ),
        'box_text_color'   => array(
            'label'    => _lang('Text Color'),
            'type'     => 'text',
            'value'    => '#000',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_color',
            ),
        ),
        'box_border'       => array(
            'label'    => _lang('Border'),
            'type'     => 'text',
            'value'    => '0px solid',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_border',
            ),
        ),
        'box_border_color' => array(
            'label'    => _lang('Border Color'),
            'type'     => 'text',
            'value'    => '#000',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_border-color',
            ),
        ),
        'box_width'       => array(
            'label'    => _lang('Width'),
            'type'     => 'text',
            'value'    => '100%',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_width',
            ),
        ),
		'box_height'       => array(
            'label'    => _lang('Height'),
            'type'     => 'text',
            'value'    => '50px',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '',
                'action' => 'css_height',
            ),
        ),
        'box_float' => array(
			'label'    => _lang('Float'),
			'type'     => 'select',
			'options'  => array(
			                'none'   => _lang('None'),
							'left' => _lang('Left'),
							'right'  => _lang('Right'),
						),
			'value'    => 'none',
			'column'   => 'col-md-6',
			'required' => true,
			'change'    => array(
			               'class'   => '',
						   'action'  => 'css_float',
						),
		),
        'box_custom_class' => array(
            'label'    => _lang('Custom Class'),
            'type'     => 'text',
            'value'    => '',
            'column'   => 'col-md-6',
            'required' => false,
            'change'   => array(
                'class'  => '',
                'action' => 'addClass',
            ),
        ),
    );

    return $options;
}

function element() {
    return '<div class="element-box dot-element" data-sort="true">
				<i class="far fa-trash-alt"></i>
				<i class="far fa-edit"></i>
				<i class="fas fa-clone"></i>
			</div>';
}
