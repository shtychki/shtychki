<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc,
	  \Bitrix\Main\Web\Json;?>
<?if($arResult["ITEMS"]):?>
	<div id="bigdata_recommended_products_Zz2YMH_items" class="bigdata_recommended_products_items">
		<div class="font_md darken subtitle option-font-bold"><?=Loc::getMessage("RECOMMENDED")?></div>
		<div class="block-items swipeignore">
			<?foreach ($arResult['ITEMS'] as $key => $arItem){?>
				<?$strMainID = $this->GetEditAreaId($arItem['ID'] . $key);?>
				<div class="block-item bordered rounded3 box-shadow">
					<div class="block-item__wrapper colored_theme_hover_bg-block" id="<?=$strMainID;?>">
						<div class="block-item__inner flexbox flexbox--row">
							<?
							$totalCount = CMax::GetTotalCount($arItem, $arParams);
							$arQuantityData = CMax::GetQuantityArray($totalCount);
							$arItem["FRONT_CATALOG"]="Y";
							$arItem["RID"]=$arResult["RID"];
							$arAddToBasketData = CMax::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);

							$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

							$strMeasure='';
							if($arItem["OFFERS"])
							{
								$strMeasure=$arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
							}
							else
							{
								if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"]))
								{
									$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
									$strMeasure=$arMeasure["SYMBOL_RUS"];
								}
							}
							$arItem["DETAIL_PAGE_URL"] .= ($arResult["RID"] ? '?RID='.$arResult["RID"] : '');
							?>

							<div class="block-item__image block-item__image--wh80">
								<?$arItem["BIG_DATA"] = "Y";?>
								<?\Aspro\Functions\CAsproMaxItem::showImg($arParams, $arItem, false);?>
							</div>
							<div class="block-item__info item_info">
								<div class="block-item__title">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark-color font_xs"><span><?=$elementName?></span></a>
								</div>
								<div class="block-item__cost cost prices clearfix">
									<?if($arItem["OFFERS"]):?>
										<?\Aspro\Functions\CAsproMaxSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, array(), 'Y');?>
									<?else:?>
										<?
										if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
										{?>
											<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
												<?=CMax::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));?>
											<?endif;?>
											<?=CMax::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);?>
										<?
										}
										elseif($arItem["PRICES"])
										{?>
											<?\Aspro\Functions\CAsproMaxItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id, 'Y');?>
										<?}?>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?}?>
		</div>
	</div>
<?endif;?>