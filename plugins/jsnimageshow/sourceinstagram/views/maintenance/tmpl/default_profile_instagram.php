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
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$params = JSNUtilsLanguage::getTranslated(array(
						'JSN_IMAGESHOW_SAVE',
						'JSN_IMAGESHOW_CLOSE',
						'JSN_IMAGESHOW_CONFIRM'));
?>
<script type="text/javascript">
var objISMaintenance = null;
require(['imageshow/joomlashine/maintenance'], function (JSNISMaintenance) {
	objISMaintenance = new JSNISMaintenance({
		language: <?php echo json_encode($params); ?>
	});
});

require(['jquery'], function ($) {
	$(function () {
		function onSubmit(ciframe, imageSourceLink)
		{
			var form 				= $('#frm-edit-source-profile');
			var params 				= {};
			params.find_user 		= $('input[name="instagram_find_user"]', form).val();
			params.find_hashtag		= $('input[name="instagram_find_hashtag"]', form).val();
			params.profile_title	= $('input[name="external_source_profile_title"]', form).val();
			params.access_token		= $('input[name="instagram_access_token"]', form).val();

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
				var url  				= 'index.php?option=com_imageshow&controller=maintenance&task=checkEditProfileExist&source=instagram&external_source_profile_title=' + params.profile_title + '&external_source_id=' + <?php echo $this->sourceInfo->external_source_id; ?>;
				params.validate_url 	= 'index.php?option=com_imageshow&controller=maintenance&task=validateProfile&source=instagram&instagram_access_token=' + params.access_token + '&rand=' + Math.random();
				objISMaintenance.checkEditedProfile(url, params, ciframe, imageSourceLink);
			}
			return false;
		}

		function submitForm ()
		{
			var form = $('#frm-edit-source-profile');
				form.submit();
		}

		parent.gIframeOnSubmitFunc = onSubmit;
		gIframeSubmitFunc =submitForm;
	});
});
</script>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('INSTAGRAM_SHOWLIST_PROFILE_TITLE');?> <a class="hint-icon jsn-link-action" href="javascript:void(0);">(?)</a></label>
	<div class="controls">
		<div class="jsn-preview-hint-text">
			<div class="jsn-preview-hint-text-content clearafter">
				<?php echo JText::_('INSTAGRAM_SHOWLIST_PROFILE_TITLE');?>
				<a href="javascript:void(0);" class="jsn-preview-hint-close jsn-link-action">[x]</a>
			</div>
		</div>
		<input type="text" class="jsn-master jsn-input-xxlarge-fluid" name ="external_source_profile_title" id="external_source_profile_title" value = "<?php echo @$this->sourceInfo->external_source_profile_title;?>"/>
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
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly"  type="text" name="instagram_app_id" id="instagram_app_id" value="<?php echo @$this->sourceInfo->instagram_app_id ?>" />
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
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly"  type="text" name="instagram_secret" id="instagram_secret" value="<?php echo @$this->sourceInfo->instagram_secret ?>" />
	</div>
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
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly" type="text" name="instagram_access_token" id="instagram_access_token" value="<?php echo @$this->sourceInfo->instagram_access_token ?>" />
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
		<input class="jsn-master jsn-input-xxlarge-fluid readonly" readonly="readonly" type="text" name="instagram_current_user_id" id="instagram_current_user_id" value="<?php echo @$this->sourceInfo->instagram_current_user_id ?>" />
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
		<input class="jsn-master jsn-input-xxlarge-fluid" type="text" name="instagram_find_user" id="instagram_find_user" value="<?php echo @$this->sourceInfo->instagram_find_user ?>" />
	</div>
</div>
<input type="hidden" name="option" value="com_imageshow" />
<input type="hidden" name="controller" value="maintenance" />
<input type="hidden" name="task" value="saveprofile" id="task" />
<input type="hidden" name="source" value="instagram" />
<input type="hidden" name="external_source_id" value="<?php echo @$this->sourceInfo->external_source_id; ?>" id="external_source_id" />
<?php echo JHTML::_( 'form.token' ); ?>