<?php

/*
 * Plugin Name:  Field in Woocommerce checkout
 * Description:  добавляет кастомное поле в чекаут
 * Version: 1.0
 * Text Domain: field-in-checkout
 * Domain Path: /lang/
 * Author: TechnoPreacher
 * License: GPLv2 or later
 * Requires at least: 5.0
 * Requires PHP: 7.4
*/

//add_action( 'plugins_loaded', 'field_in_checkout_loaded' );//подключаем регистратор
add_action( 'save_post_shop_order', 'field_in_checkout_update', 0 );//обновление полей при сохранении заказа(!) Вукомерс
add_filter( 'manage_edit-shop_order_columns', 'customsFields' );//как можно, находясь в здравом уме, это узнать, если post_type=shop_order????
add_action( 'manage_posts_custom_column', 'customsFieldsDataOutput', 10, 2 );//а тут, надо же, стандартно!
add_action( 'wp_enqueue_scripts', 'add_scripts' );//хук для подключения скрипта

//add_action( 'woocommerce_email_header', 'mm_email_header', 10, 2 );//так можно в хидер
//add_action( 'woocommerce_email_footer', 'insert_custom_value_in_email', 10, 1 );//так можно в футер
add_action( 'woocommerce_email_after_order_table', 'insert_custom_value_in_email', 10, 4 );//ну а так после таблицы

//register_deactivation_hook( __FILE__, 'field_in_checkout_deactivate' );//убираю всё что сделал плагин

function field_in_checkout_update( $post_id )//Сохрание маета-данных, при сохранении поста
{
// базовая проверка
	if (//тут не помешает проверка правильности отправителя данных
		empty( $_POST['field_in_checkout_metadata'] )
		|| wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
	) {
		return false;
	}

	$_POST['field_in_checkout_metadata'] = sanitize_text_field( $_POST['field_in_checkout_metadata'] );

	update_post_meta( $post_id, 'field_in_checkout_metadata',
		$_POST['field_in_checkout_metadata'] ); // add_post_meta() работает автоматически

	return $post_id;
}

function customsFields( $columns ) {//вывод значений мета-полей в общем списке в админке!
	$my_columns = [//идентификатор колонки
		'field_in_checkout_metadata' => '<p style=\'border: 3px solid pink\'>' .
		            __( 'Костюмное поле', 'field-in-checkout' ) . '</p>',
	];
	return $my_columns + $columns;//кастомная колонка впереди
}

//данные! - нужно проверить есть ли они
function customsFieldsDataOutput( $column ) {

	$custom_fields = get_post_custom();//мета-данные поста

	$my_custom_field = $custom_fields['field_in_checkout_metadata'][0] ?? null;//если есть - ок, нет - пустота
	$strstyle        = '';
	if ( isset( $my_custom_field ) ) {
		$strstyle = '<p style=\'border: 2px solid deeppink\'>';
	}
	switch ( $column ) {//выбираю в какой столбец админки сунуть данные!
		case 'field_in_checkout_metadata' ://имя колонки - задаётся при формировании её заголовка в customsFields($columns) !!!
			echo $strstyle . __( $my_custom_field,
					'field-in-checkout' ) . '</p>';// " <p style=\'border: 5px solid red\'>ояние события</p>";
			break;
	}
}

function add_scripts() {
	/*
	отработает при выведении страниц чекаута - сюда инжектировать метаданные дял отображения на фронте!
	это может быть как страница подтверждения заказа, так и страница с инфой о доставке заказа
	в первой случае не будет $wp->query_vars['order-received'] !!
	*/
	if ( is_checkout() ) {

		wp_enqueue_script( 'field-in-checkout-js',
			plugins_url( '/js/field-in-checkout.js', __FILE__ ),//путь к жс-скрипту относительно этого пхп-файла!
			array( 'jquery' )
		);//подлючаю JS-скрипт и говорю что он зависим от JQuery

		global $wp; //чтоб иметь доступ к глобальным данным

		$custom_field = '';

		if ( isset( $wp->query_vars['order-received'] ) ) {
			if ( isset( get_post_custom( $wp->query_vars['order-received'] )['field_in_checkout_metadata'] ) ) {
				$custom_field = get_post_custom( $wp->query_vars['order-received'] )['field_in_checkout_metadata'][0];
			}
		}

		$variables = array(
			'value' => $custom_field, //кастомное значение
			'label'=>__('Метаданные','field-in-checkout'), //переводимы заголовок кастомного поля
		);

		//передача значения переменной  на фронт внутри объекта. доступ в ЖС такой: window.field_in_checkout.field-in-checkout;
		wp_localize_script( 'field-in-checkout-js', 'field_in_checkout', $variables );
	};
}

function insert_custom_value_in_email( $order, $sent_to_admin, $plain_text, $email ) {
	echo '<div  style=\'border: 3px dotted hotpink\'>
                <h4>'. __('Дополнительная информация','field-in-checkout').':'. '</h4> 
                <p>' . $_POST['field_in_checkout_metadata'] . '</p>
          </div>
          <br>';
}

//function field_in_checkout_loaded() {}

//function field_in_checkout_deactivate() {
//	unregister_widget( 'ajax_filter_widget' );//убить виджет}
