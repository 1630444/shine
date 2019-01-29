(function($) {
	$(document).ready(function () {
		$('.sport-head > div').append('<div class="bg-head-menu"></div>');
		$('.sport-banner > div').append('<div class="bg-head-banner"><div class="bg"></div></div>');
		$('.sport-top > div').append('<div class="bg-head-top"><div class="bg"></div></div>');

		$('.search.sport-search i.fa').click(function () {
			$('.search.sport-search').toggleClass('open');
		});
	});
})(jQuery);