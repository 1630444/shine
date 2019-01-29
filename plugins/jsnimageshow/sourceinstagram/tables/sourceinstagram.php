<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class TableSourceInstagram extends JTable
{
	var $external_source_id 			= null;
	var $external_source_profile_title 	= null;
	var $instagram_app_id 				= null;
	var $instagram_secret 				= null;
	var $instagram_callback_url			= null;
	var $instagram_access_token			= null;
	var $instagram_current_user_id		= null;
	var $instagram_find_user			= null;
	var $instagram_find_hashtag			= null;
	
	function __construct(&$db) {
		parent::__construct('#__imageshow_external_source_instagram', 'external_source_id', $db);
	}
}