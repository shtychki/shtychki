<?global $arTheme;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
//	"top_catalog_only_custom",
//	"top_catalog_wide_custom",
	"top_custom_catalog",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
//		"COMPONENT_TEMPLATE" => "top_catalog_wide_custom",
		"COMPONENT_TEMPLATE" => "top_custom_catalog",
		"COUNT_ITEM" => "6",
		"DELAY" => "N",
		"MAX_LEVEL" => $arTheme["MAX_DEPTH_MENU"]["VALUE"],
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"ALLOW_MULTI_SELECT" => "Y",
		"ROOT_MENU_TYPE" => "only_catalog",
		"USE_EXT" => "Y"
	)
);?>