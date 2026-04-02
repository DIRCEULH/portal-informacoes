<?php

## Вывод и голосование отдельного опроса.
## Нуждается в классе плагина Dem

class DemPoll {

	var $id; // id опроса, 0 или 'last'
	var $poll;

	var $has_voted        = false;
	var $votedFor         = '';
	var $blockVoting      = false; // блокировать голосование
	var $blockForVisitor  = false; // только для зарегистрированных
	var $not_show_results = false; // не показывать результаты

	var $inArchive    = false; // в архивной странице

	var $cachegear_on = false; // проверка включен ли механихм кэширвоания
	var $for_cache    = false;

	var $cookey;           // Название ключа cookie

	function __construct( $id = 0 ){
		global $wpdb;

		if( ! $id )
			$poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE active = 1 ORDER BY RAND() LIMIT 1");
		elseif( $id === 'last' )
			$poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE open = 1 ORDER BY id DESC LIMIT 1");
		else
			$poll = self::get_poll( $id );

		if( ! $poll ) return print "<!-- democracy: there is no poll -->";

		// устанавливаем необходимые переменные
		$this->id = (int) $poll->id;

		if( ! $this->id ) return; // влияет на весь класс, важно!

		$this->cookey = 'demPoll_' . $this->id;
		$this->poll   = $poll;

		// отключим демокраси опцию
		if( Dem::$opt['democracy_off'] )
			$this->poll->democratic = false;
		// отключим опцию переголосования
		if( Dem::$opt['revote_off'] )
			$this->poll->revote = false;

		$this->cachegear_on = Dem::$i->is_cachegear_on();

		$this->setVotedData();
		$this->set_answers(); // установим свойство $this->poll->answers

		// закрываем опрос т.к. срок закончился
		if( $this->poll->end && $this->poll->open && ( current_time('timestamp') > $this->poll->end ) )
			$wpdb->update( $wpdb->democracy_q, array( 'open'=>0 ), array( 'id'=>$this->id ) );

		// только для зарегистрированных
		if( ( Dem::$opt['only_for_users'] || $this->poll->forusers ) && ! is_user_logged_in() )
			$this->blockForVisitor = true;

		// блокировка возможности голосовать
		if( $this->blockForVisitor || ! $this->poll->open || $this->has_voted )
			$this->blockVoting = true;

		if( (! $poll->show_results || Dem::$opt['dont_show_results']) && $poll->open && ( ! is_admin() || defined('DOING_AJAX') ) )
			$this->not_show_results = true;

		return $this->id;
	}

	public function __get( $var ){
		return isset( $this->poll->{$var} ) ? $this->poll->{$var} : null;
	}

	static function get_poll( $poll_id ){
		global $wpdb;

		if( $poll = $wpdb->get_row("SELECT * FROM $wpdb->democracy_q WHERE id = ". intval( $poll_id ) ." LIMIT 1") )
			$poll = apply_filters('dem_get_poll', $poll );

		return $poll;
	}

	/**
	 * Получает HTML опроса
	 * @param bool $show_screen Какой экран показывать: vote, voted, force_vote
	 * @return HTML
	 */
	public function get_screen( $show_screen = 'vote', $before_title = '', $after_title = '' ){
		if( ! $this->id ) return false;

		$this->inArchive = ( @ $GLOBALS['post']->ID == Dem::$opt['archive_page_id'] ) && is_singular();

		if( $this->blockVoting && $show_screen != 'force_vote' )
			$show_screen = 'voted';

		$___ = '';
		$___ .= Dem::$i->add_css();

		$dem_opts = array(
			'ajax_url'         => Dem::$i->ajax_url,
			'pid'              => $this->id,
			'max_answs'        => ($this->poll->multiple > 1) ? $this->poll->multiple : 0,
			'answs_max_height' => @ Dem::$opt['answs_max_height'],
			'anim_speed'       => @ Dem::$opt['anim_speed'],
		);

		$___ .= '<div id="democracy-'. $this->id .'" class="democracy" data-opts=\''. json_encode($dem_opts) .'\' >';
			$___ .=  ( $before_title ?: Dem::$opt['before_title'] ) . $this->poll->question . ( $after_title  ?: Dem::$opt['after_title'] );

			// изменяемая часть
			$___ .=  $this->get_screen_basis( $show_screen );
			// изменяемая часть

			$___ .= $this->poll->note ? '<div class="dem-poll-note">'. wpautop( $this->poll->note ) .'</div>' : '';
			if( current_user_can('manage_options') )
				$___ .= '<a class="dem-edit-link" href="'. Dem::$i->edit_poll_url($this->id) .'" title="'. __('Edit poll','dem') .'"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="1.5em" height="100%" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><path d="M617.8,203.4l175.8,175.8l-445,445L172.9,648.4L617.8,203.4z M927,161l-78.4-78.4c-30.3-30.3-79.5-30.3-109.9,0l-75.1,75.1 l175.8,175.8l87.6-87.6C950.5,222.4,950.5,184.5,927,161z M80.9,895.5c-3.2,14.4,9.8,27.3,24.2,23.8L301,871.8L125.3,696L80.9,895.5z"/></svg>
</a>';
		// copyright
		if( Dem::$opt['show_copyright'] && ( is_home() || is_front_page() ) )
			$___ .=  '<a class="dem-copyright" href="http://wp-kama.ru/?p=67" title="'. __('Download the Democracy Poll','dem') .'" onmouseenter="var $el = jQuery(this).find(\'span\'); $el.stop().animate({width:\'toggle\'},200); setTimeout(function(){ $el.stop().animate({width:\'toggle\'},200); }, 4000);"> © <span style="display:none;white-space:nowrap;">Kama</span></a>';

		// loader
		if( Dem::$opt['loader_fname'] ){
			static $loader; // оптимизация, чтобы один раз выводился код на странице
			if( ! $loader ){
				$loader = '<div class="dem-loader"><div>'. file_get_contents( DEMOC_PATH .'styles/loaders/'. Dem::$opt['loader_fname'] ) .'</div></div>';
				$___ .=  $loader;
			}
		}

		$___ .=  "</div><!--democracy-->";


		// для КЭША
		if( $this->cachegear_on && ! $this->inArchive ){
			$___ .= '
			<!--noindex-->
			<div class="dem-cache-screens" style="display:none;" data-opt_logs="'. Dem::$opt['keep_logs'] .'">';

			// запоминаем
			$votedFor = $this->votedFor;
			$this->votedFor = false;
			$this->for_cache = 1;

			$compress = function( $str ){
				return preg_replace("~[\n\r\t]~u", '', preg_replace('~\s+~u',' ',$str) );
			};

			if( ! $this->not_show_results )
				$___ .= $compress( $this->get_screen_basis('voted') );  // voted_screen

			if( $this->poll->open )
				$___ .= $compress( $this->get_screen_basis('force_vote') ); // vote_screen

			$this->for_cache = 0;
			$this->votedFor = $votedFor; // возвращаем

			$___ .=	'
			</div>
			<!--/noindex-->';
		}

		if( ! Dem::$opt['disable_js'] )
			$___ .= Dem::$i->add_js_once(); // подключаем скрипты (один раз)

		return $___;
	}

	/**
	 * Получает сердце HTML опроса (изменяемую часть)
	 * @param bool $show_screen
	 * @return HTML
	 */
	public function get_screen_basis( $show_screen = 'vote' ){
		$class_suffix = $this->for_cache ? '-cache' : '';

		if( $this->not_show_results )
			$show_screen = 'force_vote';

		$screen = ( $show_screen == 'vote' || $show_screen == 'force_vote' ) ? 'vote' : 'voted';

		$___ = '<div class="dem-screen'. $class_suffix .' '. $screen  .'">';
		$___ .= ( $screen == 'vote' ) ? $this->get_vote_screen() : $this->get_result_screen();
		$___ .=  '</div>';

		if( ! $this->for_cache )
			$___ .=  '<noscript>Poll Options are limited because JavaScript is disabled in your browser.</noscript>';

		return $___;
	}

	/**
	 * Получает код для голосования
	 * @return HTML
	 */
	public function get_vote_screen(){
		if( ! $this->id ) return false;

		$poll = $this->poll;

		$auto_vote_on_select = ( ! $poll->multiple && $poll->revote && @ Dem::$opt['hide_vote_button'] );

		$___ = $dem_act = ''; // vars

		$___ .= '<form method="POST" action="#democracy-'. $this->id .'">';
			$___ .= '<ul class="dem-vote">';

				$type = $poll->multiple ? 'checkbox' : 'radio';

				foreach( $poll->answers as $answer ){
					$answer = apply_filters('dem_vote_screen_answer', $answer );

					$auto_vote = $auto_vote_on_select ? 'data-dem-act="vote"' : '';

					$checked = $disabled = '';
					if( $this->votedFor ){
						if( in_array( $answer->aid, explode(',', $this->votedFor ) ) )
							$checked = ' checked="checked"';

						$disabled = ' disabled="disabled"';
					}

					$___ .= '
					<li data-aid="'. $answer->aid .'">
						<label class="dem__'. $type .'_label">
							<input class="dem__'. $type .'" '. $auto_vote .' type="'. $type .'" value="'. $answer->aid .'" name="answer_ids[]"'. $checked . $disabled .'><span class="dem__spot"></span> '. $answer->answer .'
						</label>
					</li>';
				}

				if( $poll->democratic && ! $this->blockVoting ){
					$___ .= '<li class="dem-add-answer"><a href="javascript:void(0);" rel="nofollow" data-dem-act="newAnswer" class="dem-link">'. __dem('Add your answer') .'</a></li>';
				}
			$___ .= "</ul>";

			$___ .= '<div class="dem-bottom">';
				$___ .= '<input type="hidden" name="dem_act" value="vote">';
				$___ .= '<input type="hidden" name="dem_pid" value="'. $this->id .'">';

				$btnVoted  = '<div class="dem-voted-button"><input class="dem-button '. Dem::$opt['btn_class'] .'" type="submit" value="'. __dem('Already voted...') .'" disabled="disabled"></div>';
				$btnVote   = '<div class="dem-vote-button"><input class="dem-button '. Dem::$opt['btn_class'] .'" type="submit" value="'. __dem('Vote') .'" data-dem-act="vote"></div>';

				if( $auto_vote_on_select )
					$btnVote = '';

				$for_users_alert = $this->blockForVisitor ? '<div class="dem-only-users">'. self::registered_only_alert_text() .'</div>' : '';

				// для экша
				if( $this->for_cache ){
					$___ .= $this->__voted_notice();

					if( $for_users_alert )
						$___ .= str_replace( array('<div', 'class="'), array('<div style="display:none;"', 'class="dem-cache-notice '), $for_users_alert );

					if( $poll->revote )
						$___ .= preg_replace('~(<[^>]+)~s', '$1 style="display:none;"', $this->__revote_btn(), 1 );
					else
						$___ .= substr_replace( $btnVoted, '<div style="display:none;"', 0, 4 );
					$___ .= $btnVote;
				}
				// не для кэша
				else {
					if( $for_users_alert ){
						$___ .= $for_users_alert;
					}
					else{
						if( $this->has_voted )
							$___ .= $poll->revote ? $this->__revote_btn() : $btnVoted;
						else
							$___ .= $btnVote;
					}

				}

				if( ! $this->not_show_results && ! Dem::$opt['dont_show_results_link'] )
					$___ .= '<a href="javascript:void(0);" class="dem-link dem-results-link" data-dem-act="view" rel="nofollow">'. __dem('Results') .'</a>';


			$___ .= '</div>';

		$___ .= '</form>';

		return apply_filters('dem_vote_screen', $___, $this );
	}

	/**
	 * Получает код результатов голосования
	 * @return HTML
	 */
	public function get_result_screen(){
		if( ! $this->id ) return false;
		$poll = $this->poll;

		// отсортируем по голосам
		$answers = Dem::objects_array_sort( $poll->answers, array('votes'=>'desc') );

		$___ = '';

		$max = $total = 0;

		foreach( $answers as $answer ){
			$total += $answer->votes;
			if( $max < $answer->votes )
				$max = $answer->votes;
		}

		$voted_class = 'dem-voted-this';
		$voted_txt   = __dem('This is Your vote.');
		$___ .= '<ul class="dem-answers" data-voted-class="'. $voted_class .'" data-voted-txt="'. $voted_txt .'">';

			foreach( $answers as $answer ){
				// склонение голосов
				$__sclonenie = function( $number, $titles, $nonum = false ){
					$titles = explode(',', $titles);
					$cases = array (2, 0, 1, 1, 1, 2);
					return ($nonum ? '' : "$number "). $titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
				};

				$answer = apply_filters('dem_result_screen_answer', $answer );

				$votes         = (int) $answer->votes;
				$is_voted_this = ( $this->has_voted && in_array( $answer->aid, explode(',', $this->votedFor) ) );
				$is_winner     = ( $max == $votes );

				$novoted_class = ( $votes == 0 ) ? ' dem-novoted' : '';
				$li_class      = ' class="'. ( $is_winner ? 'dem-winner':'' ) . ( $is_voted_this ? " $voted_class":'' ) . $novoted_class .'"';
				$sup           = $answer->added_by ? '<sup class="dem-star" title="'. __dem('The answer was added by a visitor') .'">*</sup>' : '';
				$percent       = ( $votes > 0 ) ? round($votes / $total * 100) : 0;

				$percent_txt = sprintf( __dem('%s - %s%% of all votes'), $__sclonenie( $votes, __dem('vote,votes,votes') ), $percent );
				$title       = ( $is_voted_this ? $voted_txt : '' ) . ' '. $percent_txt;
				$title       = " title='$title'";

				$votes_txt = $votes .' '. '<span class="votxt">'. $__sclonenie( $votes, __dem('vote,votes,votes'), 'nonum' ) .'</span>';

				$___ .= '<li'. $li_class . $title .' data-aid="'. $answer->aid .'">';
					$label_perc_txt = ' <span class="dem-label-percent-txt">'. $percent .'%, '. $votes_txt .'</span>';
					$percent_txt    = '<div class="dem-percent-txt">'. $percent_txt .'</div>';
					$votes_txt      = '<div class="dem-votes-txt">
						<span class="dem-votes-txt-votes">'. $votes_txt .'</span>
						'. ( ( $percent > 0 ) ? ' <span class="dem-votes-txt-percent">'. $percent .'%</span>' : '' ) . '
						</div>';

					$___ .= '<div class="dem-label">'. $answer->answer . $sup . $label_perc_txt .'</div>';

					// css процент
					$graph_percent = ( ( ! Dem::$opt['graph_from_total'] && $percent != 0 ) ? round( $votes / $max * 100 ) : $percent ) . '%';
					if( $graph_percent == 0 ) $graph_percent = '1px';

					$___ .= '<div class="dem-graph">';
						$___ .= '<div class="dem-fill" style="width:'. $graph_percent .';"></div>';
						$___ .= $votes_txt;
						$___ .= $percent_txt;
					$___ .= "</div>";
				$___ .= "</li>";
			}
		$___ .= '</ul>';

		// dem-bottom
		$___ .= '<div class="dem-bottom">';
			$___ .= '<div class="dem-poll-info">';
				$___ .= '<div class="dem-total-votes">'. sprintf( __dem('Total Votes: %s'), $total ) .'</div>';
				$___ .= ($poll->multiple  ? '<div class="dem-users-voted">'. sprintf( __dem('Voters:%s'), $poll->users_voted ) .'</div>' : '');
				$___ .= '
				<div class="dem-date" title="'. __dem('Begin') .'">
					<span class="dem-begin-date">'. date_i18n( get_option('date_format'), $poll->added ) .'</span>
					'.( $poll->end ? ' - <span class="dem-end-date" title="'. __dem('End') .'">'. date_i18n( get_option('date_format'), $poll->end ) .'</span>' : '' ).'
				</div>';
				$___ .= $answer->added_by ? '<div class="dem-added-by-user"><span class="dem-star">*</span>'. __dem(' - added by visitor') .'</div>' : '';
				$___ .= ! $poll->open     ? '<div>'. __dem('Voting is closed') .'</div>' : '';
				if( ! $this->inArchive && Dem::$opt['archive_page_id'] )
					$___ .= '<a class="dem-archive-link dem-link" href="'. get_permalink( Dem::$opt['archive_page_id'] ) .'" rel="nofollow">'. __dem('Polls Archive') .'</a>';
			$___ .= '</div>';

		if( $poll->open ){
			// заметка для незарегистрированных пользователей
			$for_users_alert = $this->blockForVisitor ? '<div class="dem-only-users">'. self::registered_only_alert_text() .'</div>' : '';

			// вернуться к голосованию
			$vote_btn = '<a href="javascript:void(0);" class="dem-button '. Dem::$opt['btn_class'] .' dem-vote-link" data-dem-act="vote_screen" rel="nofollow">'. __dem('Vote') .'</a>';

			// для кэша
			if( $this->for_cache ){
				$___ .= $this->__voted_notice();

				if( $for_users_alert )
					$___ .= str_replace( array('<div', 'class="'), array('<div style="display:none;"', 'class="dem-cache-notice '), $for_users_alert );

				if( $poll->revote )
					$___ .= $this->__revote_btn();
				else
					$___ .= $vote_btn;
			}
			// не для кэша
			else {
				if( $for_users_alert ){
					$___ .= $for_users_alert;
				}
				else {
					if( $this->has_voted ){
						if( $poll->revote )
							$___ .= $this->__revote_btn();
					}
					else
						$___ .= $vote_btn;
				}

			}
		}

		$___ .= '</div>'; // / dem-bottom


		return apply_filters('dem_result_screen', $___, $this );
	}

	static function registered_only_alert_text(){
		return sprintf( __dem('Only registered users can vote. <a href="%s" rel="nofollow">Login</a> to vote.'), esc_url( wp_login_url( $_SERVER['REQUEST_URI'] ) ) );
	}

	public function __revote_btn(){
		return '
		<span class="dem-revote-button-wrap">
		<form action="#democracy-'. $this->id .'" method="POST">
			<input type="hidden" name="dem_act" value="delVoted">
			<input type="hidden" name="dem_pid" value="'. $this->id .'">
			<input type="submit" value="'. __dem('Revote') .'" class="dem-revote-link dem-revote-button dem-button '. Dem::$opt['btn_class'] .'" data-dem-act="delVoted" data-confirm-text="'. __dem('Are you sure you want cancel the votes?') .'">
		</form>
		</span>';
	}

	/**
	 * заметка: вы уже голосовали
	 * @return string Текст заметки
	 */
	public function __voted_notice(){
		return '
		<div class="dem-cache-notice dem-youarevote" style="display:none;">
			<div class="dem-notice-close" onclick="jQuery(this).parent().fadeOut();">&times;</div>
			'. __dem('You or your IP had already vote.') .'
		</div>';
	}

	/**
	 * Добавляет голос
	 * @param  str   $aids ID ответов через запятую. Там может быть строка, тогда она будет добавлена, как ответ пользователя.
	 * @return false/null
	 */
	public function addVote( $aids ){
		if( ! $this->id ) return false;

		if( $this->has_voted && ($_COOKIE[ $this->cookey ] === 'notVote') )
			$this->set_cookie(); // установим куки повторно, был баг...

		// должен идти после првоерки $this->has_voted потому что если $this->has_voted то $this->blockVoting всегда включен
		if( $this->blockVoting ) return false;

		global $wpdb;

		if( ! is_array( $aids ) ){
			$aids = trim( $aids );
			$aids = explode('~', $aids );
		}

		$aids = array_map('trim', $aids );

		// Добавка ответа пользователя
		// Првоеряет значение массива, ищет строку, если есть то это и есть произвольный ответ.
		if( $this->poll->democratic ){
			$new_user_answer = false;

			foreach( $aids as $k => $id ){
				if( ! preg_match('~^[0-9]+$~', $id ) ){
					$new_user_answer = $id;
					unset( $aids[ $k ] ); // удалим из общего массива, чтобы дельше ответа не было

					if( ! $this->poll->multiple )
						$aids = array(); // опусташим массив так как множественное голосование запрещено

					//break; !!!!NO
				}
			}

			// есть ответ пользователя, добавляем и голосуем
			if( $new_user_answer ){
				if( $aid = $this->__add_democratic_answer( $new_user_answer ) );
					$aids[] = $aid;
			}
		}

		$AND = '';

		// соберем $ids в строку для кук. Там только числа
		$aids = array_map('esc_sql', $aids);
		$aids = implode(',', $aids );

		if( ! $aids ) return false;

		// один ответ
		if( false === strpos($aids, ',') ){
			$aid = esc_sql( $aids );
			$AND = " AND aid = $aid LIMIT 1";
		}
		// несколко ответов (multiple)
		elseif( $this->poll->multiple ){
			$aids = esc_sql( $aids );
			$AND = " AND aid IN ($aids)";
		}

		if( ! $AND ) return false;

		// обновляем в БД
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->democracy_a SET votes = (votes+1) WHERE qid = %d $AND", $this->id ) );

		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->democracy_q SET users_voted = (users_voted+1) WHERE id = %d", $this->id ) );
		$this->poll->users_voted++;

		$this->blockVoting = true;
		$this->has_voted   = true;
		$this->votedFor    = $aids;

		$this->set_answers(); // переустановим ответы

		$this->set_cookie(); // установим куки

		if( Dem::$opt['keep_logs'] )
			$this->add_logs();

	}

	/**
	 * Удаляет данные пользователя о голосовании
	 * Отменяет установленные $this->has_voted и $this->votedFor
	 * Должна вызываться как зоголовки, до вывода данных
	 */
	public function deleteVote(){
		if ( ! $this->id ) return false;
		if ( ! $this->poll->revote ) return false;

		// если опция логов не включена, то отнимаем по кукам,
		// тут голоса можно откручивать назад, потому что разные браузеры проверить не получится
		if( ! Dem::$opt['keep_logs'] )
			$this->minus_vote();

		// Прежде чем удалять, проверим включена ли опция ведения логов и есть ли записи о голосовании в БД,
		// так как куки могут удалить и тогда, данные о голосовании пойдут в минус
		if( Dem::$opt['keep_logs'] && $this->get_vote_log() ){
			$this->minus_vote();
			$this->delete_vote_from_log(); // чистим логи
		}

		$this->unset_cookie();

		$this->has_voted   = false;
		$this->votedFor    = false;
		$this->blockVoting = $this->poll->open ? false : true; // тут еще нужно учесть открыт опрос или нет...

		$this->set_answers(); // переустановим ответы, если вдруг добавленный ответ был удален
	}

	private function __add_democratic_answer( $answer ){
		global $wpdb;

		$new_answer = Dem::$i->sanitize_answer_data( $answer ); // чистим
		$new_answer = wp_unslash( $new_answer );

		// проверим нет ли уже такого ответа
		if( $wpdb->query( $wpdb->prepare("SELECT aid FROM $wpdb->democracy_a WHERE answer = '%s' AND qid = $this->id", $new_answer ) ) )
			return;

		$cuser_id = get_current_user_id();

		// добавлен из фронта - демократический вариант ответа не важно какой юзер!
		$added_by = $cuser_id ?: self::get_ip();
		$added_by .= (! $cuser_id || $this->poll->added_user != $cuser_id ) ? '-new' : '';

		// если есть порядок, ставим 'max+1'
		$aorder = $this->poll->answers[0]->aorder > 0 ? max(wp_list_pluck($this->poll->answers, 'aorder')) +1 : 0;

		$inserted = $wpdb->insert( $wpdb->democracy_a, array( 'qid'=>$this->id, 'answer'=>$new_answer, 'votes'=>0, 'added_by'=>$added_by, 'aorder'=>$aorder ) );

		return $inserted ? $wpdb->insert_id : 0;
	}

	/**
	 * Устанавливает глобальные переменные $this->has_voted и $this->votedFor
	 */
	protected function setVotedData(){
		if( ! $this->id )
			return false;

		// база приоритетнее куков, потому что в одном браузере можно отменить голосование, а куки в другом будут показывать что голосовал...
		// ЗАМЕТКА: обновим куки, если не совпадают. Потому что в разных браузерах могут быть разыне. Не работает,
		// потому что куки нужно устанавливать перед выводом данных и вообще так делать не нужно, потмоу что проверка
		// по кукам становится не нужной в целом...
		if( Dem::$opt['keep_logs'] && ($res = $this->get_vote_log()) ){
			$this->has_voted = true;
			$this->votedFor = $res->aids;
		}
		// проверяем куки
		elseif( isset($_COOKIE[ $this->cookey ]) && ($_COOKIE[ $this->cookey ] != 'notVote') ){
			$this->has_voted = true;
			$this->votedFor = preg_replace('/[^0-9, ]/', '', $_COOKIE[ $this->cookey ] ); // чистим
		}

	}

	## отнимает голоса в БД и удаляет ответ, если надо
	protected function minus_vote(){
		global $wpdb;

		$INaids = implode(',', $this->get_answ_aids_from_str( $this->votedFor ) ); // чистит для БД!

		if( ! $INaids ) return false;

		// сначала удалим добавленные пользователем ответы, если они есть и у них 0 или 1 голос
		$r1 = $wpdb->query("DELETE FROM $wpdb->democracy_a WHERE added_by != '' AND votes IN (0,1) AND aid IN ($INaids) ORDER BY aid DESC LIMIT 1");

		// отнимаем голоса
		$r2 = $wpdb->query("UPDATE $wpdb->democracy_a SET votes = IF( votes>0, votes-1, 0 ) WHERE aid IN ($INaids)");
		// отнимаем кол голосовавших
		$r3 = $wpdb->query("UPDATE $wpdb->democracy_q SET users_voted = IF( users_voted>0, users_voted-1, 0 ) WHERE id = ". (int) $this->id );

		return ($r1 || $r2);
	}

	/**
	 * Получает массив ID ответов из переданной строки, где id разделены запятой.
	 * Чистит для БД!
	 * @param  string $str Строка с ID ответов
	 * @return array  ID ответов
	 */
	protected function get_answ_aids_from_str( $str ){
		$arr = explode(',', $str);
		$arr = array_map('trim', $arr );
		$arr = array_map('intval', $arr );
		$arr = array_filter( $arr ); // удалим пустые
		return $arr;
	}

	## время до которого логи будут жить
	public function expire_time(){
		return current_time('timestamp') + ( intval( Dem::$opt['cookie_days'] ) * DAY_IN_SECONDS );
	}

	/**
	 * Устанавливает куки для текущего опроса
	 * @param str $value Значение куки, по умолчанию текущие голоса.
	 * @param int $expire Время окончания кики.
	 * @return none.
	 */
	public function set_cookie( $value = '', $expire = false ){
		$expire = $expire ?: $this->expire_time();
		$value  = $value  ?: $this->votedFor;

		setcookie( $this->cookey, $value, $expire, COOKIEPATH );

		$_COOKIE[ $this->cookey ] = $value;
	}

	public function unset_cookie(){
		setcookie( $this->cookey, null, strtotime('-1 day'), COOKIEPATH );
		$_COOKIE[ $this->cookey ] = '';
	}

	/**
	 * Устанавливает ответы в $this->poll->answers и сортирует их в нужном порядке.
	 * @return array Массив объектов
	 */
	protected function set_answers(){
		global $wpdb;

		$answers = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->democracy_a WHERE qid = %d", $this->id ) ) ;

		// если не установлен порядок
		if( $answers[0]->aorder == 0  ){
			$ord = $this->poll->answers_order ?: Dem::$opt['order_answers'];

			if( $ord == 'by_winner' || $ord == 1 )
				$answers = Dem::objects_array_sort( $answers, array('votes'=>'desc') );
			elseif( $ord == 'mix' )
				shuffle( $answers );
			elseif( $ord == 'by_id' ){}
		}
		// по порядку
		else
			$answers = Dem::objects_array_sort( $answers, array('aorder'=>'asc') );

		$answers = apply_filters('dem_set_answers', $answers );

		return $this->poll->answers = $answers;
	}

	/**
	 * Получает строку логов по ID или IP пользователя
	 * @return object/null democracy_log table row
	 */
	public function get_vote_log(){
		global $wpdb;

		$user_ip = self::get_ip();
		$AND = $wpdb->prepare('AND ip = %s', $user_ip );

		// нужно проверять юзера и IP отдельно! Иначе, если юзер не авторизован его id=0 и он будет совпадать с другими пользователями
		if( $user_id = get_current_user_id() ){
			// только для юзеров, IP не учитывается - если вы голосовали как посетитель, а потом залогинились, то можно голосовать еще раз
			$AND = $wpdb->prepare('AND userid = %d', $user_id );
			//$AND = $wpdb->prepare('AND (userid = %d OR ip = %s)', $user_id, $user_ip );
		}

		// получаем первую строку найденого лога по IP или ID юзера
		$sql = $wpdb->prepare("SELECT * FROM $wpdb->democracy_log WHERE qid = %d $AND ORDER BY logid DESC LIMIT 1", $this->id );

		return $wpdb->get_row( $sql );
	}

	/**
	 * Удаляет записи о голосовании в логах.
	 * @return bool
	 */
	protected function delete_vote_from_log(){
		global $wpdb;

		$user_ip = self::get_ip();

		// Ищем пользвоателя или IP в логах
		$sql = $wpdb->prepare("DELETE FROM $wpdb->democracy_log WHERE qid = %d AND (ip = %s OR userid = %d)", $this->id, $user_ip, get_current_user_id() );
		return $wpdb->query( $sql );
	}

	protected function add_logs(){
		if( ! $this->id ) return false;

		global $wpdb;

		$ip = self::get_ip();

		$data = array(
			'ip'      => $ip,
			'qid'     => $this->id,
			'aids'    => $this->votedFor,
			'userid'  => (int) get_current_user_id(),
			'date'    => current_time('mysql'),
			'expire'  => $this->expire_time(),
			'ip_info' => Dem::ip_info_format( $ip ),
		);

		$foo = $wpdb->insert( $wpdb->democracy_log, $data );
	}

	static function shortcode_html( $poll_id ){
		if( ! $poll_id )
			return '';
		return '<span style="cursor:pointer;padding:0 2px;background:#fff;" onclick="var sel = window.getSelection(), range = document.createRange(); range.selectNodeContents(this); sel.removeAllRanges(); sel.addRange(range);">[democracy id="'. $poll_id .'"]</span>';
	}

	static function get_ip(){
		$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
		if( ! filter_var($ip, FILTER_VALIDATE_IP) ) $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
		if( ! filter_var($ip, FILTER_VALIDATE_IP) )	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if( ! filter_var($ip, FILTER_VALIDATE_IP) )	$ip = '';

		return $ip;
	}

}
