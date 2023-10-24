<?php

namespace Itcentre;

/**
 * Класс определяет и записыввает минимальные цены товаров в разделах
 */
class CatalogPrice
{
    /**
     * ID каталога
     * @var int
     */
    private $iblockId;

    /**
     * Символьный код раздела - Минимальная цена товара
     * @var string
     */
    private $minPriceCode = 'UF_MIN_PRICE';

    /**
     * ID тип цены
     * @var int
     */
    private $priceTypeId;


    /**
     * Массив ID разделов
     * @var array
     */
    private array $sectionsId;

    /**
     * Массив, где ключ - ID раздела, значение - минимальная цена товара в разделе
     * @var array
     */
    private array $sectionsMinPrice;

    /**
     * Имя лог файла
     */
    public $logFile = 'min_price_tlog.txt';

    public function __construct($iblockId, $priceTypeId)
    {
        $this->iblockId = (int) $iblockId;
        $this->priceTypeId = (int) $priceTypeId;

        $tempFile = fopen($_SERVER["DOCUMENT_ROOT"]."/".$this->logFile, "w");
        fclose($tempFile);
    }

    /**
     * Получаем ID разделов каталога
     * @param $iblockId
     */
    public function getSectionsId()
    {
        $sort = ["SORT" => "ASC"];
        $filter = ["IBLOCK_ID" => $this->iblockId, "ACTIVE" => "Y"];
        $select = ["IBLOCK_ID", "ID", "NAME"];

        $rsSections = \CIBlockSection::GetList($sort, $filter, false, $select);

        while($arSections = $rsSections->Fetch()) {
            $this->sectionsId[] = (int) $arSections["ID"];
        }

//        return $this->sectionsId;
    }

    /**
     * Поиск минимальных цен товаров в разделах
     * @return array
     */
    public function findMinPrice()
    {

        $this->getSectionsId();

        $sectionsIDs = $this->sectionsId;

        $priceCode = "CATALOG_PRICE_{$this->priceTypeId}";

        $categoryMinPrice = [];

        foreach($sectionsIDs as $sectionID) {

            $sort = [
                $priceCode => "ASC"
            ];

            $filter = [
                "IBLOCK_ID" => $this->iblockId,
                "SECTION_ID" => $sectionID,
                "INCLUDE_SUBSECTIONS" => "Y",
                "ACTIVE" => "Y",
                ">PRICE_{$this->priceTypeId}" => 0
            ];

            $nav = [
                "nTopCount" => 1
            ];

            $select = [
                "IBLOCK_ID",
                "ID",
                "NAME",
                "CATALOG_GROUP_{$this->priceTypeId}"
            ];

            $rsProducts = \CIBlockElement::GetList($sort, $filter, false, $nav, $select);

            $ob = $rsProducts->Fetch();

            $categoryMinPrice[$sectionID] = (int) $ob[$priceCode];

        }

        $this->sectionsMinPrice = $categoryMinPrice;


        return $categoryMinPrice;
    }

    /**
     * Запись минимальных цен товаров в разделы
     */
    public function setMinPriceCategory()
    {
        $sectionsMinPrice = $this->sectionsMinPrice;

        $bs = new \CIBlockSection();

        if(is_array($sectionsMinPrice) && !empty($sectionsMinPrice)) {
            foreach($sectionsMinPrice as $sectionID => $minPriceSection) {
                $arFields = [
                    "IBLOCK_ID" => $this->iblockId,
                    $this->minPriceCode => $minPriceSection,
                ];

                $res = $bs->Update($sectionID, $arFields);

                if(!$res) {
                    \Bitrix\Main\Diag\Debug::writeToFile("SECTION ID {$sectionID}: " . $bs->LAST_ERROR, '', $this->logFile);
                } else {
                    \Bitrix\Main\Diag\Debug::writeToFile("SECTION_ID: {$sectionID} MIN_PRICE: {$minPriceSection} - update", '', $this->logFile);
                }

            }
        } else {
            throw new Exception('Массив пуст');
        }

    }

    /**
     * Обнуление минимальных цен товаров в разделах
     */
    public function setZeroMinPriceCategory()
    {
        $sectionsMinPrice = $this->sectionsMinPrice;

        $bs = new \CIBlockSection();

        if(is_array($sectionsMinPrice) && !empty($sectionsMinPrice)) {
            foreach($sectionsMinPrice as $sectionID => $minPriceSection) {
                $arFields = [
                    "IBLOCK_ID" => $this->iblockId,
                    $this->minPriceCode => 0,
                ];

                $res = $bs->Update($sectionID, $arFields);

                if(!$res) {
                    \Bitrix\Main\Diag\Debug::writeToFile("SECTION ID {$sectionID}: " . $bs->LAST_ERROR, '', $this->logFile);
                } else {
                    \Bitrix\Main\Diag\Debug::writeToFile("SECTION_ID: {$sectionID} MIN_PRICE: {$minPriceSection} - update", '', $this->logFile);
                }

            }
        } else {
            throw new Exception('Массив пуст');
        }

    }

}