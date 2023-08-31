

function azu_include_style(id,selector,data) {
    ( function( $ ) {
        var azu_custom_style = $('style[id="'+id+'"]');
        if(azu_custom_style.length>0)
            azu_custom_style.html(data);
        else
            $(selector).first().after('<style type="text/css" id="'+id+'" media="all">'+data+'</style>');
        
    } )( jQuery );
}

function azu_include_link(id,link) {
    ( function( $ ) {
        $('link[id="'+ id + '-font-preview"]').remove();
        $('head').append( '<link id="' + id + '-font-preview" href="' + link + '" rel="stylesheet" type="text/css">');
    } )( jQuery );
}

/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */



( function( $ ) {
        "use strict";
        function cus_html(value, selector){
		value.bind( function( to ) {
			$( selector ).html( to );
		} );
        } 
        
        function cus_text(value, selector){
		value.bind( function( to ) {
			$( selector ).text( to );
		} );
        }
        function cus_hide(value, selector, reverse){
                reverse = typeof reverse === 'undefined' ? true: reverse;
		value.bind( function( to ) {
                var ob = $(selector);
                if(ob.length>0) {
                    if(to && reverse)
                        ob.show();
                    else
                        ob.hide();
                }
            } );
        }
        function cus_class(value, selector, $class,reverse){
                reverse = typeof reverse === 'undefined' ? true: reverse;
		value.bind( function( to ) {
                var ob = $(selector);
                if(ob.length>0) {
                    if(to && reverse)
                        ob.addClass($class);
                    else
                        ob.removeClass($class);
                }
            } );
        }
        
        function cus_align(value, selector){
            value.bind( function( to ) {
                var ob = $(selector);
                ob.removeClass('col-middle col-sm-auto-right col-sm-auto');
                if(to=='center')
                    ob.addClass('col-middle');
                else if(to=='right')
                    ob.addClass('col-sm-auto-right');
                else if(to=='left')
                    ob.addClass('col-sm-auto');
            } );
        }
        function cus_radio(value, selector,arr,prefix){
            prefix = typeof prefix === 'undefined' ? '': prefix;
            value.bind( function( to ) {
                var ob = $(selector),remove='';
                for (var cls in arr) {
                  if (arr.hasOwnProperty(cls)) { 
                      if( to === cls)
                        ob.addClass(prefix + arr[cls]);
                      else
                        remove += ' ' + prefix + arr[cls];
                  }
                }
                ob.removeClass(remove);
            } );
        }
        
        function cus_size(value, selector, attr, diff){
            diff = typeof diff === 'undefined' ? 0: parseFloat(diff);
            value.bind( function( to ) {
                var ob = $(selector),str_array = attr.split(",");
                for(var i = 0; i < str_array.length; i++) {
                    ob.css( str_array[i], (diff + parseFloat(to)) +'px' );
                }
            } );
        }

        function cus_attr(value, selector, attr, unit){
            unit = typeof unit === 'undefined' ? '': unit;
            value.bind( function( to ) {
                var ob = $(selector),str_array = attr.split(",");
                for(var i = 0; i < str_array.length; i++) {
                    ob.attr( str_array[i], to + unit );
                }
            } );
        }
        
        function cus_image(value, selector, element,header){
            header = typeof header === 'undefined' ? false: header;
            value.bind( function( to ) {
                var ob = $(element, selector);
                if(ob.length > 0)
                    ob.attr( 'src', to );
                else {
                    ob = $(selector);
                    ob.show();
                    if(header)
                        $('.site-title').hide();
                    if($('a',selector).length === 0){
                        ob.append('<a href="#"></a>');
                    }
                    $('a',selector).append('<img class="'+element.replace(".", "")+'" src="'+to+'">');
                }
            } );
        }
        
        function cus_css(value,selector,attr,unit){
            unit = typeof unit === 'undefined' ? '': unit;
            value.bind( function( to ) {
                var ob = $(selector),str_array = attr.split(",");
                for(var i = 0; i < str_array.length; i++) {
                    if(str_array[i] == 'background-image')
                        to = "url('" + to + "')";
                    else if(str_array[i] == 'background-attachment'){
                        to = to ? "fixed" : "scroll";
                    }
                    else if(str_array[i] == 'background-size'){
                        to = to ? "cover" : "auto";
                    }
                    else
                        to = to + unit;
                    ob.css( str_array[i], to );
                }
            } );
        }
        
        function cus_background_bind(id,selector,attr){
                wp.customize(get_full_key(id),function( value ) {
                    cus_css(value,selector, attr);
                } );
        }
        
        function cus_background(id,selector){
                var arr = ['image','repeat','position-x','position-y'];
                for(var i = 0; i < arr.length; i++) {
                    cus_background_bind(id + '][' + arr[i].replace('-', '_'),selector,'background-' + arr[i]);
                }
        }

        var api = wp.customize;    
        function get_full_key(key){
            return  azuGlobals.theme.toLowerCase().replace("-", "") + '['+ key +']';
        }
        
        /* General */
        api(get_full_key('general-layout'),function( value ) {
            value.bind( function( to ) {
                $('body').addClass('azu-boxed');
                $('.azu-page, .azu-topbar, .azu-navigation, .azu-header, .azu-page-header, .azu-content, .azu-footer, .azu-bottombar').removeClass('container nopadding');
                if(to=='wide'){
                      $('body').removeClass('azu-boxed');
                }
                else if(to=='boxed'){
                      $('.azu-page').addClass('container nopadding');
                }
                else if(to=='menu'){
                      $('.azu-topbar, .azu-header, .azu-page-header, .azu-content, .azu-footer, .azu-bottombar').addClass('container nopadding');
                }
                else if(to=='top'){
                      
                      $('.azu-navigation, .azu-header, .azu-page-header, .azu-content, .azu-footer').addClass('container nopadding');
                }
                else if(to=='header'){
                      $('.azu-page-header, .azu-content').addClass('container nopadding');
                }
            });
        } );
        
        api( get_full_key('azu-layout-width'), function( value ) {
            var selector ='style[id="style-layout-width"]';
            if($(selector).length===0)
                $('link[id="style-css"]').after('<style id="style-layout-width" type="text/css"></style>');
            value.bind( function( to ) {
                    to = parseInt(to);
                    to = '@media (min-width:960px) { .container { width: '+(960 - parseInt(azuLocal.gutter_width))+'px !important; }} @media (min-width:'+to+'px) { .container { width: '+(to - parseInt(azuLocal.gutter_width))+'px !important; }}';
                    $( selector ).html( to );
            } );
	} );
        
        api(get_full_key('general-layout-style'),function( value ) {
            cus_radio(value, 'body', {none:"none",divider:"divider",full:"full",boxed:"boxed"},"azu-content-style-");
        } );
        
        api(get_full_key('hover-style'),function( value ) {
            cus_radio(value, 'body', {none:"none",grayscale:"grayscale",color:"color",zoom:"zoom",blur:"blur"},"azu-main-hover-");
        } );
//        api(get_full_key('general-hover_icon'),function( value ) {
//            cus_class(value,'body','general-hover-icon-on');
//        } );
        //general-button-style
        api(get_full_key('general-thin_divider_style'),function( value ) {
            cus_radio(value, 'body', { "style-1":"style-1","style-2":"style-2","style-3":"style-3"},"azu-divider-");
        } );
        
        // CSS & JS
        api( get_full_key('general-custom_css'), function( value ) {
            if($('style[id="style-inline-css"]').length===0)
                $('link[id="style-css"]').after('<style id="style-inline-css" type="text/css"></style>');
            cus_html(value, 'style[id="style-inline-css"]');
	} );
        
	api( get_full_key('general-tracking_code'), function( value ) {
                cus_html(value, 'script[id="azu_custom_js"]');
	} );
        
        //advanced
        
        api(get_full_key('general-site-title'),function( value ) {
            cus_class(value,'.site-title','azu-seo-text',false);
        } );
        
        
        api(get_full_key('title-bg-height'),function( value ) {
            cus_css(value,'.azu-page-header','height','px');
        } );
        
        
        api(get_full_key('general-preloader'),function( value ) {
            value.bind( function( to ) {
                var ob = $('#preloader');
                if(ob.length>0) {
                    if(to){
                        ob.css('opacity',1);
                        ob.show();
                        setTimeout(function(){ ob.hide(); }, 2000);
                    }
                    else
                        ob.hide();
                }
            });
        } );
        
        api(get_full_key('general-scrollbar'),function( value ) {
            cus_class(value,'body','azu-scroll-bar-style-on');
        } );
        
        api(get_full_key('header-bg-height'),function( value ) {
            value.bind( function( to ) {
                    var ob = $('.navbar-header .azu-branding img'), ht = $('#site-navigation').css('height');
                    to = parseInt(to);
                    if(parseInt(ht) > to)
                        ob.css('height',to);
                    else
                        ob.css('height',ht);
            } );
        } );
        
        /* Header & Footer */
        api(get_full_key('top_bar-show'),function( value ) {
            cus_hide(value,'.azu-topbar-container');
        } );
        api(get_full_key('top_bar-content_alignment'),function( value ) {
            cus_align(value,'#topbar-widget > .azu-widget');
        } );
        api(get_full_key('top-bar-text'),function( value ) {
            cus_text(value, '.azu-topbar-text span');
        } );
        api(get_full_key('top_bar-arrow'),function( value ) {
            cus_hide(value,'.azu-topbar-arrow');
        } );
        api(get_full_key('header-show_floating_menu'),function( value ) {
            value.bind( function( to ) {
                if(to) {
                    if(azuLocal.floatingMenu == 0)
                        $(window).on('scroll', floating_navbar_call);
                    azuLocal.floatingMenu = 1;
                }
                else 
                    azuLocal.floatingMenu = -1;
                floating_navbar_call();
            } );
        } );
//        api(get_full_key('header-layout'),function( value ) {
//            cus_radio(value, 'body', {left:"left",middle:"middle",center:"center",right:"right",side:"side"},"azu-header-layout-");
//        } );
        
        api(get_full_key('header-menu_alignment'),function( value ) {
            cus_radio(value, 'body', {left:"left",center:"center",right:"right"},"azu-nav-menu-alignment-");
        } );
        
        api(get_full_key('menu-bg-height'),function( value ) {
            value.bind( function( to ) {
                    var ob = $('#site-navigation,#fixed_navigation'),menu_image=wp.customize.value(get_full_key('menu-image-position')).get();
                    $('.azu-nav-start').css('min-height' , to + 'px' );
                    ob.css('height' , to + 'px' );
                    ob = ob.first();
                    ob.find('.navbar-header .azu-branding img').css('height' , to + 'px' ).css('max-height' , to + 'px' );
                    ob.find('.azu-widget-area').css('height' , to + 'px' );
                    
                    ob.find('.azu-widget-area > .azu-widget').each(function(){
                        if($(this).hasClass('widget_search') || $(this).hasClass('widget_shopping_cart') || $(this).hasClass('widget_nav_menu') || $(this).hasClass('widget_azzu-wpml') || $(this).hasClass('widget_icl_lang_sel_widget')){ 
                            var wh = $(this).find(':not(.widget-title):first').height();
                            if(wh === 0)
                                wh = 37;
                            if($(this).hasClass('widget_nav_menu'))
                                $(this).find(':first-child').css('padding-top',(to - 37)/2 + 'px' );
                            else
                                $(this).css('padding-top',(to - parseInt(wh))/2 + 'px' );
                        }
                    });
                    
                    ob = ob.find('.navbar-nav > li > a');
                    ob.css('height' , to + 'px' );
                    if( menu_image === 'left' || menu_image === 'right')
                        ob.css('line-height' , to + 'px' );
                    else
                        ob.css('line-height' , 'normal' );
                    var pd = (to - ob.find(':first-child').css('height'))/2;
                    ob.css('padding-top' , pd + 'px' );
                    ob.css('padding-bottom' , pd + 'px' );
                    //dependency another controll
                    var vl = wp.customize.value(get_full_key('menu-item-padding')).get();
                    wp.customize.value(get_full_key('menu-item-padding')).set(vl+1);
                    wp.customize.value(get_full_key('menu-item-padding')).set(vl);
            } );
        } );
        
        api(get_full_key('menu-side-width'),function( value ) {
            value.bind( function( to ) {
                var ob = $('body');
                if(parseInt(ob.css('padding-left'))>200)
                {
                    ob.css('padding-left',to+'px');
                    $('.navbar-offcanvas').css('width',to+'px');
                    
                }
                else if(parseInt(ob.css('padding-right'))>200)
                {
                    ob.css('padding-right',to+'px');
                    $('.navbar-offcanvas').css('width',to+'px');
                }
            } );
        } );
        
        api(get_full_key('header-hover_style'),function( value ) {
            cus_radio(value, 'body', {none:"none",though:"though",top:"top",bottom:"bottom",underline:"underline",text:"text",border:"border",bg:"bg"},"azu-nav-hover-style-");
        } );
        
        api(get_full_key('header-caret-style'),function( value ) {
            cus_radio(value, 'body', {none:"none",caret:"caret",effect:"effect"},"azu-menu-ct-");
        } );
        
        api(get_full_key('menu-image-position'),function( value ) {
            cus_radio(value, 'body', {left:"left",top:"top",right:"right",bottom:"bottom"},"azu-nav-icon-position-");
        } );
        
        api(get_full_key('menu-item-padding'),function( value ) {
            value.bind( function( to ) {
                var ob = $('#site-navigation .navbar-nav > li > a'),menu_style = wp.customize.value(get_full_key('menu-item-style')).get(),menu_height = wp.customize.value(get_full_key('menu-bg-height')).get();
                if(menu_style !== 'none'){
                    var padding = (menu_height - to * 2);
                    ob.css('height',padding+'px');
                    ob.css('margin-top',to+'px');
                    ob.css('margin-bottom',to+'px');
                    padding = (padding - parseInt(ob.find('span:first-child').css('height')))/2;
                    ob.css('padding-bottom',padding+'px').css('padding-top',padding+'px');
                    ob.css('line-height' , 'normal' );
                    if(menu_style !== 'divider')
                        $("#static-stylesheet").html('.azu-nav-style-divider .navbar-azu .navbar-nav > li + li > a:before{ bottom: 0px !important; top:0px !important; }');
                }
                else {
                    ob.css('margin-bottom','0px').css('margin-top','0px').css('padding-bottom','0px').css('padding-top','0px');
                    ob.css('line-height' , menu_height+'px' ).css('height','auto');
                }
            } );
        } );
        
        api(get_full_key('menu-item-style'),function( value ) {
            value.bind( function( to ) {
                var selector='body', prefix = "azu-nav-style-",arr = {none:"none",border:"border",divider:"divider"};
                var ob = $(selector),remove='';
                for (var cls in arr) {
                  if (arr.hasOwnProperty(cls)) { 
                      if( to === cls)
                        ob.addClass(prefix + arr[cls]);
                      else
                        remove += ' ' + prefix + arr[cls];
                  }
                }
                ob.removeClass(remove);
                //dependency another controll
                var vl = wp.customize.value(get_full_key('menu-item-padding')).get();
                wp.customize.value(get_full_key('menu-item-padding')).set(vl+1);
                wp.customize.value(get_full_key('menu-item-padding')).set(vl);
            } );
        } );
        
        
        api(get_full_key('header-icons_size][width'),function( value ) {
            cus_css(value, '#site-navigation .navbar-nav > li > a > img', 'width', 'px');
        } );
        api(get_full_key('header-icons_size][height'),function( value ) {
            cus_css(value, '#site-navigation .navbar-nav > li > a > img', 'height', 'px');
        } );
        
        api(get_full_key('header-submenu_next_level_indicator'),function( value ) {
            cus_class(value, 'body','azu-submenu-next-level-ind');
        } );
        
        api(get_full_key('header-submenu_icons_size][width'),function( value ) {
            cus_css(value, '#site-navigation .navbar-nav li ul > li > a > img', 'width', 'px');
        } );
        api(get_full_key('header-submenu_icons_size][height'),function( value ) {
            cus_css(value, '#site-navigation .navbar-nav li ul > li > a > img', 'height', 'px');
        } );
          
        
        api(get_full_key('menu-items_distance'),function( value ) {
            cus_size(value, '#site-navigation .navbar-nav > li', 'margin-right', -20);
        } );
        
        api(get_full_key('sidebar_distance'),function( value ) {
            value.bind( function( to ) {
                    var content = $( '#primary.azu-content-area' );
                    if(content.length > 0){
                        var half_gutter = parseInt(azuLocal.gutter_width)/2;
                        if(content.hasClass('azu-content-right'))
                            content.css( 'padding-right' ,(to - half_gutter) +'px' );
                        else if(content.hasClass('azu-content-left'))
                            content.css( 'padding-left' ,(to - half_gutter) +'px' );
                        else if(content.hasClass('azu-content-dual')){
                            content.css( 'padding-left' ,(to - half_gutter) +'px' );
                            content.css( 'padding-right' ,(to - half_gutter) +'px' );
                        }
                    }
            } );
        } );
        api(get_full_key('sidebar_wide'),function( value ) {
            value.bind( function( to ) {
                    var content = $( '#primary.azu-content-area' ),sidebar = $('.azu-sidebar-column');
                    if(sidebar.length > 0){
                        if(to){
                            if(content.hasClass('azu-content-right') || content.hasClass('azu-content-left'))
                            {   content.removeClass( 'col-sm-4 col-sm-6 col-sm-9' ).addClass( 'col-sm-8' );
                                sidebar.removeClass( 'col-sm-3' ).addClass( 'col-sm-4' );
                            }
                            else if(content.hasClass('azu-content-dual')){
                                content.removeClass( 'col-sm-6 col-sm-8 col-sm-9' ).addClass( 'col-sm-4' );
                                sidebar.removeClass( 'col-sm-3' ).addClass( 'col-sm-4' );
                            }
                        }
                        else{
                            if(content.hasClass('azu-content-right') || content.hasClass('azu-content-left'))
                            {   content.removeClass( 'col-sm-4 col-sm-6 col-sm-8' ).addClass( 'col-sm-9' );
                                sidebar.removeClass( 'col-sm-4' ).addClass( 'col-sm-3' );
                            }
                            else if(content.hasClass('azu-content-dual')){
                                content.removeClass( 'col-sm-4 col-sm-8 col-sm-9' ).addClass( 'col-sm-6' );
                                sidebar.removeClass( 'col-sm-4' ).addClass( 'col-sm-3' );
                            }
                        }

                    }
            } );
        } );
        api(get_full_key('sidebar_position'),function( value ) {
            value.bind( function( to ) {
                    var content = $( '#primary.azu-content-area' ),sidebar = $('.azu-sidebar-column:not(azu-sidebar-left)');
                    if(sidebar.length > 0){
                        var second_sidebar = $('#sidebar-left').parent().parent();
                        $('.azu-sidebar-column').show();
                        content.css( 'width', '' );
                        content.removeClass( 'azu-content-left azu-content-right azu-content-dual col-sm-push-3 col-sm-push-4' );
                        if(to === 'left' || to === 'right') {
                            content.addClass( 'azu-content-' + to );
                            content.removeClass( 'col-sm-4 col-sm-6' );
                            if(sidebar.hasClass('col-sm-4'))
                                content.addClass( 'col-sm-8' );
                            else
                                content.addClass( 'col-sm-9' );
                            second_sidebar.hide();
                            sidebar.removeClass( 'col-sm-push-3 col-sm-push-4' );
                        }
                        else if(to === 'dual'){
                            content.removeClass( 'col-sm-8 col-sm-9' );
                            content.addClass( 'azu-content-dual' );
                            if(sidebar.hasClass('col-sm-4'))
                                content.addClass( 'col-sm-push-4 col-sm-4' );
                            else 
                                content.addClass( 'col-sm-push-3 col-sm-6' );
                            if(second_sidebar.length > 0)
                                second_sidebar.show();
                            else {
                                if(sidebar.hasClass('col-sm-4'))
                                    sidebar.addClass( 'col-sm-push-4' );
                                else
                                    sidebar.addClass( 'col-sm-push-3' );
                            }   
                        }
                        else {
                            content.css( 'width', '100%' );
                            $('.azu-sidebar-column').hide();
                        }
                    }
            } );
        } );
        
//        api(get_full_key('sidebar_sticky'),function( value ) {
//            cus_class(value, 'body','azu-sticky-js');
//        } );
        
        /* Footer */
        
        api(get_full_key('bottom_bar-copyrights'),function( value ) {
            cus_text(value, '.azu-bottombar-copy span');
        } );
        api(get_full_key('footer_show'),function( value ) {
            value.bind( function( to ) {
                var ob = $('.azu-footer-container > .azu-widget'), clss = '';
                ob.removeClass(function (index, css) {
                    return (css.match (/\bcol-sm-\S+/g) || []).join(' ');
                });
                if(to == "disabled" || ob.length==0){
                    $('.azu-footer-container').hide();
                    return;
                }
                else
                    $('.azu-footer-container').show();
                $('.azu-footer-container > .footer-divider').remove();
                ob.each(function (index){
                    clss = 4;
                    switch(to){
                        case "one": 
                                clss = 12;
                                break;
                        case "two": 
                                clss = 6;
                                break;
                        case "tree": 
                                clss = 4;
                                break;
                        case "three1":
                            if(index%3 == 0)
                                clss=6;
                            else
                                clss=3;
                            break;
                        case "three2":
                            if(index%3 == 2)
                                clss=6;
                            else
                                clss=3;
                            break;
                        case "four": 
                                clss = 3;
                                break;
                        case "six": 
                                clss = 2;
                                break;
                    }
                    $(this).addClass('col-sm-' + clss);
                    var $print_divider=false;
                    if(index>1)
                        switch (to) {
                            case 'two':
                                if(index%2 == 0)
                                    $print_divider=true;
                                break;
                            case 'one':
                                break;
                            case 'six':
                                if(index%6 == 0)
                                    $print_divider=true;
                                break;
                            case 'four':
                                if(index%4 == 0)
                                    $print_divider=true;
                                break;
                            default:
                                if(index%3 == 0)
                                    $print_divider=true;
                                break;
                        }
                    if($print_divider)
                        $(this).after('<div class="footer-divider col-sm-12"><hr></div>');
                });
            } );
        } );
        api(get_full_key('bottom_bar-content_alignment'),function( value ) {
            cus_align(value,'.azu-widget-area.azu-bottombar-td-4 > .azu-widget');
        } );
        api(get_full_key('general-scrollup'),function( value ) {
            value.bind( function( to ) {
                var ob = $('.azu-scroll-top-wrapper');
                if(ob.length>0) {
                    if( parseInt(to) == 0 )
                        ob.css('cssText','display: none !important;');
                    else if(parseInt(to) == 1)
                        ob.addClass('hidden-lg hidden-md');
                    else {
                        ob.css('display','');
                        ob.removeClass('hidden-lg hidden-md');
                    }
                }
            } );
        } );
        api(get_full_key('bottom-bar-padding'),function( value ) {
            value.bind( function( to ) {
                var ob = $('.azu-bottombar-td-1'),height = wp.customize.value(get_full_key('bottombar-bg-height')).get();
                ob.css('padding-top', to + 'px').css('padding-bottom', to + 'px');
                $('.azu-bottombar-td-2, .azu-bottombar-td-3, .azu-bottombar-td-4 > .azu-widget').css('height', (to * 2 + parseInt(height) ) + 'px');
            } );
        } );
        

        /* Image */
        
	api( get_full_key('general-favicon'), function( value ) {
                value.bind( function( to ) {
                    $('link[rel="icon"],link[rel="shortcut icon"]',window.parent.document).remove();
                    $('head',window.parent.document).append('<link rel="icon" href="'+to+'"><link rel="shortcut icon" href="'+to+'">');
                });
	} );
        
        api( get_full_key('header-logo][uri'), function( value ) {
                cus_image(value, '#site-navigation .azu-branding', '.logo-default',true);
	} );
        
        api( get_full_key('header-float-logo][uri'), function( value ) {
                cus_image(value, '#site-navigation .azu-branding', '.logo-float',true);
	} );
        
        api( get_full_key('header-light-logo][uri'), function( value ) {
                cus_image(value, '#site-navigation .azu-branding', '.logo-light',true);
	} );
        
        api(get_full_key('header-bg-height'),function( value ) {
            value.bind( function( to ) {
                    var ob = $('.navbar-header .azu-branding img'), ht = $('#site-navigation').css('height');
                    to = parseInt(to);
                    if(parseInt(ht) > to)
                        ob.css('height',to);
                    else
                        ob.css('height',ht);
            } );
        } );
        
        api( get_full_key('bottom-bar-logo][uri'), function( value ) {
                cus_image(value, '.azu-bottombar #branding-bottom', '.azu-the-image');
	} );
        
        api(get_full_key('bottombar-bg-height'),function( value ) {
            value.bind( function( to ) {
                    to = parseInt(to);
                    var ob = $('.azu-bottombar .azu-bottombar-td-1 a');
                    ob.css('height',to);
                    ob.css('min-height',to);
                    $('.azu-bottombar .azu-bottombar-td-1 a img').css('max-height',to).css('height',to);
            } );
        } );
        
        cus_background('general-boxed_bg_image','body');
        api( get_full_key('general-boxed_bg_fullscreen'), function( value ) {
                cus_css(value, 'body','background-size');
	} );
        api( get_full_key('general-boxed_bg_fixed'), function( value ) {
                cus_css(value, 'body','background-attachment');
	} );
        cus_background('top_bar-bg_image','.azu-topbar'); 
        cus_background('header-bg_image','.azu-header');
        cus_background('title-bg-image','.azu-page-header');
        api( get_full_key('title-bg-fullscreen'), function( value ) {
                cus_css(value, '.azu-page-header','background-size');
	} );
        cus_background('general-bg_image','.azu-content');
        api( get_full_key('general-bg_fullscreen'), function( value ) {
                cus_css(value, '.azu-content','background-size');
	} );
        cus_background('sidebar-bg_image','.azu-sidebar-area');
        cus_background('footer-bg_image','.azu-footer');
        cus_background('bottom_bar-bg_image','.azu-bottombar');
        
        /* Blog & Portfolio */
        api(get_full_key('general-rel_posts_head_title'),function( value ) {
            cus_text(value, '#azu-rel-posts-title');
        } );
        api(get_full_key('general-rel_projects_head_title'),function( value ) {
            cus_text(value, '#azu-rel-projects-title');
        } );
        
    	// Site title and description.
	api( 'blogname', function( value ) {
                cus_text(value, '.site-title a');
	} );
	api( 'blogdescription', function( value ) {
                cus_text(value, '.site-description');
	} );
        
        /* Theme additional */

        
        
        $(document).ready(function () {
            //customizer menu fix dropdown
            $('#site-navigation .dropdown-toggle:not(hoverReady)[data-toggle="dropdown"]').live('mouseover', function() {
                $(this).addClass('hoverReady').dropdownHover();
            });
        });

} )( jQuery );