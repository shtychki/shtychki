/*
You can use this file with your scripts.
It will not be overwritten when you upgrade solution.
*/

$(window).on("load", function () {
	//check width for menu and search
	CheckSearchWidth();
});

$(document).ready(function () {
    // $('.inline-search-show, .inline-search-hide').off('click');
    // function delay(callback, ms) {
    //     var timer = 0;
    //     return function () {
    //         var context = this, args = arguments;
    //         clearTimeout(timer);
    //         timer = setTimeout(function () {
    //             callback.apply(context, args);
    //         }, ms || 0);
    //     };
    // }

    // if (sessionStorage.search_obj) {
    //     search(sessionStorage);
    // }

    // открывает закрывает список параметров
    $('.expand_block').on('click', function () {
        let btn_show = $(this).parents('.show_hidden_params').find('.expand_block.show_all');
        let btn_hide = $(this).parents('.show_hidden_params').find('.expand_block.hide_all');
        let count_show = 0

        if ($(this).hasClass('show_all')) {
            $(this).parents('.bx_filter_parameters_box_container').find('label.filter-hidden').addClass('--show');
            // $(this).parents('.bx_filter_parameters_box_container').find('label.hidden').not('.off').addClass('--show');
            // $(this).parents('.bx_filter_parameters_box_container').find('label.hidden').not('.hidden').addClass('--show');
            // $(this).parents('.bx_filter_parameters_box_container').find('label.hidden').not('.off').removeClass('hidden');

            btn_show.removeClass('--active');
            btn_hide.addClass('--active');
        } else {
            $(this).parents('.bx_filter_parameters_box_container').find('label').each(function (key, element) {
                if ($(element).not('.disabled').length || $(element).hasClass('checked'))  count_show++;

                // console.log(count_show);
                // console.log(element);
                if ((count_show > 5 && $(element).hasClass('checked') == false) || $(element).hasClass('disabled')) {
                    $(element).removeClass('--show');
                }
            });
            btn_show.addClass('--active');
            btn_hide.removeClass('--active');
        }
    })

    /*$('.phone a').attr('vochi-operator', 0);*/
    $('.phone a').addClass('ct-phone');

    $('.search_inp').keyup(delay(function (e) {
        var search_obj = {};
        var all = $('.box_' + this.id + ' label');
        $.each(all, function (key, value) {
            if ($(value).hasClass('off') == false) {
                $(value).addClass('off');
                $(value).removeClass('--show')
            }
        });
        if (this.value == '') {
            // $(this).parents('.bx_filter_parameters_box').find('.hidden_values').hide();
            var str = this.value.toLowerCase();
            let count_search = 0;
            $.each(all, function (key, value) {
                var string = value.dataset.name.toLowerCase();
                if (string.indexOf(str) + 1) {
                    count_search++;
                    $(value).removeClass('off');
                }
                $(value).removeClass('off');

                if ($(this).parents('.bx_filter_parameters_box').find('.hide_all').hasClass('--active')) {
                    $(value).addClass('--show')
                } else {
                    if (key > 5 && count_search < 5) {
                        $(value).removeClass('--show')
                    }
                }
            });
            if (count_search >= 5) {
                $(this).parents('.bx_filter_parameters_box').find('.show_hidden_params').show();
            }
            document.querySelector('.remove-'+this.id).remove();
        }

        if (this.value.length > 0) {
            // $(this).parents('.bx_filter_parameters_box').find('.hidden_values').show();
            var str = this.value.toLowerCase();
            let count_search = 0;
            $.each(all, function (key, value) {
                var string = value.dataset.name.toLowerCase();
                if (string.indexOf(str) + 1) {
                    count_search++;
                    $(value).removeClass('off');

                    if ($(this).parents('.bx_filter_parameters_box').find('.hide_all').hasClass('--active')) {
                        $(value).addClass('--show')
                    } else {
                        if (key > 6 && count_search < 6) {
                            $(value).addClass('--show')
                        }
                    }
                }
            });

            if (count_search <= 5) {
                $(this).parents('.bx_filter_parameters_box').find('.show_hidden_params').hide();
            }else {
                $(this).parents('.bx_filter_parameters_box').find('.show_hidden_params').show();
            }
            if(!document.querySelector('.remove-'+this.id)){
                document.getElementById(this.id).insertAdjacentHTML('afterend', '<span data-id="'+this.id+'" class="search-text-remove remove-'+this.id+'"></span>');
            }
        }

        search_obj[this.id] = this.value;
        sessionStorage.setItem('search_obj', JSON.stringify(search_obj));
    }, 500));

    $('.search_inp_ajax').keyup(delay(function (e) {
        let search_obj = {};
        search_obj[this.id] = this.value;
        sessionStorage.setItem('search_obj', JSON.stringify(search_obj));
        search(sessionStorage);
    }, 500));
    $(document).on('click', '.section--js-more', function(){
        var $this = $(this),
            block = $this.find('> span'),
            dataOpened = $this.data('opened'),
            thisText = block.text()
            dataText = block.data('text'),
            showCount = 6;
            item = $this.closest('.landings-list__info').find('.landings-list__item.js-hide').get();

        var items = item.filter(function(item1, index){
            return ++index <= showCount
        });

        if(items){
            var lastVisibleIndex = 0;

            items.forEach(function(item, index){
                $(item).removeClass('js-hide');
                lastVisibleIndex = $(item).index();
            });

            if (lastVisibleIndex) {
                // show group breaks
                $this.closest('.landings-list__info').find('.landings-list__break').each(function(){
                    if ($(this).index() < lastVisibleIndex) {
                        $(this).show();
                    }
                });
            }
        }

        if(!item.length){
            $this.closest('.landings-list__info').find('.landings-list__item.hidden-item').addClass('js-hide');
            block.data('text', thisText).text(dataText);
            $this.removeClass('opened').data('opened', 'N');
        }
        else if(item.length <= showCount){
            block.data('text', thisText).text(dataText);
            $this.addClass('opened').data('opened', 'Y');
        }
    });
});
//
//top menu
// $(document).on("click", ".menu .mega-menu table td.catalog, .menu-row .mega-menu table td.catalog", function (e) {
$(document).on("click", ".header-v10 .mega-menu table td.catalog", function (e) {
    e.preventDefault();

    var _this = $(this);
    menu = _this.find("> .wrap > .dropdown-menu");
    // var bDarkness = $(".wrapper1.dark-hover-overlay").length > 0;

    if (_this.hasClass('active_menu')) {
        _this.removeClass('active_menu');
        $('.icon-close').addClass('hide');
        $('.icon-open').removeClass('hide');
        menu.toggle('slow');
    } else {
        InitMenuNavigationAim();
        CheckTopVisibleMenu();
        _this.addClass('active_menu');
        $('.icon-open').addClass('hide');
        $('.icon-close').removeClass('hide');
        menu.toggle('slow');
    }
    // menu.toggle('slow');
});

$(document).on('click', '.search-text-remove', function (e) {
    document.getElementById(this.getAttribute('data-id')).value = '';
    document.querySelector('.remove-'+this.getAttribute('data-id')).remove();
    search(sessionStorage, this.getAttribute('data-id'));
});

$(document).click(function (e) {
    var btn_menu = $('.menu-row .mega-menu table td.catalog');
    if (btn_menu.has(e.target).length === 0 && btn_menu.hasClass('active_menu')) {
        btn_menu.removeClass('active_menu');
        $('.icon-close').addClass('hide');
        $('.icon-open').removeClass('hide');
        btn_menu.find("> .wrap > .dropdown-menu").toggle('slow');
    }
});

$(function () {
    $('#more-items').click(function () {
        $('.list_values_wrapper li:nth-of-type(n+8)').toggle('');
    });
});

function search(data, id = false) {
    if(id){
        if (data.search_obj) {
            let obj = JSON.parse(data.search_obj);
            let count_search = 0;
            for (key in obj) {
                if(key == id) {
                    var all = $('.box_' + key + ' label');
                    $.each(all, function (key, value) {
                        $(value).removeClass('--show');
                        if (count_search < 5 && $(value).not('.disabled').length) {
                            count_search++;
                            $(value).addClass('--show');
                        }
                    });
                    obj[key] = '';
                    sessionStorage.setItem('search_obj', JSON.stringify(obj));
                    if($('.box_' + key +' .show_all').not('--active') && $('.box_' + key +' .hide_all').not('--active')){
                        $('.box_' + key +' .show_all').addClass('--active');
                    }
                }
            }
        }
    } else {
        if (data.search_obj) {
            let obj = JSON.parse(data.search_obj);
            for (key in obj) {
                var all = $('.box_' + key + ' label');
                let count_search = 0;
                if (obj[key] == '') {
                    // console.log('empty');
                    $.each(all, function (key, value) {
                        if ($(value).hasClass('filter-hidden') && count_search < 5 && $(value).not('.disabled').length) {
                            count_search++;
                            $(value).removeClass('filter-hidden');
                        }
                    });
                    if(document.querySelector('.remove-'+key)) {
                        document.querySelector('.remove-' + key).remove();
                    }
                }
                if (obj[key].length > 0) {
                    document.getElementById(key).value = obj[key];
                    if(!document.querySelector('.remove-'+key)){
                        document.getElementById(key).insertAdjacentHTML('afterend', '<span data-id="'+key+'" class="search-text-remove remove-'+key+'"></span>');
                    }
                    // console.log(obj[key]);
                    var str = obj[key].toLowerCase();
                    $.each(all, function (key, value) {
                        var string = value.dataset.name.toLowerCase();
                        if (string.indexOf(str) == -1) {
                            $(value).removeClass('--show');
                        } else {
                            if (($(value).hasClass('--show') && count_search < 5) || ($(value).hasClass('filter-hidden') && count_search < 5 && $(value).not('.disabled').length)) {
                                count_search++;
                                $(value).addClass('--show');
                            } else {
                                $(value).removeClass('--show');
                            }
                        }
                    });
                }
                if(count_search < 5) {
                    $('.box_' + key + ' .expand_block.show_all').removeClass('--active');
                    $('.box_' + key + ' .expand_block.hide_all').removeClass('--active');
                } else {
                    $('.box_' + key + ' .expand_block.show_all').addClass('--active');
                }
            }
        }
    }
}

function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

// $(document).click(function (e) {
// 	var btn_menu = $('.header-v10 .mega-menu table td.catalog');
// 	if (btn_menu.has(e.target).length === 0) {
// 		btn_menu.removeClass('active_menu');
// 		$('.icon-close').addClass('hide');
// 		$('.icon-open').removeClass('hide');
// 		btn_menu.find("> .wrap > .dropdown-menu").toggle('slow');
// 	};
// });

// $('.header-v10 .mega-menu table td.catalog').on('mouseover mouseenter mouseleave mouseup mousedown', function() {
// 	// console.log('asd');
// 	event.stopPropagation();
// 	return false;
// });