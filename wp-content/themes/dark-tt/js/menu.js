jQuery(document).ready(function($) {
	$(window).scroll(function(e){
		$el = $('#access');
		if ( $(window).width() > 600) {
			if ($(this).scrollTop() > 250 && $el.css('position') != 'fixed'){
				$el.addClass("fixedMenu");
				$('.scroll-top').show('slow');
			}
			else if ($(this).scrollTop() < 250 && $el.css('position') == 'fixed'){
				$el.removeClass("fixedMenu");
				$('.scroll-top').hide('slow');
			}
		}
		else {
			$el.removeClass("fixedMenu");
			$('.scroll-top').hide('slow');
		}
	});

	$('.scroll-top').click(function(e){
		e.preventDefault();
		scroller('#wrapper');
	});

	function scroller(hash) {
        var lastElementTop = $(hash).position().top;
        var scrollAmount = lastElementTop - 250;
        $('html,body').animate({scrollTop: scrollAmount},800);
    };
});