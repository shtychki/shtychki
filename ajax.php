<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?error_reporting(0);?>
<?if(!empty($_GET["act"])){
	if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")){

		$OPTION_QUANTITY_TRACE = COption::GetOptionString("catalog", "default_quantity_trace");
		$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
		global $USER;
		
		if($_GET["act"] == "add"){

			if(!empty($_GET["multi"]) && !empty($_GET['id'])){
				$addElements = explode(";", $_GET["id"]);
				foreach ($addElements as $x => $nextID) {
					if(!Add2BasketByProductID(intval($nextID), intval($_GET["q"]), false)){
						$error = true;
					}
				}

				if(!$error){
					echo '{"error" : "false"}';
				}

			}else{
				
				$getList = CIBlockElement::GetList(
					Array(),
					array(
						"ID" => intval($_GET['id'])
					),
					false,
					false,
					array(
						"ID",
						"NAME",
						"DETAIL_PICTURE",
						"DETAIL_PAGE_URL",
						"CATALOG_QUANTITY"
					)
				);

				$obj = $getList->GetNextElement();
				$arResult = $obj->GetFields();
				
				if(!empty($arResult)){
					
					if(Add2BasketByProductID(intval($_GET["id"]), intval($_GET["q"]), false)){

						$dbBasketItems = CSaleBasket::GetList(
							false,
							array(
								"FUSER_ID" => CSaleBasket::GetBasketUserID(),
								"LID" => SITE_ID,
								"ORDER_ID" => "NULL",
								"PRODUCT_ID" => intval($_GET["id"])
							),
							false,
							false,
							array(
								"ID",
								"QUANTITY",
								"PRICE",
								"PRODUCT_ID"
							)
						);
						
						$basketQty = $dbBasketItems->Fetch();

						if(!empty($basketQty)){
								
							$arResult["DETAIL_PICTURE"] = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false);
							$arResult["DETAIL_PICTURE"] = !empty($arResult["DETAIL_PICTURE"]["src"]) ? $arResult["DETAIL_PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png";

							foreach ($arResult as $index => $arValues) {
								$arJsn[] = '"'.$index.'":"'.addslashes($arValues).'"';
							}

							$arJsn[] = '"PRODUCT_ID": "'.intval($basketQty["PRODUCT_ID"]).'","CART_ID": "'.intval($basketQty["ID"]).'","QUANTITY": "'.intval($basketQty["QUANTITY"]).'","~PRICE": "'.round($basketQty["PRICE"]).'","PRICE": "'.priceFormat(round($basketQty["PRICE"])).'","SUM": "'.round($basketQty["PRICE"]) * intval($basketQty["QUANTITY"]).'"';
							
							echo "{".implode($arJsn, ",")."}";


						}else{
							echo '{"error" : "productCartNotFound"}';
						}
					
					}else{

						if($OPTION_QUANTITY_TRACE == "Y"){
							if($arResult["CATALOG_QUANTITY"] < ($basketQty["QUANTITY"] + intval($_GET["q"]))){
								$quantityError = true;
							}
						}

						if($quantityError){
							echo '{"error" : "quantityError"}';
						}
					}

				}else{
					echo '{"error" : "productNotFound"}';
				}
			}
		}
		elseif($_GET["act"] == "del"){
			echo CSaleBasket::Delete(intval($_GET["id"]));
		}elseif($_GET["act"] == "upd"){
			
			if(!empty($_GET["id"])){
				$getList = CIBlockElement::GetList(
					Array(),
					array(
						"ID" => intval($_GET['id'])
					),
					false,
					false,
					array(
						"ID",
						"NAME",
						"DETAIL_PICTURE",
						"DETAIL_PAGE_URL",
						"CATALOG_QUANTITY"
					)
				);

				$obj = $getList->GetNextElement();
				$arProduct = $obj->GetFields();
				if(!empty($arProduct)){
					$dbBasketItems = CSaleBasket::GetList(
						false, 
						array(
							"FUSER_ID" => CSaleBasket::GetBasketUserID(),
							"ORDER_ID" => "NULL",
							"PRODUCT_ID" => intval($_GET["id"])
						), 
						false, 
						false, 
						array("ID")
					);
					
					$basketRES = $dbBasketItems->Fetch();
					if(!empty($basketRES)){
							
						if($OPTION_QUANTITY_TRACE == "Y"){
							if($arProduct["CATALOG_QUANTITY"] < intval($_GET["q"])){
								$quantityError = true;
							}
						}

						if(!$quantityError){
							if(CSaleBasket::Update($basketRES["ID"],array("QUANTITY" => intval($_GET["q"])))){
								echo '{"success" : "true"}';
							}else{
								echo '{"error" : "basketUpdateError"}';
							}
						}else{
							echo '{"error" : "quantityError", "currentQuantityValue": "'.$arProduct["CATALOG_QUANTITY"].'"}';
						}

					}else{
						echo '{"error" : "productCartError"}';
					}
				}else{
					echo '{"error" : "productNotFoundError"}';
				}
			}else{
				echo '{"error" : "empty product id"}';
			}
		}
		elseif($_GET["act"] == "skuADD"){ 
			if(!empty($_GET["id"]) && !empty($_GET["ibl"])){

				$PRODUCT_ID = intval($_GET["id"]);				
				$IBLOCK_ID  = intval($_GET["ibl"]);
				$SKU_INFO   = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
				$PRODUCT_INFO = CIBlockElement::GetByID($PRODUCT_ID)->GetNext();
				$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
				$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

				$dbPriceType = CCatalogGroup::GetList(
			        array("SORT" => "ASC"),
			        array("BASE" => Y)
				);

				while ($arPriceType = $dbPriceType->Fetch()){
				    $OPTION_BASE_PRICE = $arPriceType["ID"];
				}

				if (is_array($SKU_INFO)){  
					
					$arResult   = array();
					$rsOffers = CIBlockElement::GetList(array(),array("ACTIVE" => Y, "IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $PRODUCT_ID), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
					while($ob = $rsOffers->GetNextElement()){ 
						$arFields = $ob->GetFields();  
						$arProps = $ob->GetProperties();
						
						$arPrice = CCatalogProduct::GetOptimalPrice($arFields["ID"], 1, $USER->GetUserGroupArray());
						
						if($arPrice["PRICE"]["CURRENCY"] != $OPTION_CURRENCY){
							$arPrice["PRICE"]["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"], $OPTION_CURRENCY);
						}

						$arFields["DISCONT_PRICE"] = $arPrice["PRICE"]["PRICE"] > $arPrice["DISCOUNT_PRICE"] ? CurrencyFormat($arPrice["PRICE"]["PRICE"], $OPTION_CURRENCY) : false;
						$arFields["PRICE"] = CurrencyFormat($arPrice["DISCOUNT_PRICE"], $OPTION_CURRENCY);
									
						$picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width' => 200, 'height' => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						$arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH."/images/empty.png";
						$arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
						$arResult[] = array_merge($arFields, array("PROPERTIES" => $arProps));

					}

					foreach ($arResult[0]["PROPERTIES"] as $i => $arProp) {
						$propVisible = false;
						if(empty($arProp["VALUE"])){
							if(empty($propDelete[$i])){
								foreach ($arResult as $x => $arElement) {
									if(!empty($arElement["PROPERTIES"][$i]["VALUE"])){
										$propVisible = true;
										break;
									}
								}
							
								if($propVisible === false){
									$propDelete[$i] = true;
								}
							}
						}
					}
	
					foreach ($arResult as $i => $arElement) {
						foreach ($propDelete as $x => $val) {
							unset($arResult[$i]["PROPERTIES"][$x]);
						}
					}

					if(!empty($arResult)){
						echo jsonMultiEn($arResult);
					}

				} 

			}
		}
		elseif($_GET["act"] == "compADD"){
			if(!empty($_GET["id"])){
				
				$res = CIBlockElement::GetList(
					Array(), 
					Array(
						"ID" => IntVal($_GET["id"])
					), 
					false, 
					false, 
					Array(
						"ID", 
						"NAME", 
						"IBLOCK_ID"
					)
				);

				$ob = $res->GetNextElement();
			 	$arFields = $ob->GetFields();
				$_SESSION["COMPARE_LIST"]["ITEMS"][$_GET["id"]] = $_GET["id"];
				echo '{"NAME":"'.$arFields["NAME"].'"}';
			}
		}elseif($_GET["act"] == "compDEL"){
			if(!empty($_GET["id"])){
				foreach ($_SESSION["COMPARE_LIST"]["ITEMS"] as $key => $arValue){
					if($arValue == $_GET["id"]){
						echo true;
						unset($_SESSION["COMPARE_LIST"]["ITEMS"][$key]);
						break;
					}
				}
			}
		}elseif($_GET["act"] == "search"){
			$_GET["name"] = BX_UTF != 1 ? htmlspecialcharsbx(trim(iconv("UTF-8", "CP1251//IGNORE", $_GET["name"]))) : htmlspecialcharsbx(trim($_GET["name"]));
			if(CModule::IncludeModule("search")){
				$arLang = CSearchLanguage::GuessLanguage($_GET["name"]);
				if(is_array($arLang) && $arLang["from"] != $arLang["to"]){
	  				$_GET["name"] = CSearchLanguage::ConvertKeyboardLayout($_GET["name"], $arLang["from"], $arLang["to"]);
				}
			}
			
			global $USER;

			$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
			$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
			$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();
			
			if(!empty($_GET["name"]) && strLen($_GET["name"]) > 1 && !empty($_GET["iblock_id"])){
				$section = !empty($_GET["section"]) ? intval($_GET["section"]) : 0;
				$arSelect = Array("ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_QUANTITY");
				$arFilter = Array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "IBLOCK_ID" => intval($_GET["iblock_id"]));
				$arFilter[] =  array("LOGIC" => "OR", "?NAME" => $_GET["name"], "PROPERTY_ARTICLE" => $_GET["name"]);
				if($section){
					 $arFilter["SECTION_ID"] = $section;
				}
				$arFilter["INCLUDE_SUBSECTIONS"] = Y;
				$res = CIBlockElement::GetList(Array("shows" => "DESC"), $arFilter, false, Array("nPageSize" => 4), $arSelect);
				while($ob = $res->GetNextElement()){ 
					$arFields = $ob->GetFields(); 
					
					$arPrice = CCatalogProduct::GetOptimalPrice($arFields["ID"], 1, $USER->GetUserGroupArray());
					$arFields["DISCONT_PRICE"] = $arPrice["PRICE"]["PRICE"] > $arPrice["DISCOUNT_PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
					$arFields["PRICE"] = CurrencyFormat($arPrice["DISCOUNT_PRICE"], $OPTION_CURRENCY);

					if(empty($arPrice["PRICE"]) && !$arFields["PRICE"] = 0){
						$arFields["SKU"] = CCatalogSKU::IsExistOffers($arFields["ID"]);
						if($arFields["SKU"]){
							$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
							if (is_array($SKU_INFO)){
								
								$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $arFields["ID"]), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
								while($arSku = $rsOffers->GetNext()){
									$arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
									if(!empty($arSkuPrice)){
										$arFields["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
									}
									$arFields["PRICE"] = (empty($arFields["PRICE"]) || $arFields["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"]) ? $arSkuPrice["DISCOUNT_PRICE"] : $arFields["PRICE"];
									$arFields["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
		
									if($arSku["CATALOG_QUANTITY"] > 0){
										$arFields["CATALOG_QUANTITY"] = $arSku["CATALOG_QUANTITY"];
									}
								}

								$arFields["DISCONT_PRICE"] = null;	
								
								if(min($arFields["SKU_PRICES"]) != max($arFields["SKU_PRICES"])){
									$arFields["PRICE"] = "от ".CurrencyFormat($arFields["PRICE"], $OPTION_CURRENCY);
								}else{
									$arFields["PRICE"] = CurrencyFormat($arFields["PRICE"], $OPTION_CURRENCY);
								}

								$arFields["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;

							}else{
								$arFields["SKU"] = false;
							}

						}
					}

					$arFields["ADDCART"] = $OPTION_ADD_CART == "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
					$picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false);
					$arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH."/images/empty.png";

					foreach ($arFields as $key => $arPropX){
						$arJsn[] = '"'.$key.'" : "'.addslashes($arPropX).'"';
					}
					$arReturn[] = '{'.implode($arJsn, ",").'}';
					$arJsn = array();
				}

				echo "[".implode($arReturn, ",")."]";
			}
		}elseif($_GET["act"] == "flushCart"){
		   ?>
		   <ul>
			   <li class="dl">
					<?$APPLICATION->IncludeComponent(
						"bitrix:sale.basket.basket.line", 
						"topCart", 
					array(
						"HIDE_ON_BASKET_PAGES" => "N",
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"PATH_TO_PROFILE" => SITE_DIR."personal/",
						"PATH_TO_REGISTER" => SITE_DIR."login/",
						"POSITION_FIXED" => "N",
						"SHOW_AUTHOR" => "N",
						"SHOW_EMPTY_VALUES" => "Y",
						"SHOW_NUM_PRODUCTS" => "Y",
						"SHOW_PERSONAL_LINK" => "N",
						"SHOW_PRODUCTS" => "Y",
						"SHOW_TOTAL_PRICE" => "Y",
						"COMPONENT_TEMPLATE" => "topCart",
						"SHOW_DELAY" => "N",
						"SHOW_NOTAVAIL" => "N",
						"SHOW_SUBSCRIBE" => "N",
						"SHOW_IMAGE" => "Y",
						"SHOW_PRICE" => "Y",
						"SHOW_SUMMARY" => "Y"
					),
					false
					);?>
				</li>
				<li class="dl">
					<?$APPLICATION->IncludeComponent(
						"bitrix:sale.basket.basket.line", 
						"footCart", 
					array(
						"HIDE_ON_BASKET_PAGES" => "N",
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"PATH_TO_PROFILE" => SITE_DIR."personal/",
						"PATH_TO_REGISTER" => SITE_DIR."login/",
						"POSITION_FIXED" => "N",
						"SHOW_AUTHOR" => "N",
						"SHOW_EMPTY_VALUES" => "Y",
						"SHOW_NUM_PRODUCTS" => "Y",
						"SHOW_PERSONAL_LINK" => "N",
						"SHOW_PRODUCTS" => "Y",
						"SHOW_TOTAL_PRICE" => "Y",
						"COMPONENT_TEMPLATE" => "footCart",
						"SHOW_DELAY" => "N",
						"SHOW_NOTAVAIL" => "N",
						"SHOW_SUBSCRIBE" => "N",
						"SHOW_IMAGE" => "Y",
						"SHOW_PRICE" => "Y",
						"SHOW_SUMMARY" => "Y"
					),
					false
					);?>
				</li>
			</ul><?
		}elseif($_GET["act"] == "rating"){
			global $USER;
			if ($USER->IsAuthorized()){
				if(!empty($_GET["id"])){
					$arUsers[] = $USER->GetID();
					$res = CIBlockElement::GetList(Array(), Array("ID" => intval($_GET["id"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"), false, false, Array("ID", "IBLOCK_ID", "PROPERTY_USER_ID", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW"));
					while($ob = $res->GetNextElement()){ 
						$arFields = $ob->GetFields();  
						if($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]){
							$result = array(
								"result" => false,
								"error" => "Вы уже голосовали!",
								"heading" => "Ошибка"
							);
							break;
						}
					}
					if(!$result){
						$propCODE = $_GET["trig"] ? "GOOD_REVIEW" : "BAD_REVIEW";
						$propVALUE = $_GET["trig"] ? $arFields["PROPERTY_GOOD_REVIEW_VALUE"] + 1 : $arFields["PROPERTY_BAD_REVIEW_VALUE"] + 1;
						$db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), Array("CODE" => "USER_ID"));
						if($arProps = $db_props->Fetch()){
							$arUsers[] = $arProps["VALUE"];
						}
						CIBlockElement::SetPropertyValuesEx($arFields["ID"], $arFields["IBLOCK_ID"], array($propCODE => $propVALUE, "USER_ID" => $arUsers));
						$result = array(
							"result" => true
						);
					}
				}else{
					$result = array(
						"result" => false,
						"error" => "Элемент не найден",
						"heading" => "Ошибка"
					);
				}
			}
			else{
				$result = array(
					"error" => "Для голосования вам необходимо авторизаваться",
					"result" => false,
					"heading" => "Ошибка"
				);
			}
			echo jsonEn($result);
		
		}elseif($_GET["act"] == "newReview"){
			global $USER;
			if ($USER->IsAuthorized()){
				if(!empty($_GET["DIGNITY"])      && 
				   !empty($_GET["SHORTCOMINGS"]) && 
				   !empty($_GET["COMMENT"])      && 
				   !empty($_GET["NAME"])         && 
				   !empty($_GET["USED"])         && 
				   !empty($_GET["RATING"])       && 
				   !empty($_GET["PRODUCT_NAME"]) && 
				   !empty($_GET["PRODUCT_ID"])
				  ){
					$arUsers = array($USER->GetID());
					$res = CIBlockElement::GetList(
						Array(), 
						Array(
							"ID" => intval($_GET["PRODUCT_ID"]),
							"ACTIVE_DATE" => "Y",
							"ACTIVE" => "Y"
						), 
						false, 
						false, 
						Array(
							"ID", 
							"IBLOCK_ID", 
							"PROPERTY_USER_ID", 
							"PROPERTY_VOTE_SUM", 
							"PROPERTY_VOTE_COUNT"
						)
					);
					while($ob = $res->GetNextElement()){
						$arFields = $ob->GetFields();
						if($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]){
							$result = array(
								"heading" => "Ошибка",
								"message" => "Вы уже оставляли отзыв к этому товару."
							);
							break;
						}
						$arUsers[] = $arFields["PROPERTY_USER_ID_VALUE"];
					}
					if(empty($result)){
						$newElement = new CIBlockElement;

						// DIGNITY - достоинства
						// SHORTCOMINGS - недостатки
						// RATING - рейтинг
						// EXPERIENCE - опыт использования
						// NAME - Имя

						$PROP = array(
							"DIGNITY" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["DIGNITY"])) : htmlspecialcharsbx($_GET["DIGNITY"]),
							"SHORTCOMINGS" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["SHORTCOMINGS"])) : htmlspecialcharsbx($_GET["SHORTCOMINGS"]),
							"NAME" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["NAME"])) : htmlspecialcharsbx($_GET["NAME"]),
							"EXPERIENCE" => intval($_GET["USED"]),
							"RATING" => intval($_GET["RATING"])
						);

						$arLoadProductArray = Array(
							"MODIFIED_BY"    => $USER->GetID(),
							"IBLOCK_SECTION_ID" => false,
							"IBLOCK_ID"      => intval($_GET["iblock_id"]),
							"PROPERTY_VALUES"=> $PROP,
							"NAME"           => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["PRODUCT_NAME"])) : htmlspecialcharsbx($_GET["PRODUCT_NAME"]),
							"ACTIVE"         => "N",
							"DETAIL_TEXT"    => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["COMMENT"])) : htmlspecialcharsbx($_GET["COMMENT"]),
							"CODE"           => intval($_GET["PRODUCT_ID"])
						);

						if($PRODUCT_ID = $newElement->Add($arLoadProductArray)){
							$result = array(
								"heading" => "Отзыв добавлен",
								"message" => "Ваш отзыв будет опубликован после модерации.",
								"reload" => true
							);

							$VOTE_SUM   = $arFields["PROPERTY_VOTE_SUM_VALUE"] + intval($_GET["RATING"]);
							$VOTE_COUNT = $arFields["PROPERTY_VOTE_COUNT_VALUE"] + 1;
							$RATING = ($VOTE_SUM / $VOTE_COUNT);

							CIBlockElement::SetPropertyValuesEx(
								intval($_GET["PRODUCT_ID"]),
								$arFields["IBLOCK_ID"], 
								array(
									"VOTE_SUM" => $VOTE_SUM,
									"VOTE_COUNT" => $VOTE_COUNT,
									"RATING" => $RATING,
									"USER_ID" => $arUsers
								)
							);

						}
						else{
							$result = array(
								"heading" => "Ошибка",
								"message" => "error(1)"
							);
						}
					}
				}else{
					$result = array(
						"heading" => "Ошибка",
						"message" => "Заполните все поля!"
					);
				}
			}else{
				$result = array(
					"heading" => "Ошибка",
					"message" => "Ошибка авторизации"
				);
			}

			echo jsonEn($result);

		}elseif($_GET["act"] === "fastBack"){
			
				if(!empty($_GET["phone"]) && !empty($_GET["id"])){

					if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale")){
						$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();
						$arElement = CIBlockElement::GetByID(intval($_GET["id"]))->GetNext();
						if(!empty($arElement)){
							
							$dbPrice = CPrice::GetList(
						        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
						        array("PRODUCT_ID" => $arElement["ID"]),
						        false,
						        false,
						        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
							);
							
							while ($arPrice = $dbPrice->Fetch()){
								
								$arDiscounts = CCatalogDiscount::GetDiscountByPrice(
									$arPrice["ID"],
									$USER->GetUserGroupArray(),
									"N",
									SITE_ID
								);
								
								$arElement["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
									$arPrice["PRICE"],
									$arPrice["CURRENCY"],
									$arDiscounts
								);
								$arElement["~PRICE"] = $arElement["PRICE"];
								$arElement["PRICE"] = CurrencyFormat($arElement["PRICE"], $arPrice["CURRENCY"]);
							
							}

							$postMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE" => "SALE_DRESSCODE_FASTBACK_SEND"))->GetNext();

							if(empty($postMess)){
								
								$MESSAGE = "<h3>С сайта #SITE# поступил новый заказ в 1 клик. </h3> <p> Товар: <b>#PRODUCT#</b>  <br /> Имя: <b>#NAME#</b> <br /> Телефон: <b>#PHONE#</b> <br /> Комментарий: #COMMENT#";
								$FIELDS = "#SITE# \n #PRODUCT# \n #NAME# \n #PHONE# \n #COMMENT# \n";

								$et = new CEventType;
							    $et->Add(
							    	array(
								        "LID"           => "ru",
								        "EVENT_NAME"    => "SALE_DRESSCODE_FASTBACK_SEND",
								        "NAME"          => "Купить в один клик",
								        "DESCRIPTION"   => $FIELDS
							        )
							    );

								$arr["ACTIVE"] = "Y";
								$arr["EVENT_NAME"] = "SALE_DRESSCODE_FASTBACK_SEND";
								$arr["LID"] = SITE_ID;
								$arr["EMAIL_FROM"] = COption::GetOptionString('main', 'email_from', 'webmaster@webmaster.com');
								$arr["EMAIL_TO"] = COption::GetOptionString("sale", "order_email");
								$arr["BCC"] = COption::GetOptionString("main", 'email_from', 'webmaster@webmaster.com');
								$arr["SUBJECT"] = "Покупка товара в один клик";
								$arr["BODY_TYPE"] = "html";
								$arr["MESSAGE"] = $MESSAGE;

								$emess = new CEventMessage;
								$emess->Add($arr);

							}						

							$arMessage = array(
								"SITE" => SITE_SERVER_NAME,
								"PRODUCT" => $arElement["NAME"]." (ID:".$arElement["ID"]." )"." - ".$arElement["PRICE"],
								"NAME" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]),
								"PHONE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]),
								"COMMENT" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
							);

							CEvent::SendImmediate("SALE_DRESSCODE_FASTBACK_SEND", htmlspecialcharsbx($_GET["SITE_ID"]), $arMessage, "Y", false);

							// NEW ORDER

							$getPersonType = CSalePersonType::GetList(Array("SORT" => "ASC"), Array("LID" => htmlspecialcharsbx($_GET["SITE_ID"]))); 
							if ($arPersonItem = $getPersonType->Fetch()){
								$USER_ID = intval($USER->GetID());
		  						if($USER_ID == 0){
					  				$rsUser = CUser::GetByLogin("unregistered");
									$arUser = $rsUser->Fetch();
									if(!empty($arUser)){
										$USER_ID = $arUser["ID"];
									}else{

										$newUser = new CUser;
										$newPass = rand(0, 999999999);
										$arUserFields = Array(
										  "NAME"              => "unregistered",
										  "LAST_NAME"         => "unregistered",
										  "EMAIL"             => "unregistered@unregistered.com",
										  "LOGIN"             => "unregistered",
										  "LID"               => "ru",
										  "ACTIVE"            => "Y",
										  "GROUP_ID"          => array(),
										  "PASSWORD"          => $newPass,
										  "CONFIRM_PASSWORD"  => $newPass,
										);
										
										$USER_ID = $newUser->Add($arUserFields);
									}
								}

								//paysystem 

								$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT" => "ASC", "PSA_NAME" => "ASC"), 
									Array("ACTIVE" => "Y", "PERSON_TYPE_ID" => $arPersonItem["ID"])
								);

								if ($ptype = $db_ptype->Fetch()){

									//delivery

									$db_dtype = CSaleDelivery::GetList(
									    array(
									            "SORT" => "ASC",
									            "NAME" => "ASC"
									        ),
									    array(
									            "LID" => htmlspecialcharsbx($_GET["SITE_ID"]),
									            "ACTIVE" => "Y",
									        ),
									    false,
									    false,
									    array()
									);
									
									if ($ar_dtype = $db_dtype->Fetch()){

										// CSaleBasket::GetBasketUserID()

										$arFields = array(
										   "LID" => htmlspecialcharsbx($_GET["SITE_ID"]),
										   "PERSON_TYPE_ID" => $arPersonItem["ID"],
										   "PAYED" => "N",
										   "CANCELED" => "N",
										   "STATUS_ID" => "N",
										   "PRICE" => $arElement["~PRICE"],
										   "CURRENCY" => $OPTION_CURRENCY,
										   "USER_ID" => $USER_ID,
										   "PAY_SYSTEM_ID" => $ptype["ID"],
										   "PRICE_DELIVERY" => 0,
										   "DELIVERY_ID" => $ar_dtype["ID"],
										   "DISCOUNT_VALUE" => 0,
										   "TAX_VALUE" => 0.0,
										   "USER_DESCRIPTION" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
										);

										$ORDER_ID = CSaleOrder::Add($arFields);
										$ORDER_ID = IntVal($ORDER_ID);


										$db_props = CSaleOrderProps::GetList(
										        array("SORT" => "ASC"),
										        array(
										                "PERSON_TYPE_ID" => $arPersonItem["ID"],
										                "UTIL" => "N"
										            ),
										        false,
										        false,
										        array()
										    );

										while ($props = $db_props->Fetch()){
											if($props["IS_PROFILE_NAME"] == "Y"){
												CSaleOrderPropsValue::Add(array(
												   "ORDER_ID" => $ORDER_ID,
												   "ORDER_PROPS_ID" => $props["ID"],
												   "NAME" => $props["NAME"],
												   "CODE" => $props["CODE"],
												   "VALUE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"])
												));
											}else if(strtoupper($props["CODE"]) == "TELEPHONE" || strtoupper($props["CODE"]) == "PHONE" || $props["IS_PHONE"] == "Y"){
												CSaleOrderPropsValue::Add(array(
												   "ORDER_ID" => $ORDER_ID,
												   "ORDER_PROPS_ID" => $props["ID"],
												   "NAME" => $props["NAME"],
												   "CODE" => $props["CODE"],
												   "VALUE" => BX_UTF != 1 ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"])
												));											
											}
										}							
										
										CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(), False);
										
										Add2BasketByProductID(
											$arElement["ID"], 
											1, 
											array("ORDER_ID" => $ORDER_ID), 
											array()
										);
										
										CSaleBasket::OrderBasket($ORDER_ID, $USER_ID, $_GET["SITE_ID"]);


									}else{
										$result = array(
											"heading" => "Ошибка",
											"message" => "Ошибка, служба доставки не создана!",
											"success" => false
										);
									}

								}else{
									$result = array(
										"heading" => "Ошибка",
										"message" => "Ошибка, платежная система не создана!",
										"success" => false
									);
								}

							}
							if(empty($result)){
								$result = array(
									"heading" => "Ваш заказ успешно отправлен",
									"message" => "В ближайшее время Вам перезвонит наш менеджер для уточнения деталей заказа.",
									"success" => true
								);
							}
						}else{

							$result = array(
								"heading" => "Ошибка",
								"message" => "Ошибка, товар не найден!",
								"success" => false
							);

						}

					}

				}else{
					$result = array(
						"heading" => "Ошибка",
						"message" => "Ошибка, заполните обязательные поля!",
						"success" => false
					);
				}
			
			echo jsonEn($result);

		}
	}
	else{
		die(false);
	}
}

function priceFormat($data, $str = ""){
	$price = explode(".", $data);
	$strLen = strlen($price[0]);
	for ($i = $strLen; $i > 0 ; $i--) { 
		$str .=	(!($i%3) ? " " : "").$price[0][$strLen - $i];
	}
	return $str.($price[1] > 0 ? ".".$price[1] : "");
}

function jsonEn($data, $multi = false){
	
	if(!$multi){
		foreach ($data as $index => $arValue) {
			$arJsn[] = '"'.$index.'" : "'.addslashes($arValue).'"';
		}
		return  "{".implode($arJsn, ",")."}";
	}
}

function jsonMultiEn($data){
	if(is_array($data)){
		if(count($data) > 0){
			$arJsn = "[".implode(getJnLevel($data, 0), ",")."]";
		}else{
			$arJsn = implode(getJnLevel($data), ",");
		}
	}
	return str_replace(array("\t", "\r", "\n"), "", $arJsn);
}

function getJnLevel($data, $level = 1){
	foreach ($data as $i => $arNext) {
		if(!is_array($arNext)){
			$arJsn[] = '"'.$i.'":"'.addslashes(str_replace("'", '"', $arNext)).'"';
		}else{
			if($level === 0){
				$arJsn[] = "{".implode(getJnLevel($arNext), ",")."}";
			}else{
				$arJsn[] = '"'.$i.'":{'.implode(getJnLevel($arNext),",").'}';
			}
			
		}
	}
	return $arJsn;
}
?>