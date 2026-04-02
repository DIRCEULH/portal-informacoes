<?php
/**
 * Plugin Name: Democracy Poll
 * Description: Allows to create democratic polls. Visitors can vote for more than one answer & add their own answers.
 * Author: Kama
 * Author URI: http://wp-kama.ru/
 * Plugin URI: http://wp-kama.ru/id_67/plagin-oprosa-dlya-wordpress-democracy-poll.html
 * Text Domain: dem
 * Domain Path: lang
 * Version: 5.3.4.5
 *
 * Build: 200
 *
 * PHP: 5.3+
 */

__('Allows to create democratic polls. Visitors can vote for more than one answer & add their own answers.');


$data = get_file_data( __FILE__, array('ver'=>'Version', 'lang_dir'=>'Domain Path') );

define('DEM_MAIN_FILE',  __FILE__ );
define('DEM_VER', $data['ver'] );
define('DEM_LANG_DIRNAME', $data['lang_dir'] );
define('DEMOC_URL',  plugin_dir_url(DEM_MAIN_FILE) );
define('DEMOC_PATH', plugin_dir_path(DEM_MAIN_FILE) );

//if( isset($_GET['hard_translate_files_to_eng']) ) include DEMOC_PATH .'__translate_files_to_eng.php';


/**
 * Устанавливает названия таблиц, для реинициализации при мультисайте
 */
function dem_set_dbtables(){
	global $wpdb;
	$wpdb->democracy_q   = $wpdb->prefix .'democracy_q';
	$wpdb->democracy_a   = $wpdb->prefix .'democracy_a';
	$wpdb->democracy_log = $wpdb->prefix .'democracy_log';
}
dem_set_dbtables();


//if( is_admin() )
require_once DEMOC_PATH .'admin/upgrade_activate.php';

require_once DEMOC_PATH .'class.DemInit.php';
require_once DEMOC_PATH .'admin/class.DemAdminInit.php';
require_once DEMOC_PATH .'class.DemPoll.php';


## Активируем плагин, виджет, если включен
add_action('plugins_loaded', function(){
	Dem::init();

	if( Dem::$opt['use_widget'] )
		require_once DEMOC_PATH . 'widget_democracy.php';
} );

//function Dem(){ return Dem::init(); }
register_activation_hook( DEM_MAIN_FILE, 'democracy_activate' );


/**
 * Функция локализации внешней части
 * @private
 * @param  string $str Строка локализации
 * @return string      Переведенная строка
 */
function __dem( $str ){
	static $cache;
	if( $cache === null )
		$cache = get_option('democracy_l10n');

	return isset( $cache[ $str ] ) ? $cache[ $str ] : __( $str, 'dem');
}


## Wrap functions
if(1){
	function democracy_poll( $id = 0, $before_title = '', $after_title = '', $from_post = 0 ){
		echo get_democracy_poll( $id, $before_title, $after_title, $from_post );
	}

	/**
	 * Для вывода отдельного опроса
	 * @param  integer  [$poll_id = 0]       ID опроса
	 * @param  string   [$before_title = ''] Текст меред заголовком
	 * @param  string   [$after_title = '']  Текст после заголовка
	 * @param  integer  [$from_post = 0]     ID записи с которой вызван опрос. К которой нужно прикрепить опрос.
	 * @return string   HTML код
	 */
	function get_democracy_poll( $poll_id = 0, $before_title = '', $after_title = '', $from_post = 0 ){
		$poll = new DemPoll( $poll_id );

		if( ! $poll ) return 'Poll not found';

		// обновим ID записи с которой вызван опрос, если такого ID нет в данных
		$from_post = is_object($from_post) ? $from_post->ID : intval($from_post);
		if( $from_post && ( ! $poll->in_posts || ! preg_match('~(?:^|,)'. $from_post .'(?:,|$)~', $poll->in_posts) ) ){
			global $wpdb;

			$new_in_posts = $poll->in_posts ? "$poll->in_posts,$from_post" : $from_post;
			$new_in_posts = trim( $new_in_posts, ','); // на всякий...
			$wpdb->update( $wpdb->democracy_q, array('in_posts'=>$new_in_posts), array('id'=>$poll_id) );
		}

		$show_screen = __query_poll_screen_choose( $poll );

		return $poll->get_screen( $show_screen, $before_title, $after_title );
	}

	/**
	 * Для вывода архивов
	 * @param bool $hide_active Не показывать активные опросы?
	 * @return HTML
	 */
	function democracy_archives( $hide_active = false, $before_title = '', $after_title = '' ){
		echo get_democracy_archives( $hide_active, $before_title, $after_title );
	}

	function get_democracy_archives( $hide_active = false, $before_title = '', $after_title = '' ){
		global $wpdb;

		$WHERE = $hide_active ? 'WHERE active = 0' : '';
		$ids = $wpdb->get_col("SELECT id FROM $wpdb->democracy_q $WHERE ORDER BY active DESC, open DESC, id DESC");

		$output = '<div class="dem-archives">';
		foreach( $ids as $poll_id ){
			$poll = new DemPoll( $poll_id );

			$show_screen = isset($_REQUEST['dem_act']) ? __query_poll_screen_choose( $poll ) : 'voted';

			$output .= $poll->get_screen( $show_screen, $before_title, $after_title );
		}
		$output .= "</div>";

		return $output;
	}

	## Какой экран показать, на основе переданных запросов: 'voted' или 'vote'
	function __query_poll_screen_choose( $poll ){
		if( $poll->open && ! $poll->show_results )
			return 'vote'; // view results is closed in options

		$screen = ( isset($_REQUEST['dem_act']) && isset($_REQUEST['dem_pid']) && $_REQUEST['dem_act'] == 'view' && $_REQUEST['dem_pid'] == $poll->id ) ? 'voted' : 'vote';

		return apply_filters('dem_poll_screen_choose', $screen, $poll );
	}
}



