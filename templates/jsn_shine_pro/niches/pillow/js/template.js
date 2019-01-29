//Custom JS Pillow
jQuery(document).ready(function ()	{
	(function($) {
	// Change position markup button PageBuilder
		$(".pillow-banner .pb-element-text").each(function(){
			$(this).prev('.pb-element-image').children('a').append(this);
		});
	})(jQuery);
});