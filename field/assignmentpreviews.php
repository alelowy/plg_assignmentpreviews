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
            padding: 0 10px 0 0;
        }

        .icon-eye-open::before, .icon-eye::before {
            position: relative;
            bottom: -1px;
        }

    </style>


<?php

// The class name must always be the same as the filename (in camel case)
class JFormFieldAssignmentPreviews extends JFormFieldSpacer {
    // extend from JFormFieldSpacer. This do not write a value in the registry, it is only to view.

	//The field class must know its own type through the variable $type.
    protected $type = 'assignmentpreviews';

	public function getLabel() {

		if( property_exists ( $this,'form' ) === true ){
			$fieldsForm = $this->form;

		}else{
			return '<div class="w3st-assignmentpreviews-cont"> Error: property "form" do not exists !</div>';
        }

		$assigned = $fieldsForm->getValue('assigned');

		$modalLinks = "";
		$count = count( $assigned );
		$added = 0;

		JHTML::_('behavior.modal');
		$menues = MenusHelper::getMenuLinks();
		$allPages = false;
		$exceptSelectedMode = false;

		if( $count > 0 )
		{
			// set default values
			$width1 = 640;
			$width2 = 960;
			$width3 = 1024;

			// get width1, width2 and width 3 values from fiels of same form getting the first uint (/[0-9]+/).
            // check if field exists and
			$fieldSet = $fieldsForm->getFieldset('assignment_previews_field_set');
			if( count( $fieldSet ) > 0 ){

				if( isset( $fieldSet['jform_params_assignment_previews_width_1'] ) &&
                    property_exists( $fieldSet['jform_params_assignment_previews_width_1'] ,'value') )
				{
                    if( preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_1']->value, $matches) > 0)
                    {
                        $width1 = $matches[0];
                    }
                }

				if( isset( $fieldSet['jform_params_assignment_previews_width_2'] ) &&
                    property_exists( $fieldSet['jform_params_assignment_previews_width_2'] ,'value') )
				{
					if( preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_2']->value, $matches) > 0)
					{
						$width2 = $matches[0];
					}
                }

				if( isset( $fieldSet['jform_params_assignment_previews_width_3'] ) &&
                    property_exists( $fieldSet['jform_params_assignment_previews_width_3'] ,'value') )
				{
					if( preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_3']->value, $matches) > 0)
					{
						$width3 = $matches[0];
					}
                }
            }

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
				if( $allPages === false ){
				    $modalLinks .= '<p><span style="font-size: 14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_ONSELECTED')  . '</span></p>';
				}

				foreach ($menues as $menue)
				{
					$modalLinks .= '<hr><p><span style="font-size: 14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';

					$menuLinks = $menue->links;
					foreach ($menuLinks as $link)
					{
						foreach ($assigned as $item)
						{
							if ( $link->value === $item || $allPages === true )
							{
								$prevLink   = juri::root(false) . 'index.php?Itemid=' . $link->value;
								$published = "";
								if ( property_exists ( $link, 'published' ) && $link->published === "0" ){
									$published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
								}

								$level = "";
								if( property_exists ( $link, 'level') ) {
									$level = str_repeat(' -', $link->level ) . '&nbsp;' ;
                                }

								$modalLink  = $level . '&nbsp;' .
                                    '<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: (window.innerWidth * 0.8 ) , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">' . $link->text . '</a>' .
                                    '<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width1   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width1 . ']</a>' .
                                    '<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width2   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width2 . ']</a>' .
                                    '<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width3   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width3 . ']</a>' .
                                  $published . '<p></p>';
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

				$modalLinks .= '<p><span style="font-size:14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_EXCEPTSELECTED') . '</span></p>';

				foreach ($menues as $menue)
				{
					$modalLinks .= '<hr><p><span style="font-size:14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';

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
							if ( property_exists ( $link, 'published' ) && $link->published === "0" ){
								$published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
							}

							$level = "";
							if( property_exists ( $link, 'level') ) {
								$level = str_repeat(' -', $link->level ) . '&nbsp;' ;
							}

							if ($link->published === "0")
							{
								$published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
							}
							$modalLink  = $level . '&nbsp;' .
								'<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: (window.innerWidth * 0.8 ) , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">' . $link->text . '</a>' . '&nbsp;' .
								'<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width1   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width1 . ']</a>' .
								'<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width2   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width2 . ']</a>' .
								'<a   href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width3   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}"> [' . $width3 . ']</a>' .
								$published . '<p></p>';
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

}