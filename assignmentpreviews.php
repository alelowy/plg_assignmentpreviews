<?php

/**
 * @subpackage Plugin Assignment Previews
 * @copyright 2018 - 2018, Alejandro Loewy, w3smart-tools / regio-sites
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @link http://w3-smart-tools.com
 * @link http://regio-sites.de
 */

defined('_JEXEC') or die;

class plgSystemAssignmentPreviews extends JPlugin
{
	function onContentPrepareForm($form, $data)
	{
		$app = JFactory::getApplication();

		// check if user is admin
		if ($app->isAdmin())
		{
			// check if is a form
			if ($form instanceof JForm)
			{
				// check if is a module
				if($form->getName() == 'com_modules.module')
				{
					JForm::addFormPath( dirname(__FILE__).'/form' ); // adds form in /form
					$form->loadFile('com_modules.module', false); // adds form like a subform
				}
			}

			// load language after prepare form !
			$this->loadLanguage('plg_system_assignmentpreviews', JPATH_ADMINISTRATOR ); // 3.0 compatible

		}
		return true;
	}
}
