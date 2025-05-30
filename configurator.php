<?php
/**
 *  configurator
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_59089a20ba7d8',
			'title'                 => 'Конфигуратор',
			'fields'                => array(
				array(
					'key'               => 'field_59089dd8d3865',
					'label'             => 'Вставка код',
					'name'              => '',
					'type'              => 'tab',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'placement'         => 'top',
					'endpoint'          => 0,
				),
				array(
					'key'               => 'field_5908a002e1847',
					'label'             => 'HEAD код для всех страниц',
					'name'              => 'head_code',
					'type'              => 'textarea', // acf_code_field
					'mode'              => 'htmlmixed',
					'theme'             => 'elegant',
					'value'             => null,
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength'         => '',
					'rows'              => 15,
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_5908a002e18471',
					'label'             => 'BODY код для всех страниц',
					'name'              => 'body_code',
					'type'              => 'textarea', // acf_code_field
					'mode'              => 'htmlmixed',
					'theme'             => 'elegant',
					'value'             => null,
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength'         => '',
					'rows'              => 15,
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_5908a072e1848',
					'label'             => 'FOOTER код для всех страниц',
					'name'              => 'footer_code',
					'type'              => 'textarea', // acf_code_field
					'mode'              => 'htmlmixed',
					'theme'             => 'elegant',
					'value'             => null,
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength'         => '',
					'rows'              => 15,
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_5910b0a008c0b',
					'label'             => 'Информация',
					'name'              => '',
					'type'              => 'tab',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'placement'         => 'top',
					'endpoint'          => 0,
				),
				array(
					'key'               => 'field_5b6a912d38d5c',
					'label'             => 'Информация о сайте',
					'name'              => '',
					'type'              => 'message',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Версия PHP: <b>' . phpversion() . '</b>',
					'new_lines'         => 'wpautop',
					'esc_html'          => 0,
				),
			),

			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'config',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
		),
	);

endif;
