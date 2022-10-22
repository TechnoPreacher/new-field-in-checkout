<?php

/*
 * Plugin Name:  Field in Woocommerce checkout
 * Description:  добавляет кастомное поле в чекаут
 * Version: 0.1
 * Text Domain: field-in-checkout
 * Domain Path: /lang/
 * Author: TechnoPreacher
 * License: GPLv2 or later
 * Requires at least: 5.0
 * Requires PHP: 7.4
*/

add_action( 'plugins_loaded', 'field_in_checkout_loaded' );//подключаем переводчик

//add_action( 'widgets_init', 'new-field-in-checkout_widget' );//прикручиваю виджет
//add_action( 'wp_ajax_filter_plugin', 'ajax_filter_posts_query' );//AJAX для своих
//add_action( 'wp_ajax_nopriv_filter_plugin', 'ajax_filter_posts_query' );//AJAX для чужих

register_deactivation_hook( __FILE__, 'field_in_checkout_deactivate' );//убираю всё что сделал плагин

//include_once __DIR__ . '/includes/ajax-filter-widget.php';// виджет



function my_extra_fields_update($post_id)//Сохрание маета-данных, при сохранении поста
{
	// базовая проверка
	if (
		empty($_POST['extra'])
		|| !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__)
		|| wp_is_post_autosave($post_id)
		|| wp_is_post_revision($post_id)
	)
		return false;
	// Все ОК! Теперь, нужно сохранить/удалить данные
	$_POST['extra'] = array_map('sanitize_text_field', $_POST['extra']); // чистим все данные от пробелов по краям
	foreach ($_POST['extra'] as $key => $value) {
		if (empty($value)) {
			delete_post_meta($post_id, $key); // удаляем поле если значение пустое
			continue;
		}
		update_post_meta($post_id, $key, $value); // add_post_meta() работает автоматически
	}
	return $post_id;
}

add_action('save_post', 'my_extra_fields_update', 0); // включаем обновление полей при сохранении
add_action('add_meta_boxes', 'my_extra_fields', 1);//кастомные поля


// подключаем функцию активации мета блока (my_extra_fields) - нужно для вывода кастомных полей на странице "добавить" в админке!
function my_extra_fields()
{
	add_meta_box('extra_fields', __('Поля ивента', 'event-plugin'), 'extra_fields_box_func', 'events', 'normal', 'high');
}

function extra_fields_box_func($post)// код блока (внешний вид на странице добавления события в админке)
{
	?>
	<div style="width:100%;height:100%;border:5px solid orangered;">

		<p> <?php  _e('Статус ивента: ', 'event-plugin'); $mark_v = get_post_meta($post->ID, 'status', 1); ?>
			<label>
				<input type="radio" name="extra[status]"
				       value="open" <?php checked($mark_v, 'open'); ?> /> open
			</label>
			<label>
				<input type="radio" name="extra[status]"
				       value="closed" <?php checked($mark_v, 'closed'); ?> />closed
			</label>
		</p>

		<p> <?php _e('Дата ивента: ', 'event-plugin'); $eventDate = get_post_meta($post->ID, 'eventdate', 1); ?>
			<input type='date' name="extra[eventdate]"
			       value="<?= $eventDate ?>"/>
		</p>

		<input type="hidden" name="extra_fields_nonce"
		       value="<?php echo wp_create_nonce(__FILE__); ?>"/>
	</div>
	<?php
}



 function customsFields($columns) {//вывод значений мета-полей в общем списке в админке!
	$my_columns = [
		'status' => '<p style=\'border: 3px solid red\'>'.__('и_игорь','field-in-checkout').'</p>',
	];
	//array_pop($columns);//удаляю дату создания записи о событии, для меня важнее метадата самого события!
	return $my_columns+$columns;// + $my_columns;
};

//как можно, находясь в здравом уме, это узнать, если post_type=shop_order????
add_filter( 'manage_edit-shop_order_columns', 'customsFields' );




/* сортировка!
add_filter( "manage_edit-shop_order_sortable_columns", 'MY_COLUMNS_SORT_FUNCTION' );
function MY_COLUMNS_SORT_FUNCTION( $columns )
{
	$custom = array(
			'MY_COLUMN_ID_1'    => 'MY_COLUMN_1_POST_META_ID',
			'MY_COLUMN_ID_2'    => 'MY_COLUMN_2_POST_META_ID'
			);
	return wp_parse_args( $custom, $columns );
}
*/
/*
add_filter( 'manage_edit-shop_order_columns', 'MY_COLUMNS_FUNCTION' );
function MY_COLUMNS_FUNCTION( $columns ) {
	$new_columns = ( is_array( $columns ) ) ? $columns : array();
	unset( $new_columns[ 'order_actions' ] );

	//edit this for your column(s)
	//all of your columns will be added before the actions column
	$new_columns['MY_COLUMN_ID_1'] = 'MY_COLUMN_1_TITLE';
	$new_columns['MY_COLUMN_ID_2'] = 'MY_COLUMN_2_TITLE';

	//stop editing
	$new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
	return $new_columns;
}
*/

//данные! - нужно проверить есть ли они
function customsFieldsDataOutput( $column, $post_id ) {
	switch ( $column ) {

		case 'status' :
			echo '<p style=\'border: 1px solid yellow\'>'.__('и_игорь','field-in-checkout').'</p>';// " <p style=\'border: 5px solid red\'>ояние события</p>";
			break;
	}
}
//а тут, надо же, стандартно!
add_action( 'manage_posts_custom_column' , 'customsFieldsDataOutput', 10, 2 );

/* вариант проверки!
add_action( 'manage_shop_order_posts_custom_column', 'MY_COLUMNS_VALUES_FUNCTION', 2 );
function MY_COLUMNS_VALUES_FUNCTION( $column ) {
	global $post;
	$data = get_post_meta( $post->ID );

	//start editing, I was saving my fields for the orders as custom post meta
	//if you did the same, follow this code

	if ( $column == 'MY_COLUMN_ID_1' ) {
		echo ( isset( $data[ 'MY_COLUMN_1_POST_META_ID' ] ) ? $data[ 'MY_COLUMN_1_POST_META_ID' ] : '' );
	}

	if ( $column == 'MY_COLUMN_ID_2' ) {
		echo ( isset( $data[ 'MY_COLUMN_2_POST_META_ID' ] ) ? $data[ 'MY_COLUMN_2_POST_META_ID' ] : '' );
	}
}
*/


/* что-то на умном
if ( get_option( 'orddd_show_column_on_orders_page_check' ) == 'on' ) {
    add_filter( 'manage_edit-shop_order_columns', array( 'orddd_filter', 'orddd_woocommerce_order_delivery_date_column' ), 20, 1 );
    add_action( 'manage_shop_order_posts_custom_column', array( 'orddd_filter', 'orddd_woocommerce_custom_column_value' ), 20, 1 );
    add_filter( 'manage_edit-shop_order_sortable_columns', array( 'orddd_filter', 'orddd_woocommerce_custom_column_value_sort' ) );
    add_filter( 'request', array( 'orddd_filter', 'orddd_woocommerce_delivery_date_orderby' ) );
*/

function add_scripts(){
	if ( is_checkout() ) {
		wp_enqueue_script( 'field-in-checkout',
			plugins_url( '/js/field-in-checkout.js', __FILE__ ),//путь к жс-скрипту относительно этого пхп-файла!
			array( 'jquery' ) );//подлючаю JS-скрипт и говорю что он зависим от JQuery
	};
}

function field_in_checkout_loaded() {
	add_action( 'wp_enqueue_scripts', 'add_scripts' );//хук для подключения скрипта


/*
	$variables = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ) //путь к скрипту-обработчитку аякс-запросов
	);
	//передача урла аякса на фронт внутри объекта ajax_filter_plugin
//	wp_localize_script( 'ajax-filter-js', 'ajax_filter_plugin', $variables );

	$text_domain_dir = dirname( plugin_basename( __FILE__ ) ) . '/lang/';//путь к переводу
	load_plugin_textdomain( 'ajax-filter-plugin', false, $text_domain_dir );
	add_filter( 'posts_search', '__search_by_title_only', 500, 2 );//активирую поиск по заголовку

	*/
}


function field_in_checkout_deactivate() {
//	unregister_widget( 'ajax_filter_widget' );//убить виджет
}




/*
 * @hooked WC_Emails::email_header() Output the email header
 */
//do_action( 'woocommerce_email_header', $email_heading, $email ); //
/*
<?php  ?>
	<p><?php printf( esc_html__( 'You’ve received the following order from %s:', 'woocommerce' ), $order->get_formatted_billing_full_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php


 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.


do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );



/*
 * @hooked WC_Emails::email_footer() Output the email footer

do_action( 'woocommerce_email_footer', $email );


*/

/*


function ajax_filter_register_widget() {
	register_widget( 'ajax_filter_widget' );
}

function ajax_filter_posts_query() {

	if ( ! isset( $_POST ) ) {//если не пришли данные - вываливаемся
		wp_send_json_error( [ 'status' => 'bad!' ] );
	}

	$title  = $_POST['title'] ?? '';
	$number = $_POST['number'] ?? 0;
	$date   = $_POST['fromdate'] ?? '';

	$args2 = array(
		'post_type'      => 'post',
		'posts_per_page' => $number,
		'orderby'        => 'date',
		'order'          => 'ASC',
		's'              => $title,
	);

	if ( $date != '' )//фильтрую по дате, если она задавалась
	{
		$fromdate            = new DateTime( $date );//для удобства форматирования-извлечения сегментов (ООП)
		$args2['date_query'] = array(
			'after' => array(
				'year'  => $fromdate->format( "Y" ),
				'month' => $fromdate->format( "m" ),
				'day'   => $fromdate->format( "d" ),
			),
		);
	}
	$query    = new WP_Query;
	$my_posts = $query->query( $args2 );//цикл с фильтарцией
	foreach ( $my_posts as $my_post ) {
		$a       = [
			'id'    => $my_post->ID,
			'title' => $my_post->post_title,
			'link'  => get_permalink( $my_post->ID ),
		];
		$posts[] = $a;//наполняю массив записей сведениями
	}

	wp_send_json_success( $posts );//JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
}

*/