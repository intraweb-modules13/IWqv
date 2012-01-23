<?php
/**
 * Show the manage module site
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @return	The configuration information
 */
function IWqv_admin_main()
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADMIN)) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	// Get module vars	
	$skins = ModUtil::getVar('IWqv','skins');
	$langs = ModUtil::getVar('IWqv','langs');
	$maxdelivers = ModUtil::getVar('IWqv','maxdelivers');
	$basedisturl = ModUtil::getVar('IWqv','basedisturl');
		
	
/*	if(!file_exists(ModUtil::getVar('IWmain', 'documentRoot').'/'.ModUtil::getVar('IWqv','attached')) || ModUtil::getVar('IWqv','attached') == ''){
		$view -> assign('noFolder', true);
	}
*/	

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	$view -> assign('security', SecurityUtil::generateAuthKey());
	$view -> assign('skins', $skins);
	$view -> assign('langs', $langs);
	$view -> assign('maxdelivers', $maxdelivers);
	$view -> assign('basedisturl', $basedisturl);
	return $view -> fetch('IWqv_admin_conf.htm');
}

/**
 * Show the module information
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @return	The module information
 */
function IWqv_admin_module(){
	$dom = ZLanguage::getModuleDomain('IWqv');
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADMIN)) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}

	$module = ModUtil::func('IWmain', 'user', 'module_info', array('module_name' => 'IWqv', 'type' => 'admin'));

	// Create output object
	$view = Zikula_View::getInstance('IWqv',false);
	$view -> assign('module', $module);
	return $view -> fetch('IWqv_admin_module.htm');
}

/**
 * Update the configuration values
 * @author: Sara Arjona TÃ©llez (sarjona@xtec.cat)
 * @params	The config values from the form
 * @return	Thue if success
 */
function IWqv_admin_confupdate($args)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	$skins = FormUtil::getPassedValue('skins', isset($args['skins']) ? $args['skins'] : null, 'POST');
	$langs = FormUtil::getPassedValue('langs', isset($args['langs']) ? $args['langs'] : null, 'POST');
	$maxdelivers = FormUtil::getPassedValue('maxdelivers', isset($args['maxdelivers']) ? $args['maxdelivers'] : null, 'POST');
	$basedisturl = FormUtil::getPassedValue('basedisturl', isset($args['basedisturl']) ? $args['basedisturl'] : null, 'POST');
	
	// Security check
	if (!SecurityUtil::checkPermission('IWqv::', "::", ACCESS_ADMIN)) {
		return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
	}
	
	// Confirm authorisation code
	if (!SecurityUtil::confirmAuthKey()) {
		return LogUtil::registerAuthidError (ModUtil::url('IWqv', 'admin', 'main'));
	}
	if (isset($skins)) ModUtil::setVar('IWqv', 'skins', $skins);
	if (isset($langs)) ModUtil::setVar('IWqv', 'langs', $langs);
	if (isset($maxdelivers)) ModUtil::setVar('IWqv', 'maxdelivers', $maxdelivers);
	if (isset($basedisturl)) ModUtil::setVar('IWqv', 'basedisturl', $basedisturl);
	
	LogUtil::registerStatus (pnML(__f('Done! %1$s updated.', __('settings', $dom), $dom)));
	return System::redirect(ModUtil::url('IWqv', 'admin', 'main'));
}

