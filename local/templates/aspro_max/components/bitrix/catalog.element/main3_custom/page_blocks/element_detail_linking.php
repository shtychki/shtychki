<?//linking?>
<?if($arResult["ITEM_PROPS"] && $arResult["ITEM_PROPS_CNT"] != 0){?>
    <div class="text-form sku_props">
        <div class="char-side__title font_sm darken"><?=GetMessage("CT_NAME_ITEM_PROPS");?></div>

        <?foreach ($arResult["ITEM_PROPS"] as $key => $cpropItem){?>
			<?if(count($cpropItem) > 1){?>
				<div class="bx_catalog_item_scu wrapper_sku" id="">
					<div class="<?=(($key == 'Длина кабеля, м' || $key == 'Объем') ? "bx_item_detail_size" : "bx_item_detail_scu");?>" style="" id="">
                        <?php
//                        echo '<pre>';
//                        print_r($cpropItem);
//                        print_r($arResult['test3']);
//                        echo '</pre>';
                        ?>
						<span class="show_class bx_item_section_name"><span><?//=$key;?></span></span>
						<div class="">
							<div class="<?=(($key == 'Длина кабеля, м' || $key == 'Объем') ? "bx_size" : "bx_scu");?>">
								<?
								ksort($cpropItem);
								$i = 1;
								$cnt = count($cpropItem);
								?>
								<ul id="" class="list_values_wrapper">
									<?foreach ($cpropItem as $item) { ?>
										<li class="<?=$item['TYPE'];?> <?=(($item["ID"] == $arResult["ID"]) ? 'active' : '');?>">
											<?if ($item["ID"] != $arResult["ID"]){?>
												<a href="<?=$item["LINK"]?>">
													<?if (!empty($item["NAME"])){?>
														<span class="cnt1">
															<span class="cnt_item" style="background:url('<?=$item["NAME"]["src"];?>') no-repeat top center" data-obgi="url('<?=$item["NAME"]["src"];?>')" title="<?=$item["VALUE"];?>"></span>
														</span>
													<?} else {?>
														<span class="cnt">
															<?=$item["VALUE"];?>
														</span>
													<?}?>
												</a>
											<?} else {?>
												<?if (!empty($item["NAME"])){?>
													<span class="cnt1">
														<span class="cnt_item current" style="background:url('<?=$item["NAME"]["src"];?>') no-repeat top center" data-obgi="url('<?=$item["NAME"]["src"];?>')" title="<?=$item["VALUE"];?>"></span>
													</span>
												<?} else {?>
													<span class="cnt">
														<?=$item["VALUE"];?>
													</span>
												<?}?>
											<?}?>
										</li>
										<?	$i++;} ?>
									<? if ($cnt > 7) { ?>
										<li id="more-items">Ещё <?= ($cnt - 7)?></li>
									<? } ?>
								</ul>

							</div>
						</div>
					</div>
				</div>
			<?}?>
        <?}?>
    </div>
<?}?>

