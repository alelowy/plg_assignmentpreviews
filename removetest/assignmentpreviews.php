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

        .modal-link{
            text-decoration: underline!important;
            color: #0B55C4!important;
            cursor: pointer!important;
        }

    </style>

    <script>
        $(document).ready(function(){
            $(".modal-link").click(function(){

                $(".modal.mydialog").remove(); // remove previous dialog/s

                var height = ((window.innerHeight - 60) * 0.9) + "px;";
                var modalID = $(this).data('target');
                modalID = modalID.replace('#','');
                var modalLink =  $(this).data('link');
                var modalWidth =  $(this).data('width');
                var title = "Width: " + modalWidth + "px";

                var modalDialogStyle = "";
                if( modalID === "myModal0" ){
                    modalDialogStyle += "max-width:95%;width:95%!important;";
                    title = "Width: 95%";
                }else{
                    modalDialogStyle += "max-width:" + modalWidth + "px!important;" + "width:" + modalWidth + "px!important;";
                }
                var iframeStyle = "height:" + ((window.innerHeight - 60) * 0.9) + "px;";


                var bootStrap4Modal =
                    '<div class="modal mydialog fade" id="' + modalID + '">' +
                    '<div class="modal-dialog" style="' + modalDialogStyle + '">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<h4 class="modal-title" style="" >' + title + '</h4>' +
                    '<button style="margin-top:5px;" type="button" class="btn btn-danger" data-dismiss="modal">X</button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '<iframe style="' + iframeStyle + '" src="' + modalLink + '"></iframe>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                $(this).parent().append(bootStrap4Modal);
                return true;
            });

        });
    </script>
<?php

// The class name must always be the same as the filename (in camel case)
class JFormFieldAssignmentPreviews extends JFormFieldSpacer {
	// extend from JFormFieldSpacer. This do not write a value in the registry, it is only to view.

	//The field class must know its own type through the variable $type.
	protected $type = 'assignmentpreviews';

	public function getLabel() {

		if( isset ( $this->form ) === true ){
			$fieldsForm = $this->form;

		}else{
			return '<div class="w3st-assignmentpreviews-cont"> Error: In JFormFieldAssignmentPreviews: Property "form" do not exists!</div>';
		}

		$assigned = $fieldsForm->getValue('assigned');
		$assignmentMode = $fieldsForm->getValue('assignment');

        if($assigned === null || $assignmentMode === null){
	        return '<div class="w3st-assignmentpreviews-cont"> Error: In JFormFieldAssignmentPreviews: assigned or assignment is/are null!</div>';
        }
		$assignmentMode = (string)$assignmentMode;
		$htmlFieldPreviews = "";
		$count = count( $assigned );
		$added = 0;

		JHTML::_('behavior.modal'); // for versions 3.x
		$menues = MenusHelper::getMenuLinks();


        // set default width values
        $width1 = 640;
        $width2 = 960;
        $width3 = 1024;


        // get width1, width2 and width 3 values from fiels of same form getting the first uint (/[0-9]+/).
        // check if field exists and
        $fieldSet = $fieldsForm->getFieldset('assignment_previews_field_set');
        if ( count($fieldSet) > 0 )
        {

            if ( isset($fieldSet['jform_params_assignment_previews_width_1']->value) )
            {
                if (preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_1']->value, $matches) > 0)
                {
                    $width1 = $matches[0];
                }
            }

            if ( isset($fieldSet['jform_params_assignment_previews_width_2']->value) )
            {
                if (preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_2']->value, $matches) > 0)
                {
                    $width2 = $matches[0];
                }
            }

            if (isset($fieldSet['jform_params_assignment_previews_width_3']->value)
            )
            {
                if (preg_match('/[0-9]+/', $fieldSet['jform_params_assignment_previews_width_3']->value, $matches) > 0)
                {
                    $width3 = $matches[0];
                }
            }
        }

        switch ($assignmentMode)
        {
            case "-" : // no pages
            {
                $htmlFieldPreviews .= '<p><span style="font-size:20px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_NOPAGES') . '</span></p>';
                break;
            }

            case "0": // all pages assigned
            case "1": // on selected
            {
                $allPages = false;
                if ($assignmentMode === "0")
                {
                    $htmlFieldPreviews .= '<p><span style="font-size:20px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_ALLPAGES') . '</span></p>';
                    $allPages  = true;
                }
                else
                {
                    $htmlFieldPreviews .= '<p><span style="font-size:20px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_ONSELECTED') . '</span></p>';
                }

                foreach ($menues as $menue)
                {
                    $menuModalTitle = '<hr><p><span style="font-size: 14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';;

                    $menuModalLinks = "";

                    $menuLinks = $menue->links;
                    foreach ($menuLinks as $link)
                    {
                        foreach ($assigned as $item)
                        {
                            if ($link->value === $item || $allPages === true)
                            {
                                $prevLink  = juri::root(false) . 'index.php?Itemid=' . $link->value;
                                $published = "";
                                if (isset($link->published) && (bool) $link->published === false)
                                {
                                    $published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
                                }

                                $level = "";
                                if ( isset($link->level) )
                                {
                                    $level = str_repeat(' -', $link->level) . '&nbsp;';
                                }


                                $menuModalLinks .= self::createLinks($level, $link->text, $prevLink, $width1, $width2, $width3) . $published . '<p></p>';
                                $added++;
                                if ($added >= $count && $allPages === false) // never break if all pages are assigned or break after list is filled
                                {
                                    if (empty($menuModalLinks) === false)
                                    {

                                        $htmlFieldPreviews .= $menuModalTitle . $menuModalLinks;
                                    }

                                    break(3);
                                }
                            }
                        }
                    }

                    if (empty($menuModalLinks) === false)
                    {

                        $htmlFieldPreviews .= $menuModalTitle . $menuModalLinks;
                    }
                }

                break; // switch
            }

            case "-1": // all except selected
            {

                $htmlFieldPreviews .= '<p><span style="font-size:20px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_SELECTMODE_EXCEPTSELECTED') . '</span></p>';

                foreach ($menues as $menue)
                {
                    $menuModalTitle = '<hr><p><span style="font-size:14px;font-weight:400;">' . JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_MENUTYPE') . ' <strong>' . $menue->menutype . '</strong>:</span></p>';

                    $menuModalLinks = "";
                    $menuType       = $menue->menutype;
                    $menuLinks      = $menue->links;
                    foreach ($menuLinks as $link)
                    {
                        $match = false;
                        foreach ($assigned as $item) // search for assigned in links
                        {
                            $intItem = abs(intval($item));
                            if ($intItem === intval($link->value))
                            {
                                $match = true;
                                break 1;
                            }
                        }

                        if ($match === false)
                        {
                            $prevLink = juri::root(false) . 'index.php?Itemid=' . $link->value;

                            $published = "";
                            if (isset($link->published) && (bool) $link->published === false)
                            {
                                $published = JText::_('PLG_SYSTEM_ASSIGNMENTPREVIEWS_UNPUBLISHED');
                            }

                            $level = "";
                            if (isset($link->level))
                            {
                                $level = str_repeat(' -', $link->level) . '&nbsp;';
                            }

                            $menuModalLinks .= self::createLinks($level, $link->text, $prevLink, $width1, $width2, $width3) . $published . '<p></p>';

                            $added++;
                        }
                    }

                    if (empty($menuModalLinks) === false)
                    {

                        $htmlFieldPreviews .= $menuModalTitle . $menuModalLinks;
                    }
                }
            }
            break; // switch
        }

    	return '<div class="w3st-assignmentpreviews-cont">' . $htmlFieldPreviews . '</div>';
	}

	function createLinks($level, $linkText, $prevLink , $width1, $width2, $width3){

		if (version_compare(JVERSION, '4','<')){
			$modalLink  = $level . '&nbsp;' . $linkText . '&nbsp;' .
				'<a href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: (window.innerWidth * 0.95 ) , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">[95%]</a>' .
				'<a href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width1   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">&nbsp;[' . $width1 . 'px]</a>' .
				'<a href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width2   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">&nbsp;[' . $width2 . 'px]</a>' .
				'<a href=' . $prevLink . ' style=";" class="modal" rel="{size: {x: ' . $width3   . ' , y: ( window.innerHeight  * 0.8 ) }, handler:\'iframe\'}">&nbsp;[' . $width3 . 'px]</a>';
		}
		else{
			/* working with bootstrap 4 */
			/* end modal structure */
			$modalLink  = $level . '&nbsp;' . $linkText . '&nbsp;' .
				'&nbsp;<a class="modal-link"  data-link="' . $prevLink . '" data-width="95%" data-toggle="modal" data-target="#myModal0">[95%]</a>' .
				'&nbsp;[<a class="modal-link" data-link="' . $prevLink . '" data-width="' . $width1 . '"data-toggle="modal" data-target="#myModal1">' . $width1 . '</a>]' .
				'&nbsp;[<a class="modal-link" data-link="' . $prevLink . '" data-width="' . $width2 . '"data-toggle="modal" data-target="#myModal2">' . $width2 . '</a>]' .
				'&nbsp;[<a class="modal-link" data-link="' . $prevLink . '" data-width="' . $width3 . '"data-toggle="modal" data-target="#myModal3">' . $width3 . '</a>]';
		}
		return $modalLink;
	}

}