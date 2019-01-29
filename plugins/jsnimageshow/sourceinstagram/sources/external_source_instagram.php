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

class JSNExternalSourceInstagram extends JSNImagesSourcesExternal
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getCategories($config = array())
	{
		$returnXMl 		= '';
		$session 		= JFactory::getSession();
		// Load RSS ImageSource ID
		$cid	 		= JRequest::getVar( 'cid', array(0), 'get', 'array' );
		$showlist_id 	= (int) $cid[0];
		$instagram 		= self::_getInstagramObj($showlist_id);
		if ($instagram) {
			$returnXMl .= "<node label='Instagram' data=''>\n";
			if (!empty($instagram->instagram_current_user_id)) {
				$returnXMl .= "<node label='User ID: {$instagram->instagram_current_user_id}' data='current_id_{$instagram->instagram_current_user_id}'>";
				$returnXMl .= "</node>\n";
				$session->set('instagram_next_url_' . $instagram->instagram_current_user_id, '', 'jsn_instagram_session');
			}
			if (!empty($instagram->instagram_find_user)) {
				$returnXMl .= "<node label='Find By Username: {$instagram->instagram_find_user}' data='username_{$instagram->instagram_find_user}'>";
				$listUsers = '';
				try{
					$listUsers = JSNUtilsHttp::get("https://api.instagram.com/v1/users/search?q={$instagram->instagram_find_user}&access_token={$instagram->instagram_access_token}");
				} catch (Exception $e) {
					print_r($e->getMessage());
				}
				$listUsers = json_decode($listUsers['body']);
				if (!empty($listUsers->data)) {
					$users = $listUsers->data;
					foreach ($users as $i=>$user) {
						if ($user->id != $instagram->instagram_current_user_id) {
							$returnXMl .= "<node label='{$user->username}' data='username_{$instagram->instagram_find_user}/current_id_{$user->id}'>";
							$returnXMl .= "</node>\n";
							$session->set('instagram_next_url_' . $user->id, '', 'jsn_instagram_session');
						}
					}
				}
				$returnXMl .= "</node>\n";
			}
			/*if (!empty($instagram->instagram_find_hashtag)) {
				$returnXMl .= "<node label='Find By Hashtag: {$instagram->instagram_find_hashtag}' data='hashtag_{$instagram->instagram_find_hashtag}'>";
				$listTags	= '';
				try {
					$listTags	= JSNUtilsHttp::get("https://api.instagram.com/v1/tags/search?q={$instagram->instagram_find_hashtag}&access_token={$instagram->instagram_access_token}");
				} catch(Exception $e) {
					print_r($e->getMessage());
				}
				$listTags 	= json_decode($listTags['body']);
				if (!empty($listTags->data)) {
					$tags = $listTags->data;
					$count = array();
					foreach ($tags as $i=>$tag) {
						if ($tag->name != $instagram->instagram_find_hashtag) {
							$returnXMl .= "<node label='{$tag->name}' data='hashtag_{$tag->name}'>";
							$returnXMl .= "</node>\n";
							$session->set('instagram_next_tag_url_' . $tag->name, '', 'jsn_instagram_session');
						}
						// Save count
						$count[] = array('name' => $tag->name, 'value' => $tag->media_count);
					}
					$session->set('instagram_count_hashtag', json_encode($count), 'jsn_instagram_session');
					$session->set('instagram_next_tag_url_' . $instagram->instagram_find_hashtag, '', 'jsn_instagram_session');
				}
				$returnXMl .= "</node>\n";
			}*/
			$returnXMl .= "</node>\n";
		}

		return $returnXMl;
	}

	public function loadImages($config = array())
	{
		$photosList 	= array();
		$showlistId 	= JRequest::getVar('showListID', 0, 'post', 'int');
		$shortUrl 		= JRequest::getVar('cateName', '', 'post', 'string');

		if (isset($config['album'])) {
			$cid		= JRequest::getVar('cid', '', 'get', 'array');
			if (!empty($config['album']) AND !empty($cid[0])) {
				$album 		= $config['album'];
				$showlistId	= (int) $cid[0];
				$instagram 	= self::_getInstagramObj($showlistId);
				if (strpos($album, $instagram->instagram_find_user) !== false || strpos($album, $instagram->instagram_find_hashtag) !== false || strpos($album, $instagram->instagram_current_user_id) !== false)
					$shortUrl 	= $config['album'];
				else
					return null;
			}
		}

		if (!empty($showlistId)) {
			$session 		= JFactory::getSession();
			$instagram 		= $session->get('instagram_object', '', 'jsn_instagram_data');
			if (empty($instagram)) {
				$instagram 	= self::_getInstagramObj($showlistId);
			} else {
				$instagram 	= json_decode($instagram);
			}
			$accessToken 	= $instagram->instagram_access_token;
			$baseUrl		= $shortUrl;

			if (strpos($shortUrl, '/') !== false)
				$shortUrl	= substr($shortUrl, strrpos($shortUrl, '/') + 1);
			if (strpos($shortUrl, 'current_id_') !== false) {
				$userID 	= str_replace('current_id_', '', $shortUrl);
				$photosList = self::_getPhotosByUserID($userID, $accessToken, $baseUrl, $config);
			} elseif (strpos($shortUrl, 'hashtag_') !== false) {
				$hashtag 	= str_replace('hashtag_', '', $shortUrl);
				$photosList = self::_getPhotosByHashTag($hashtag, $accessToken, $baseUrl, $config);
			}
		}

		$data = new stdClass();
		$data->images = $photosList;
		return $data;
	}

	public function countImages($albumId)
	{
		$num		= 0;
		$showlistId = JRequest::getVar('showListID', 0, 'post', 'int');
		if (!empty($showlistId)) {
			$session 		= JFactory::getSession();
			$instagram 		= $session->get('instagram_object', '', 'jsn_instagram_data');
			if (empty($instagram)) {
				$instagram 	= self::_getInstagramObj($showlistId);
			} else {
				$instagram 	= json_decode($instagram);
			}
			$accessToken 	= $instagram->instagram_access_token;
		}

		if (strpos($albumId, '/') !== false)
			$albumId	= substr($albumId, strrpos($albumId, '/') + 1);
		if (strpos($albumId, 'current_id_') !== false) {
			$userID 	= str_replace('current_id_', '', $albumId);
			try {
				$user = file_get_contents("https://api.instagram.com/v1/users/{$userID}/?access_token={$instagram->instagram_access_token}");
			} catch (Exception $e) {
				print_r($e->getMessage());
			}

			$user = json_decode($user);

			if (isset($user->data->counts->media)) {
				$num = (int) $user->data->counts->media;
			}
		} elseif (strpos($albumId, 'hashtag_') !== false) {
			$hashtag 	= str_replace('hashtag_', '', $albumId);
			$count		= $session->get('instagram_count_hashtag', '', 'jsn_instagram_session');
			$count		= json_decode($count);
			if (count($count)) {
				foreach ($count as $key=>$value) {
					if ($value->name == $hashtag AND $value->value > 1) {
						$num = (int) $value->value;
					}
				}
			}
		}
		return $num;
	}

	public function saveImages($config = array())
	{
		parent::saveImages($config);

		$config 	= $this->_data['saveImages'];
		$imgExtID	= $config['imgExtID'];

		if (count($imgExtID)) {
			$objJSNImages	= JSNISFactory::getObj('classes.jsn_is_images');
			$ordering		= $objJSNImages->getMaxOrderingByShowlistID($config['showlistID']);

			if (count($ordering) < 0 || is_null($ordering)) {
				$ordering = 1;
			} else {
				$ordering = $ordering[0] + 1;
			}

			$imagesTable 	= JTable::getInstance('images', 'Table');
			$countImgExtID 	= count($imgExtID);
			for ($i = 0; $i < $countImgExtID; $i++) {
				$imagesTable->showlist_id 		= $config['showlistID'];
				$imagesTable->image_extid 		= $imgExtID[$i];
				$imagesTable->album_extid 		= $config['albumID'][$imgExtID[$i]];
				$imagesTable->image_small 		= $config['imgSmall'][$imgExtID[$i]];
				$imagesTable->image_medium 		= $config['imgMedium'][$imgExtID[$i]];
				$imagesTable->image_big			= $config['imgBig'][$imgExtID[$i]];
				$imagesTable->image_title   	= $config['imgTitle'][$imgExtID[$i]];
				if (isset($config['imgAltText'][$imgExtID[$i]]))
				{
					$imagesTable->image_alt_text	= $config['imgAltText'][$imgExtID[$i]];
				}				
				$imagesTable->ordering			= $ordering;
				$imagesTable->image_description = $config['imgDescription'][$imgExtID[$i]];
				$imagesTable->image_link 		= $config['imgLink'][$imgExtID[$i]];
				$imagesTable->custom_data 		= $config['customData'][$imgExtID[$i]];
				$imagesTable->exif_data 		= '';
				$result = $imagesTable->store(array('replcaceSpace' => false));
				$imagesTable->image_id = null;
				$ordering ++;
			}

			if ($result) {
				return true;
			}

			return false;
		}

		return false;
	}

	public function getValidation($config = array())
	{
		if (!isset($config['instagram_access_token'])) {
			$this->_errorMsg = JText::_('INSTAGRAM_MAINTEANCE_ACCESS_TOKEN_EXIST');
			return false;
		}
		$checkValid	= true;
		$objJSNInstagram = JSNISFactory::getObj('sourceinstagram.classes.jsn_is_instagram', null, null, 'jsnplugin');
		if ($objJSNInstagram->checkApplicationExists(trim($config['instagram_app_id']))) {
			$checkValid = false;
			$this->_errorMsg = JText::_('INSTAGRAM_PROFILE_EXIST', true);
		}

		return $checkValid;
	}

	protected function getInfoPhoto($imageID)
	{
		$session 		= JFactory::getSession();
		$instagram 		= $session->get('instagram_object', '', 'jsn_instagram_data');
		if (empty($instagram)) {
			return null;
		} else {
			$instagram 	= json_decode($instagram);
		}

		$accessToken 	= $instagram->instagram_access_token;
		$currentUrl 	= "https://api.instagram.com/v1/media/{$imageID}?access_token={$accessToken}";
		try {
			$data 			= JSNUtilsHttp::get($currentUrl);
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
		$data = json_decode($data['body']);
		if (!empty($data->data)) {
			$title 			= $data->data->caption->text;
			$description 	= '';
			$link 			= $data->data->images->standard_resolution->url;
		} else {
			$title = $description = $link = '';
		}

		$photo 					= array();
		$photo['title'] 		= $title;
		$photo['description'] 	= $description;
		$photo['url'] 			= $link;

		return $photo;
	}

	public function addOriginalInfo()
	{
		$data = array();

		if (is_array($this->_data['images']))
		{
			foreach ($this->_data['images'] as $img)
			{
				if ($img->custom_data == 1)
				{
					$info 	= $this->getInfoPhoto($img->image_extid);
					$img->original_title 		= (is_array($info['title'])) ? '' : trim($info['title']);
					$img->original_description 	= (is_array($info['description'])) ? '' : trim($info['description']);
					$img->original_link 		= (is_array($info['url'])) ? '' : trim($info['url']);
				}
				else
				{
					$img->original_title 		= $img->image_title;
					$img->original_description 	= $img->image_description;
					$img->original_link			= $img->image_link;
				}

				$data[] = $img;
			}
		}

		return $data;
	}

	public function getImages2JSON($config = array())
	{
		parent::getImages2JSON($config);

		$arrayImage = array();

		if (count($this->_data['images']))
		{
			foreach ($this->_data['images'] as $image)
			{
				$imageDetailObj 						= new stdClass();
				$image									= (array) $image;
				$imageDetailObj->{'thumbnail'} 		= $image['image_small'];
				$imageDetailObj->{'image'} 			= $image['image_big'];
				$imageDetailObj->{'title'} 			= $image['image_title'];
				if (isset($image['image_alt_text']))
				{
					$imageDetailObj->{'alt_text'} 		= $image['image_alt_text'];
				}
				else
				{
					$imageDetailObj->{'alt_text'} 		= $image['image_title'];
				}				
				$imageDetailObj->{'description'} 	= (!is_null($image['image_description'])) ? $image['image_description'] : '';
				$imageDetailObj->{'link'} 			= $image['image_link'];
				$imageDetailObj->exif_data			= $image['exif_data'];
				$arrayImage[]		 				= $imageDetailObj;
			}
		}
		return $arrayImage;
	}

	private function _getPhotosByUsername($username, $accessToken)
	{
		$photosList = array();
		if (!empty($username) AND !empty($accessToken)) {
			$next_url = $next_max_id = '';
			$username 		= trim($username);
			$accessToken 	= trim($accessToken);
			$currentUrl 	= "https://api.instagram.com/v1/users/search?q={$username}&access_token={$accessToken}&count=10";
			try {
				$data 			= JSNUtilsHttp::get($currentUrl);
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
			$data 			= json_decode($data['body']);
			$pagination 	= array();
			$pagination[] 	= $currentUrl;
			if (!empty($data->pagination)) {
				$next_url 		= $data->pagination->next_url;
				$next_max_id 	= $data->pagination->next_max_id;
			}
		}
	}

	private function _getPhotosByUserID($userID, $accessToken, $baseUrl = '', $config)
	{
		$photosList = array();
		if (!empty($userID) AND !empty($accessToken)) {
			$session 		= JFactory::getSession();
			$next_url 		= $next_max_id = '';
			$userID 		= trim($userID);
			$accessToken 	= trim($accessToken);
			$nextUrl		= $session->get('instagram_next_url_' . $userID, '', 'jsn_instagram_session');
			$nextUrl		= json_decode($nextUrl);
			$offset 		= ($config['offset']=='')?0:$config['offset'];
			if (!empty($nextUrl) AND !empty($offset)) {
				$currentUrl 	= $nextUrl;
			} else {
				$currentUrl 	= "https://api.instagram.com/v1/users/{$userID}/media/recent/?access_token={$accessToken}&count=33";
			}

			try {
				$data 			= JSNUtilsHttp::get($currentUrl);
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
			$data 			= json_decode($data['body']);
			if (!empty($data->pagination->next_url)) {
				$next_url		= $data->pagination->next_url;
				$session->set('instagram_next_url_' . $userID, json_encode($next_url), 'jsn_instagram_session');
			} else {
				$session->set('instagram_next_url_' . $userID, '', 'jsn_instagram_session');
			}

			// Get all current urls
			$items 			= $data->data;
			foreach ($items as $i=>$item) {
				$photo['image_title'] 		= isset($item->caption->text) ? $item->caption->text : '';
				$photo['image_extid'] 		= $item->id;
				$photo['image_small'] 		= isset($item->images->thumbnail->url) ? str_replace('http://', 'https://', $item->images->thumbnail->url) : '';
				$photo['image_medium']   	= isset($item->images->low_resolution->url) ? str_replace('http://', 'https://', $item->images->low_resolution->url) : '';
				$photo['image_big']			= isset($item->images->standard_resolution->url) ? str_replace('http://', 'https://', $item->images->standard_resolution->url) : '';
				$photo['album_extid']		= $baseUrl;
				$photo['image_link']		= isset($item->images->standard_resolution->url) ? str_replace('http://', 'https://', $item->images->standard_resolution->url) : '';
				$photo['image_description'] = '';
				array_push($photosList, $photo);
			}
		}
		return $photosList;
	}

	private function _getPhotosByHashTag($hashTag, $accessToken, $baseUrl = '', $config)
	{
		$photosList = array();
		if (!empty($hashTag) AND !empty($accessToken)) {
			$session 		= JFactory::getSession();
			$next_url 		= $next_max_id = '';
			$hashTag 		= trim($hashTag);
			$accessToken 	= trim($accessToken);
			$nextUrl		= $session->get('instagram_next_tag_url_' . $hashTag, '', 'jsn_instagram_session');
			$nextUrl		= json_decode($nextUrl);
			$offset 		= ($config['offset']=='')?0:$config['offset'];
			if (!empty($nextUrl) AND !empty($offset)) {
				$currentUrl 	= $nextUrl;
			} else {
				$currentUrl 	= "https://api.instagram.com/v1/tags/{$hashTag}/media/recent/?access_token={$accessToken}";
			}

			try {
				$data 			= JSNUtilsHttp::get($currentUrl);
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
			$data 			= json_decode($data['body']);

			if (!empty($data->pagination->next_url)) {
				$next_url			= $data->pagination->next_url;
				$session->set('instagram_next_tag_url_' . $hashTag, json_encode($next_url), 'jsn_instagram_session');
			} else {
				$session->set('instagram_next_tag_url_' . $hashTag, '', 'jsn_instagram_session');
				$countHas = $session->get('instagram_count_hashtag', '', 'jsn_instagram_session');
				if (!empty($countHas)) {
					$countHas = json_decode($countHas);
					foreach ($countHas as $i=>$item) {
						if ($item->name == $hashTag) {
							$countHas[$i]->value = count($data->data);
						}
					}
					$session->set('instagram_count_hashtag', json_encode($countHas), 'jsn_instagram_session');
				}
			}

			// Get all current urls
			$items 			= $data->data;
			foreach ($items as $i=>$item) {
				$photo['image_title'] 		= isset($item->caption->text) ? $item->caption->text : '';
				$photo['image_extid'] 		= $item->id;
				$photo['image_small'] 		= isset($item->images->thumbnail->url) ? str_replace('http://', 'https://', $item->images->thumbnail->url) : '';
				$photo['image_medium']   	= isset($item->images->low_resolution->url) ? str_replace('http://', 'https://', $item->images->low_resolution->url) : '';
				$photo['image_big']			= isset($item->images->standard_resolution->url) ? str_replace('http://', 'https://', $item->images->standard_resolution->url) : '';
				$photo['album_extid']		= $baseUrl;
				$photo['image_link']		= isset($item->images->standard_resolution->url) ? str_replace('http://', 'https://', $item->images->standard_resolution->url) : '';
				$photo['image_description'] = '';
				array_push($photosList, $photo);
			}
		}

		return $photosList;
	}

	private function _getInstagramObj($showlistID)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('image_source_profile_id'));
		$query->from($db->quoteName('#__imageshow_showlist'));
		$query->where($db->quoteName('showlist_id') . ' = ' . $db->quote($showlistID));
		$db->setQuery($query);
		$showlist = $db->loadObject();
		if (empty($showlist))
			return null;
		if (empty($showlist->image_source_profile_id))
			return null;

		$query->clear();
		$query->select($db->quoteName('external_source_id'));
		$query->from($db->quoteName('#__imageshow_source_profile'));
		$query->where($db->quoteName('external_source_profile_id') . ' = ' . $db->quote($showlist->image_source_profile_id));
		$db->setQuery($query);
		$source_id = $db->loadObject();
		if (empty($source_id))
			return null;
		$query->clear();
		$query->select('*');
		$query->from($db->quoteName('#__imageshow_external_source_instagram'));
		$query->where($db->quoteName('external_source_id') . ' = ' . $db->quote($source_id->external_source_id));
		$db->setQuery($query);
		$instagram = $db->loadObject();
		if (!empty($instagram)) {
			// Cache this object
			$session = JFactory::getSession();
			$session->set('instagram_object', json_encode($instagram), 'jsn_instagram_data');
		}

		return $instagram;
	}
}