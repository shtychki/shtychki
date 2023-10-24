<?//set common properties
if (!empty($arResult['PROPERTIES']['ID']['VALUE']))
{
	if($arParams["SECTION_COMMON_PROPERTIES"])
	{
		$arResult['ITEM_PROPS_CNT'] = 0;
		$arCPropData = array();
		$CPropCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID'=> $arResult['ID']);
		$propCache = new CPHPCache();
		if ($propCache->InitCache(3600000, serialize($CPropCacheID), "/sections")){
			$arCPropData = $propCache->GetVars();
		}elseif ($propCache->StartDataCache()){
			$prItems = array();
			$prItemsN = array();
			$ar_color = array();			
			$tmp_color = '';
			
			foreach($arParams["SECTION_COMMON_PROPERTIES"] as $propID) {
				$rsProps = CUserFieldEnum::GetList(array(), array("ID" => $propID));
				if($arPropsID = $rsProps->GetNext())
					$propCode = $arPropsID['XML_ID'];
					$propName = trim($arPropsID['VALUE']);
				if($propCode)
				{
					$propCode = 'PROPERTY_'.$propCode;

					$db_res = CIBlockElement::GetList(array(), array('IBLOCK_ID' => 119, 'ACTIVE' => 'Y', '=PROPERTY_ID' => $arResult['PROPERTIES']['ID']['VALUE']), false, array(), array('IBLOCK_ID', 'ID', 'NAME', 'SORT', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', $propCode));
					$i = 0;
					
					while ($res = $db_res->GetNext())
					{
						if(!empty($res[mb_convert_case($propCode, MB_CASE_UPPER, "UTF-8").'_VALUE'])){
							if($res['PREVIEW_PICTURE']){
								$res['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
									$res['PREVIEW_PICTURE'],
									array("width" => 50, "height" => 50),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);
							} else {
								$res['PREVIEW_PICTURE']['src'] = '/bitrix/templates/aspro_next/images/no_photo_medium.png';
							}
							$sort = $res['ID'];
							if($arResult['ID'] == $res['ID'])
								$sort = 1;
							$val = '';
							if($propCode == 'PROPERTY_COLOR_REF2')
							{
								if (CModule::IncludeModule('highloadblock')) {
									$arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(22)->fetch();
									$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
									$strEntityDataClass = $obEntity->getDataClass();
									$resData = $strEntityDataClass::getList(array(
										'select' => array('UF_NAME', 'UF_LINK'),
										'filter' => array('UF_XML_ID' => $res['PROPERTY_COLOR_REF2_VALUE']),
										'order'  => array('ID' => 'ASC'),
										'limit'  => 100,
									));
									if ($arItem = $resData->Fetch()) {
										$val = $arItem["UF_NAME"];
										$val_link = $arItem["UF_LINK"];
									}
								}
								if ((array_search(trim($val), $ar_color) === false) && $res['PROPERTY_COLOR_REF2_VALUE'] != $arResult['PROPERTIES']['COLOR_REF2']['VALUE']) {
									$prItems[$propName][$sort] = array(
										'NAME' 	=> $res['PREVIEW_PICTURE'],
										'LINK' 	=> $res['DETAIL_PAGE_URL'],
										'VALUE' => trim($val),
										'ID'	=> $res['ID'],
										'SORT'	=> $sort,
										'CODE'	=> $arPropsID['XML_ID'],
										'COLOR'	=> $val_link
									);
									$ar_color[] = trim($val);
								} elseif ($arResult['ID'] == $res['ID']) {
									$prItems[$propName][$sort] = array(
										'NAME' 	=> $res['PREVIEW_PICTURE'],
										'LINK' 	=> $res['DETAIL_PAGE_URL'],
										'VALUE' => trim($val),
										'ID'	=> $res['ID'],
										'SORT'	=> $sort,
										'CODE'	=> $arPropsID['XML_ID'],
										'COLOR'	=> $val_link,
										'TYPE'	=> 'pic'
									);
								}
								$tmp_color = $prItems[$propName][1]['COLOR'];
							}

							if ((strpos($res['DETAIL_PAGE_URL'], $tmp_color) !== false) || ($tmp_color == '')) {
								if ($propCode == 'PROPERTY_MG_DLINA_KABELYA') {
									$res['PREVIEW_PICTURE'] = '';
									$prItems[$propName][(int)$res['PROPERTY_MG_DLINA_KABELYA_VALUE']] = array(
										'NAME' 	=> $res['PREVIEW_PICTURE'],
										'LINK' 	=> $res['DETAIL_PAGE_URL'],
										'VALUE' => $res['PROPERTY_MG_DLINA_KABELYA_VALUE'],
										'ID'	=> $res['ID'],
										'SORT'	=> $sort,
										'CODE'	=> $arPropsID['XML_ID'],
										'TYPE'	=> 'number'
									);
								}
								if ($propCode == 'PROPERTY_MG_OBEM') {
									$res['PREVIEW_PICTURE'] = '';
									$prItems[$propName][(int)$res['PROPERTY_OBEM_VALUE']] = array(
										'NAME' 	=> $res['PREVIEW_PICTURE'],
										'LINK' 	=> $res['DETAIL_PAGE_URL'],
										'VALUE' => $res['PROPERTY_OBEM_VALUE'],
										'ID'	=> $res['ID'],
										'SORT'	=> $sort,
										'CODE'	=> '',
										'TYPE'	=> 'number'
									);
								}
							}
							$i++;
						}
					}
					if($i > 1)
						$arResult['ITEM_PROPS_CNT'] = $i;
				}
			}
			$arCPropData = $prItems;
			$propCache->EndDataCache($arCPropData);
		}
		$arResult['ITEM_PROPS'] = $arCPropData;
	}
}