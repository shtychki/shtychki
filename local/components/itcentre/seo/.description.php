<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("BXCERT_CMPT_NAME"),
	"DESCRIPTION" => GetMessage("BXCERT_CMPT_DESC"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "itcentre", // for example "my_project"
		/*"CHILD" => array(
			"ID" => "", // for example "my_project:services"
			"NAME" => "",  // for example "Services"
		),*/
	),
	"COMPLEX" => "N",
);

?>