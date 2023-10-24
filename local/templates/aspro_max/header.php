<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?if($_GET["debug"] == "y")
	error_reporting(E_ERROR | E_PARSE);
IncludeTemplateLangFile(__FILE__);
global $APPLICATION, $arRegion, $arSite, $arTheme, $bIndexBot, $bIframeMode;
$arSite = CSite::GetByID(SITE_ID)->Fetch();
$htmlClass = ($_REQUEST && isset($_REQUEST['print']) ? 'print' : false);
$bIncludedModule = (\Bitrix\Main\Loader::includeModule("aspro.max"));?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>" <?=($htmlClass ? 'class="'.$htmlClass.'"' : '')?> <?=($bIncludedModule ? CMax::getCurrentHtmlClass() : '')?>>
<head>
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TNGNTRV');</script>
	<!-- End Google Tag Manager -->
	<title><?$APPLICATION->ShowTitle()?></title>
	<?$APPLICATION->ShowMeta("viewport");?>
	<?$APPLICATION->ShowMeta("HandheldFriendly");?>
	<?$APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes");?>
	<?$APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style");?>
	<?$APPLICATION->ShowMeta("SKYPE_TOOLBAR");?>
	<?/*$APPLICATION->ShowHead();*/?>
	<?$APPLICATION->AddHeadString('<script>BX.message('.CUtil::PhpToJSObject( $MESS, false ).')</script>', true);?>
    <?$APPLICATION->ShowMeta("robots")?>
    <?$APPLICATION->ShowCSS()?>
    <?$APPLICATION->ShowHeadStrings()?>
    <?$APPLICATION->ShowHeadScripts()?>
    <?$APPLICATION->ShowMeta("description");?>
	<?if($bIncludedModule)
		CMax::Start(SITE_ID);?>
	<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/header_include/head.php'));?>
</head>
<?$bIndexBot = $bIncludedModule ? CMax::checkIndexBot() : false;?>
<body
	 class="<?=($bIndexBot ? "wbot" : "");?> site_<?=SITE_ID?> <?=($bIncludedModule ? CMax::getCurrentBodyClass() : '')?>" id="main" data-site="<?=SITE_DIR?>">

	<?if(!$bIncludedModule):?>
		<?$APPLICATION->SetTitle(GetMessage("ERROR_INCLUDE_MODULE_ASPRO_MAX_TITLE"));?>
		<center><?$APPLICATION->IncludeFile(SITE_DIR."include/error_include_module.php");?></center></body></html><?die();?>
	<?endif;?>
	<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/header_include/body_top.php'));?>

	<?$arTheme = $APPLICATION->IncludeComponent("aspro:theme.max", ".default", array("COMPONENT_TEMPLATE" => ".default"), false, array("HIDE_ICONS" => "Y"));?>
	<?include_once('defines.php');?>
	<?CMax::SetJSOptions();?>

	<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/header_include/under_wrapper1.php'));?>
	<div class="wrapper1 <?=($isIndex && $isShowIndexLeftBlock ? "with_left_block" : "");?> <?=CMax::getCurrentPageClass();?> <?$APPLICATION->AddBufferContent(array('CMax', 'getCurrentThemeClasses'))?>  ">
		<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/header_include/top_wrapper1.php'));?>

		<div class="wraps hover_<?=$arTheme["HOVER_TYPE_IMG"]["VALUE"];?>" id="content">
			<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/header_include/top_wraps.php'));?>

			<?if($isIndex):?>
				<?$APPLICATION->ShowViewContent('front_top_big_banner');?>
				<div class="wrapper_inner front <?=($isShowIndexLeftBlock ? "" : "wide_page");?> <?=$APPLICATION->ShowViewContent('wrapper_inner_class')?>">
			<?elseif(!$isWidePage):?>
				<div class="wrapper_inner <?=($isHideLeftBlock ? "wide_page" : "");?> <?=$APPLICATION->ShowViewContent('wrapper_inner_class')?>">
			<?endif;?>
				
				<div class="container_inner clearfix <?=$APPLICATION->ShowViewContent('container_inner_class')?>">
				<?if(($isIndex && ($isShowIndexLeftBlock || $bActiveTheme)) || (!$isIndex && !$isHideLeftBlock)):?>
					<div class="right_block <?=(defined("ERROR_404") ? "error_page" : "");?> wide_<?=CMax::ShowPageProps("HIDE_LEFT_BLOCK");?> <?=$APPLICATION->ShowViewContent('right_block_class')?>">
				<?endif;?>
					<div class="middle <?=($is404 ? 'error-page' : '');?> <?=$APPLICATION->ShowViewContent('middle_class')?>">
						<?CMax::get_banners_position('CONTENT_TOP');?>
						<?if(!$isIndex):?>
							<div class="container">
								<?//h1?>
								<?if($isHideLeftBlock && !$isWidePage):?>
									<div class="maxwidth-theme">
								<?endif;?>
						<?endif;?>
						<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TNGNTRV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?CMax::checkRestartBuffer();?>