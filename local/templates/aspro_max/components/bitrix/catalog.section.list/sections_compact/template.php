<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$textExpand = Loc::getMessage('SHOW_STEP_ALL');
$textHide = Loc::getMessage('HIDE');
$opened = 'N';
?>
<?if($arResult["SECTIONS"]){?>
	<?global $arTheme;
	$bSlick = ($arParams['NO_MARGIN'] == 'Y');
	$bIcons = ($arParams['SHOW_ICONS'] == 'Y');?>

	<div class="landings-list__info">
		<div class="d-inline landings-list__info-wrapper flexbox flexbox--row flexbox--wrap">
			<?foreach( $arResult["SECTIONS"] as $key => $arItems ){
				$this->AddEditAction($arItems['ID'], $arItems['EDIT_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "SECTION_EDIT"));
				$this->AddDeleteAction($arItems['ID'], $arItems['DELETE_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_SECTION_DELETE_CONFIRM')));
			?>
				<div class="landings-list__item font_xs <?=($key>=3 ? 'js-hide hidden-item' : '');?>" id="<?=$this->GetEditAreaId($arItems['ID']);?>">
					<div class="">
						<a href="<?=$arItems["SECTION_PAGE_URL"]?>" class="sections-list__name landings-list__item--filled-bg box-shadow-sm rounded3"><span><?=$arItems["NAME"]?></span></a>
					</div>
				</div>
			<?}?>
            <div class="landings-list__item font_xs visible-xs">
                <span class="landings-list__name colored_theme_text_with_hover section--js-more" data-opened="<?=$opened?>" data-visible=6>
                    <span data-opened="<?=$opened?>" data-text="<?=$textHide?>"><?=$textExpand?></span><?=CMax::showIconSvg('wish ncolor', SITE_TEMPLATE_PATH.'/images/svg/arrow_showmoretags.svg');?>
                </span>
            </div>
		</div>
    </div>
<?}?>