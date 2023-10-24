<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?
global $arTheme;
$iVisibleItemsMenu = ($arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] ? $arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] : 10);
?>
<?if($arResult):?>
<?
$MENU_TYPE = $arTheme['MEGA_MENU_TYPE']['VALUE'];
$bRightSide = $arTheme['SHOW_RIGHT_SIDE']['VALUE'] == 'Y';
$RightContent = $arTheme['SHOW_RIGHT_SIDE']['DEPENDENT_PARAMS']['RIGHT_CONTENT']['VALUE'];
$bRightBanner = $bRightSide && $RightContent == 'BANNER';
$bRightBrand = $bRightSide && $RightContent == 'BRANDS';
?>
	<div class="table-menu <?=$bRightSide ? 'with_right' : ''?> ">
		<table>
			<tr>
				<?foreach($arResult as $arItem):?>
					<?$bShowChilds = $arParams["MAX_LEVEL"] > 1;
					$bWideMenu = $arItem["PARAMS"]['FROM_IBLOCK'];?>
                    <?if((isset($arItem["PARAMS"]["STATUS"]) && $arItem["PARAMS"]["STATUS"] == 1) || $arItem["PARAMS"]["CLASS"] == 'icon sale_icon'):?>
					<td class="menu-item unvisible <?=($arItem["CHILD"] ? "dropdown" : "")?> <?=($bWideMenu ? 'wide_menu' : '');?> <?=(isset($arItem["PARAMS"]["CLASS"]) ? $arItem["PARAMS"]["CLASS"] : "");?>  <?=($arItem["SELECTED"] ? "active" : "")?>">
						<div class="wrap">
                            <a class="" href="<?=$arItem["LINK"]?>">
								<div>
									<?if(isset($arItem["PARAMS"]["ICON"]) && $arItem["PARAMS"]["ICON"]):?>
										<?=CMax::showIconSvg($arItem["PARAMS"]["ICON"], SITE_TEMPLATE_PATH.'/images/svg/'.$arItem["PARAMS"]["ICON"].'.svg', '', '');?>
									<?endif;?>
									<?=$arItem["TEXT"]?>
									<?if(isset($arItem["PARAMS"]["CLASS"]) && $arItem["PARAMS"]["CLASS"] == "catalog"):?>
										<?=CMax::showIconSvg($arItem["PARAMS"]["ICON"], SITE_TEMPLATE_PATH.'/images/svg/trianglearrow_down.svg', '', '');?>
									<?endif;?>
									<div class="line-wrapper"><span class="line"></span></div>
								</div>
							</a>
							<?if($arItem["CHILD"] && $bShowChilds):?>
								<?$bRightSideShow = ($arItem['PARAMS']['BANNERS'] || $arItem['PARAMS']['BRANDS']) && $bRightSide;?>
							<?endif;?>
						</div>
					</td>
                    <?endif;?>
				<?endforeach;?>

				<td class="menu-item dropdown js-dropdown nosave unvisible">
					<div class="wrap">
						<a class="dropdown-toggle more-items" href="#">
							<span><?=\Bitrix\Main\Localization\Loc::getMessage("S_MORE_ITEMS");?></span>
						</a>
						<span class="tail"></span>
						<ul class="dropdown-menu"></ul>
					</div>
				</td>

			</tr>
		</table>
	</div>
	<script data-skip-moving="true">
		//CheckTopMenuPadding();
		//CheckTopMenuOncePadding();
		CheckTopMenuDotted();
	</script>
<?endif;?>