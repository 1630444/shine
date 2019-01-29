<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Flickr
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
$jsnImageSourceIntagram = array(
	'name' => 'Instagram',
	'identified_name' => 'instagram',
	'type' => 'external',
	'description' => 'Instagram Description',
	'thumb' => 'plugins/jsnimageshow/sourceinstagram/assets/images/thumb-instagram.png',
	'sync'	=> false,
	'pagination' => true
);
define('JSN_IS_SOURCEINSTAGRAM', json_encode($jsnImageSourceIntagram));
