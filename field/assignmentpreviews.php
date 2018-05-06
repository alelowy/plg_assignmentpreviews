<?php
/**
 * @subpackage Plugin Assignment Previews
 * @copyright 2018 - 2018, Alejandro Loewy, w3smart-tools / regio-sites
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @link http://w3-smart-tools.com
 * @link http://regio-sites.de
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JFormHelper::loadFieldClass('spacer');

?>
    <style>
        .w3st-assignmentpreviews-cont {
            width: 100%;
            white-space: nowrap;
            padding: 10px;
        }

    </style>


<?php


// The class name must always be the same as the filename (in camel case)
class JFormFieldAssignmentPreviews extends JFormFieldSpacer {

	//The field class must know its own type through the variable $type.
	protected $type = 'assignmentpreviews';

	public function getLabel() {

		$assigned = $this->form->getValue('assigned');
		$modalLinks = "";
		$count = count( $assigned );
		$added = 0;

		JHTML::_('behavior.modal');
		$menues = MenusHelper::getMenuLinks();
		$allPages = false;
		$exceptSelectedMode = false;
		if( $count > 0 )
		{
			if ($count === 1 && isset($assigned[0]) && $assigned[0] === "0")
			{
				$allPages   = true;
				$modalLinks .= '<h3>' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_ALL_PAGES_ASSIGNED') . '</h3>';
			}else{
				$modalLinks .= '<h3>' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_PAGES_ASSIGNED') . '</h3>';
				if( isset($assigned[0]) )
				{
					str_replace('-', '', $assigned[0], $matches );
					if( $matches === 1){
						$exceptSelectedMode = true;
					}
				}

			}

			if( $exceptSelectedMode === false )
			{
				foreach ($menues as $menue)
				{
					$modalLinks .= '<hr><p><span style="font-size: 14px;font-wheight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';

					$menuLinks = $menue->links;
					foreach ($menuLinks as $link)
					{
						foreach ($assigned as $item)
						{
							if ( $link->value === $item || $allPages === true )
							{
								$prevLink   = juri::root(false) . 'index.php?Itemid=' . $link->value;
								$published = "";
								if ( $link->published === "0"){
									$published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
								}
								$modalLink  = '<a   href=' . $prevLink .
									' style=";" class="modal" rel="{size: {x: (window.innerWidth * 0.8 ) , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">' . '<div class="icon-eye" ></div>' .
									$link->text . '</a>' . $published . '<p></p>';
								$modalLinks .= $modalLink;
								$added++;
								if ( $added >= $count && $allPages === false ) // never break if all pages are assigned or break after list is filled
								{
									break(3);
								}
							}
						}
					}
				}
			}else{
				foreach ($menues as $menue)
				{
					$modalLinks .= '<hr><p><span style="font-size: 14px;font-wheight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';

					$menuLinks = $menue->links;
					foreach ($menuLinks as $link)
					{
						$match = false;
						foreach ($assigned as $item) // search for assigned in links
						{
							$item = str_replace('-', '', $item);
							if( $item === $link->value )
							{
								$match = true;
								break 1;
							}
						}

						$item = str_replace('-', '', $item);

						if( $match === false )
						{
							$prevLink = juri::root(false) . 'index.php?Itemid=' . $link->value;

							$published = "";
							if ($link->published === "0")
							{
								$published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
							}
							$modalLink  = '<a   href=' . $prevLink .
								' style=";" class="modal" rel="{size: {x: (window.innerWidth * 0.8 ) , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">' . '<div class="icon-eye" ></div>' .
								$link->text . '</a>' . $published . '<p></p>';;
							$modalLinks .= $modalLink;
							$added++;
						}
					}
				}
			}
		}

		if( $added < 1 && $allPages === false ){
			$modalLinks = '<h3>' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_NO_ASSIGNMENTS') . '</h3>';
		}

		return '<div class="w3st-assignmentpreviews-cont">' . $modalLinks . '</div>';
	}


	public function getInput()
	{
		return "";
	}
}