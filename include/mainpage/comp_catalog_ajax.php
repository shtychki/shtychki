<?$bAjaxMode = (isset($_POST["AJAX_POST"]) && $_POST["AJAX_POST"] == "Y");
if($bAjaxMode)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	global $APPLICATION;
	if(\Bitrix\Main\Loader::includeModule("aspro.max"))
	{
		$arRegion = CMaxRegionality::getCurrentRegion();
	}
}?>
<?if((isset($arParams["IBLOCK_ID"]) && $arParams["IBLOCK_ID"]) || $bAjaxMode):?>
	<?
	$arIncludeParams = ($bAjaxMode ? $_POST["AJAX_PARAMS"] : $arParamsTmp);
	$arGlobalFilter = ($bAjaxMode ? $_POST["GLOBAL_FILTER"] : ($_GET['GLOBAL_FILTER'] ?? '' ));
	$signer = new \Bitrix\Main\Component\ParameterSigner();
	try {
		$componentName = CMax::partnerName . ':tabs.' . CMax::solutionName;
		$arComponentParams = $signer->unsignParameters($componentName, $arIncludeParams);
		$arGlobalFilter = strlen($arGlobalFilter) ? $signer->unsignParameters($componentName, $arGlobalFilter) : [];
	} catch (\Bitrix\Main\Security\Sign\BadSignatureException $e) {
		die($e->getMessage());
	}

	$_SERVER['REQUEST_URI'] = SITE_DIR;

	$application = \Bitrix\Main\Application::getInstance();
	$request = $application->getContext()->getRequest();

	$context = $application->getContext();
	$server = $context->getServer();

	$server_get = $server->toArray();
	$server_get["REQUEST_URI"] = $_SERVER["REQUEST_URI"];

	$server->set($server_get);

	\Aspro\Functions\CAsproMaxReCaptcha::reInitContext($application, $request);
	// $APPLICATION->reinitPath();

	$GLOBALS["NavNum"]=0;
	?>
	
	<?
	if(is_array($arGlobalFilter) && $arGlobalFilter)
		$GLOBALS[$arComponentParams["FILTER_NAME"]] = $arGlobalFilter;

	if(/*$bAjaxMode &&*/ $_REQUEST["FILTER_HIT_PROP"])
		$arComponentParams["FILTER_HIT_PROP"] = $_REQUEST["FILTER_HIT_PROP"];

	/* hide compare link from module options */
	if(CMax::GetFrontParametrValue('CATALOG_COMPARE') == 'N')
		$arComponentParams["DISPLAY_COMPARE"] = 'N';
	/**/

	if(CMax::checkAjaxRequest() && $request['ajax'] == 'y')
	{
		$arComponentParams['AJAX_REQUEST'] = 'Y';
	}

	?>
	
	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"catalog_block",
		$arComponentParams,
		false, array("HIDE_ICONS"=>"Y")
	);?>
	
<?endif;?>