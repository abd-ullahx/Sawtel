<?php

function option_fields() {
    $options = array(
        'raw_html_text'         => array(
            'label'    => _lang('HTML Code'),
            'type'     => 'html',
            'value'    => '<p>Write Your Code</p>',
            'column'   => 'col-md-12',
            'required' => true,
            'change'   => array(
                'class'  => '.raw-html',
                'action' => 'html',
            ),
        ),
        'raw_html_custom_class' => array(
            'label'    => _lang('Custom Class'),
            'type'     => 'text',
            'value'    => '',
            'column'   => 'col-md-12',
            'required' => false,
            'change'   => array(
                'class'  => '.raw-html',
                'action' => 'addClass',
            ),
        ),

    );

    return $options;
}

function element() {
    return '<div class="element-raw-html">
                <i class="far fa-trash-alt"></i>
                <i class="far fa-edit"></i>
                <i class="fas fa-clone"></i>
				<div class="raw-html">Write Your HTML Code</div>
			</div>';
}
