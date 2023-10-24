<?php
//v1
namespace Itcentre;

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyIndex;

Loader::includeModule("main");

\CBitrixComponent::includeComponentClass('bitrix:catalog.smart.filter');

/**
 * Класс для генерации sitemap с ссылками умного фильтра
 */
class SitemapFilter {

    /**
     * Домена сайта полный с протоколом
     * @var string
     */
    private string $domain;

    /**
     * ID каталога
     * @var int
     */
    private int $iblockId;

    /**
     * ID каталога ТП
     * @var int
     */
    private int $iblockSkuId;

    /**
     * Массив ID разделов каталога
     * @var array
     */
    private array $sectionsId;

    /**
     * Объект умного фильтра ($FilterObj)
     * @var
     */
    private $filterObj;

    /**
     * Объект фасетного индекса ($Facet)
     * @var
     */
    private $facet;

    /**
     * Массив ссылок умного фильтра
     * @var array
     */
    private array $arResultSmartLink;

    public function __construct($iblockId, $iblockSkuId, $domain) {

        if(Loader::includeModule("iblock")) {
            $this->iblockId = (int) $iblockId;
            $this->iblockSkuId = (int) $iblockSkuId;
            $this->domain = trim($domain);

            if(!$iblockId) {
                throw new \Exception("Не задан ID инфоблока");
            }

            if(!$iblockSkuId) {
                throw new \Exception("Не задан ID инфоблока торговых предложений");
            }

            if(!$domain) {
                throw new \Exception("Домен не задан");
            }

            $this->filterObj = new \CBitrixCatalogSmartFilter;
            $this->facet = new PropertyIndex\Facet($this->iblockId);
        } else {
            throw new \Exception("Модуль инфоблока не найден");
        }

    }

    /**
     * Получаем ID разделов каталога
     * @throws \Exception
     */
    public function getSectionsId() {

        $sort = ["ID" => "DESC"];
        $filter = ["IBLOCK_ID" => $this->iblockId, "ACTIVE" => "Y"];
        $select = ["ID"];

        $arSections = \CIBlockSection::GetList($sort, $filter, false, $select);

        $arSectionID = [];

        while ($arSectionsRes = $arSections->Fetch()) {
            $arSectionID[] = $arSectionsRes['ID'];
        }

        if(count($arSectionID) > 0) {
            $this->sectionsId = $arSectionID;
        } else {
            throw new \Exception('В каталоге нет разделов');
        }

    }

    public function generateSmartLinks() {
        $this->getSectionsId();

        $sectionsId = $this->sectionsId;
        $FilterObj = $this->filterObj;
        $Facet = $this->facet;

        if ($sectionsId) {
            foreach ($sectionsId as $sectId) {

                $arResultLink = \CIBlockSectionPropertyLink::GetArray($this->iblockId, $sectId);
                $arResultLinkSku = \CIBlockSectionPropertyLink::GetArray($this->iblockSkuId, $sectId);

                // массив со свойствами раздела
                $arResultProperty = array();
                //for product
                foreach ($arResultLink as $PID => $arLink) {

                    if ($arLink["SMART_FILTER"] !== "Y")
                        continue;

                    if ($arLink["ACTIVE"] === "N")
                        continue;

                    if ($arLink['FILTER_HINT'] <> '') {
                        $arLink['FILTER_HINT'] = \CTextParser::closeTags($arLink['FILTER_HINT']);
                    }

                    $rsProperty = \CIBlockProperty::GetByID($PID);
                    $arProperty = $rsProperty->Fetch();

                    $arResultProperty[$arProperty['ID']] = $arProperty;
                }
                //for sku
                foreach ($arResultLinkSku as $PID => $arLink) {

                    if ($arLink["SMART_FILTER"] !== "Y")
                        continue;

                    if ($arLink["ACTIVE"] === "N")
                        continue;

                    if ($arLink['FILTER_HINT'] <> '') {
                        $arLink['FILTER_HINT'] = \CTextParser::closeTags($arLink['FILTER_HINT']);
                    }

                    $rsProperty = \CIBlockProperty::GetByID($PID);
                    $arProperty = $rsProperty->Fetch();

                    $arResultProperty[$arProperty['ID']] = $arProperty;
                }

                if (!empty($arResultProperty)) {

                    $arSectionInfo = \CIBlockSection::GetByID($sectId)->GetNext();
                    $sefSmartUrl = $arSectionInfo['SECTION_PAGE_URL'] . "filter/#SMART_FILTER_PATH#/apply/";

                    $arFacetFilter = array(
                        'ACTIVE_DATE' => 'Y',
                        'CHECK_PERMISSIONS' => 'Y',
                        'CATALOG_AVAILABLE' => 'Y'
                    );

                    if ($Facet->isValid()) {

                        $Facet->setSectionId($sectId);

                        $res = $Facet->query(
                            $arFacetFilter
                        );


                        while ($row = $res->fetch()) {

                            $facetId = $row["FACET_ID"];

                            if (\Bitrix\Iblock\PropertyIndex\Storage::isPropertyId($facetId)) {

                                $PID = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId($facetId);

                                if ($arResultProperty[$PID]["PROPERTY_TYPE"] == "N") {
                                    continue;
                                } elseif ($arResultProperty[$PID]["DISPLAY_TYPE"] == "U") {
                                    continue;
                                } elseif ($arResultProperty[$PID]["PROPERTY_TYPE"] == "S") {
                                    $addedKey = $FilterObj->fillItemValues($arResultProperty[$PID], $Facet->lookupDictionaryValue($row["VALUE"]), true);
                                    if (strlen($addedKey) > 0) {
                                        $arResultProperty[$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                                        $arResultProperty[$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
                                    }
                                } else {

                                    $addedKey = $FilterObj->fillItemValues($arResultProperty[$PID], $row["VALUE"], true);
                                    if (strlen($addedKey) > 0) {
                                        $arResultProperty[$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
                                        $arResultProperty[$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
                                    }
                                }

                            }
                        }

                        foreach ($arResultProperty as $PID => $item) {

                            $code = null;
                            if ($item["CODE"]) {
                                $code = toLower($item["CODE"]);
                            } else {
                                $code = $item["ID"];
                            }

                            if (!empty($item["VALUES"]) && is_array($item["VALUES"])) {

                                foreach ($item["VALUES"] as $key => $val) {
                                    $smartLink = str_replace("#SMART_FILTER_PATH#", implode("/", $FilterObj->encodeSmartParts(
                                        array(
                                            array(
                                                $code, $val["URL_ID"])
                                        )
                                    )
                                    ), $sefSmartUrl);

                                    $this->arResultSmartLink[] = "{$this->domain}$smartLink";

                                }

                            }
                        }
                    }
                }
            }
        } else {
            throw new \Exception('Разделы каталога не найдены');
        }

    }

    /**
     * Генерация sitemap
     * @param $documentRoot
     * @param $sitemapName
     * @throws \Exception
     */
    public function generateSitemap($documentRoot, $sitemapName) {
        $arResultSmartLink = $this->arResultSmartLink;

        if(!$documentRoot) {
            throw new \Exception('Переменная $documentRoot не передана');
        }

        if(!$sitemapName) {
            throw new \Exception('Не задано имя sitemap');
        }

        if($arResultSmartLink && !empty($arResultSmartLink)) {
            $dom = new \DOMDocument('1.0', 'utf-8');
            $urlset = $dom->createElement('urlset');
            $urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');

            foreach($arResultSmartLink as $smartLink) {
                $url = $dom->createElement('url');

                // Элемент <loc>.
                $loc = $dom->createElement('loc');
                $text = $dom->createTextNode(
                    htmlentities($smartLink, ENT_QUOTES)
                );
                $loc->appendChild($text);
                $url->appendChild($loc);

                // Элемент <lastmod>.
                $lastmod = $dom->createElement('lastmod');
                $text = $dom->createTextNode(date('Y-m-d'));
                $lastmod->appendChild($text);
                $url->appendChild($lastmod);

                $urlset->appendChild($url);
            }

            $dom->appendChild($urlset);

            // Сохранение в файл.
            $dom->save("{$documentRoot}/{$sitemapName}");

        } else {
            throw new \Exception('Ссылки не сформированы');
        }
    }

}