<?php
/**
 * @version    $Id$
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');
$baseUrl = JURI::root();
?>
<script type="text/javascript">
(function($){
	$(document).ready(function () { 
		$('#get_instagram_token').click(function () {
			var baseUrl 			= '<?php echo $baseUrl ?>';
			var instagram_app_id 	= $('#instagram_app_id').val();
			var instagram_secret	= $('#instagram_secret').val();
			var callback_url 		= $('#instagram_callback_url').val();

			// Progress save app id and secret of instagram client
			if (instagram_app_id != '' && instagram_secret != '' && callback_url != '' && baseUrl != '') {
				var data = {};
				data.instagram_app_id = instagram_app_id;
				data.instagram_secret = instagram_secret;
				data.callback_url = callback_url;
				var json_data = JSON.stringify(data);
				if (json_data) {
					var c_value = escape(json_data) + ";max-age=" + 60 * 3 + "; path=/";
					document.cookie = "jsn_instagram_cookie=" + c_value;
				}
				
				var reques_url 			= 'https://api.instagram.com/oauth/authorize/?client_id=' + instagram_app_id + '&redirect_uri=' + callback_url + '&response_type=code';
				var new_window 			= window.open(reques_url, 'Get Instagram Access Token', "width=600, height=400");
				new_window.onbeforeunload = confirmExit;
				listenCookieChange();
			}

			function listenCookieChange() {
				setInterval(function () {
					confirmExit();
				}, 300);
			}
			
			function confirmExit(){
				var cookie = getCookie('jsn_instagram_access_token_cookie');
				if (cookie) {
					var data = JSON.parse(cookie);
					$('#instagram_app_id').attr('readonly', 'readonly');
					$('#instagram_secret').attr('readonly', 'readonly');
					$('#instagram_callback_url').attr('readonly', 'readonly');
					$('#instagram_access_token').val(data.access_token);
					$('#instagram_current_user_id').val(data.user.id);
					document.cookie = "jsn_instagram_access_token_cookie=;max-age=0; path=/";
					document.cookie = "jsn_instagram_cookie=;max-age=0; path=/";
				}
			}

			function getCookie(c_name) {
				var c_value = document.cookie;
				var c_start = c_value.indexOf(" " + c_name + "=");
				if (c_start == -1) {
					c_start = c_value.indexOf(c_name + "=");
				}
				if (c_start == -1) {
					c_value = null;
				} else {
					c_start = c_value.indexOf("=", c_start) + 1;
					var c_end = c_value.indexOf(";", c_start);
					if (c_end == -1) {
						c_end = c_value.length;
					}
					c_value = unescape(c_value.substring(c_start,c_end));
				}

				if (c_name == 'jsn_instagram_access_token_cookie' && c_value != null)
				{					
					c_value = c_value.replace(/\+/g, '');
				} 
				return c_value;
			}
			return false;
		});
	});
})(jQuery);
function submitFormProfile()
{
	var external_source_profile_title 	= jQuery('#external_source_profile_title').val();
	var instagram_app_id 				= jQuery('#instagram_app_id').val();
	var instagram_secret 				= jQuery('#instagram_secret').val();
	var instagram_access_token			= jQuery('#instagram_access_token').val();
	var instagram_current_user_id		= jQuery('#instagram_current_user_id').val();

	if (external_source_profile_title)
	
	var form 			= jQuery('#frm-edit-source-profile');
	var params			= {};
	params.profile_title	= jQuery('input[name="external_source_profile_title"]', form).val();
	params.access_token		= jQuery('input[name="instagram_access_token"]', form).val();
	params.app_id			= jQuery('input[name="instagram_app_id"]', form).val();
	params.secret			= jQuery('input[name="instagram_secret"]', form).val();
	params.current_user_id	= jQuery('input[name="instagram_current_user_id"]', form).val();

	if (params.profile_title == '') {
		alert('<?php echo JText::_('INSTAGRAM_MAINTENANCE_REQUIRED_FIELD_PROFILE_CANNOT_BE_LEFT_BLANK') ?>');
		return false;
	} else if (params.app_id == '') {
		alert('<?php echo JText::_('INSTAGRAM_MAINTENANCE_REQUIRED_FIELD_CLIENT_ID_CANNOT_BE_LEFT_BLANK') ?>');
		return false;
	} else if (params.secret == '') {
		alert('<?php echo JText::_('INSTAGRAM_MAINTENANCE_REQUIRED_FIELD_CLIENT_SECRET_CANNOT_BE_LEFT_BLANK') ?>');
		return false;
	} else if (params.access_token == '') {
		alert('<?php echo JText::_('INSTAGRAM_MAINTENANCE_REQUIRED_FIELD_ACCESS_TOKEN_CANNOT_BE_LEFT_BLANK') ?>');
		return false;
	} else if (params.current_user_id == '') {
		alert('<?php echo JText::_('INSTAGRAM_MAINTENANCE_REQUIRED_FIELD_USER_ID_CANNOT_BE_LEFT_BLANK') ?>');
		return false;
	} else {
		var url  				= 'index.php?option=com_imageshow&controller=maintenance&task=checkEditProfileExist&source=instagram&external_source_profile_title=' + params.profile_title + '&external_source_id=0&rand=' + Math.random();
		params.validate_url 	= 'index.php?option=com_imageshow&controller=maintenance&task=validateProfile&source=instagram&instagram_access_token=' + params.access_token + '&instagram_app_id=' + params.app_id + '&rand=' + Math.random();
		objISShowlist.checkEditedProfile(url, params);
	}
	return false;
}

</script>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_PROFILE_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_PROFILE_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" name="external_source_profile_title" id="external_source_profile_title" value="" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_APP_ID_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_APP_ID_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" name="instagram_app_id" id="instagram_app_id" value="" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_SECRET_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_SECRET_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" name="instagram_secret" id="instagram_secret" value="" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_CALLBACK_URL_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_CALLBACK_URL_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" type="text" name="instagram_callback_url" id="instagram_callback_url" value="<?php echo $baseUrl . 'plugins/jsnimageshow/sourceinstagram/classes/jsn_instagram_process.php' ?>" />
	</div>
</div>
<div class="control-group">
	<div class="btn" id="get_instagram_token"><?php echo JText::_('INSTAGRAM_GET_ACCESS_TOKEN_FIELD') ?></div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_ACCESS_TOKEN_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_ACCESS_TOKEN_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly" type="text" name="instagram_access_token" id="instagram_access_token" value="" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_CURRENT_USER_ID_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_CURRENT_USER_ID_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly" type="text" name="instagram_current_user_id" id="instagram_current_user_id" value="" />
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_FIND_USER_TITLE') ?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_FIND_USER_DESC') ?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" name="instagram_find_user" id="instagram_find_user" value="" />
	</div>
</div>
<?php echo JHTML::_('form.token'); ?>