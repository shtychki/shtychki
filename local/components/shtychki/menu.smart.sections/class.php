<?
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");

class CCustomSmartMenuSections extends CBitrixCatalogSmartFilter
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 36000000;
        $arParams["IBLOCK_ID"] = (int)$arParams["IBLOCK_ID"];
        $arParams["SECTION_ID"] = (int)$arParams["SECTION_ID"];
        if ($arParams["SECTION_ID"] <= 0 && Loader::includeModule('iblock')) {
            $arParams["SECTION_ID"] = CIBlockFindTools::GetSectionID(
                $arParams["SECTION_ID"],
                $arParams["SECTION_CODE"],
                array(
                    "GLOBAL_ACTIVE" => "Y",
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                )
            );
            if (!$arParams["SECTION_ID"] && strlen($arParams["SECTION_CODE_PATH"]) > 0) {
                $arParams["SECTION_ID"] = CIBlockFindTools::GetSectionIDByCodePath(
                    $arParams["IBLOCK_ID"],
                    $arParams["SECTION_CODE_PATH"]
                );
            }
        }
        return $arParams;
    }

    public function getFilters($params)
    {
//				$arResult["SECTIONS"][$arSection["ID"]] = array(
//					"ID" => $arSection["ID"],
//					"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
//					"~NAME" => $arSection["~NAME"],
//					"~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
//				);

        $this->facet->setSectionId($params['SECTION_ID']);
        $tmpResult["FACET_FILTER"] = array_merge(array(
            "ACTIVE_DATE" => "Y",
            "CHECK_PERMISSIONS" => "Y",
        ), (array)$this->arParams['FILTER']);
//$arResult["FACET_FILTER"]['CATALOG_AVAILABLE'] = 'Y';
//				$this->SECTION_ID = $arSection["ID"];
        $tmpResult['ITEMS'] = $this->getIBlockItems($this->arParams['IBLOCK_ID']);
        $res = $this->facet->query($tmpResult["FACET_FILTER"]);
        CTimeZone::Disable();
//                pre($arResult['ITEMS']);
        while ($row = $res->fetch()) {
            $facetId = $row["FACET_ID"];
            if (\Bitrix\Iblock\PropertyIndex\Storage::isPropertyId($facetId)) {
                $PID = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId($facetId);
                if (!in_array($PID, $this->arParams['PROPERTIES'])) {
                    unset($tmpResult['ITEMS'][$PID]);
                    continue;
                }
                if ($tmpResult["ITEMS"][$PID]["PROPERTY_TYPE"] == "N") {
                    $this->fillItemValues($tmpResult["ITEMS"][$PID], $row["MIN_VALUE_NUM"]);
                    $this->fillItemValues($tmpResult["ITEMS"][$PID], $row["MAX_VALUE_NUM"]);
                    if ($row["VALUE_FRAC_LEN"] > 0)
                        $tmpResult["ITEMS"][$PID]["DECIMALS"] = $row["VALUE_FRAC_LEN"];
                } elseif ($tmpResult["ITEMS"][$PID]["DISPLAY_TYPE"] == "U") {
                    $this->fillItemValues($tmpResult["ITEMS"][$PID], FormatDate("Y-m-d", $row["MIN_VALUE_NUM"]));
                    $this->fillItemValues($tmpResult["ITEMS"][$PID], FormatDate("Y-m-d", $row["MAX_VALUE_NUM"]));
                } elseif ($tmpResult["ITEMS"][$PID]["PROPERTY_TYPE"] == "S") {
                    $addedKey = $this->fillItemValues($tmpResult["ITEMS"][$PID], $this->facet->lookupDictionaryValue($row["VALUE"]), true);
                    if ($addedKey) {
                        $tmpResult["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                        $tmpResult["ITEMS"][$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
                    }
                } else {
                    $addedKey = $this->fillItemValues($tmpResult["ITEMS"][$PID], $row["VALUE"], true);
                    if ($addedKey) {
                        $tmpResult["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                        $tmpResult["ITEMS"][$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
                    }
                }
            }
        }
        $result = [];
        $arResult = $this->arResult;
        foreach ($tmpResult['ITEMS'] as $pId => $pData) {
            uasort($pData["VALUES"], array($this, "_sort"));

            $this->arResult = $tmpResult;
            if (count($pData['VALUES']) > 0) {
//                    $result[] = [
//                        $pData['NAME'],
//                        '',
//                        [],
//                        array_merge((array)$this->arParams['ROOT_ITEM_PARAMS'][$pId],
//                            [
//                                'FROM_IBLOCK' => 1,
//                                'DEPTH_LEVEL' => 1,
//                                'IS_PARENT' => 1,
//                            ])
//                    ];
                foreach ($pData['VALUES'] as $key => $value) {
                    $this->arResult['ITEMS'][$pId]['VALUES'][$key]['CHECKED'] = 1;
                    $result[] = [
                        $value['VALUE'],
                        $this->makeSmartUrl($params["SECTION_CODE_PATH"] . $this->arParams['SMART_FILTER_URL'], true),
                        [],
                        [
                            'FROM_IBLOCK' => 1,
                            'DEPTH_LEVEL' => $params['DEPTH_LEVEL'],
                            'IS_PARENT' => '',
                        ]
                    ];
                    unset($this->arResult['ITEMS'][$pId]['VALUES'][$key]['CHECKED']);

                }
            }
        }
        $this->arResult = $arResult;

        return $result;
    }

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            parent::executeComponent();
            $arFilter = array(
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                "GLOBAL_ACTIVE" => "Y",
                "IBLOCK_ACTIVE" => "Y",
                "<=" . "DEPTH_LEVEL" => $this->arParams["DEPTH_LEVEL"],
            );
            $arOrder = array(
                "left_margin" => "asc",
            );

            $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, array(
                "ID",
                "DEPTH_LEVEL",
                "NAME",
                "SECTION_PAGE_URL",
                "SORT",
                "PICTURE",
                "DETAIL_PICTURE",
                "UF_DESC",
                "UF_MENU_PICTURE",
            ));
            if ($this->arParams["IS_SEF"] !== "Y")
                $rsSections->SetUrlTemplates("", $this->arParams["SECTION_URL"]);
            else
                $rsSections->SetUrlTemplates("", $this->arParams["SEF_BASE_URL"] . $this->arParams["SECTION_PAGE_URL"]);
            while ($arSection = $rsSections->GetNext()) {
                $arResult["SECTIONS"][] = array(
                    "ID" => $arSection["ID"],
                    "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                    "~NAME" => $arSection["~NAME"],
                    "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
                    "SORT" => $arSection["SORT"],
                    "PICTURE" => $arSection["PICTURE"],
                    "DETAIL_PICTURE" => $arSection["DETAIL_PICTURE"],
                    "UF_DESC" => $arSection["~UF_DESC"],
                    "UF_MENU_PICTURE" => $arSection["~UF_MENU_PICTURE"],
                );
                $arResult["ELEMENT_LINKS"][$arSection["ID"]] = array();
            }
            $aMenuLinksNew = array();
            $menuIndex = 0;
            $previousDepthLevel = 1;
            $prevSection = false;

            foreach ($arResult["SECTIONS"] as $arSection) {
                $arPicture = NULL;

                if ($menuIndex > 0) {
                    $aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
                    if (!$aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"]) {
                        $filterUrls = $this->getFilters(['SECTION_CODE_PATH' => $prevSection["~SECTION_PAGE_URL"], 'SECTION_ID' => $prevSection["ID"], 'DEPTH_LEVEL' => $prevSection["DEPTH_LEVEL"] + 1]);
                        foreach ($filterUrls as $fu) {
                            $aMenuLinksNew[$menuIndex++] = $fu;
                        }
                    }
                }
                $previousDepthLevel = $arSection["DEPTH_LEVEL"];

                $arResult["ELEMENT_LINKS"][$arSection["ID"]][] = urldecode($arSection["~SECTION_PAGE_URL"]);

                if ($arSection["DEPTH_LEVEL"] == 1 && !empty($arSection["PICTURE"])) {
                    $arPicture = CFile::ResizeImageGet(
                        CFile::GetFileArray($arSection["PICTURE"]),
                        array("width" => 30, "height" => 20),
                        BX_RESIZE_IMAGE_PROPORTIONAL,
                        false
                    );
                    $arPictureDetail = CFile::ResizeImageGet(
                        CFile::GetFileArray($arSection["DETAIL_PICTURE"]),
                        array("width" => 180, "height" => 180),
                        BX_RESIZE_IMAGE_PROPORTIONAL,
                        false
                    );

                }
                if (isset($filterMenuIndex) && $filterMenuIndex > 0)
                    $menuIndex = $filterMenuIndex;
                $aMenuLinksNew[$menuIndex++] = array(
                    htmlspecialcharsbx($arSection["~NAME"]),
                    $arSection["~SECTION_PAGE_URL"],
                    $arResult["ELEMENT_LINKS"][$arSection["ID"]],
                    array(
                        "ID" => $arSection["ID"],
                        "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                        "FROM_IBLOCK" => $arSection["SORT"],
                        "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                        "PICTURE" => $arPicture,
                        "DETAIL_PICTURE" => $arPictureDetail,
                        "UF_DESC" => $arSection["UF_DESC"],
                        "UF_MENU_PICTURE" => $arSection["UF_MENU_PICTURE"],
                        "IS_PARENT" => false
                    ),
                );
                $prevSection = $arSection;
            }
            $this->arResult = $aMenuLinksNew;
            $this->endResultCache();
        }
        return $this->arResult;
    }

}