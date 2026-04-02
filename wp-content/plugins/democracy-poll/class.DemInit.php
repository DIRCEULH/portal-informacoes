<?php

## Root plugin class. Init plugin, split init to 'admin' and 'front'

class Dem {

	public $ajax_url;

	public $admin_access; // доступ пользователя к админ-функциям Democracy
	public $super_access; // доступ для управления всем чем можно - обычто это только админ!

	public $msg = array();

	const OPT_NAME = 'democracy_options';

	// теги допустимые в вопросах и ответах. Добавляются к глобальной $allowedtags
	static $allowed_tags = array(
		'a' => array(
			'href' => true,
			'rel' => true,
			'name' => true,
			'target' => true,
		),
	);

	static $opt;
	static $i;

	static function init(){
		if( ! is_null( self::$i ) )
			return self::$i;

		# admin part
		if( is_admin() && ! defined('DOING_AJAX') )
			self::$i = new DemAdminInit();
		# front-end
		else {
			self::$i = new self;
			self::$i->dem_front_init();
		}

		return self::$i;
	}

	function __construct(){
		if( ! is_null( self::$i ) )
			return self::$i;

		self::$allowed_tags = array_merge( $GLOBALS['allowedtags'], array_map('_wp_add_global_attributes', self::$allowed_tags) );

		$this->ajax_url = admin_url('admin-ajax.php');

		self::$opt = $this->get_options();

		// dem_init - Инициализирует основные хуки Democracy
		if(1){
			self::load_textdomain();

			// set access
			$administrator = current_user_can('manage_options');
			$this->super_access = apply_filters('dem_super_access', $administrator );
			$this->admin_access = $administrator;

			// open admin manage access for other roles
			if( ! $this->admin_access && ! empty(self::$opt['access_roles']) ){
				foreach( wp_get_current_user()->roles as $role ){
					if( in_array($role, self::$opt['access_roles'] ) ){
						$this->admin_access = true;
						break;
					}
				}
			}

			// меню в панели инструментов
			if( @ self::$opt['toolbar_menu'] && $this->admin_access )
				add_action('admin_bar_menu', array( &$this, 'toolbar'), 99);

			// hide duplicate content. For 5+ versions it's no need
			if( isset($_GET['dem_act']) || isset($_GET['dem_pid']) || isset($_GET['show_addanswerfield']) ){
				add_action('wp', function(){ status_header( 404 ); });
				add_action('wp_head', function(){ echo "\n".'<meta name="robots" content="noindex,nofollow">'."\n"; });
			}

		}
	}

	## подключаем файл перевода
	static function load_textdomain(){
		$locale = get_locale();

		if( $locale == 'en_US' )
			return;

		$mofile_path = DEMOC_PATH . DEM_LANG_DIRNAME ."/$locale.mo";
		if( ! file_exists( $mofile_path ) )
			return;

		load_textdomain('dem', $mofile_path );
	}

	## Добавляет пункты меню в панель инструментов
	function toolbar( $toolbar ){
		$toolbar->add_node( array(
			'id'    => 'dem_settings',
			'title' => 'Democracy',
			'href'  => $this->admin_page_url(),
		) );

		$list = array();
		$list['']                 = __('Polls List','dem');
		$list['add_new']          = __('Add Poll','dem');
		$list['logs']             = __('Logs','dem');
		$list['general_settings'] = __('Settings','dem');
		$list['design']           = __('Theme Settings','dem');
		$list['l10n']             = __('Texts changes','dem');

		if( ! $this->super_access )
			unset( $list['general_settings'], $list['design'], $list['l10n'] );

		foreach( $list as $id => $title ){
			$toolbar->add_node( array(
				'parent' => 'dem_settings',
				'id'     => $id ?: 'dem_main',
				'title'  => $title,
				'href'   => add_query_arg( array('subpage'=>$id), $this->admin_page_url() ),
			) );
		}
	}

	## Получает настройки. Устанавливает если их нет
	function get_options(){
		if( empty( self::$opt ) ) self::$opt = get_option( self::OPT_NAME );
		if( empty( self::$opt ) ) $this->update_options('default');

		return self::$opt;
	}

	/**
	 * Возвращает УРЛ на главную страницу настроек плагина. Кэширует.
	 * @return string URL
	 */
	function admin_page_url(){
		static $url; if( ! $url ) $url = admin_url('options-general.php?page='. basename( DEMOC_PATH ) );

		return $url;
	}

	/**
	 * Ссылка на редактирование опроса.
	 * @param  integer $poll_id ID опроса
	 * @return string URL
	 */
	function edit_poll_url( $poll_id ){
		return $this->admin_page_url() .'&edit_poll='. $poll_id;
	}

	/**
	 * Проверяет используется ли страничный плагин кэширования на сайте
	 * @return boolean
	 */
	function is_cachegear_on(){
		if( self::$opt['force_cachegear'] ) return true;

		// wp total cache
		if( defined('W3TC') && @w3_instance('W3_ModuleStatus')->is_enabled('pgcache') ) return true;
		// wp super cache
		if( defined('WPCACHEHOME') && @$GLOBALS['cache_enabled'] ) return true;
		// WordFence
		if( defined('WORDFENCE_VERSION') && @wfConfig::get('cacheType') == 'falcon' ) return true;
		// WP Rocket
		if( class_exists('HyperCache')  ) return true;
		// Quick Cache
		if( class_exists('quick_cache') && @\quick_cache\plugin()->options['enable'] ) return true;
		// wp-fastest-cache
		// aio-cache

		return false;
	}

	/**
	 * Очищает данные ответа
	 * @param  string/array $data Что очистить? Если передана строка, удалить из нее недопустимые HTML теги.
	 * @return string/array Чистые данные.
	 */
	function sanitize_answer_data( $data ){
		$allowed_tags = $this->admin_access ? self::$allowed_tags : 'strip';

		if( is_string( $data ) )
			return wp_kses( trim($data), $allowed_tags );

		foreach( $data as $key => & $val ){
			if( is_string($val) ) $val = trim($val);

			if(0){}
			// допустимые теги
			elseif( $key == 'answer' )
				$val = wp_kses( $val, $allowed_tags );

			// числа
			elseif( in_array( $key, array('qid','aid','votes') ) )
				$val = (int) $val;

			// остальное
			else
				$val = wp_kses( $val, 'strip' );
		}

		return apply_filters('dem_sanitize_answer_data', $data );
	}

	## FRONT --------------------------------------
	function dem_front_init(){
		# шоткод [democracy]
		add_shortcode('democracy',          array( &$this, 'poll_shortcode'));
		add_shortcode('democracy_archives', array( &$this, 'archives_shortcode'));

		//if( ! self::$opt['inline_js_css'] ) $this->add_css(); // подключаем стили как файл, если не инлайн

		# для работы функции без AJAX
		if( !isset($_POST['action']) || $_POST['action'] !== 'dem_ajax' ) $this->not_ajax_request_handler();

		# ajax request во frontend_init нельзя, потому что срабатывает только как is_admin()
		add_action('wp_ajax_dem_ajax',        array( &$this, 'ajax_request_handler') );
		add_action('wp_ajax_nopriv_dem_ajax', array( &$this, 'ajax_request_handler') );
	}

	## обрабатывает запрос AJAX
	function ajax_request_handler(){
		$vars = (object) $this->__sanitize_request_vars();

		if( ! $vars->act ) wp_die('error: no parameters have been sent or it is unavailable');
		if( ! $vars->pid ) wp_die('error: id unknown');

		// Вывод
		$poll = new DemPoll( $vars->pid );

		// switch
		// голосуем и выводим результаты
		if( $vars->act === 'vote' && $vars->aids ){
			// если пользователь голосует с другого браузера и он уже голосовал, ставим куки
			//if( $poll->cachegear_on && $poll->votedFor ) $poll->set_cookie();

			$poll->addVote( $vars->aids );

			if( $poll->not_show_results )
				echo $poll->get_vote_screen();
			else
				echo $poll->get_result_screen();
		}
		// удаляем результаты
		elseif( $vars->act === 'delVoted' ){
			$poll->deleteVote();
			echo $poll->get_vote_screen();
		}
		// смотрим результаты
		elseif( $vars->act === 'view' ){
			if( $poll->not_show_results )
				echo $poll->get_vote_screen();
			else
				echo $poll->get_result_screen();
		}
		// вернуться к голосованию
		elseif( $vars->act === 'vote_screen' ){
			echo $poll->get_vote_screen();
		}
		elseif( $vars->act === 'getVotedIds' ){
			if( $poll->votedFor ){
				$poll->set_cookie(); // установим куки, т.к. этот запрос делается только если куки не установлены
				echo $poll->votedFor;
			}
			elseif( $poll->blockForVisitor ){
				echo 'blockForVisitor'; // чтобы вывести заметку
			}
			else {
				// если не голосовал ставим куки на пол дня, чтобы не делать эту првоерку каждый раз
				$poll->set_cookie('notVote', (current_time('timestamp') + (DAY_IN_SECONDS/2)) );
			}
		}

		wp_die();
	}

	## для работы функции без AJAX
	function not_ajax_request_handler(){
		$vars = (object) $this->__sanitize_request_vars();

		if( ! $vars->act || ! $vars->pid || ! isset($_SERVER['HTTP_REFERER']) ) return;

		$poll = new DemPoll( $vars->pid );

		if( $vars->act == 'vote' && $vars->aids ){
			$poll->addVote( $vars->aids );
			wp_redirect( remove_query_arg( array('dem_act','dem_pid'), $_SERVER['HTTP_REFERER'] ) );
			exit;
		}
		elseif( $vars->act == 'delVoted' ){
			$poll->deleteVote();
			wp_redirect( remove_query_arg( array('dem_act','dem_pid'), $_SERVER['HTTP_REFERER'] ) );
			exit;
		}
	}

	## Делает предваритеьную проверку передавемых переменных запроса
	function __sanitize_request_vars(){
		return array(
			'act'  => isset($_POST['dem_act']) ? $_POST['dem_act'] : false,
			'pid'  => isset($_POST['dem_pid'])  ? absint( $_POST['dem_pid'] ) : false,
			'aids' => isset($_POST['answer_ids']) ? wp_unslash( $_POST['answer_ids'] ) : false,
		);
	}

	## шоткод архива опросов
	function archives_shortcode(){
		return '<div class="dem-archives-shortcode">'. get_democracy_archives() .'</div>';
	}

	## шоткод опроса
	function poll_shortcode( $atts ){
		// на всякий случай проверка, а то любят шорткоды вызывать в сайдбаре каком нить...
		$post_id = ( is_singular() && is_main_query() ) ? $GLOBALS['post'] : 0;

		return '<div class="dem-poll-shortcode">'. get_democracy_poll( @ $atts['id'], '', '', $post_id ) .'</div>';
	}

	## добавляет стили в WP head
	function add_css(){
		static $once; if( $once ) return; $once=1; // выполняем один раз!

		$demcss = get_option('democracy_css');
		$minify = @$demcss['minify'];

		if( ! $minify ) return;

		// пробуем подключить сжатые версии файлов
//		$css_name = rtrim( $css_name, '.css');
//		$css      = 'styles/' . $css_name;
//		$cssurl   = DEMOC_URL  . "$css.min.css";
//		$csspath  = DEMOC_PATH . "$css.min.css";
//
//		if( ! file_exists( $csspath ) ){
//			$cssurl   = DEMOC_URL  . "$css.css";
//			$csspath  = DEMOC_PATH . "$css.css";
//		}

		// inline HTML
//		if( self::$opt['inline_js_css'] )
			return "\n<!--democracy-->\n" .'<style type="text/css">'. $minify .'</style>'."\n";

//		else{
//			add_action('wp_enqueue_scripts', function() use ($cssurl){ wp_enqueue_style('democracy', $cssurl, array(), DEM_VER ); } );
//		}
	}

	## добавляет скрипты в подвал
	function add_js_once(){
		static $once; if( $once ) return; $once=1; // выполняем один раз!

		$jsurl    = DEMOC_URL  . "js/democracy.min.js";
		$jspath   = DEMOC_PATH . "js/democracy.min.js";

		// inline HTML
		if( self::$opt['inline_js_css'] ){
			wp_enqueue_script('jquery');
			return "\n" .'<script type="text/javascript">'. file_get_contents( $jspath ) .'</script>'."\n";
		}
		else
			wp_enqueue_script('democracy', $jsurl, array(), DEM_VER, true );
	}

	## Сортировка массива объектов. Передаете в $array массив объектов, указываете в $args параметры сортировки и получаете отсортированный массив объектов.
	static function objects_array_sort( $array, $args = array('votes'=>'desc') ){
		usort( $array, function( $a, $b ) use ( $args ){
			$res = 0;

			if( is_array($a) ){
				$a = (object) $a;
				$b = (object) $b;
			}

			foreach( $args as $k => $v ){
				if( $a->$k == $b->$k ) continue;

				$res = ( $a->$k < $b->$k ) ? -1 : 1;
				if( $v=='desc' ) $res= -$res;
				break;
			}

			return $res;
		} );

		return $array;
	}

	/**
	 * Добавляет сообщение в массив
	 * @param string $msg                Сообщение
	 * @param string [$type = 'updated'] Тип: updated, notice, error
	 */
	static function add_msg( $msg, $type = 'updated' ){
		Dem::$i->msg[ $type ][] = $msg;
	}

	/**
	 * Получает HTML код всех сообщений находящихся в массиве Dem::msg
	 */
	function msgs_html(){
		if( ! $this->msg ) return '';
		$out = '';

		if( isset($this->msg['error']) )
			foreach( $this->msg['error'] as $msg )
				$out .= Dem::msg_html( $msg, 'error' );

		if( isset($this->msg['notice']) )
			foreach( $this->msg['notice'] as $msg )
				$out .= Dem::msg_html( $msg, 'notice' );

		if( isset($this->msg['updated']) )
			foreach( $this->msg['updated'] as $msg )
				$out .= Dem::msg_html( $msg, 'updated' );


		foreach( $this->msg as $k => $msg ){
			if( in_array( $k, array('error', 'notice', 'updated'), true ) ) // $k = 0 не работает. поэтому $strict = true
				continue; // === because (0 == 'foo') = true

			$out .= Dem::msg_html( $msg, 'updated' );
		}

		return $out;
	}

	static function msg_html( $msg, $type = 'updated' ){
		return '<div class="'. $type .' notice is-dismissible"><p>'. $msg .'</p></div>';
	}

	static function admin_notices( $msg, $type = '' ){
		add_action('admin_notices', function() use ($msg, $type){ echo Dem::msg_html( $msg, $type ); } );
	}

	/**
	 * Получает данные локации переданного IP.
	 * @param  string [$ip = NULL]            IP для проверки. По умолчанию текущий IP.
	 * @param  string [$purpose = "location"] Какие данные нужно получить. Может быть: location address city state region country countrycode
	 * @return array/string Данные в виде массива или строки. Массив при $purpose = "location" в остальных случаях вернется строка.
	 */
	static function get_ip_info( $ip = NULL, $purpose = "location" ){
		$output = NULL;

		if( filter_var($ip, FILTER_VALIDATE_IP) === FALSE )
			$ip = DemPoll::get_ip();

		$purpose    = str_replace( array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)) );
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			'AF' => 'Africa',
			'AN' => 'Antarctica',
			'AS' => 'Asia',
			'EU' => 'Europe',
			'OC' => 'Australia (Oceania)',
			'NA' => 'North America',
			'SA' => 'South Americ',
		);

		if( filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support) ){
			$ipdat = json_decode( wp_remote_retrieve_body( wp_remote_get("http://www.geoplugin.net/json.gp?ip=$ip") ) );

			if( @ strlen(trim($ipdat->geoplugin_countryCode)) == 2 ){
				switch( $purpose ){
					case "location":
						$output = array(
							'city'           => @ $ipdat->geoplugin_city,
							'state'          => @ $ipdat->geoplugin_regionName,
							'country'        => @ $ipdat->geoplugin_countryName,
							'country_code'   => @ $ipdat->geoplugin_countryCode,
							'continent'      => @ $continents[strtoupper($ipdat->geoplugin_continentCode)],
							'continent_code' => @ $ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}

		return $output;
	}

	/**
	 * Получает строку: Формат ip_info для таблицы логов
	 * @param  array/string    $ip_info IP или уже полученные данные IP в массиве
	 * @return string Формат: 'название_страны,код_страны,город' или 'текущее время UNIX'
	 * Зависит от метода Dem::get_ip_info()
	 */
	static function ip_info_format( $ip_info ){
		// если передан IP
		if( filter_var($ip_info, FILTER_VALIDATE_IP) ){
			if( $ip_info === '127.0.0.1' )
				$format = time() + YEAR_IN_SECONDS * 10;
			else
				$ip_info = self::get_ip_info( $ip_info );
		}

		if( empty($format) ){
			/*Array(
				[city] =>
				[state] =>
				[country] => Uzbekistan
				[country_code] => UZ
				[continent] => Asia
				[continent_code] => AS
			)*/
			if( @ $ip_info['country'] && @  $ip_info['country_code'] )
				$format = $ip_info['country'] .','. $ip_info['country_code'] .','. $ip_info['city'];
			else
				$format = time();
		}

		return $format;
	}

}

