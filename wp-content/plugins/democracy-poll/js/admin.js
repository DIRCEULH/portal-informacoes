jQuery(document).ready(function($){
	'use strict';

	// EDIT POLL -----------------------
	var $answers_wrap = $('.new-poll-answers');
	if( $answers_wrap.length ){
		var focusFunction = function(){
			// проверка нужно ли добавлять поле новое
			var $li          = $(this).closest('li'),
				$nextAnsw    = $li.next('li.answ'),
				$nextAnswTxt = $nextAnsw.find('.answ-text');

			if( $nextAnsw.length ) return this;

			// добавляем поле
			$(this).addAnswField();
		};

		//var blurFunction = function(){};

		// добавляет li блок (поле нового ответа) после текущего li
		$.fn.addAnswField = function(){
			var $li  = this.closest('li'),
				$_li = $li.clone().addClass('new');

			$_li.find('input').remove();

			var $input = $('<input class="answ-text" type="text" name="dmc_new_answers[]">');
			$input.focus( focusFunction );
			$input.blur( function(){ if( $input.val() == '' ) $_li.remove(); } ); // удаляем блок, если в поле не было введено данных

			$_li.prepend( $input );
			$li.after( $_li );
			return this;
		};

		// поле с новым ответом
		$answers_wrap.find('.answ-text').focus( focusFunction );
		$answers_wrap.find('li.answ').last().addAnswField();


		// кнопки удаления
		$answers_wrap.find('li.answ').each( function(){
			$(this).append('<span class="dem-del-button">×</span>');
		} );
		// событие удаления
		$answers_wrap.on('click', '.dem-del-button', function(){
			$(this).parent('li').remove();

			// Перестроим порядок, если он вообще установлен
			if( $answers_wrap.find('li.answ:first input[name $= "[aorder]"]').val() > 0 )
				window.updateAnswersOrder();
		} );

		// datepicker
		$('input[name="dmc_end"], input[name="dmc_added"]').datepicker({ dateFormat : 'dd-mm-yy' });

		// множественный ответ и user_voted
		var $multiple = $('input[name="dmc_multiple"]'),
			$multiNum   = $multiple.parent().find('[type="number"]'),
			$users_voted = $answers_wrap.find('input[name="dmc_users_voted"]');

		// ReSet value of 'dmc_users_voted' when vote count change
		$answers_wrap.on('change.reset_users_voted', 'input[name$="[votes]"]', function(){
			if( ! $multiple.is(':checked') ){
				var sum = 0;
				$answers_wrap.find('input[name$="[votes]"]').each(function() {
					sum += Number($(this).val());
				});

				$users_voted.val( sum );
			}
		})

		$multiple.change(function(){
			$multiple.is(':checked') ? $multiNum.show().focus() : $multiNum.hide();
			$multiple.is(':checked') ? $users_voted.removeProp('readonly') : $users_voted.prop('readonly',1);

			$answers_wrap.find('input[name$="[votes]"]').first().trigger('change.reset_users_voted'); // to reset dmc_users_voted
		})
		$multiNum.change(function(){
			$multiple.val( $multiNum.val() );
		})

		// sortable - set answer order
		if(1){
			var $orderEls = $answers_wrap.find('> .answ:not(.new)');

			// для глобального доступа
			window.updateAnswersOrder = function(){
				$answers_wrap.find('> .answ:not(.new)').each(function(nn){
					$(this).find('input[name $= "[aorder]"]').val( nn+1 );
				});

				$answers_wrap.find('.reset__aorder').slideDown();
				$('.answers__order').slideUp();
			};

			// add order handle
			$orderEls.css({ position:'relative' }).prepend('<span class="sorthand dashicons dashicons-menu" style="position:absolute; left:-2.5em; margin-left:0; margin-top:.19em; cursor:move;"></span>');

			$answers_wrap.sortable({
				axis: "y",
				handle: ".sorthand",
				items: "> .answ:not(.new)",
				update: window.updateAnswersOrder
			});

			$answers_wrap.find('.reset__aorder').click(function(){
				var $elsVotes   = $answers_wrap.find('> .answ:not(.new)'),
					$elsVotesNo = $answers_wrap.find('> .answ.new, > .not__answer');

				// сбросим значения
				$elsVotes.find('input[name $= "[aorder]"]').val('0');

				// отсортируем элементы
				$elsVotes.sort(function(a, b) {
					return parseInt( $(b).find('input[name $= "[votes]"]').val() ) - parseInt( $(a).find('input[name $= "[votes]"]').val() );
				}).appendTo( $answers_wrap );

				// и в конец добавим несортируемые
				$elsVotesNo.appendTo( $answers_wrap );

				// спрячем кнопку
				$(this).slideUp();
				$('.answers__order').slideDown();
			});

		}

	}


	// DESIGN ---------------------------------------
	if(1){
		$('.dem-screen').height(function(){ return $(this).outerHeight(); } );

		$('[data-dem-act], .democracy a').click(function(e){ e.preventDefault(); }); // отменяем клики

		// предпросмотр
		var $demLoader = $(document).find('.dem-loader').first(); // loader
		$('.poll.show-loader .dem-screen').append( $demLoader.css('display','table') );

		// wpColorPicker
		$('.iris_color').wpColorPicker();


		var myOptions ={},
			$preview = $('.polls-preview');
		myOptions.change = function(event, ui){
			var hexcolor = $(this).wpColorPicker('color');
			$preview.css('background-color', hexcolor );
			//console.log( hexcolor );
		};
		$('.preview-bg').wpColorPicker( myOptions );


		// ACE ------
		var $textarea = $('[name="additional_css"]').hide(),
			aceEl     = $('<pre style="font-size:100%;"></pre>'),
			editor    = ace.edit( aceEl[0] ),
			settxval  = function(){
				$textarea.val( editor.getSession().getValue() );
			};

		$textarea.before( aceEl );

		editor.setOptions({
			maxLines: 'Infinity',
			minLines: 10,
			printMargin: false
		} );
		//    editor.getSession().setUseWrapMode( true );
		editor.setTheme("ace/theme/monokai");
		editor.getSession().setMode("ace/mode/css");
		editor.getSession().setValue( $textarea.val() );

		// set textarea value on change
		$textarea.closest('form').submit( settxval );
		editor.getSession().on('change', settxval);
		// / ACE ------

		// toggle blocks set arrows
		var $arrow = $('<div class="tog-arrow" style="cursor:pointer; font-size:80%; width:80%;text-align: right;">▼</div>').css({ position:'absolute', right:0, top:0, padding:'1em 1.2em', color:'#ccc' });
		$('.group .title').each(function(){
			var $_arrow = $arrow.clone();
			$(this).append( $_arrow );

			$_arrow.click(function(){
				if( $(this).text() == '▼' ){
					$(this).text('▲');
					var h = $(this).height() * 0.6;
					$(this).closest('.group').css('overflow','hidden').height( h );
				}
				else{
					$(this).text('▼');
					$(this).closest('.group').css('overflow','visible').height('auto');
				}
			});

		})
	}


	// POLLS LIST
	// height toggle
	var $answs = $('.compact-answ'),
		$icon  = $('<span class="dashicons dashicons-exerpt-view"></span>').click(function(){
			$(this).toggleClass('active');
			$answs.trigger('click');
		}),
		$table = $('.tablenav-pages');

	$answs.css({ cursor:'pointer'}).click(function(){
		var dataHeight = $(this).data('height') || 'auto';
		$(this).data('height', $(this).height() ).height( dataHeight );
	});

	// убедимяс что это та таблица
	if( $table.closest('.wrap').find('table .column-id').length )
		$table.prepend( $icon );

});











