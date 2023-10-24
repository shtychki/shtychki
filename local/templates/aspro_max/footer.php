						<?CMax::checkRestartBuffer();?>
						<?IncludeTemplateLangFile(__FILE__);?>
							<?if(!$isIndex):?>
								<?if($isHideLeftBlock && !$isWidePage):?>
									</div> <?// .maxwidth-theme?>
								<?endif;?>
								</div> <?// .container?>
							<?else:?>
								<?CMax::ShowPageType('indexblocks');?>
							<?endif;?>
							<?CMax::get_banners_position('CONTENT_BOTTOM');?>
						</div> <?// .middle?>
					<?//if(($isIndex && $isShowIndexLeftBlock) || (!$isIndex && !$isHideLeftBlock) && !$isBlog):?>
					<?if(($isIndex && ($isShowIndexLeftBlock || $bActiveTheme)) || (!$isIndex && !$isHideLeftBlock)):?>
						</div> <?// .right_block?>
						<?if($APPLICATION->GetProperty("HIDE_LEFT_BLOCK") != "Y" && !defined("ERROR_404")):?>
							<?CMax::ShowPageType('left_block');?>
						<?endif;?>
					<?endif;?>
					</div> <?// .container_inner?>
				<?if($isIndex):?>
					</div>
				<?elseif(!$isWidePage):?>
					</div> <?// .wrapper_inner?>
				<?endif;?>
			</div> <?// #content?>
			<?CMax::get_banners_position('FOOTER');?>
		</div><?// .wrapper?>
		<script>
		(function(w, d, s, h, id) {
		    w.roistatProjectId = id; w.roistatHost = h;
		    var p = d.location.protocol == "https:" ? "https://" : "http://";
		    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init?referrer="+encodeURIComponent(d.location.href);
		    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
		})(window, document, 'script', 'cloud.roistat.com', 'f5449e4d83c425550078315fb16fe8b7');
		</script>
		<script>
		window.onRoistatModuleLoaded = function() {
    		var yandexMarketCheck = function() {
        		if (window.roistat.getSource().indexOf('yamarket') === 0) {
            		window.roistat.leadHunter.isEnabled = false;
        		}
    		};
    	window.roistat.registerOnVisitProcessedCallback(yandexMarketCheck);
		}
		</script>
		<footer id="footer">
			<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/under_footer.php'));?>
			<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/top_footer.php'));?>
		</footer>
		<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/bottom_footer.php'));?>
<script type='text/javascript'>
addEventListener("DOMContentLoaded", () => {	waiterObj = setInterval(()=> {	if (typeof(b24Tracker) != "undefined") {document.cookie = "b__trace=" + b24Tracker.guest.getTrace() + ";Max-Age=3600;path=/";clearInterval(waiterObj);}}, 100);})
</script>
<!-- BEGIN CALL2ME CODE {literal} -->
<script type='text/javascript'>
(function() {
var widgetId = '2889a066ed0c93ea77b571e1be2a6b76';
var s = document.createElement('script');
s.type = 'text/javascript';
s.charset = 'utf-8';
s.async = true;
s.src = '//callme1.voip.com.ua/lirawidget/script/'+widgetId;
var ss = document.getElementsByTagName('script')[0];
ss.parentNode.insertBefore(s, ss);}
)();
</script>
<!-- {/literal} END CALL2ME CODE -->

</body>
</html>
<?
$APPLICATION->IncludeComponent(
	"itcentre:filter404",
	"",
	Array(
		"CACHE_TYPE" => "N"
	)
);?>
<?$APPLICATION->IncludeComponent(
    "itcentre:seo",
    "",
    Array(
        "CACHE_TYPE" => "N"
    )
);?>