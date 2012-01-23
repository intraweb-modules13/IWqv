<?php
/**
 * Show the list of qv that an user has got assigned and has assigned
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The list of qvs that the user has and has got assigned
*/
function IWqv_user_main($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_READ) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == ''){$uid = '-1';}
	
	$teacherassignments = ModUtil::apiFunc('IWqv', 'user', 'getall', array("teacher"=>$uid));
	$studentassignments = ModUtil::apiFunc('IWqv', 'user', 'getall', array("student"=>$uid));
								
	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	$view -> assign('teacherassignments', $teacherassignments);
	$view -> assign('studentassignments', $studentassignments);
	return $view -> fetch('IWqv_user_main.htm');
}


/**
 * Show the list of assignments to correct
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The list of assignments to correct current user
*/
function IWqv_user_show_assignments_to_correct($args)
{
	return ModUtil::func('IWqv', 'user', 'show_assignments', array("viewas"=>'teacher'));
}

/**
 * Show the list of assigned assignments
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The list of assigned assignments to the current user
*/
function IWqv_user_show_assignments_to_answer($args)
{
	return ModUtil::func('IWqv', 'user', 'show_assignments', array("viewas"=>'student'));
}

/**
 * Show the list of assignments
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  viewas: specifies the kind of assignments to show (to answer or to correct)
 * @return:	The list of assigned assignments to the current user
*/
function IWqv_user_show_assignments($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_READ) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}
	// Get the parameters
   	$viewas = FormUtil::getPassedValue('viewas', isset($args['viewas']) ? $args['viewas'] : 'student', 'POST');

	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == '') $uid = '-1';

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	
	// Get the assignments list
	switch ($viewas){
		case 'teacher':
			// Security check
			if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD)) {
				return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
			}
			$params=array("teacher"=>$uid);
			break;
		case 'student':
		default:
			$params=array("student"=>$uid);
	}
	$assignments = ModUtil::apiFunc('IWqv', 'user', 'getall', $params);
	
	$security=SecurityUtil::generateAuthKey();	
	$view->assign('security', $security);	
	$view->assign('assignments', $assignments);	
	$view->assign('viewas', $viewas);
	return $view -> fetch('IWqv_user_show_assignments.htm');
}

/**
 * Show the specified assignment
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The specified assignment information
*/
function IWqv_user_show_assignment($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Get the parameters
   	$qvid = FormUtil::getPassedValue('qvid', isset($args['qvid']) ? $args['qvid'] : null, 'REQUEST');
   	$viewas = FormUtil::getPassedValue('viewas', isset($args['viewas']) ? $args['viewas'] : 'student', 'REQUEST');
	
	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == ''){$uid = '-1';}

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	
	switch ($viewas){
		case 'teacher':
			// Security check
			if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD)) {
				return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
			}
		   	//$userid = FormUtil::getPassedValue('userid', isset($args['userid']) ? $args['userid'] : null, 'REQUEST');
			// Get the qv and the list of assignments
			$qv = ModUtil::apiFunc('IWqv', 'user', 'get', array('qvid'=>$qvid, 'userid'=>$userid));
			$assignments = ModUtil::apiFunc('IWqv', 'user', 'getteacherassignments', array('qv'=>$qv, 'teacher'=>$uid));
			$security=SecurityUtil::generateAuthKey();	
			$view->assign('security', $security);	
			$view->assign('qv', $qv);	
			$view->assign('assignments', $assignments);	
			$view->assign('viewas', $viewas);
			return $view -> fetch('IWqv_user_show_teacher_assignments.htm');
			
		case 'student':
		default:
			// Security check
			if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_READ)) {
				return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
			}
			$userid = $uid;
			// Get the qv and the assignment
			$qv = ModUtil::apiFunc('IWqv', 'user', 'get', array('qvid'=>$qvid, 'userid'=>$userid));
			$assignment = ModUtil::apiFunc('IWqv', 'user', 'getassignment', array('qv'=>$qv, 'userid'=>$userid, 'viewas'=>$viewas));
	}	


	$security=SecurityUtil::generateAuthKey();	
	$view->assign('security', $security);	
	$view->assign('qv', $qv);	
	$view->assign('assignment', $assignment);	
	return $view -> fetch('IWqv_user_show_assignment.htm');
}


/**
 * Show the form to create/edit an assignment
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The form to create/edit an assignment
*/
function IWqv_user_assignment_form($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get the parameters
   	$qvid = FormUtil::getPassedValue('qvid', isset($args['qvid']) ? $args['qvid'] : null, 'REQUEST');

	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == ''){$uid = '-1';}

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	
	// Get the values for some of the parameters
	$skins = ModUtil::apiFunc('IWqv', 'user', 'getskins');
	$langs = ModUtil::apiFunc('IWqv', 'user', 'getlangs');
	$maxdelivers = ModUtil::apiFunc('IWqv', 'user', 'getmaxdelivers');
	$targets = ModUtil::apiFunc('IWqv', 'user', 'gettargets');
	
	$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
	$groups = ModUtil::func('IWmain', 'user', 'getAllGroups', array('sv' => $sv,
																	'plus' => __('Select one...', $dom),
																	'less' => ModUtil::getVar('iw_myrole', 'rolegroup')));

	if(isset($qvid)){
		$actiontext=__('Edit', $dom);
		// Get the assignment
		$assignment = ModUtil::apiFunc('IWqv', 'user', 'get', array("qvid"=>$qvid));
	}else{
		$actiontext=__('Add', $dom);
	}	
	
	$security=SecurityUtil::generateAuthKey();
	$view->assign('security', $security);
	$view->assign('qvid', $qvid);
	$view->assign('skins', $skins);
	$view->assign('langs', $langs);
	$view->assign('maxdelivers', $maxdelivers);
	$view->assign('targets', $targets);
	$view->assign('groups', $groups);
	$view->assign('assignment', $assignment);
	$view->assign('actiontext', $actiontext);
	
	return $view -> fetch('IWqv_user_assignment_form.htm');
}



/**
 * Receive the information from the form and create a new entry in the database
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  Array with the values sended from the form
 * @return:	True if success
*/
function IWqv_user_create_assignment($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get the parameters
   	$qvid = FormUtil::getPassedValue('qvid', isset($args['qvid']) ? $args['qvid'] : null, 'POST');
   	$groups = FormUtil::getPassedValue('groups', isset($args['groups']) ? $args['groups'] : null, 'POST');
   	$title = FormUtil::getPassedValue('title', isset($args['title']) ? $args['title'] : null, 'POST');
   	$description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');
   	$url = FormUtil::getPassedValue('url', isset($args['url']) ? $args['url'] : null, 'POST');
   	$skin = FormUtil::getPassedValue('skin', isset($args['skin']) ? $args['skin'] : null, 'POST');
   	$qvlang = FormUtil::getPassedValue('qvlang', isset($args['qvlang']) ? $args['qvlang'] : null, 'POST');
   	$maxdeliver = FormUtil::getPassedValue('maxdeliver', isset($args['maxdeliver']) ? $args['maxdeliver'] : null, 'POST');
   	$showcorrection = FormUtil::getPassedValue('showcorrection', isset($args['showcorrection']) ? $args['showcorrection'] : null, 'POST');
   	$showinteraction = FormUtil::getPassedValue('showinteraction', isset($args['showinteraction']) ? $args['showinteraction'] : null, 'POST');
   	$ordersections = FormUtil::getPassedValue('ordersections', isset($args['ordersections']) ? $args['ordersections'] : null, 'POST');
   	$orderitems = FormUtil::getPassedValue('orderitems', isset($args['orderitems']) ? $args['orderitems'] : null, 'POST');
   	$target = FormUtil::getPassedValue('target', isset($args['target']) ? $args['target'] : null, 'POST');
   	$width = FormUtil::getPassedValue('width', isset($args['width']) ? $args['width'] : null, 'POST');
   	$height = FormUtil::getPassedValue('height', isset($args['height']) ? $args['height'] : null, 'POST');
   	$observations = FormUtil::getPassedValue('observations', isset($args['observations']) ? $args['observations'] : null, 'POST');
   	$active = FormUtil::getPassedValue('active', isset($args['active']) ? $args['active'] : 0, 'POST');

	// Confirm authorization code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (ModUtil::url('IWqv', 'user', 'assignment_form'));
	}
	
	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == ''){$uid = '-1';}

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	
	// Create the new record
	$assignment = array('teacher' => $uid,
						'groups' => $groups,
						'title' => $title,
						'description' => $description,
						'url' => $url,
						'skin' => $skin,
						'lang' => $qvlang,
						'maxdeliver' => $maxdeliver,
						'showcorrection' => $showcorrection,
						'showinteraction' => $showinteraction,
						'ordersections' => $ordersections,
						'orderitems' => $orderitems,
						'target' => $target,
						'width' => $width,
						'height' => $height,
						'observations' => $observations,
						'active' => $active);
	if (isset($qvid) && $qvid!=null){
		$assignment['qvid']=$qvid;
		$isupdate=true;
	}
	
	$qvid = ModUtil::apiFunc('IWqv', 'user', 'createqv', $assignment);
	if (!$qvid) {
		if ($isupdate) return LogUtil::registerError (__('Error! Update attempt failed.', $dom));
		else return LogUtil::registerError (__('Error! Creation attempt failed.', $dom));
	}

	// Creation successfully, so the user will be notified        
	if ($isupdate) LogUtil::registerStatus (pnML(__f('Done! %1$s updated.', __('User assignment', $dom), $dom)));
	else LogUtil::registerStatus (pnML(__f('Done! %1$s created.', __('QV assignment', $dom), $dom)));
	System::redirect(ModUtil::url('IWqv', 'user', 'show_assignment', array("qvid"=>$qvid, 'viewas'=>'teacher')));
	return true;
//	return ModUtil::func('IWqv', 'user', 'show_assignment', array('qvid'=>$qvid, 'viewas'=>'teacher'));
}



/**
 * Show the form to create/edit an user assignment
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @return:	The form to create/edit an user assignment
*/
function IWqv_user_user_assignment_form($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get the parameters
   	$qvid = FormUtil::getPassedValue('qvid', isset($args['qvid']) ? $args['qvid'] : null, 'REQUEST');
   	$userid = FormUtil::getPassedValue('userid', isset($args['userid']) ? $args['userid'] : null, 'REQUEST');

	// Needed argument
	if ( (!isset($qvid) || !is_numeric($qvid) || !isset($userid) || !is_numeric($userid)) ) {
		return LogUtil::registerError (__('Error! Could not do what you wanted. Please check your input.', $dom));
	}
	$assignment = ModUtil::apiFunc('IWqv', 'user', 'getassignment', array("qvid"=>$qvid, "userid"=>$userid));
	
	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	$view->assign('security', SecurityUtil::generateAuthKey());
	$view->assign('qvid', $qvid);
	$view->assign('userid', $userid);
	$view->assign('assignment', $assignment);
	return $view -> fetch('IWqv_user_user_assignment_form.htm');
}




/**
 * Receive the information from the form and create/update a new entry in the database
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  Array with the values sended from the form
 * @return:	True if success
*/
function IWqv_user_create_user_assignment($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADD) || !UserUtil::isLoggedIn()) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get the parameters
   	$qvid = FormUtil::getPassedValue('qvid', isset($args['qvid']) ? $args['qvid'] : null, 'POST');
   	$userid = FormUtil::getPassedValue('userid', isset($args['userid']) ? $args['userid'] : null, 'POST');
   	$teachercomments = FormUtil::getPassedValue('teachercomments', isset($args['teachercomments']) ? $args['teachercomments'] : null, 'POST');
   	$teacherobservations = FormUtil::getPassedValue('teacherobservations', isset($args['teacherobservations']) ? $args['teacherobservations'] : null, 'POST');

	// Confirm authorization code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (ModUtil::url('IWqv', 'user', 'user_assignment_form'));
	}
	
	// Get user identity
	$uid = UserUtil::getVar('uid');
	if($uid == ''){$uid = '-1';}

	// Create or update the new record
	$assignment = ModUtil::apiFunc('IWqv', 'user', 'updateassignment', array('qvid'=>$qvid, 'userid'=>$userid, 'teachercomments' => $teachercomments, 'teacherobservations' => $teacherobservations));
	if (!$assignment) {
		return LogUtil::registerError (__('Error! Update attempt failed.', $dom));
	}

	// Creation successfully, so the user will be notified
	LogUtil::registerStatus (pnML(__f('Done! %1$s updated.', __('User assignment', $dom), $dom)));	
//	return ModUtil::func('IWqv', 'user', 'show_assignment', array('qvid'=>$qvid, 'viewas'=>'teacher'));
	System::redirect(ModUtil::url('IWqv', 'user', 'show_assignment', array("qvid"=>$qvid, 'viewas'=>'teacher')));
	return true;
}



/**
 * Process the xml bean request and generates its response
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @param:	args  -
 * @param:	HTTP_RAW_POST_DATA Way to get the XMLRequest
 * @return:	An String with the response to the bean request
*/
function IWqv_user_beans($args)
{
	// Get the XML
	if($GLOBALS["HTTP_RAW_POST_DATA"]){
	  $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
	} else {
	  $xml = $HTTP_RAW_POST_DATA;
	}
	if (!isset($xml)){
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="get_sections" ><param name="assignmentid" value="6"/></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="get_section" ><param name="assignmentid" value="6"/><param name="sectionid" value="1212751335006440"/></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="save_section" ><param name="assignmentid" value="6"/><param name="sectionid" value="1212751335006440"/><responses><![CDATA[save_responses 123]]></responses></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="deliver_section" ><param name="assignmentid" value="6"/><param name="sectionid" value="121261124124135"/><param name="responses"><![CDATA[deliver_responses123]]></param><param name="scores"><![CDATA[deliver_scores123]]></param></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="correct_section" ><param name="assignmentid" value="6"/><param name="sectionid" value="1212751335006440"/><responses><![CDATA[correct_responses123]]></responses><scores><![CDATA[correct_scores123]]></scores></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="add_message" ><param name="assignmentid" value="6"/><param name="sectionid" value="1212751335006440x1"/><param name="itemid" value="1212752136191708"/><param name="userid" value="2"/><message><![CDATA[message_1]]></message></bean>';
		//$xml = '<?'.'xml version="1.0" encoding="UTF-8"?'.'><bean id="get_messages" ><param name="assignmentid" value="6"/><param name="sectionid" value="1212751335006440"/></bean>';
	}
	
	if (!isset($xml)) {
		// XML not specified and it's mandatory
		$result ='<bean id="">';
		$result.=' <param name="error" value="error_bean_not_defined"/>';
		$result.='</bean>';
	}else{
		// Get and process the XML Request
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$bean = $doc->getElementsByTagName("bean")->item(0);
		$result = ModUtil::apiFunc('IWqv', 'user', 'processbean', array('bean'=>$bean));
	}
	
	// Generate the XML output
	header("Content-Type:text/xml;charset=UTF-8");
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public"); 
	header("Content-Description: File Transfer");
	
	$result='<?xml version="1.0" encoding="UTF-8"?>'.$result;
	echo $result;
	exit;
}
