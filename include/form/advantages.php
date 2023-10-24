<div class="cabinet_prem">
    <div class="inner__header">
        <div class="inner__title hidden"> Давайте дружить! </div>
        <div class="inner__subtitle"> После авторизации вы сможете: </div>
    </div>
    <div class="inner__list">
    <?php
    if (CModule::IncludeModule('highloadblock')) {
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(24)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $resData = $strEntityDataClass::getList(array(
            'select' => array('*'),
        ));
        while ($arItem = $resData->Fetch()) { ?>
            <div class="inner__listitemrow">
                <div class="inner__listitemcol _img">
                    <img data-lazyload="" class="inner__listitemimg ls-is-cached lazyloaded" src="<?=CFile::GetPath($arItem['UF_IMAGE']);?>" data-src="<?=CFile::GetPath($arItem['UF_IMAGE']);?>">
                </div>
                <div class="inner__listitemcol _text"><?=$arItem['UF_NAME']?></div>
            </div>
    <?php
        }
    }
    ?>
    </div>
</div>