<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
if(isset($_GET['iblock_id']) && $_GET['iblock_id'])
{
	global $APPLICATION, $arRegion, $arTheme;
	$arRegion = CMaxRegionality::getCurrentRegion();
	$arTheme = CMax::GetFrontParametrsValues(SITE_ID);
	
	$context = \Bitrix\Main\Application::getInstance()->getContext();
	$request = $context->getRequest();

	$href = $request['item_href'] ?? $result['DETAIL_PAGE_URL']; // from fastViewNav.php
	$url = htmlspecialcharsbx(urldecode($href));
	$url = str_replace('&amp;', '&', $href );

	\Bitrix\Main\Loader::includeModule('sale');
	\Bitrix\Main\Loader::includeModule('currency');
	\Bitrix\Main\Loader::includeModule('catalog');?>
	<script>
		var objUrl = parseUrlQuery(),
			add_url = '<?=(strpos($url, '?') !== false ? '&' : '?')?>FAST_VIEW=Y';
		if('clear_cache' in objUrl)
		{
			if(objUrl.clear_cache == 'Y')
				add_url += '&clear_cache=Y';
		}
		$('.fast_view_frame').addClass('loading_block');
		BX.ajax({
			url: <?var_export($url)?> + add_url,
			method: 'POST',
			data: BX.ajax.prepareData({'FAST_VIEW':'Y'}),
			dataType: 'html',
			processData: false,
			start: true,
			headers: [{'name': 'X-Requested-With', 'value': 'XMLHttpRequest'}],
			onfailure: function(data) {
				alert('Error connecting server');
			},
			onsuccess: function(html){
				var ob = BX.processHTML(html);

				<?if($_GET['skip_preview'] == true):?>
					ob.HTML = ob.HTML.replace(/(calculate-delivery[^>]*?)with_preview/, '$1').replace(/<span class=\"calculate-delivery-preview\"><\/span>/, '');
				<?endif;?>

				if(getComputedStyle($('.fast_view_frame')[0]).getPropertyValue('--fast-view-css-loaded') != 1){
					$('.fast_view_frame').addClass('wait_css_loading');
				}

				// inject
				BX('fast_view_item').innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
				$('#fast_view_item').closest('.form').addClass('init');
				$('.fast_view_frame').removeClass('loading_block');

				setBasketStatusBtn();

				BX.loadScript(arAsproOptions.SITE_TEMPLATE_PATH + '/js/jquery.fancybox.min.js', function(event){
					BX.loadCSS(arAsproOptions.SITE_TEMPLATE_PATH + '/css/jquery.fancybox.min.css');
					InitFancyBox();
					InitFancyBoxVideo();
				})

				if (typeof showOutOfProductionBlock !== 'undefined') {
					showOutOfProductionBlock();
				}
				
				if (typeof initSwiperSlider === 'function') {
					initSwiperSlider();
				}

				if(typeof useOfferSelect === 'function'){
					useOfferSelect();
				}
				
				// init calculate delivery with preview
				if($('#fast_view_item .fastview-product.noffer').length){
					initCalculatePreview();
				}

				setTimeout(function(){
					showTotalSummItem('Y');
				}, 100);

				$('.popup .animate-load').click(function(){
					if(!jQuery.browser.mobile)
						$(this).parent().addClass('loadings');
				})

				$('#fast_view_item .counter_block input[type=text]').numeric({allow:"."});

				$('.navigation-wrapper-fast-view .fast-view-nav').removeClass('noAjax');

				let bCSSLoaded = getComputedStyle($('.fast_view_frame')[0]).getPropertyValue('--fast-view-css-loaded') == 1,
					timerInterval = bCSSLoaded ? 0 : 100;

				var countTicks = 0;
				var timer = setInterval(function(){
					const maxCountTicks = 10;
					if(countTicks > maxCountTicks){
						document.querySelector('body').style.setProperty('--fast-view-css-loaded', 1);
					}

					if(getComputedStyle($('.fast_view_frame')[0]).getPropertyValue('--fast-view-css-loaded') == 1){
						clearInterval(timer);
						$('.fast_view_frame').removeClass('wait_css_loading');
					} else {
						countTicks++;
					}
				}, timerInterval);
			}
		})
		$(document).on('click', '.jqmClose', function(e){
			e.preventDefault();
			$(this).closest('.jqmWindow').jqmHide();
		})
	</script>
	<div id="fast_view_item"><div class="loading_block"></div></div>
<?}?>
