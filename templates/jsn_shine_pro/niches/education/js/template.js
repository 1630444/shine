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

(function($) {
$(document).ready( function() 
	{
		// Change position markup button PageBuilder
		$(".sunfw-btn-style").each(function(){
			$(this).prev('.pb-element-image').append(this);
		});
		// Remove 'Publish' text in PageBuilder
		$("#sunfw_section_header li.articlelist-item .meta-data-wrapper .published").each(function(){
			var string = $(this).text();
			var newstring = string.substr(string.indexOf(" ") + 1);
			$(this).html(newstring).css('display','block');
		});
	});
})(jQuery);