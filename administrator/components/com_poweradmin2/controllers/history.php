<?php
/**
 * @version    $Id$
 * @package    JSN_PowerAdmin_2
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access.
defined('_JEXEC') or die();

// Register Ajax controller.
JLoader::register('JSNPowerAdmin2ControllerAjax', JPATH_COMPONENT_ADMINISTRATOR . '/controllers/ajax.php');

// Register tables class path.
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

/**
 * History controller.
 */
class JSNPowerAdmin2ControllerHistory extends JSNPowerAdmin2ControllerAjax
{

	var $_descriptionMaps = array(
		'com_menus.item' => "Menu: \n{desc}",
		'com_categories.category' => "Category description: \n{desc}",
		'com_content.article' => "Article intro text: \n{desc}",
		'com_modules.module' => "Module description: \n{desc}",
		'com_plugins.plugin' => "Plug-in description: \n{desc}",
		'com_templates.style' => "Template description: \n{desc}",
		'com_banners.banner' => "Banners: \n{desc}",
		'com_contacts.contact' => "Contacts: \r{desc}",
		'com_weblinks.weblink' => "Web Links: \r{desc}"
	);

	/**
	 * Get and output all activities of current user in database.
	 *
	 * @return  void
	 */
	public function load()
	{
		JSession::checkToken('get') or die('Invalid Token');

		// Get current user.
		$user = JFactory::getUser();

		// Get config.
		$params = JSNPowerAdmin2Helper::getConfig();

		// Query activities from database.
		$limit = $params['history_count'];

		$this->dbo->setQuery(
			"SELECT * FROM #__jsn_poweradmin2_history WHERE user_id={$user->id} AND is_deleted = 0 ORDER BY visited DESC LIMIT {$limit}");

		$histories = $this->dbo->loadObjectList();

		// Prepare activities.
		$_histories = array();

		foreach ($histories as $history)
		{
			$_history = new stdClass();

			$_history->title = ( strlen($history->title) > 40 ) ? substr($history->title, 0, 40) . ' ...' : $history->title;
			$_history->link = "index.php?option=com_poweradmin2&task=history.open&id={$history->id}&" . JSession::getFormToken() . '=1';
			$_history->css = '';
			$_history->deleted = $history->is_deleted;
			$_history->fulltitle = $history->description;

			if (!empty($_history->fulltitle))
			{
				$params = array();

				parse_str($history->object_key, $params);

				// Remove unnecessarily while space.
				$_history->fulltitle = preg_replace('/[ ]+/', ' ', $_history->fulltitle);

				// Truncate to 30 words.
				$wordsLimit = 30;

				if (str_word_count($_history->fulltitle) > $wordsLimit)
				{
					$words = explode(' ', $_history->fulltitle);
					$usableWords = array_slice($words, 0, $wordsLimit);

					$_history->fulltitle = trim(implode(' ', $usableWords), '\'".') . '...';
				}

				if (isset($params['view']) && isset($this->_descriptionMaps["{$params['option']}.{$params['view']}"]))
				{
					$_history->fulltitle = str_replace('{desc}', $_history->fulltitle,
						$this->_descriptionMaps["{$params['option']}.{$params['view']}"]);
				}
			}
			else
			{
				$_history->fulltitle = "{$history->list_page} \"{$history->title}\"";
			}

			$_histories[] = $_history;
		}

		// Set event tracking attributes.
		unset($_history);

		foreach ($_histories as &$_history)
		{
			$_history = array_merge((array) $_history, array(
				'data-event-tracking' => 'com_poweradmin2',
				'data-event-category' => 'Admin Bar',
				'data-event-action' => 'Go To History Page'
			));
		}

		$this->sendResponse(array(
			'success' => true,
			'activities' => $_histories
		));
	}

	/**
	 * Open saved activity.
	 *
	 * @return  void
	 */
	public function open()
	{
		JSession::checkToken('get') or die('Invalid Token');

		// Get history entry ID.
		$id = $this->app->input->getInt('id');

		if (empty($id))
		{
			header("Location: {$_SERVER['HTTP_RERFERER']}");
			exit();
		}

		$history = JTable::getInstance('History', 'JSNPowerAdmin2Table');

		$history->load($id);

		if ((int) $history->is_deleted)
		{
			header("Location: {$_SERVER['HTTP_RERFERER']}");
			exit();
		}

		if (empty($history->form))
		{
			$params = array();

			parse_str($history->params, $params);

			$pattern = '/' . implode('|',
				array(
					'admin',
					'config',
					'checkin',
					'cache',
					'login',
					'users',
					'menus',
					'content',
					'categories',
					'media',
					'banners',
					'contact',
					'messages',
					'newsfeeds',
					'redirect',
					'search',
					'weblinks',
					'installer',
					'modules',
					'plugins',
					'templates',
					'languages'
				)) . '/i';

			if (preg_match($pattern, $params['option']) && isset($params['view']) && isset($params['layout']))
			{
				$params['task'] = "{$params['view']}.{$params['layout']}";

				unset($params['view']);
				unset($params['layout']);

				$history->params = str_replace('&amp;', '&', http_build_query($params));
			}

			header("Location: index.php?{$history->params}");
		}
		else
		{
			$form = json_decode($history->form, true);
			$fields = '';

			// Define function to generate HTML for neccessary form fields.
			function genHtmlFormFields($data, $prefix = '')
			{
				$html = '';

				foreach ($data as $name => $value)
				{
					if (is_array($value))
					{
						$html .= genHtmlFormFields($value, empty($prefix) ? $name : "{$prefix}[{$name}]");
					}
					else
					{
						if (empty($prefix))
						{
							$html .= "<input type='hidden' name='{$name}' value='{$value}' />";
						}
						else
						{
							$html .= "<input type='hidden' name='{$prefix}[{$name}]' value='{$value}' />";
						}
					}
				}
			}

			$fields = genHtmlFormFields($form);

			echo "<form id='edit-form' action='' method='POST'>{$fields}</form>";
			echo "<script type='text/javascript'>document.getElementById('edit-form').submit()</script>";
		}

		exit();
	}

	/**
	 * Save activity.
	 *
	 * @return  void
	 */
	public function save()
	{
		JSession::checkToken('get') or die('Invalid Token');

		if ($this->app->input->getMethod() == 'GET')
		{
			exit();
		}

		//$post = $this->app->input->getArray();
		$post = JRequest::get('post');
		$session = JFactory::getSession();

		if (empty($post['pageKey']) || empty($post['title']))
		{
			exit();
		}

		if (!empty($post['postSessionKey']))
		{
			$historyId = $this->saveByPost($session, $post, $post['pageKey'], $post['title']);

			$session->clear($post['postSessionKey']);
		}
		elseif (!empty($post['lastClickedLink']))
		{
			$historyId = $this->saveByGet($session, $post, $post['pageKey'], $post['title']);
		}

		$params = JSNPowerAdmin2Helper::getConfig();
		$limit = $params['history_count'];

		$this->dbo->setQuery('SELECT id FROM #__jsn_poweradmin2_history ORDER BY visited DESC', 0, $limit);

		if ($ids = $this->dbo->loadColumn())
		{
			$this->dbo->setQuery('DELETE FROM #__jsn_poweradmin2_history WHERE id NOT IN(' . implode(', ', $ids) . ')')->execute();
		}

		echo $historyId;

		exit();
	}

	/**
	 * Save activity that has query string.
	 *
	 * @param   JSession  $session
	 * @param   mixed     $post
	 * @param   string    $pageKey
	 * @param   string    $title
	 *
	 * @return  int  ID of saved activity.
	 */
	protected function saveByGet($session, $post, $pageKey, $title)
	{
		$link = $post['lastClickedLink'];

		if ($post['lastClickedLink'] != $post['currentLink'])
		{
			if (strpos($post['lastClickedLink'], 'option=com_poweradmin2&task=history.open') !== false)
			{
				$link = $post['currentLink'];
			}

			elseif (preg_match('/[?&](cid\[\]|id)=\d+/i', $post['currentLink']) &&
				 !preg_match('/[?&](cid\[\]|id)=\d+/i', $post['lastClickedLink']))
			{
				$link = $post['currentLink'];
			}
		}

		if (preg_match('/[?&](cid\[\]|id)=\d+/i', $link) && preg_match('/[?&](cid\[\]|id)=(\d+)/i', $post['currentLink'], $matches))
		{
			$link = preg_replace('/([?&])(cid\[\]|id)=\d+/i', '\\1\\2=' . $matches[2], $link);
		}

		$params = array();

		parse_str($link, $params);

		$object_id = 0;

		if (isset($params['id']))
		{
			$object_id = $params['id'];
		}
		elseif (isset($params['cid']))
		{
			$object_id = ( is_array($params['cid']) ) ? array_shift($params['cid']) : $params['cid'];
		}
		else
		{
			foreach ($params as $key => $value)
			{
				if (preg_match('/[\-\._]?id$/i', $key) && is_numeric($value))
				{
					$object_id = $value;

					break;
				}
			}
		}

		// Skip saving activity if object id is not found.
		if ($object_id == 0)
		{
			return;
		}

		$userId = JFactory::getUser()->id;
		$history = JTable::getInstance('History', 'JSNPowerAdmin2Table');

		$history->load(array(
			'user_id' => $userId,
			'object_key' => $pageKey,
			'object_id' => $object_id
		));

		if ($history->id == null)
		{
			$history->load(array(
				'user_id' => $userId,
				'object_id' => $object_id,
				'params' => $link
			));

			if ($history->id == null)
			{
				$history->bind(array(
					'object_key' => $pageKey,
					'user_id' => $userId,
					'object_id' => $object_id
				));
			}
		}

		$history->title = $title;
		$history->params = $link;
		$history->visited = time();
		$history->component = ( empty($history->component) && !empty($post['parent']) ) ? $post['parent'] : $history->component;
		$history->list_page = ( empty($history->list_page) && !empty($post['name']) ) ? $post['name'] : $history->list_page;
		$history->list_page_params = ( empty($history->list_page_params) && !empty($post['params']) ) ? $post['params'] : $history->list_page_params;
		$history->icon = ( empty($history->icon) && !empty($post['iconPath']) ) ? $post['iconPath'] : $history->icon;
		$history->css = ( empty($history->css) && !empty($post['iconCss']) ) ? $post['iconCss'] : $history->css;
		$history->description = $post['description'];

		$get = array();

		parse_str($history->params, $get);

		if ($get['option'] == 'com_templates')
		{
			if (!isset($get['task']))
			{
				$history->params = "option=com_templates&task={$get['view']}.{$get['layout']}&id={$object_id}";

				unset($get['view']);
				unset($get['layout']);
			}

			$history->icon = 'templates/bluestork/images/menu/icon-16-themes.png';
			$history->css = 'icon-16-themes';
			$history->component = 'Template Manager';
			$history->list_page = 'Template Manager';
		}

		$history->store();

		return $history->id;
	}

	/**
	 * Save activity that has form data.
	 *
	 * @param   JSession  $session
	 * @param   mixed     $post
	 * @param   string    $pageKey
	 * @param   string    $title
	 *
	 * @return  int  ID of saved history
	 */
	protected function saveByPost($session, $post, $pageKey, $title)
	{
		if (!$session->has($post['postSessionKey']))
		{
			return;
		}

		$formData = $session->get($post['postSessionKey']);
		$formHash = md5($formData);
		$form = json_decode($formData);
		$id = $form->cid;

		if (is_array($id))
		{
			$id = array_shift($id);
		}

		$userId = JFactory::getUser()->id;
		$history = JTable::getInstance('History', 'JSNPowerAdmin2Table');

		$history->load(array(
			'user_id' => $userId,
			'object_key' => $pageKey,
			'object_id' => $id
		));

		if ($history->id == null)
		{
			$history->bind(array(
				'user_id' => $userId,
				'object_key' => $pageKey,
				'object_id' => $id
			));
		}

		$history->bind(
			array(
				'title' => $title,
				'visited' => time(),
				'form' => $formData,
				'form_hash' => $formHash,
				'component' => ( empty($history->component) && !empty($post['parent']) ) ? $post['parent'] : $history->component,
				'list_page' => ( empty($history->list_page) && !empty($post['name']) ) ? $post['name'] : $history->list_page,
				'list_page_params' => ( empty($history->list_page_params) && !empty($post['params']) ) ? $post['params'] : $history->list_page_params,
				'icon' => ( empty($history->icon) && !empty($post['iconPath']) ) ? $post['iconPath'] : $history->icon,
				'css' => ( empty($history->css) && !empty($post['iconCss']) ) ? $post['iconCss'] : $history->css
			));

		$history->description = $post['description'];

		$history->store();

		return $history->id;
	}
}
