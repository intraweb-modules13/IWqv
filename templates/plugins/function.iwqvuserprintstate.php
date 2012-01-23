<?php
function smarty_function_iwqvuserprintstate($params, &$smarty)
{
	$dom = ZLanguage::getModuleDomain('IWqv');
	if (!isset($params['class'])) {
		$params['class'] = 'pn-menuitem-title';
	}

	$html ="<span class=\"" . $params['class'] . "\">" ;
	if (count($params['states'])>0){
		if ($params['sections']==$params['states'][2]){
			// All the sections are revised
			$html.="<img src=\"".System::getBaseUri()."/modules/IWqv/pnimages/state_corrected.gif\" alt=\"".__('Corrected', $dom)."\">";
		} else if ($params['states'][1]>0){
			// There is at least one section to revise
			$html.="<img src=\"".System::getBaseUri()."/modules/IWqv/pnimages/state_delivered.gif\" alt=\"".__('Delivered', $dom)."\">";
		} else{
			// The qv is started
			$html.="<img src=\"".System::getBaseUri()."/modules/IWqv/pnimages/state_started.gif\" alt=\"".__('Started', $dom)."\">";
		}
	}
	
	$html.="</span>\n";

	return $html;
}
