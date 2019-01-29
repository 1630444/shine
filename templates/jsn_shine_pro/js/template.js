/**
 * @version    $Id$
 * @package    SUN Framework
 * @subpackage Layout Builder
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
var SunBlank = {

	_templateParams:		{},

	initOnDomReady: function()
	{
		// Setup event to update submenu position
		(function($) {

			var RtlMenu = false;
			if($("body").hasClass("sunfw-direction-rtl"))
	        RtlMenu = true;
	    else {
	        RtlMenu = false;
	    }

			SunFwUtils.setSubmenuPosition(RtlMenu,$);

		})(jQuery);
		(function($) {

			if($('.sunfw-megamenu-sub-menu ul.nav:not(.sub-menu) li.parent').length) {

				$('.sunfw-megamenu-sub-menu ul.nav li.parent > span.nav-header').append('<span class="caret"></span>');
				$('.sunfw-megamenu-sub-menu ul.nav li.parent .caret').click(function (e) {
					if ($(this).hasClass('open'))
					{
						$(this).addClass('open');
					}
					else 
					{
						$(this).removeClass('open');
					}
					if ($(this).parent().next('ul').hasClass('menuShow')) {
						$(this).parent().next('ul').addClass('menuShow');
					}
					else{
						$(this).parent().next('ul').removeClass('menuShow');
					}
					$(this).toggleClass('open');
					$(this).parent().next('ul').toggleClass('menuShow');
					e.stopPropagation();
					e.preventDefault();
				});
				$('.sunfw-megamenu-sub-menu ul.nav li.parent > a').append('<span class="caret"></span>');

				$('.sunfw-megamenu-sub-menu ul.nav li.parent .caret').click(function (e) {
					$(this).toggleClass('open');
					$(this).parent().next('ul').toggleClass('menuShow');
					e.stopPropagation();
					e.preventDefault();
				});
			}

		})(jQuery);

		// Fixed Menu Open Bootstrap
		(function($) {
			
			$('.sunfw-menu li.dropdown-submenu a.dropdown-toggle .caret, .sunfw-menu li.dropdown-submenu span.nav-header .caret, .sunfw-menu li.megamenu a.dropdown-toggle .caret').on("click", function(e){
				
				$(this).toggleClass('open');
				$(this).parent().next('ul').toggleClass('menuShow');
				e.stopPropagation();
				e.preventDefault();

			});
			//Accirdidon SideMenu at mobile device
			if (jQuery('.menu-sidemenu li.parent').length > 0) { 
				jQuery('.menu-sidemenu li.parent > :first-child').append('<span class="caret"></span>');
			};
			if ( jQuery(window).width() <= 991){
				jQuery('.menu-sidemenu li.parent span.caret').on('click', function(e){
					jQuery(this).closest('li').toggleClass('active');
					e.preventDefault();
				});	            
			}
		})(jQuery);

		// Animation Menu when hover
		(function($) {
			var timer_out;
			timer_out = setTimeout(function() {
                $('.sunfwMenuSlide .dropdown-submenu, .sunfwMenuSlide .megamenu').hover(
			        function() {
			            $('.sunfw-megamenu-sub-menu, .dropdown-menu', this).stop( true, true ).slideDown('fast');
			        },
			        function() {
			            $('.sunfw-megamenu-sub-menu, .dropdown-menu', this).stop( true, true ).slideUp('fast');
			        }
			    );

			    $('.sunfwMenuFading .dropdown-submenu, .sunfwMenuFading .megamenu').hover(
			        function() {
			            $('.sunfw-megamenu-sub-menu, .dropdown-menu', this).stop( true, true ).fadeIn('fast');
			        },
			        function() {
			            $('.sunfw-megamenu-sub-menu, .dropdown-menu', this).stop( true, true ).fadeOut('fast');
			        }
			    );
            }, 100);

		})(jQuery);

		//Scroll Top
		(function($) {
			if($('.sunfw-scrollup').length) {

			    $(window).scroll(function() {
			        if ($(this).scrollTop() > 500) {

			            $('.sunfw-scrollup').fadeIn();
			        } else {
			            $('.sunfw-scrollup').fadeOut();
			        }
			    });

			    $('.sunfw-scrollup').click(function(e) {
			    	e.preventDefault();
			        $("html, body").animate({
			            scrollTop: 0
			        }, 600);
			        return false;
			    });
			}
		})(jQuery);	
		
	},

	initOnLoad: function()
	{
		//console.log('initOnLoad');
		
	},

	stickyMenu: function (element) {
		var header       = '.sunfw-sticky';
		var stickyNavTop = jQuery(header).offset().top;

		var stickyNav = function () {

			var scrollTop    = jQuery(document).scrollTop();

			if (scrollTop > stickyNavTop) {

				jQuery(header).addClass('sunfw-sticky-open');

			} else {

				jQuery(header).removeClass('sunfw-sticky-open');

			}
		};

		stickyNav();

		jQuery(window).scroll(function() {
			stickyNav();
		});
	},

	initTemplate: function(templateParams)
	{
		// Store template parameters
		_templateParams = templateParams;

		jQuery(document).ready(function ()
		{
			SunBlank.initOnDomReady();
			
		});

		jQuery(window).load(function ()
		{
			SunBlank.initOnLoad();
			// Check sticky
			if( jQuery('.sunfw-section').hasClass('sunfw-sticky')) {
				SunBlank.stickyMenu();
			}

		});
	}
}