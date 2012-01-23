<?php
/**
 * Delete specified qv 
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args Array with:
 * 			- qvid qv identifier
 * @return:	Show an error or status message
*/
function IWqv_ajax_deleteqv($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', '::', ACCESS_ADD)) {
	    AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Sorry! No authorization to access this module.', $dom)));
	}

	// Get the parameters
    $qvid  = FormUtil::getPassedValue('qvid', null, 'POST');
	if (ModUtil::apiFunc('IWqv', 'user', 'deleteqv', array('qvid' => $qvid))) {
	    $output = DataUtil::formatForDisplayHTML(pnML(__f('Done! %1$s deleted.', __('QV assignment', $dom), $dom)));
	} else {
		$output=AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Error! Sorry! Deletion attempt failed.', $dom)));
	}

	AjaxUtil::output(array('qvid' => $qvid,
						   'result' => $output));
}


/**
 * Delete specified user assignment
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args Array with:
 * 			- qvaid user assignment identifier
 * @return:	Show an error or status message
*/
function IWqv_ajax_deleteuserassignment($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', '::', ACCESS_ADD)) {
	    AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Sorry! No authorization to access this module.', $dom)));
	}

	// Get the parameters
    $qvaid  = FormUtil::getPassedValue('qvaid', null, 'POST');
	if (ModUtil::apiFunc('IWqv', 'user', 'deleteuserassignment', array('qvaid' => $qvaid))) {
	    $output = DataUtil::formatForDisplayHTML(pnML(__f('Done! %1$s deleted.', __('QV assignment', $dom), $dom)));
	} else {
		$output=AjaxUtil::error(DataUtil::formatForDisplayHTML(__('Error! Sorry! Deletion attempt failed.', $dom)));
	}

	AjaxUtil::output(array('qvaid' => $qvaid,
						   'result' => $output));
}