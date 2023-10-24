<?php

namespace lib;
use CMaxCache;
use CMenu;
use CFile;


class CMaxMod
{
	public static function replaceMenuChilds(&$arResult, $arParams) {
		global $arTheme;

		$arMegaLinks = $arMegaItems = array();
		$replaceType = $arTheme['MEGA_MENU_STRUCTURE']['DEPENDENT_PARAMS']['REPLACE_TYPE']['VALUE'];

		$menuIblockId = CMaxCache::$arIBlocks[SITE_ID]['aspro_max_catalog']['aspro_max_megamenu'][0];
		if($menuIblockId){
			$arMenuSections = CMaxCache::CIblockSection_GetList(
				array(
					'SORT' => 'ASC',
					'ID' => 'ASC',
					'CACHE' => array(
						'TAG' => CMaxCache::GetIBlockCacheTag($menuIblockId),
						'GROUP' => array('DEPTH_LEVEL'),
						'MULTI' => 'Y',
					)
				),
				array(
					'ACTIVE' => 'Y',
					'GLOBAL_ACTIVE' => 'Y',
					'IBLOCK_ID' => $menuIblockId,
					'<=DEPTH_LEVEL' => $arParams['MAX_LEVEL'],
				),
				false,
				array(
					'ID',
					'NAME',
					'IBLOCK_SECTION_ID',
					'DEPTH_LEVEL',
					'PICTURE',
					'UF_MENU_LINK',
					'UF_MEGA_MENU_LINK',
					"UF_CATALOG_ICON",
                    'UF_MENU_ITEM_STATUS',
				)
			);

			ksort($arMenuSections);

			if($arMenuSections){
				$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
				$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);
				$some_selected = false;
				$bMultiSelect = $arParams['ALLOW_MULTI_SELECT'] === 'Y';

				foreach($arMenuSections as $depth => $arLinks){
					foreach($arLinks as $arLink){
						$url = trim($arLink['UF_MEGA_MENU_LINK']);
						$url = $url ? $url : trim($arLink['UF_MENU_LINK']);
						if(
							(
								$depth == 1 &&
								strlen($url)
							) ||
							$depth > 1
						){
							$arMegaItem = array(
								'TEXT' => htmlspecialcharsbx($arLink['NAME']),
								'NAME' => htmlspecialcharsbx($arLink['NAME']),
								'LINK' => strlen($url) ? $url : 'javascript:;',
								'SECTION_PAGE_URL' => strlen($url) ? $url : 'javascript:;',
								'SELECTED' => false,
								'PARAMS' => array(
									'PICTURE' => $arLink['PICTURE'],
									'SORT' => $arLink['SORT'],
									'SECTION_ICON' => $arLink['UF_CATALOG_ICON'],
                                    'STATUS' => $arLink['UF_MENU_ITEM_STATUS'],
								),
								'CHILD' => array(),
							);

							if( $arLink['PICTURE'] ) {
								$arMegaItem['IMAGES']['src'] = CFile::GetPath($arLink['PICTURE']);
							}

							$arMegaItems[$arLink['ID']] =& $arMegaItem;

							if($depth > 1){
								if(
									strlen($url) &&
									($bMultiSelect || !$some_selected)
								){
									$arMegaItem['SELECTED'] = CMenu::IsItemSelected($url, $cur_page, $cur_page_no_index);
								}

								if($arMegaItems[$arLink['IBLOCK_SECTION_ID']]){
									$arMegaItems[$arLink['IBLOCK_SECTION_ID']]['IS_PARENT'] = 1;
									$arMegaItems[$arLink['IBLOCK_SECTION_ID']]['CHILD'][] =& $arMegaItems[$arLink['ID']];
								}
							}
							else{
								$arMegaLinks[] =& $arMegaItems[$arLink['ID']];
							}

							unset($arMegaItem);
						}
					}
				}
			}
		}

		if($arMegaLinks){
			foreach($arResult as $key => $arItem){
				foreach($arMegaLinks as $arLink){
					if($arItem['LINK'] == $arLink['LINK']){
						if($replaceType == 'REPLACE') {
							if($arResult[$key]['PARAMS']['MEGA_MENU_CHILDS']){
								array_splice($arResult, $key, 1, $arLink['CHILD']);
							}
							else{
								$arResult[$key]['CHILD'] =& $arLink['CHILD'];
								$arResult[$key]['IS_PARENT'] = boolval($arLink['CHILD']);
							}
						} else {
							if($arResult[$key]['PARAMS']['MEGA_MENU_CHILDS']){
								if( array_key_exists('CHILD', $arResult[$key]) && $arResult[$key]['CHILD'] ) {
									$arLink['CHILD'] = self::CompareMenuItems($arResult[$key]['CHILD'], $arLink['CHILD']);
								}
								array_splice($arResult, $key, 1, $arLink['CHILD']);
							}
							else{
								$arResult[$key]['CHILD'] = self::CompareMenuItems($arResult[$key]['CHILD'], $arLink['CHILD']);
								$arResult[$key]['IS_PARENT'] = boolval($arResult[$key]['CHILD']);
							}
						}
					}
				}
			}
		}
	}
	
	public static function CompareMenuItems($parentMenu, $childMenu) {
		$arMenuEnd = $childMenu;
		foreach($parentMenu as &$parentLink) {
			foreach($childMenu as $childKey => $childLink) {
				if($childLink['LINK'] == $parentLink['LINK']) {
					$parentLink['NAME'] = $parentLink['TEXT'] = $childLink['NAME'];

					if($childLink['PARAMS']['PICTURE'] && isset($parentLink['PARAMS']['PICTURE'])) {
						$parentLink['PARAMS']['PICTURE'] = $childLink['PARAMS']['PICTURE'];
					}

					if($childLink['PARAMS']['SORT'] && isset($parentLink['PARAMS']['SORT'])) {
						$parentLink['PARAMS']['SORT'] = $childLink['PARAMS']['SORT'];
					}

					if($childLink['CHILD']) {
						if($parentLink['CHILD']) {
							$parentLink['CHILD'] = self::CompareMenuItems($parentLink['CHILD'], $childLink['CHILD']);
						} else {
							$parentLink['CHILD'] = $childLink['CHILD'];
						}
					}
					unset($arMenuEnd[$childKey]);

					if($parentLink['CHILD'] && count($parentLink['CHILD']) > 1) {
						\Bitrix\Main\Type\Collection::sortByColumn(
							$parentLink['CHILD'],
							'PARAMS',
							function($params) {
								$result = isset($params['SORT']) ? $params['SORT'] : 500;
								return $result;
							}
						);
					}
				}
			}
		}

		if($arMenuEnd) {
			$parentMenu = array_merge($parentMenu, $arMenuEnd);
		}
		\Bitrix\Main\Type\Collection::sortByColumn(
			$parentMenu,
			'PARAMS',
			function($params) {
				$result = isset($params['SORT']) ? $params['SORT'] : 500;
				return $result;
			}
		);
		unset($parentLink);

		return $parentMenu;
	}
}