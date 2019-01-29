<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JSNISInstagram
{
	public function getSourceParameters()
	{
		$query = 'SELECT params FROM #__extensions WHERE element = "sourceinstagram" AND folder = "jsnimageshow"';
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
	}
	
	public function checkAccessTokenExist($accessToken)
	{
		if (!empty($accessToken)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)');
			$query->from($db->quoteName('#__imageshow_external_source_instagram'));
			$query->where($db->quoteName('instagram_access_token') . ' = ' . $db->quote($accessToken));
			$db->setQuery($query);
			if ($db->loadResult())
				return true;
			return false;
		}
		return false;
	}
	
	public function checkApplicationExists($appId)
	{
		if (!empty($appId)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)');
			$query->from($db->quoteName('#__imageshow_external_source_instagram'));
			$query->where($db->quoteName('instagram_app_id') . ' = ' . $db->quote($appId));
			$db->setQuery($query);
			if ($db->loadResult())
				return true;
			return false;
		}
		return false;
	}
}