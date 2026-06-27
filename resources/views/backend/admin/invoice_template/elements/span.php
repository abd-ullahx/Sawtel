<?php

function option_fields() {
    $options = array(
        'span_text'         => array(
            'label'    => _lang('Span Text'),
            'type'     => 'text',
            'value'    => 'Span text',
            'required' => true,
            'change'   => array(
                'class'  => '.span',
                'action' => 'html',
            ),
        ),
        'span_text_color'   => array(
            'label'    => _lang('Text Color'),
            'type'     => 'text',
            'value'    => '#000',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '.span',
                'action' => 'css_color',
            ),
        ),
        'span_display_type' => array(
            'label'    => _lang('Display'),
            'type'     => 'select',
            'options'  => array(
                'd-block'        => _lang('Block'),
                'd-inline-block' => _lang('Inline block'),
                'd-inline'       => _lang('Inline'),
            ),
            'value'    => 'd-inline-block',
            'column'   => 'col-md-6',
            'required' => true,
            'change'   => array(
                'class'  => '.span',
                'action' => 'addClass',
            ),
        ),
        'span_text_align'   => array(
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
            'change'   => array(
                'class'  => '.span',
                'action' => 'css_text-align',
            ),
        ),
        'span_custom_class' => array(
            'label'    => _lang('Custom Class'),
            'type'     => 'text',
            'value'    => '',
            'column'   => 'col-md-6',
            'required' => false,
            'change'   => array(
                'class'  => '.span',
                'action' => 'addClass',
            ),
        ),

    );

    return $options;
}

function element() {
    return '<div class="element-span">
                <i class="far fa-trash-alt"></i>
                <i class="far fa-edit"></i>
                <i class="fas fa-clone"></i>
				<span class="span d-inline-block">Span text</span>
			</div>';
}
