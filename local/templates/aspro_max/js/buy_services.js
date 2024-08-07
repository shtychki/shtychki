if (typeof updateBasketInfoServices === 'undefined') {
    $(document).on('click', '.services_in_product .switch_block label', function(){
        var $this = $(this);    
        var servItem = $this.closest('.services-item');
        var servBuyButton = servItem.find('.services-item__buy .button_block .to-cart');
        var switcherState = servItem.find('#'+$this.attr('for')).prop('checked');
        var parentProduct = $this.closest('.buy_services_wrap').attr('data-parent_product');
        
        if(servBuyButton.length) {
            if(switcherState){ 
                var basketItem = arBasketAspro.SERVICES[servItem.attr('data-item_id') + '_' + parentProduct];
                var currentWrapClasses = servItem.closest('.buy_services_wrap').attr('class');
                
                if(typeof(basketItem) != "undefined" && basketItem['basket_id']){
                    $.ajax({
                        url: arAsproOptions['SITE_DIR'] + 'ajax/item.php?delete_basket_id='+basketItem['basket_id'],
                        type: 'post',
                        success: function(html) {
                            updateBasketInfoServices();
                            getActualBasket();                                                
                        }
                    });
                } 
                $('.product-container .buy_services_wrap[data-parent_product='+ parentProduct +'] .services-item[data-item_id=' + servItem.attr('data-item_id')+']').removeClass('services_on');
                $('.product-container .buy_services_wrap[data-parent_product='+ parentProduct +'] .services-item[data-item_id=' + servItem.attr('data-item_id')+'] input[name="buy_switch_services"]').each(function (){
                    var _th = $(this);
                    if(_th.closest('.buy_services_wrap').attr('class') !== currentWrapClasses){
                        _th.prop('checked', false);
                    }                    
                    
                });            
            } else {
                var parentProductId =  arBasketAspro.BASKET[parentProduct];
                var currentWrapClasses = servItem.closest('.buy_services_wrap').attr('class');
                var isBasketPage = servItem.closest('.buy_services_wrap').hasClass('services_in_basket_page');
    
                if(parentProductId || isBasketPage){
                    servBuyButton[0].click();
                }
                $('.product-container .buy_services_wrap[data-parent_product='+ parentProduct +'] .services-item[data-item_id=' + servItem.attr('data-item_id')+']').addClass('services_on');
                $('.product-container .buy_services_wrap[data-parent_product='+ parentProduct +'] .services-item[data-item_id=' + servItem.attr('data-item_id')+'] input[name="buy_switch_services"]').each(function (){
                    var _th = $(this);
                    if(_th.closest('.buy_services_wrap').attr('class') !== currentWrapClasses){
                        _th.prop('checked', true);
                    }
                });
            }
        }
    });
    
    let updateBasketInfoServices = function  (refreshBasket){
        if(refreshBasket === undefined)
            refreshBasket = true;
    
        var basketFly = $('#basket_line .basket_fly').length;
    
        if(basketFly){
            $.ajax({
            url: arAsproOptions['SITE_DIR'] + 'ajax/basket_fly.php',
            type: 'post',
            success: function(html) {
                $('#basket_line .basket_fly').addClass('loaded').html(html);
                if(window.matchMedia('(min-width: 769px)').matches)
                {
                    $('.opener .basket_count').removeClass('small')
                    $('.tabs_content.basket li[item-section="AnDelCanBuy"]').addClass('cur');
                    $('#basket_line ul.tabs li[item-section="AnDelCanBuy"]').addClass('cur');
                    $("#basket_line .basket_fly .opener > div:eq(0)").addClass("cur");
                }
            }
            });
        } else if($("#ajax_basket").length){
            reloadTopBasket('add', $('#ajax_basket'), 200, 5000, 'Y');
            basketTop('', $('.basket_hover_block'));
        }
    
        //for basket page
        if($('.wrapper1.basket_page').length && refreshBasket){
            BX.Sale.BasketComponent.sendRequest('refreshAjax', {
                fullRecalculation: 'Y',
                otherParams: {
                   param: 'N'
                }
             });
        }
    }
    
    $(document).on("change", ".services_in_product .counter_block input[type=text]", function(e){
        var $this = $(this);    
        var servItem = $this.closest('.services-item');
        var servBuyButton = servItem.find('.services-item__buy .button_block .to-cart');
        var parentProduct = $this.closest('.buy_services_wrap').attr('data-parent_product');
        var servCount = servItem.find('.counter_block  input.text').val();
    
        $('.product-container .buy_services_wrap[data-parent_product='+ parentProduct +'] .services-item[data-item_id=' + servItem.attr('data-item_id')+'] .counter_block  input.text').val(servCount);
    
        if(servBuyButton.length) {
            var basketItem = arBasketAspro.SERVICES[servItem.attr('data-item_id') + '_' + parentProduct];
            var quantity = $this.val();
    
            if(typeof(basketItem) != "undefined" && basketItem['basket_id']){
                $.ajax({
                    url: arAsproOptions['SITE_DIR'] + 'ajax/item.php?update_basket_id='+basketItem['basket_id'] + '&quantity=' + quantity,
                    type: 'post',
                    success: function(html) {
                        updateBasketInfoServices();
                        getActualBasket();
                    }
                });
            }
        }
    });
    
    $(document).on('click', '.services_in_product .more-services-slide', function(){
        var _this = $(this);
        var servWrap = _this.closest('.buy_services_wrap');
        servWrap.toggleClass('show_all');
        servWrap.hasClass('show_all') ? _this.find('>span').text(_this.attr('data-close')) : _this.find('>span').text(_this.attr('data-open'));
    });
}