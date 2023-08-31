/**
 * @preserve
 * Project: Bootstrap Hover Dropdown
 * Author: Cameron Spear
 * Version: v2.0.11
 * Contributors: Mattia Larentis
 * Dependencies: Bootstrap's Dropdown plugin, jQuery
 * Description: A simple plugin to enable Bootstrap dropdowns to active on hover and provide a nice user experience.
 * License: MIT
 * Homepage: http://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin/
 */
;(function ($, window, undefined) {
    // outside the scope of the jQuery plugin to
    // keep track of all dropdowns
    var $allDropdowns = $();

    // if instantlyCloseOthers is true, then it will instantly
    // shut other nav items when a new one is hovered over
    $.fn.dropdownHover = function (options) {
        // don't do anything if touch is supported
        // (plugin causes some issues on mobile)
        if('ontouchstart' in document) return this; // don't want to affect chaining

        // the element we really care about
        // is the dropdown-toggle's parent
        $allDropdowns = $allDropdowns.add(this.parent());

        return this.each(function () {
            var $this = $(this),
                $parent = $this.parent(),
                defaults = {
                    delay: 500,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                showEvent   = 'show.bs.dropdown',
                hideEvent   = 'hide.bs.dropdown',
                // shownEvent  = 'shown.bs.dropdown',
                // hiddenEvent = 'hidden.bs.dropdown',
                settings = $.extend(true, {}, defaults, options, data),
                timeout;
            var $double_event = false;
            $parent.hover(function (event) {
                // so a neighbor can't open the dropdown
                if(!$parent.hasClass('open') && !$this.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    $double_event = true;
                    return true;
                }
                else
                    $double_event = false;

                openDropdown(event);
            }, function () {
                if(azuLocal.offCanvas)
                    return;
                timeout = window.setTimeout(function () {
                    $parent.removeClass('open');
                    $this.trigger(hideEvent);
                }, settings.delay);
            });

            // this helps with button groups!
            $this.hover(function (event) {
                // this helps prevent a double event from firing.
                // see https://github.com/CWSpear/bootstrap-hover-dropdown/issues/55
                if(!$parent.hasClass('open') && !$parent.is(event.target) ) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    if($double_event)
                        $double_event = false;
                    else
                        return true;
                }
                else
                    $double_event = false;

                openDropdown(event);
            });

            // handle submenus
            $parent.find('.dropdown-submenu').each(function (){
                var $this = $(this);
                var subTimeout;
                $this.hover(function () {
                    if(azuLocal.offCanvas)
                        return;
                    window.clearTimeout(subTimeout);
                    $this.children('.dropdown-menu').show();
                    // always close submenu siblings instantly
                    $this.siblings().children('.dropdown-menu').hide();
                }, function () {
                    if(azuLocal.offCanvas)
                        return;
                    var $submenu = $this.children('.dropdown-menu');
                    subTimeout = window.setTimeout(function () {
                        $submenu.hide();
                    }, settings.delay);
                });
            });

            function openDropdown(event) {
                if(azuLocal.offCanvas)
                    return;
                $allDropdowns.find(':focus').blur();
                if(settings.instantlyCloseOthers === true)
                    $allDropdowns.removeClass('open');

                window.clearTimeout(timeout);
                $parent.addClass('open');
                $this.trigger(showEvent);
                closeBurger();
            }
        });
    };
    
    // /* Off Canvas Menu */
    
    function megamenu_to_float(stat){
        $('.azumm-fw > ul.azumm-content > li.menu-item-has-children > a').each( function() {
            if(stat)
                $(this).parent().children('.azu-mega-menu-item').hide();
            else  
                $(this).parent().children('.azu-mega-menu-item').show();
        });
    }
    
    var     openbtn = $( '.navbar-toggle' );
    azuLocal.isOpen = false;
    azuLocal.offCanvas = true;
    
    function toggleMenu() {
            var navbar_collapse = $('#azu-navbar-collapse');
            if( azuLocal.isOpen ) {
                azuLocal.offCanvas = false;
                if(Modernizr.csstransforms3d){
                    navbar_collapse.css('-webkit-transform','');
                    navbar_collapse.css('transform','');
                }

                if(azuGlobals.menualign == 'left')
                    navbar_collapse.css('left','');
                else
                    navbar_collapse.css('right','');

                megamenu_to_float(azuLocal.offCanvas);
                $('div.azu-ui-mask-modal').removeClass('azu-ui-mask-visible');
                $('html').removeClass('azu-mask-disable-scroll');
                
                setTimeout(function() { 
                    navbar_collapse.removeClass('azu-active'); 
                    navbar_collapse.css('margin-top','');
                    navbar_collapse.css('height','');
                    navbar_collapse.css('width','');
                }, 350);
                $(".navbar-header button.navbar-toggle").removeClass('active'); 
            }
            else {
//                var top_point = $(window).scrollTop() - $("#start_navigation").offset().top;
//                navbar_collapse.css('top',top_point +'px');
                var br_width = azuGlobals.burger_width;
                // show hanburger menu
                if($(window).width()<=340) //only mobile
                {
                    br_width = $(window).width()-40;
                    navbar_collapse.css('width',br_width+'px');
                }
                br_width = br_width * (-1);
                if(azuGlobals.menualign == 'left'){
                    navbar_collapse.addClass('azu-burger-left');
                    br_width = Math.abs(br_width);
                }

                navbar_collapse.addClass('azu-active');
                if(Modernizr.csstransforms3d){
                    setTimeout(function() {
                        navbar_collapse.css('-webkit-transform','translateX('+br_width+'px)');
                        navbar_collapse.css('transform','translateX('+br_width+'px)');
                    }, 10);
                    if(azuGlobals.menualign == 'left')
                        navbar_collapse.css('left',br_width * (-1)+'px');
                    else
                        navbar_collapse.css('right',br_width+'px');
                }
                else {
                    if(azuGlobals.menualign == 'left')
                        navbar_collapse.css('left','0px');
                    else
                        navbar_collapse.css('right','0px');
                }
                
                $('html').addClass('azu-mask-disable-scroll');
                $('div.azu-ui-mask-modal').addClass('azu-ui-mask-visible');
                azuLocal.offCanvas = true;
                megamenu_to_float(azuLocal.offCanvas);
                if($("#wpadminbar").exists() && $("#wpadminbar").css("position")==='fixed'){
                    var mrg_top = parseFloat(navbar_collapse.css('margin-bottom')) + $("#wpadminbar").height();
                    navbar_collapse.css('margin-top',mrg_top+"px");
                    mrg_top +=parseFloat(navbar_collapse.css('margin-bottom'))/2;
                    navbar_collapse.css('height','calc(100vh - '+mrg_top+'px)');
                }
            }
            azuLocal.isOpen = !azuLocal.isOpen;
    }
    
    function closeBurger() {
        var burgers = $( '.azu-navigation-field .azu-widget.widget_nav_menu .azu-burger-container' );
        burgers.removeClass('azu-show-burger');
        burgers.find('ul.menu').removeClass('dl-menuopen');
    }
    
    $(document).ready(function () {
        if(azuLocal.menutype !== 'side')
            azuLocal.offCanvas = false;
        else if(azuLocal.menutype == 'side')
            megamenu_to_float(azuLocal.offCanvas);

        // apply dropdownHover to all elements with the data-hover="dropdown" attribute
        if(!azuLocal.offCanvas)
            $('.dropdown-toggle:not(.azu-social-share)[data-toggle="dropdown"]').addClass('hoverReady').dropdownHover();
        else {
            $('.dropdown-toggle:not(.azu-social-share)[data-toggle="dropdown"]').filter(function(){
                return $(this).parent().is(":not(.menu-item)");
            }).addClass('hoverReady').dropdownHover();
            azuLocal.offCanvas = false;
        }
        
        $('.azu-ui-mask-modal').on( 'click', toggleMenu);
        
        openbtn.on( 'click', toggleMenu );
//        if( closebtn ) {
//                closebtn.on( 'click', toggleMenu );
//        }
        
	/*!- Menu resize function*/
	$(window).on("debouncedresize", function( e ) {
            if( azuLocal.isOpen && azuLocal.menutype !== 'burger' && azuGlobals.mobile_width < $(window).width() ) {
                toggleMenu();
            }
            closeBurger();
	}).trigger( "debouncedresize" );
        
        // second burger menu widget
        $( '.azu-navigation-field .azu-widget.widget_nav_menu div' ).addClass('azu-burger-container').dlmenu({
                animationClasses : {  }
        });
        
        /**
          * NAME: Bootstrap 3 Triple Nested Sub-Menus
          * This script will active Triple level multi drop-down menus in Bootstrap 3.*
          */
        $('li.dropdown.menu-item-has-children > a, li.dropdown-submenu.menu-item-has-children > a, .azumm-fw > ul.azumm-content > li.menu-item-has-children > a').on('click', function(event) {
            if(azuLocal.offCanvas){
                // Avoid following the href location when clicking
                event.preventDefault(); 
                // Avoid having the menu to close when clicking
                event.stopPropagation(); 
                // Re-add .open to parent sub-menu item
                var $this = $(this).parent();
                if($this.hasClass('open')){
                    $this.children('.dropdown-menu, .azu-mega-menu-item').slideUp(200, function(){
                        $this.removeClass('open');
                    });
                }
                else  {
                    $this.children('.dropdown-menu, .azu-mega-menu-item').slideDown(300, function(){
                        $this.addClass('open');
                    });
                }
            }
            
        });
    });
    
})(jQuery, this);


(function($) {
"use strict";

azuLocal.fixedNavHeight = 50;

$.fn.exists = function () {
    return this.length !== 0;
};

// !- Responsive height hack
$.fn.heightHack = function() {
        return this.each(function() {
                var $img = $(this);
                if ($img.hasClass("height-ready") || $img.parents(".azu-testimonial-vcard, .rollover-small, .no-preload").exists()) {
                        return;
                }

                var	imgWidth = parseInt($img.attr('width')),
                        imgHeight = parseInt($img.attr('height')),
                        imgRatio = imgWidth/imgHeight,imgPadding = 100/imgRatio;
                
                if(imgPadding !== 100){
                    imgPadding = "calc("+100/imgRatio+"% - 0.001px)";
                }
                else {
                    imgPadding = 100/imgRatio+"%";
                }
                $img.parent().css({
                        "padding-bottom" : imgPadding,
                        "height" : 0,
                        "display" : "block"
                });
                $img.attr("data-ratio", imgRatio).addClass("height-ready");
        });
};

// !- Columns width calculation
$.fn.calculateColumns = function(mode) {
        var colNum = parseInt($(this).data('columns')), padding = parseInt($(this).data('padding'));
        var $container = $(this),
                min_width = (typeof $(this).data('min-width') !== 'undefined') ? parseInt($(this).data('min-width')) : 250,
                containerWidth = $container.parent().width(),
                containerPadding = (padding !== false) ? parseInt(padding) : 10,
                containerID = $container.attr("data-cont-id"),
                width = Math.floor(containerWidth / colNum),
                tempCSS = "",
                half_gutter = parseInt(azuLocal.gutter_width)/2;
        colNum = colNum ? colNum : 1;
        if(half_gutter < containerPadding && azuGlobals.mobile_width > $(window).width())
                containerPadding = half_gutter;
        containerWidth = containerWidth + containerPadding * 2;

        var containerMargin = containerPadding * -1;
        
        $container.css('width', 'calc(100% + '+(containerPadding * 2)+'px)');
        //if(colNum > 1){
            $container.css('margin-top',containerMargin+'px');
        //}
        $container.css('margin-left',containerMargin+'px');
        $container.css('margin-right',containerMargin+'px');
        
        width = Math.floor(containerWidth / colNum);
        if(width<min_width)
            colNum = Math.floor(containerWidth / min_width);
        var jsStyle;
        if (!$("#azu-style-id-"+containerID).exists()) {
                if(!$("html").hasClass("old-ie")){	// IE
                        jsStyle = document.createElement("style");
                        jsStyle.id = "azu-style-id-"+containerID;
                        jsStyle.appendChild(document.createTextNode(''));
                        document.head.appendChild(jsStyle);
                }
        } else {
                jsStyle = document.getElementById("azu-style-id-"+containerID);
        }
        var $style = $("#azu-style-id-"+containerID);

        var singleWidth,doubleWidth="100%";

        if(colNum==1){
            singleWidth = "100%";
            $container.removeClass('azu-multi-col');
        }
        else {
            $container.addClass('azu-multi-col');
            if (mode == "%") {
                    singleWidth = Math.floor(100000 / colNum)/1000 + "%";
                    doubleWidth = Math.floor(100000 / colNum)/500 + "%";
            }
            else {
                    singleWidth = Math.floor(containerWidth/colNum) + "px";
                    doubleWidth = (Math.floor(containerWidth/colNum) * 2) + "px";
            }
        }
        tempCSS = ".cont-id-"+containerID+" > .iso-item { width: "+(singleWidth )+"; padding: "+containerPadding+"px; }"+
                   ".cont-id-"+containerID+" > .iso-item.azu-media-wide { width: "+(doubleWidth )+"; padding: "+containerPadding+"px; }";

        if($("html").hasClass("old-ie")){
                $("#static-stylesheet").prop('styleSheet').cssText = tempCSS;
        }else{
                $style.html(tempCSS);
                var newRuleID = jsStyle.sheet.cssRules.length;
                jsStyle.sheet.insertRule(".webkit-hack { }", newRuleID);
                jsStyle.sheet.deleteRule(newRuleID);
        }
        $container.trigger("columnsReady");
        
};

$.fn.updateSliderSize = function() {
        var ratio = $(this).data("ratio"),
                sliderwidth = $(this).parent().width(),
                data_width = $(this).data("width"),
                data_padding = $(this).data("padding"),
                min_width = $(this).data("min-width"),
                data_height = $(this).data("height"),
                calculateHeight = false,columns = 1, 
                //mode = 'horizontal',
                proportion = true;
        if(!ratio){
            ratio = 1.75;
            proportion = false;
        }
        
        if(typeof $(this).data("swiper") !== 'undefined')
        {
            if(typeof $(this).data("swiper").params.slidesPerGroup !== 'undefined')
                columns = $(this).data("swiper").params.slidesPerGroup;
            if(typeof $(this).data("swiper").params.calculateHeight !== 'undefined')
                calculateHeight = $(this).data("swiper").params.calculateHeight;
        }
        if(typeof data_padding === 'undefined')
            data_padding = 0;
        if(typeof min_width === 'undefined')
            min_width = 200;
        if(typeof data_width === 'undefined')
            data_width = sliderwidth + 'px';
        if(typeof data_height == 'undefined')
            data_height = Math.round(parseInt(data_width)/ratio) + 'px';
        
        $(this).css('width', data_width);
        if(!calculateHeight)
            $(this).css('height',data_height);
        
        var half_gutter = parseInt(azuLocal.gutter_width)/2;
        if(data_padding !== 0) {
            if($(window).width() < 480)
                data_padding = 0;
            else if( half_gutter < data_padding && azuGlobals.mobile_width > $(window).width())
                data_padding = half_gutter;
        }
        
        if(columns>1){
            var slide_width;
            slide_width = Math.floor(parseInt(data_width) / columns);
            if(slide_width < min_width) {
                columns = Math.floor(parseInt(data_width) / min_width);
                slide_width = Math.floor(parseInt(data_width) / columns);
            }

            slide_width = slide_width - parseInt(data_padding) * 2;
            $(this).find('div.swiper-slide').each( function(i, v){
                $(this).css("padding",data_padding);
                if(sliderwidth<parseInt($(this).css("width")) )
                    $(this).css("width",sliderwidth);
                else 
                    $(this).css("width",slide_width);
            });
        }
        
        if(columns==1 && !calculateHeight) {
            $(this).find('div.swiper-slide > img.bcImg').each( function(i, v){
                if(i===0 && !proportion && ! $(this).parent().hasClass('swiper-slide-duplicate') ){
                    var img_ratio = $(this).attr('width')/$(this).attr('height');
                    if(img_ratio < 3.3 && img_ratio > 0.5) {
                        data_height = Math.round(parseInt(data_width)/img_ratio) + 'px';
                        $(this).parent().parent().parent().css('height',data_height);
                        $(this).css("height",data_height);
                        $(this).css("width",data_width);
                        return;
                    }
                }
                var img_deff = Math.round($(this).attr("width")/($(this).attr("height") / parseInt(data_height)) );
                if(parseInt(data_width)> img_deff){
                    $(this).css("height",data_height);
                    $(this).css("width",img_deff+'px');
                    $(this).css("margin-left",Math.floor((parseInt(data_width)-img_deff)/2)+'px');
                }
                else
                {
                    img_deff = Math.round($(this).attr("height")/($(this).attr("width") / parseInt(data_width)));
                    $(this).css("height",img_deff+'px');
                    $(this).css("width",data_width);
                    $(this).css("margin-top",Math.floor((parseInt(data_height)-img_deff)/2)+'px');
                }

            });
        }

//        if(calculateHeight && ( typeof $(this).data("swiper") !=='undefined') ) {
//            $(this).data("swiper").resizeFix();
//            if(calculateHeight){
//                $(this).data("swiper").reInit(true);
//            }
//        }
        $(this).css("opacity",1);
};

// !- Initialise slider
$.fn.initSlider = function(ratio,update) {
        var prefix = Math.floor(Math.random() * 1000).toString();
        var optiondefault = {
                    mode:'horizontal',//'vertical',
                    loop: true,
                    keyboardControl:true,
                    enable_arrow: true,
                    freeMode: false,
                    autoResize: false,
                    watchActiveIndex:true
                };
        return this.each(function(i, v) {
                if(!update && !$(this).hasClass('no-update'))
                    $(this).addClass('no-update');
                if($(this).is('[class*="swiper_"]')){
                    prefix = ( Math.floor(Math.random() * 10000)  + 1000 ).toString();
                    return;
                }
                var thisobject = $(this).data('option');
                if(typeof thisobject !=='undefined')
                    thisobject =  JSON.parse(JSON.stringify(thisobject));
                else
                    thisobject ={};
                var class_name = 'swiper_'+prefix+i;
                $(this).addClass(class_name);
                thisobject = $.extend({}, optiondefault, thisobject);
                if(typeof thisobject['pagination'] !== 'undefined')
                    thisobject['pagination'] = '.azu-swiper-container.'+class_name +' .carousel-indicator';

                var azuSwiper = new Swiper( '.'+class_name, thisobject);
                $(this).data("swiper",azuSwiper);
                //$(this).on('touchstart mousedown click',function(e){e.preventDefault(); });
                if(thisobject['enable_arrow']) {
                    $(this).find('.carousel-arrow-right').click(function(e){e.preventDefault(); azuSwiper.swipeNext();});
                    $(this).find('.carousel-arrow-left').click(function(e){e.preventDefault(); azuSwiper.swipePrev();});
                    thisobject['onSlideChangeEnd'] =  function(s){
                        if(!s.params.loop) {
                            var slidenum = s.activeIndex;
                            var sliderLength = (s.slides.length) - 1;
                            if ((slidenum === 0 ))
                                    $('.'+class_name+' .carousel-arrow-left').hide();
                            else 
                                    $('.'+class_name+' .carousel-arrow-left').show();

                            if ((slidenum == sliderLength))
                                    $('.'+class_name+' .carousel-arrow-right').hide();
                            else 
                                    $('.'+class_name+' .carousel-arrow-right').show();
                        }
                    };
                }
                else {
                    $(this).find('.carousel-arrow-right').hide();
                    $(this).find('.carousel-arrow-left').hide();
                }
                if(ratio !== '')
                    $(this).data("ratio",ratio);
                $(this).updateSliderSize();
        });
};

// !- Show items
$.fn.showItems = function() {
        return this.each(function() {
                
                var $item = $(this),
                        $img = $item.find(".preload-img:not(.height-ready)").first();

                if ($img.exists() && !$img.hasClass("bcImg")) {
                        $img.loaded(function() {
                                var $this = $(this);
                                setTimeout(function() {
                                        $this.parents(".iso-item").css({
                                                "opacity" : 1
                                        });					
                                }, 1);
                        }, null, true);
                }
                else {
                        setTimeout(function() {
                                $item.css({
                                        "opacity" : 1
                                });					
                        }, 1);
                }
                
        });
};

/* !- Check if element is loaded */
$.fn.loaded = function(callback, jointCallback, ensureCallback){
        var len	= this.length;
        if (len > 0) {
                return this.each(function() {
                        var	el		= this,
                                $el		= $(el);

                        $el.on("load.azu", function(event) {
                                $(this).off("load.azu");
                                if (typeof callback == "function") {
                                        callback.call(this);
                                }
                                if (--len <= 0 && (typeof jointCallback == "function")){
                                        jointCallback.call(this);
                                        
                                }
                        });

                        if (!el.complete || el.complete === undefined) {
                                el.src = el.src;
                        } else {
                                $el.trigger("load.azu");
                        }
                });
        } else if (ensureCallback) {
                if (typeof jointCallback == "function") {
                        jointCallback.call(this);
                }
                return this;
        }
};

var paddingTop = 0;

//main loader
$(window).load(function(){
        $('#preloader').fadeOut(200,function(){$(this).hide();});
        //fullWidthWrap();
        //setTimeout(function(){ SwiperResizeFix(); }, 100);
        $(".isotope.iso-grid,.isotope.iso-container", $('#page div[data-vc-full-width="true"]')).each(function() {
            if(typeof $(this).isotope === 'function')
               $(this).calculateColumns("px");
        });
        $(".isotope", $('#page')).each(function() {
            if(typeof $(this).isotope === 'function')
                $(this).isotope("layout");
        });
        if(azuGlobals.IsMobile === '0') {//only desktop
            setTimeout (function(){blurred_background();}, 100);
        }
        SearchFocusOnMenu();
        
        //tooltip
        $('.azu-social-share.dropdown-toggle[data-toggle="dropdown"]').addClass('hoverReady').parent().on('shown.bs.dropdown', function () {
            $(this).tooltip('hide');
        });
        if(azuGlobals.IsMobile === '0') //only desktop
            $('.azu-tooltip[data-toggle="tooltip"]').addClass('tooltipReady').tooltip();
        
});


var transEndEventNames = {
    'WebkitTransition' : 'webkitTransitionEnd', // Saf 6, Android Browser
    'MozTransition'    : 'transitionend',       // only for FF < 15
//    'OTransition'      : 'oTransitionEnd',
//    'msTransition'     : 'MSTransitionEnd',
    'transition'       : 'transitionend'        // IE10, Opera, Chrome, FF 15+, Saf 7+
};
var transEndEventName = transEndEventNames[ Modernizr.prefixed('transition') ];

// Navigation fixed
$(document).ready(function(){
           var top_space = parseInt(azuLocal.gutter_width)/2;
           if ($("#wpadminbar").exists())
                paddingTop = $("#wpadminbar").height();
           azu_love_post();
           image_popup($('#page')); //Magnific Popup
           azu_isotope(); //Masonry
           if(azuLocal.floatingMenu == 1) {
                floating_navbar();
                top_space += azuLocal.fixedNavHeight;
           }
           scrollToTop();
           navbar_toggle();
           topbar_arrow();
           azu_center_menu_padding();
           azu_pageborder();
           azu_fancy_block();
           azu_number_spinner();
           if(azuGlobals.IsMobile !== '1')
                azu_slideshow_detect();

           if(azuGlobals.IsMobile === '0') {
                keyboard_image_navigation(); //keyboard-image-navigation
                //Sticky sidebar
                $('.azu-sticky-js .azu-sidebar-column').theiaStickySidebar({
                        additionalMarginTop: top_space,
                        additionalMarginBottom: parseInt(azuLocal.gutter_width)/2
		});
                // VC single meta stticky
                var single_meta = $('.azu-portfolio-single-info.azu-sticky-js');
                if(single_meta.length > 0){
                    $('.azu-single-meta,.azu-post-meta').css('display','none');
                    single_meta = single_meta.first();
                    if(single_meta.hasClass('row'))
                        single_meta = single_meta.find('.azu-portfolio-single-details');
                    else
                        single_meta = single_meta.parent().parent();
                    if(!single_meta.hasClass('vc_col-sm-12')){
                        single_meta.theiaStickySidebar({ 
                            additionalMarginTop: top_space,
                            additionalMarginBottom: parseInt(azuLocal.gutter_width)/2
                        });
                    }
                }
                
            }
            azu_woo_cart(0);
            var comment_selector = { selector: "#azu-form-allowed-tags" };
            $(".comment-form-comment > textarea#comment").on( "focusin", comment_selector, azu_comment_slidedown ).on( "focusout", comment_selector, azu_comment_slidedown );
            comment_selector = { selector: ".comment-form .comment-notes" };
            $('.form-fields .form-group input[aria-required="true"]').on( "focusin", comment_selector, azu_comment_slidedown ).on( "focusout",comment_selector, azu_comment_slidedown );
	});
        
    function azu_comment_slidedown(e){ 
        var atag = $(e.data.selector); 
        if(e.type =="focusin")atag.slideDown("normal");
        else atag.slideUp("fast"); 
    } 
         

var img_slide='',tp_targets='';
function azu_slideshow_detect(){
        var slideshow_container = $('#main-slideshow');
        
        if(slideshow_container.length === 0)
            return;
        var sImages='',sEvent ='',$var_name = '',event_data,time_delay = 500;
        switch ( slideshow_container.data('mode') ) {
                case 'revolution' :
                        sImages = '.azu-slider-container li img';
                        sEvent = 'revolution.slide.onafterswap'; //revolution.slide.onloaded
                        img_slide = '.azu-slider-container li.active-revslide .tp-bgimg';  
                        $var_name = 'revapi' + $('.azu-slider-container > div').attr('id').split("_")[2];
                        if(typeof window[$var_name] !== 'undefined')
                            window[$var_name].on(sEvent, azu_slideshow_event);
                        time_delay = 0;
                        break;
                case 'master' :
                        sImages = '.azu-slider-container .master-slider .ms-slide img';
                        sEvent = MSSliderEvent.CHANGE_END;
                        img_slide = '.azu-slider-container .master-slider .ms-sl-selected .ms-slide-bgcont img';
                        //$var_name = $('.azu-slider-container .master-slider').attr('id');
                        //$var_name = 'masterslider_' + $var_name.substring($var_name.length-4);
                        if(typeof window.masterslider_instances[0] !== 'undefined')
                            setTimeout(function() { window.masterslider_instances[0].api.addEventListener(sEvent, azu_slideshow_event);  },time_delay );
                        break;
                case 'royal' :
                        sImages = '.azu-slider-container .rsContent img.rsImg';
                        sEvent = 'rsAfterSlideChange'; 
                        img_slide = '.azu-slider-container .rsContent img.rsImg';  
                        $var_name = $('.azu-slider-container #'+$('.azu-slider-container > div').attr('id'));
                        if($var_name.length > 0)
                                setTimeout(function() { $var_name.data('royalSlider').ev.on(sEvent, azu_slideshow_event); },time_delay );
                        break;
                case 'layer' :
                        sImages = '.azu-slider-container .ls-slide img';
                        img_slide = '.azu-slider-container .ls-active img'; 
                        event_data = $('.azu-slider-container #'+$('.azu-slider-container > div').attr('id')).layerSlider('userInitData');
                        event_data.cbAnimStop = azu_slideshow_event;
                        break;
                default: break;
        }
        if($('#start_navigation').hasClass('azu-nav-transparent')){
            tp_targets = '#start_navigation .azu-navigation-field';
            try{
                if(sImages){
                    if(time_delay>0)
                        setTimeout(function() { BackgroundCheck.init({ targets: tp_targets, images: sImages}); },time_delay );
                    else
                        BackgroundCheck.init({ targets: tp_targets, images: sImages});
                }
            } catch(ex){ console.log('BackgroundCheck init failed'); }
        }
}


function azu_slideshow_event(e,data) {  //slide.onchange
            //console.log('event');
            var $img = $(img_slide);
            if(tp_targets && !$('#site-navigation').hasClass('navbar-fixed-top'))
            {
                if($img.length > 0 ){
                    azu_background_check($img,tp_targets);
                    azu_total_delay = 0;
                    azu_background_recheck($img.attr('src'),tp_targets);
                }
            }
       }
       
var azu_total_delay = 0;
function azu_background_recheck($src,target){
        if(azu_total_delay < 1000){
            setTimeout(function() {
               var img = $(img_slide);
               if( $src !== img.attr('src'))
                    azu_background_check(img,target);
               else
                    azu_background_recheck($src,target);
            },100);
        }
        azu_total_delay += 100;
}

window.azu_fancy_block_call = function(){
        var block = $('#main .azu_fancyblock');
        if(block.length > 0 && Modernizr.csstransforms3d && azuGlobals.IsMobile !== '1'){
            if(azuGlobals.mobile_width > $(window).width())
            {
                return;
            }
            block.each(function(){
                
                if($(this).hasClass('azu-parallax')) {
                    console.log('azu-parallax');
                    var prlx_container = $(this);
                    if($(this).hasClass('inside')){
                        prlx_container = $(this).children('.azu_fb_wrap');
                    }
                    var fb_height = $(this).attr('data-distance');
                    fb_height = typeof fb_height !== 'undefined' ? parseInt(fb_height) : 100;
                    var prlx_top = $(this).offset().top, prlx_bottom = prlx_top + $(this).height(),
                    scrll_top = $(window).scrollTop(),scrll_bottom = scrll_top + $(window).height();
                    if( (scrll_bottom > prlx_top && scrll_top < prlx_top) || (scrll_bottom > prlx_bottom && scrll_top < prlx_bottom)){
                        fb_height = (-1) * fb_height * (scrll_bottom - prlx_top)/($(window).height() + $(this).height() );
                        //prlx_container.css({'-webkit-transform':'translate(0px, '+fb_height+'px)'});
                        prlx_container.css('transform','translate(0px, '+fb_height+'px)');
                    }
                }
                else {
                    var view_port = $(this).attr('data-viewport-pos'),ani_count = $(this).attr('data-ani-count');
                    view_port = typeof view_port !== 'undefined' ? parseInt(view_port) / 100 : 0.8;
                    ani_count = (typeof ani_count === 'undefined' || ani_count === "") ? "" : parseInt(ani_count);
                    
                    if(($(window).scrollTop() + $(window).height() * view_port) > $(this).offset().top){
                        if(!$(this).hasClass('animate-scroll') &&  ani_count > 0){
                            ani_count = ani_count === 1 ? "" : ani_count - 1;
                            $(this).attr('data-ani-count', ani_count);
                        }
                        $(this).addClass('animate-scroll');
                    }
                    else if(ani_count !== ""){
                        $(this).removeClass('animate-scroll');
                    }
                }
            });
        }
        else {
                $(window).off('scroll', azu_fancy_block_call);
        }
};

function azu_fancy_block(){
    var ani_block = $('#main .azu_fancyblock:not(.disable-animation)');
    
    if(ani_block.length > 0 ){
        $(window).on('scroll', azu_fancy_block_call);
        azu_fancy_block_call();
    }
    
}
       
function set_background_check(attr, data){
    try { BackgroundCheck.set(attr,data); } catch(ex){ }
}

//background check refresh
function azu_background_check(img,target){
        if ( img && $(img).length > 0 ) {
            img.src = img.first().attr('src');
            set_background_check('images',img);
        }
        // Change targets
        if (target && $(target).length > 0)
            set_background_check('targets', target);
        try { BackgroundCheck.refresh(); } catch(ex){ }
}


//azu-number-spinner
function azu_number_spinner(){
var group_container = $('.azu-input-group');
if(group_container.length === 0)
    return;


$('.azu-btn-number', group_container).click(function(e){
    e.preventDefault();
    var fieldName = $(this).attr('data-field');
    var type      = $(this).attr('data-type');
    
    var input = $(this).parent().parent().find("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    
    
    if (!isNaN(currentVal)) {
        var step = typeof input.attr('step') !== 'undefined' ? parseInt(input.attr('step')) : 1;
        if(type == 'minus') {
            var min = input.attr('min');
            if(typeof min === 'undefined' || currentVal > min) {
                input.val(currentVal - step).change();
            } 
            if(typeof min !== 'undefined' && parseInt(input.val()) == min) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {
            var max = input.attr('max');
            if(typeof max === 'undefined' || currentVal < max) {
                input.val(currentVal + step).change();
            }
            if(typeof max !== 'undefined' && parseInt(input.val()) == max) {
                $(this).attr('disabled', true);
            }
                
        }
    } else {
        input.val(0);
    }
});
$('.azu-input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.azu-input-number').change(function() {
    
    var minValue =  $(this).attr('min');
    var maxValue =  $(this).attr('max');
    var valueCurrent = parseInt($(this).val());
    
    var name = $(this).attr('name');
    if(typeof minValue === 'undefined' ||  valueCurrent >= parseInt(minValue)) {
        $(".azu-btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled');
    } else {
        $(this).val($(this).data('oldValue'));
    }
    if(typeof maxValue === 'undefined' || valueCurrent <= parseInt(maxValue)) {
        $(".azu-btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled');
    } else {
        $(this).val($(this).data('oldValue'));
    }
    
    
});
$(".azu-input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
}

//keyboard-image-navigation
function keyboard_image_navigation(){
	$( document ).keydown( function( e ) {
		var url = false;
		if ( e.which === 37 ) {  // Left arrow key code
			url = $( '.previous > a' ).attr( 'href' );
		}
		else if ( e.which === 39 ) {  // Right arrow key code
			url = $( '.next > a' ).attr( 'href' );
		}
		if ( url && ( !$( 'textarea, input' ).is( ':focus' ) ) ) {
			window.location = url;
		}
	} );
}
    // topbar expand/collapse arrow
    function topbar_arrow(){
            if ($.cookie('top-hide') == "true"){
                    $("#topbar-arrow").addClass("act");
                    var topBar = $('.azu-topbar-inside');
                    topBar.removeClass("hidden-xs");
                    topBar.removeClass("hidden-sm");
            }
            $("#topbar-arrow").on("click", function(){
                    var $_this = $(this);
                    var topBar = $('.azu-topbar-inside');
                    if($_this.hasClass("act")){
                            $_this.removeClass("act");
                            topBar.slideUp( 200, function() {
                                topBar.addClass("hidden-xs");
                                topBar.addClass("hidden-sm");
                                topBar.removeAttr("style");
                            });
                            $.cookie('top-hide', 'false', {expires: 1, path: '/'});
                    }else{
                            $_this.addClass("act");
                            topBar.removeClass("hidden-xs");
                            topBar.removeClass("hidden-sm");
                            topBar.hide();
                            topBar.slideDown( 300 );
                            $.cookie('top-hide', 'true', {expires: 1, path: '/'});
                    }
            });
    }
    



    //fixed navbar
    var lastScrollTop = 0;
    function floating_navbar(){
               $(window).on('scroll', floating_navbar_call);
    } //end
    
    window.floating_navbar_call = function() {
                 var st = $(window).scrollTop();
                 var s_nav = $('#site-navigation');
                 var navHeight = $("#start_navigation").offset().top,
                    wp_adminbar = $("#wpadminbar"),
                    margin_height = (wp_adminbar.exists() && wp_adminbar.css("position")==='fixed') ? wp_adminbar.height() : 0;
                 if($("#fixed_navigation").height() > azuLocal.fixedNavHeight)
                    navHeight = navHeight + $("#fixed_navigation").height() - azuLocal.fixedNavHeight;
                 if (wp_adminbar.exists())
                    navHeight = navHeight - wp_adminbar.height();
                 if ( st > navHeight && azuLocal.floatingMenu > 0) {
                        if(!s_nav.hasClass('navbar-fixed-top')){
                            $('.azu-line-though div.azu-mover-line').hide();
                            s_nav.addClass('navbar-fixed-top');
                            if (tp_targets)
                                set_background_check('targets', '#site-navigation');
                            $('.azu-navigation-field').removeClass('background--light background--dark background--complex');
                        }
                        if ($(".azu-pageborder-top").exists()){
                            margin_height += parseFloat($(".azu-pageborder-top").css("padding-top"));
                        }
                        if( margin_height > 0)
                            s_nav.css("margin-top", margin_height+"px");
                 }
                 else {
                        if(s_nav.hasClass('navbar-fixed-top')){
                            $('.azu-line-though div.azu-mover-line').hide();
                            s_nav.removeClass('navbar-fixed-top');
                            azu_slideshow_event();
                        }
                        s_nav.css("margin-top", "0px");
                 }
                 if (st > lastScrollTop && st > ($(window).height() * 2) && !azuLocal.offCanvas){
                       s_nav.addClass('navbar-fixed-hidden');
                 } else {
                       s_nav.removeClass('navbar-fixed-hidden');
                 }
                 lastScrollTop = st;
                 s_nav.on(transEndEventName, function(e){
                    azu_center_menu_padding();
                    $(this).off(e);
                 });
      };
    
    
    function azu_offcanvas_adminbar(){
            var navbar_collapse = $('#azu-navbar-collapse');
            if($("#wpadminbar").exists() && navbar_collapse.hasClass('azu-active') && $("#wpadminbar").css("position")==='fixed')
                    navbar_collapse.css('margin-top',(parseFloat(navbar_collapse.css('margin-bottom')) + $("#wpadminbar").height())+"px");
    }
    
    function azu_pageborder(){
        if ($(".azu-pageborder-top").exists() && $("#wpadminbar").exists()){
            if($("#wpadminbar").css("position")==='absolute' && document.body.getBoundingClientRect().top < $("#wpadminbar").height())
                $(".azu-pageborder-top").css("margin-top", "0px");
            else {
                $(".azu-pageborder-top").css("margin-top", $("#wpadminbar").height()+"px");
            }
        }
    }
    
    
    function audio_reinit($container){
          if($.isFunction($.fn.mediaelementplayer))
          {
		var settings = {};

		if ( typeof _wpmejsSettings !== 'undefined' ) {
			settings = _wpmejsSettings;
		}

		settings.success = settings.success || function (mejs) {
			var autoplay, loop;

			if ( 'flash' === mejs.pluginType ) {
				autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
				loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

				autoplay && mejs.addEventListener( 'canplay', function () {
					mejs.play();
				}, false );

				loop && mejs.addEventListener( 'ended', function () {
					mejs.play();
				}, false );
			}
		};
                
		$('.wp-audio-shortcode, .wp-video-shortcode',$container).each(function(){
                    if($(this).css('visibility') === 'hidden')
                        $(this).mediaelementplayer( settings );
                });
          }
    }
    
    //Center menu padding
    function azu_center_menu_padding(){
            var menu_left = $('.azu-mid-nav-left');
            if(menu_left.length > 0) {
                var logo_container = $( ".azu-branding" );
                if(logo_container.css('display') === 'none') {
                    logo_container = $( ".site-title:not(.azu-seo-text) > a" );
                }
                var logoWidth = 0;
                        
                if(logo_container.length > 0) {
                    logoWidth = logo_container.width() / 2;
                    if(logo_container.css('opacity') === 0)
                            logo_container.animate({'opacity': 1}, function() { azu_center_menu_padding();});
                }

                if(logoWidth != parseFloat(menu_left.css('padding-right'))) {
                    menu_left.stop().animate( { "padding-right": logoWidth}, 200 , function() {
                        try { $(this).overline_reinit(); } catch(err) {}
                    });
                    $('.azu-mid-nav-right').stop().animate( {"padding-left": logoWidth},200 );
                }
            }
    }

    /* Love This */
    /* -------------------------------------------------------------------- */

    function azu_love_post() {
        $('body').on('click', '.azu-love-this', function() {
            var $this = $(this),
                id = $this.attr('id');

            if ($this.hasClass('item-loved')) return false;

            if ($this.hasClass('item-inactive')) return false;

            $.post(
                azuLocal.ajaxurl, 
                {
                    'action': 'azu_love_post',
                    'post_id': id
                }, 
                function(data) {
                    $this.find('span').html(data);
                    $this.addClass('item-loved');
                }
            );

            $this.addClass('item-inactive');
            return false;
        });

    }
        
    //Scroll To Top #start
    function scrollToTop(){
         //Show and Hide the Button
        $(window).on('scroll', function(){
            if ($(window).scrollTop() > 500) {
                $('.azu-scroll-top-wrapper').addClass('show');
            } else {
                $('.azu-scroll-top-wrapper').removeClass('show');
            }
        });

        $('.azu-scroll-top-wrapper').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 'slow');
            return false;
        });
     }
    //Scroll To Top  #end

    //Burger animation
    function navbar_toggle() {
			  $(".navbar-toggle").on("click", function () {
				    $(this).toggleClass("active");
			  });
    }
                

	azuGlobals.magnificPopupBaseConfig = {
		type: 'image',
		tLoading: 'Loading image ...',
		mainClass: 'mfp-img-mobile',
                removalDelay: 300,
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return this.st.azu.getItemTitle(item);
			}
		},
		iframe: {
			markup: '<div class="mfp-iframe-scaler">'+
					'<div class="mfp-close"></div>'+
					'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+ //mfp-iframe-holder
					'<div class="mfp-bottom-bar">'+
					'<div class="mfp-title"></div>'+
					'<div class="mfp-counter"></div>'+
					'</div>'+
					'</div>'
		},
		callbacks: {
			markupParse: function(template, values, item) {
				if ( 'iframe' == item.type ) {
					template.find('.mfp-title').html( this.st.azu.getItemTitle(item) );
				}
                                if ( !this.ev.attr('data-azu-share') ) {
                                        template.addClass("no-share-buttons");
                                }
			},
                        change: function() {
                                if (this.isOpen) {
                                        /*transition between the images in the gallery*/
                                        this.wrap.addClass('mfp-open');
                                }
                        },
                        beforeClose: function() {
                                $('body, html').css('overflow','');
                                this.wrap.removeClass('mfp-open');
                                this.content.addClass('mfp-removing');
                        },
                        close: function() {
                                this.content.removeClass('mfp-removing'); 
                        },
			beforeOpen: function() {

				var magnificPopup = this;
				// create settings container
				if ( typeof this.st.azu == 'undefined' ) {
					this.st.azu = {};
				}
                                
                                var azu_share = this.ev.attr('data-azu-share');
                                // save share buttons array
                                this.st.azu.shareButtonsList = azu_share ? azu_share.split(',') : [];

                                // share buttons template
                                this.st.azu.shareButtonsTemplates = {
                                        twitter : '<a href="//twitter.com/home?status={location_href}%20{share_title}" class="share-button" target="_blank" title="twitter"><i class="icon-twitter"></i></a>',
                                        facebook : '<a href="//www.facebook.com/sharer.php?s=100&amp;p[url]={location_href}&amp;p[title]={share_title}&amp;p[images][0]={image_src}" class="share-button" target="_blank" title="facebook"><i class="icon-facebook"></i></a>',
                                        google : '<a href="//plus.google.com/share?url={location_href}&amp;title={share_title}" class="share-button" target="_blank" title="google+"><i class="icon-google"></i></a>',
                                        pinterest : '<a href="//pinterest.com/pin/create/button/?url={location_href}&amp;description={share_title}&amp;media={image_src}" class="share-button" target="_blank" title="pin it"><i class="icon-pinterest"></i></a>',
                                        linkedin : '<a href="//www.linkedin.com/shareArticle?mini=true&url={location_href}&title={share_title}" class="share-button" target="_blank" ><i class="icon-linkedin"></i></a>'
                                };

                                // share buttons
                                this.st.azu.getShareButtons = function ( itemData ) {

                                        var shareButtons = magnificPopup.st.azu.shareButtonsList,
                                                pinterestIndex = -1,
                                                shareButtonsLemgth = shareButtons.length,
                                                html = '';
                                        var i;
                                        for( i = 0; i < shareButtons.length; i++ ) {

                                                if ( 'pinterest' == shareButtons[i] ) {
                                                        pinterestIndex = i;
                                                        break;
                                                }
                                        }

                                        if ( shareButtonsLemgth <= 0 ) {
                                                return '';
                                        }

                                        for ( i = 0; i < shareButtonsLemgth; i++ ) {

                                                // exclude pinterest button for iframes
                                                if ( 'iframe' == itemData['type'] && pinterestIndex == i ) {
                                                        continue;
                                                }

                                                var	itemTitle = itemData['title'],
                                                        itemSrc = itemData['src'],
                                                        itemLocation = itemData['location'];

                                                if ( 'google' == shareButtons[i] ) {
                                                        itemTitle = itemTitle.replace(' ', '+');
                                                }

                                                html += magnificPopup.st.azu.shareButtonsTemplates[ shareButtons[i] ].replace('{location_href}', encodeURIComponent(itemLocation)).replace('{share_title}', itemTitle).replace('{image_src}', itemSrc);
                                        }

                                        return '<div class="azu-entry-share"><div class="social-ico">' + html + '<div></div>';
                                };
                                
				// item title
				this.st.azu.getItemTitle = function($item) {
					var imgTitle = $item.el.attr('title') || '',
						imgSrc = $item.el.attr('href'),
						imgDesc = $item.el.attr('data-azu-img-description') || '',
						imgLocation = $item.el.attr('data-azu-location') || location.href,
                                                shareButtons = magnificPopup.st.azu.getShareButtons( { 'title': imgTitle, 'src': imgSrc, 'type': $item.type, 'location': imgLocation } );
                                        
					return imgTitle + '<small>' + imgDesc + '</small>' + shareButtons;
				};
			}
		}
	};

//* Magnific Popup */
function image_popup($container){
    
	// single popup
	$('.azu-single-image', $container).not('.mfp-ready').magnificPopup({
		type: 'image'
	}).addClass('mfp-ready');

	$('.azu-single-video', $container).not('.mfp-ready').magnificPopup({
		type: 'iframe'
	}).addClass('mfp-ready');

	$(".azu-gallery-container", $container).not('.mfp-ready').each(function(){
		$(this).magnificPopup( $.extend( {}, azuGlobals.magnificPopupBaseConfig, {
			delegate: 'a.azu-mfp-item',
			tLoading: 'Loading image #%curr%...',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1] // Will preload 0 - before current, and 1 after the current image
			}
		} ) );
	}).addClass('mfp-ready');
        
        $('.azu-single-mfp-popup', $container).not('.mfp-ready').magnificPopup(azuGlobals.magnificPopupBaseConfig).addClass('mfp-ready');
}


//* Masonry */
function azu_isotope(){
        var isoCollection = $('.iso-container'),gridCollection=$('.iso-grid'),
        combinedCollection = isoCollection.add(gridCollection);

        isotope_creator(isoCollection,'masonry');
        isotope_creator(gridCollection,'fitRows');
        
        /* !Smart responsive columns */
        if (combinedCollection.exists()) {
            combinedCollection.each(function(i) {
                    var $container = $(this);
                    $container.addClass("cont-id-"+i).attr("data-cont-id", i);
                    $container.calculateColumns("px");
                    $(window).on("debouncedresize", function () {
                            $container.calculateColumns("px");
                    });
            });
        }
        
        $(".azu-swiper-container", $('#page')).initSlider('',true);

}

function isotope_creator($isoCollection, selected_mode){
	// Collection of masonry instances 
	$isoCollection.each(function(i) {
		var $isoContainer = $(this);
                
		// Hack to make sure that masonry will correctly calculate columns with responsive images height. 
		$(".preload-img", $isoContainer).heightHack();

                var durationOfTransition;
		if (azuGlobals.IsMobile === '0') 
			durationOfTransition = '0.4s';
		else 
			durationOfTransition = 0;
		$isoContainer.one("columnsReady", { value: selected_mode } ,function() {
                        // Slider initialization
                        $(".azu-swiper-container", $isoContainer).initSlider($isoContainer.data('ratio'),false);
			$isoContainer.isotope({
				itemSelector : '.iso-item',
				layoutMode : selected_mode,
                                transitionDuration: durationOfTransition,
                                //containerStyle: { position: 'relative' },
                                isResizeBound: false,
				masonry: { 
                                    columnWidth: 1,
                                    //isFitWidth: true,
                                    gutter: 0
                                }
			});
                        
                        //$isoContainer.isotope('unbindResize');
			// Recalculate everything on window resize
			$(window).on("columnsReady", function () {
				$(".azu-swiper-container", $isoContainer).each(function() {
					$(this).updateSliderSize();
                                        $(this).data("swiper").resizeFix();
                                        //if($(this).data("swiper").params.calculateHeight)
                                        //    $(this).data("swiper").reInit();
				});
				$isoContainer.isotope("layout");
			});
		});
                
		// Show item(s) when image inside is loaded
		$("> .iso-item", $isoContainer).showItems();
	});
}

/* !Onepage template */
	//!!!
	$(window).load(function(){

		if($('.one-page-row div[data-anchor^="#"]').length > 0 && $(".one-page-row").length > 0){
			var urlHash = window.location.hash;
                        urlHash = urlHash.substring(urlHash.substring(urlHash.lastIndexOf("#")));
			if( typeof urlHash != 'undefined' && urlHash.length > 0 ) {
				if(urlHash == "#up") {
					$("html, body").animate({
						scrollTop: 0
					}, 600, function(){});
				}else{
					setTimeout(function(){
                                            if($(urlHash+'-anchor').length > 0)
						$("html, body").animate({
							scrollTop: $(urlHash+'-anchor').offset().top - $("#site-navigation").height() 
						}, 600, function(){ setTimeout(function(){ $('#site-navigation').removeClass('navbar-fixed-hidden');},100); });
					},300);
				}
			}else {
				if(urlHash.length === 0 && $( '.menu-item > a[href="#up"]' ).length > 0) {
					$( '.menu-item > a[href="#up"]' ).parent("li").addClass("azu-act");
				}
			}
		}
	});
		var $menus = $( '.menu-item > a[href*="#"]' );

		window.clickAnchorLink = function( $a, e ) {
			var url = $a.attr( 'href' ),
				$target = url.substring(url.lastIndexOf("#")),
				base_speed  = 600,
				speed       = base_speed,
                                top=0,
                                floatMenuH;
                        if($("#site-navigation").hasClass('navbar-fixed-top'))
                            floatMenuH = $("#site-navigation").height() + paddingTop;
                        else
                            floatMenuH = 54 + paddingTop;

			if ( typeof $target != 'undefined' && $target && $target.length > 0 ) {
				location.hash = url;
				if($($target+'-anchor').length > 0) {
					top = $( $target+'-anchor').offset().top;
					var 	this_offset = $a.offset(),
						that_offset = $($target+'-anchor').offset(),
						offset_diff = Math.abs(that_offset.top - this_offset.top);
						speed = (offset_diff * base_speed) / 1000;
				}
				if($target == "#up") {
					$( 'body, html' ).animate({ scrollTop: 0 }, 600 );
				}else if(!azuLocal.isOpen){
					$( 'body, html' ).animate({ scrollTop: top - floatMenuH }, speed ,function(){ setTimeout(function(){ $('#site-navigation').removeClass('navbar-fixed-hidden'); },100); });
				}

				$('.menu-item a').parent("li").removeClass('azu-act');
				$a.parent("li").addClass('azu-act');
				return false;
			}

		};

		$( 'body' ).on( 'click', '.anchor-link[href*="#"], .logo-box a[href*="#"], #branding a[href*="#"], #branding-bottom a[href*="#"]', function( e ) {
			clickAnchorLink( $( this ), e );
		});

		$menus.on( 'click', function( e ) {
			clickAnchorLink( $( this ), e );
		});
		if($('.one-page-row div[data-anchor^="#"]').length > 0 && $(".one-page-row").length > 0){
			if(!$("body").hasClass("is-scroll")){
                            $(window).scroll(function (e) {
                                    var currentNode = null;
                                    $('.one-page-row div[data-anchor^="#"]').each(function(){
                                            var $_this = $(this),
                                                    currentId = $_this.attr('data-anchor');
                                            if($(window).scrollTop() >= ($(".one-page-row div[data-anchor='" + currentId + "']").offset().top - 100)){
                                                    currentNode = currentId;
                                            }
                                    });
                                    $('.menu-item a').parent("li").removeClass('azu-act');
                                    if($(window).scrollTop() < ($(".one-page-row div[data-anchor^='#']").first().offset().top - 100)&& $( '.menu-item > a[href="#up"]' ).length > 0) {
                                            $( '.menu-item > a[href="#up"]' ).parent("li").addClass("azu-act");
                                    }
                                    $('.menu-item a[href$="'+currentNode+'"]').parent("li").addClass('azu-act');
                                    if($('.menu-item a[href="#"]').length && currentNode === null){
                                            $('.menu-item a[href="#"]').parent("li").addClass('azu-act');
                                    }

                            });
                        }
		}
/* Onepage template:end */
                
/**********************************************************************/
/* Load more pagination
/**********************************************************************/
	var azuAjaxing = {
		xhr: false,
		settings: false,
		lunch: function( settings ) {

			var ajaxObj = this;

			if ( settings ) {
				this.settings = settings;
			}

			if ( this.xhr ) {
				this.xhr.abort();
			}

			var action = 'azzu_posttype_ajax';

			this.xhr = $.post(
				settings.ajaxurl,
				{
					action : action,
					postID : settings.postID,
					paged : settings.paged,
					targetPage : settings.targetPage,
					term : settings.term,
                                        order: settings.ajaxarray['order'],
                                        orderby: settings.ajaxarray['orderby'], 
					ajaxarray : settings.ajaxarray,
					nonce : settings.nonce,
					visibleItems : settings.visibleItems,
					contentType : settings.contentType,
					sender : settings.sender
				},
				function( responce ) {

					if ( responce.success ) {

						var $responceItems = $(responce.html),
							$isoContainer = settings.targetContainer,

							isIsotope = ('grid' == settings.ajaxarray['type'] || 'masonry' == settings.ajaxarray['type']),
							itemsToDeleteLength = responce.itemsToDelete.length,
							trashItems = [],
							sortBy = responce.orderby.replace('title', 'name'),
							sortAscending = ('asc' == responce.order.toLowerCase());


						if ( responce.newNonce ) {
							azuLocal.ajaxNonce = responce.newNonce;
						}
                                                
						// if not mobile isotope with spare parts
						if ( isIsotope && itemsToDeleteLength > 0 ) {
                                                        
							for( var i = 0; i < responce.itemsToDelete.length; i++ ) {
								trashItems.push('.iso-item[data-post-id="' + responce.itemsToDelete[i] + '"]');
							}

							$isoContainer.isotope('remove', $isoContainer.find(trashItems.join(',')));

						// if mobile or not isotope and sender is filter or paginator
						} else if ( 'filter' == settings.sender  ) {

							$isoContainer.find('.iso-item').remove();
						}

						if ( $responceItems.length > 0 ) {

							// append new items
							$isoContainer.append($responceItems);

							// for isotope - insert new elements
							if ( isIsotope ) {

								$(".preload-img", $isoContainer).heightHack();
                                                                
								// Slider initialization
                                                                $(".azu-swiper-container", $isoContainer).initSlider($isoContainer.data('ratio'),false);
								$isoContainer.calculateColumns( "px");

                                                                $isoContainer.isotope('addItems', $responceItems);
                                                                $isoContainer.isotope('layout');
                                                                //if ( 'media' != settings.contentType ) {
                                                                //	$isoContainer.isotope({ sortBy : sortBy, sortAscending : sortAscending });
                                                                //} else {
                                                                        $isoContainer.isotope({ sortBy: 'original-order' });
                                                                //}

                                                                $("> .iso-item", $isoContainer).showItems();
                                                                ajaxObj.init();


							// all other cases - append new elements
							} else {

								ajaxObj.init();

							}

							if ( typeof settings.afterSuccessInit != 'undefined' ) {
								settings.afterSuccessInit( responce );
							}

						} else if ( isIsotope ) {

							// if no responce items - reorder isotope
							$isoContainer.isotope({ sortBy : sortBy, sortAscending : sortAscending });
						}

					}

					if ( typeof settings.afterResponce != 'undefined' ) {
						settings.afterResponce( responce );
					}
				}
			);
		},
		init : function() {
			switch ( this.settings.contentType ) {
				case 'portfolio' :
					this.initPortfolio();
					break;
				case 'blog' :
					this.initBlog();
					break;
                                default: 
                                        this.basicInit();
                                        break;
			}
		},
		initPortfolio : function() {
			this.basicInit();
		},
                initBlog : function() {
			this.basicInit();
                        audio_reinit(this.settings.targetContainer);
		},
		basicInit : function() {
			var $container = this.settings.targetContainer;
                        image_popup($container);
			$(".iso-item", $container).css("visibility", "visible");
                        $('.azu-social-share.dropdown-toggle:not(.hoverReady)[data-toggle="dropdown"]', $container).addClass('hoverReady').parent().on('shown.bs.dropdown', function () {
                            $(this).tooltip('hide');
                        });
                        //$('.dropdown-toggle:not(.azu-social-share)[data-toggle="dropdown"]', $container).not('.hoverReady').dropdownHover().addClass('hoverReady');
                        if(azuGlobals.IsMobile === '0') //only desktop
                            $('.azu-tooltip:not(.tooltipReady)[data-toggle="tooltip"]', $container).addClass('tooltipReady').tooltip();
                        
		}
	};

	// get ajax data
	function azuGetAjaxData( $ajaxarray, $parentContainer ) {
		var	$filtersContainer = $parentContainer.find('.filter-ajax.with-ajax').first(),
			$itemsContainer = $parentContainer.find('.isotope.with-ajax').first(),
			$currentCategory = $filtersContainer.find('.filter-categories a.act'),
			paged = ( typeof $itemsContainer.attr('data-cur-page') !== 'undefined') ? parseInt($itemsContainer.attr('data-cur-page')) : 1,
			visibleItems = [],
			term = ( $currentCategory.length > 0 ) ? $currentCategory.attr('data-filter').replace('.category-', '').replace('*', '') : '';
                
		if ( '0' == term ) {
			term = 'none';
		}

		if ( $itemsContainer.hasClass('isotope') && $ajaxarray.loading_mode =='1') {
			$('.iso-item', $itemsContainer).each( function(){
				visibleItems.push( $(this).attr('data-post-id') );
			});
		}
                
		return {
			visibleItems : visibleItems,
			postID : azuLocal.postID,
			paged : paged,
			term : term,
			ajaxarray : $ajaxarray,
			ajaxurl : azuLocal.ajaxurl,
			nonce : azuLocal.ajaxNonce,
			targetContainer : $itemsContainer
		};
	}

	// paginator
	$('#content').on('click', '.paginator.with-ajax a', function(e){

		e.preventDefault();

		var $this = $(this),
			$paginatorContainer = $this.closest('.paginator'),
			$parentContainer = $paginatorContainer.parent(),
			$itemsContainer = $parentContainer.find('.isotope.with-ajax').first(),
                        $ag = 'ajax_'+$itemsContainer.attr('data-guid'),
                        paginatorType = window[$ag].loading_mode,
			isMore = (parseInt(paginatorType)>1),
			ajaxData = azuGetAjaxData(window[$ag],$parentContainer),
			targetPage = isMore ? ( ajaxData.paged + 1 ) : $this.attr('data-page-num');
                        
		if ( !isMore ) {
			var $scrollTo = $parentContainer.find('.filter-ajax.with-ajax').first();
			if (!$scrollTo.exists()) 
				$scrollTo = $itemsContainer;
                        // scroll to top
			$("html, body").animate({
				scrollTop: $scrollTo.offset().top - $("#site-navigation").height() - paddingTop
			}, 400);
                        $('#preloader').fadeIn(100,function(){$(this).show(); $(this).css('opacity',1);});
		}
                else {
                    $(this).find("span").text($(this).data('loading'));
                    $(this).find(".azu-loading").addClass("loader");
                }
		// lunch ajax
		azuAjaxing.lunch($.extend({}, ajaxData, {
			contentType : ajaxData.ajaxarray.template,
			targetPage : targetPage,
			sender : paginatorType,
			visibleItems : isMore ? [] : ajaxData.visibleItems,
			afterResponce : function( responce ) {

				// we have paginator
				if ( $paginatorContainer.length > 0 ) {

					if ( responce.paginationHtml ) {

						// update paginator with responce content
						$paginatorContainer.html($(responce.paginationHtml).html()).show();
					} else {

						if ( isMore ) {
							$paginatorContainer.html('<span class="loading-ready">' + azuLocal.moreButtonAllLoadedText + '</span>');
						} else {
							// clear paginator and hide it
							$paginatorContainer.html('').hide();
						}
					}
                                        if(responce.paginationType=='1')
                                            $('#preloader').fadeOut(200,function(){$(this).hide();});
                                        else
                                            setTimeout (function(){
                                                    $(".azu-loading").removeClass("loader");
                                            }, 200);

				} else if ( responce.paginationHtml ) {

					// if there are no paginator on page but ajax responce have it
					$itemsContainer.parent().append($(responce.paginationHtml));
				}


				// update current page field
				$itemsContainer.attr('data-cur-page', responce.currentPage);
                                

			}
		}));

	});
        
        $(".filter-categories > a").on("click", function(e) {
		var $this = $(this);
		
		e.preventDefault();

		$this.trigger("mouseleave");
		
		if ($this.hasClass("act") && !$this.hasClass("show-all")) {
			e.stopImmediatePropagation();
			$this.removeClass("act");
			$this.siblings("a.show-all").trigger("click");
		} else {
			$this.siblings().removeClass("act");
			$this.addClass("act");
		}
	});
        
	// filter
	$('.filter-ajax.with-ajax .filter-categories a').on('click', function(e){
		e.preventDefault();
                
		var $this = $(this),
			$filterContainer = $this.closest('.filter-ajax.with-ajax'),
			$parentContainer = $filterContainer.parent(),
			$itemsContainer = $parentContainer.find('.isotope.with-ajax').first(),
			$paginatorContainer = $parentContainer.find('.paginator').first(),
                        $ag = 'ajax_'+$itemsContainer.attr('data-guid'),
			ajaxData = azuGetAjaxData(window[$ag],$parentContainer);

		// lunch ajax
		azuAjaxing.lunch($.extend({}, ajaxData, {
			contentType : ajaxData.ajaxarray.template,
			targetPage : 1,
			paged : 1,
			sender : 'filter',
			afterResponce : function( responce ) { 

				// we have paginator
				if ( $paginatorContainer.length > 0 ) {

					if ( responce.paginationHtml ) {

						// update paginator with responce content
						$paginatorContainer.html($(responce.paginationHtml).html()).show();
					} else {

						// clear paginator and hide it
						$paginatorContainer.html('').hide();
					}

				} else if ( responce.paginationHtml ) {

					// if there are no paginator on page but ajax responce have it
					$itemsContainer.parent().append($(responce.paginationHtml));
				}


				// update current page field
				$itemsContainer.attr('data-cur-page', responce.currentPage);

			}
		}));

	});
        
        
        
        // infinite scroll
        function infinite_scroll() {
		if ( azuGlobals.loadMoreButton && azuGlobals.loadMoreButton.exists() ) {
                        
			var buttonOffset = azuGlobals.loadMoreButton.offset();

			if ( buttonOffset && ($(window).scrollTop() + $(window).height()) > buttonOffset.top   && !azuGlobals.loadMoreButton.hasClass('loader') ) {
				
                                azuGlobals.loadMoreButton.find('a').first().trigger('click',{ value: true });
			}

		}
	}

        // blurred background
        function blurred_background() {
            $('.azu-blurred-row').each(function(){
                var udiv = $(this).children('.upb_row_bg').first();
                if(typeof udiv !=='undefined'){
                        udiv.append( '<div class="azu_row_bg"></div>' );
                        udiv.addClass("azu-noblur");
                }
            });
            $('.azu-blurred-col').each(function(){
                var bimage = $(this).children('.upb_row_bg').first().css( "background-image" );
                if(typeof bimage !=='undefined'){
                    $(this).children('.wpb_column').each(function(){
                            $(this).prepend( '<div class="azu_col_bg"></div>' );
                            $(this).children('.azu_col_bg').css( "background-image", bimage);
                            $(this).children('.wpb_wrapper').addClass( "azu_wrapper" );
                    });
                }
            });
        }
        
        // search input on the menu fill
        function SearchFocusOnMenu() {
            if(azuLocal.menutype !== 'side'){
                $('#site-navigation .azu-menu-widget-area .search-form .search-field.form-control').each(function(){
                        // events:
                        $(this).on( 'focus', onInputFocus );
                        $(this).on( 'blur', onInputBlur );
                });
            }
            $('#site-navigation .azu-menu-widget-area .search-form i.azu-icon-search').on('click',function(){
                var s_field = $(this).parent().children('.search-field');
                if(s_field.is(":focus"))
                    $(this).parent().children('.search-submit').click();
                else
                    s_field.focus();
            });
        }
        
        function onInputFocus( ev ) {
            if(azuGlobals.mobile_width <= $(window).width())
                $('#site-navigation').addClass('azu-input-filled');
        }

        function onInputBlur( ev ) {
                setTimeout(function(){  $('#site-navigation').removeClass('azu-input-filled'); },400);
        }
        
	// infinite scroll load
        azuGlobals.loadMoreButton = $(".paginator-more-button").last();
        if( azuGlobals.loadMoreButton.exists() && window['ajax_'+azuGlobals.loadMoreButton.attr('data-guid')].loading_mode == '3' ){
            $(window).on('scroll', function () {
                    infinite_scroll();
            });
            infinite_scroll();
        }
        
        
	/*!- Custom resize function*/
	$(window).on("debouncedresize", function( event ) {
		$(".wpb_row").each(function(){
			var $_this = $(this),
				$_this_min_height = $_this.attr("data-min-height");
			if($.isNumeric($_this_min_height)){
				$_this.css({
					"minHeight": $_this_min_height + "px"
				});
			}else if(!$_this_min_height){
				$_this.css({
					"minHeight": 0
				});
			}else if($_this_min_height.search( '%' ) > 0){
				$_this.css({
					"minHeight": $(window).height() * (parseInt($_this_min_height)/100) + "px"
				});
			}else{
				$_this.css({
					"minHeight": $_this_min_height
				});
			}
		});
                
                fullWidthWrap();
                SwiperResizeFix();
                azu_pageborder();
                azu_offcanvas_adminbar();
	}).trigger( "debouncedresize" );
        /*Custom resize function:end*/
        
        function SwiperResizeFix(){
                $(".azu-swiper-container:not(.no-update)", $('#page')).each(function() {
                        if(typeof $(this).data("swiper") !=='undefined'){
                            $(this).updateSliderSize();
                            $(this).data("swiper").resizeFix(true);
                            if($(this).data("swiper").params.calculateHeight)
                                $(this).data("swiper").reInit();
                        }
                });
        }
        
	/* !Fullwidth wrap for shortcodes & templates */
	function fullWidthWrap(){
		if( $(".azu-full-width").length > 0 ){
			$(".azu-full-width").each(function(){
				var $_this = $(this),
					contentW = $('#primary').innerWidth();
					var $offset_fs,
						$width_fs;
                                        
                                        $offset_fs = Math.ceil( (($('body').width() - parseInt(contentW)) / 2) );
                                        
                                        if ($('.azu-boxed').length > 0)
                                        {
                                            $offset_fs = ((parseInt($('#content').width()) - parseInt(contentW)) / 2);
                                            $width_fs = $('#content').width();
                                        }
					else if($('.azu-sidebar-column').length || azuLocal.menutype === 'side'){
						$width_fs = $("#primary").innerWidth();
						$offset_fs = 0;
					}else{
						$width_fs = $('body').width();
					}
                                        
					$_this.css({
						width: $width_fs,
						"margin-left": -1 * ($offset_fs + parseInt(azuLocal.gutter_width)/2),
						"opacity": 1
					});
			});
		}
	}

	/* Fullwidth wrap for shortcodes & templates:end */
        
        //woocommerce cart
        function azu_woo_cart(delay){
            var cart_containers = '#menu-left-widget,#menu-right-widget,#topbar-widget',hascart=$('.widget_shopping_cart_content', cart_containers);
            if(typeof hascart === 'undefined')
                return;
            if( hascart.first().is(':empty') && delay < 10000)
            {
                setTimeout(function(){ azu_woo_cart(delay+500); },500);
            }
            else {
                var azuwoo = $('.woocommerce.widget_shopping_cart .widget_shopping_cart_content',cart_containers);
                azuwoo.each(function(){
                    var cartlink = $(this).find('a.button.wc-forward:not(.checkout)').attr('href'),cartcount = $(this).find('ul.cart_list').children(':not(.empty)').length;
                    if(cartcount===0){
                        cartlink = 'cart';
                        cartcount = '';
                    }
                    else
                        cartcount = '<span class="azuwoo-cart-count">'+cartcount+'</span>';
                    $(this).wrap( '<div class="azuwoo-shopping-cart"></div>' );
                    $(this).before('<a class="azuwoo-cart-link" href="'+ cartlink +'"><i class="azu-icon-cart"></i>'+cartcount+'</a>');
                });
                if(azuwoo.length>0){
                    $('.azuwoo-shopping-cart',cart_containers).on('mouseenter',function(){
                        $(this).find('.widget_shopping_cart_content').fadeIn(200);
                    });
                    $('.azuwoo-shopping-cart',cart_containers).on('mouseleave',function(){
                        var $cart = $(this).find('.widget_shopping_cart_content');
                        setTimeout(function() { if(typeof $cart.data('oncartkeep')==='undefined' || $cart.data('oncartkeep') === '0')$cart.fadeOut(300); },600);
                    });
                    $('.widget_shopping_cart_content',cart_containers).on('mouseenter',function(){
                        $(this).data('oncartkeep','1');
                    });
                    $('.widget_shopping_cart_content',cart_containers).on('mouseleave',function(){
                        var $cart = $(this);
                        $(this).data('oncartkeep','0');
                        setTimeout(function() { if(typeof $cart.data('oncartkeep')==='undefined' || $cart.data('oncartkeep') === '0')$cart.fadeOut(300); },400);
                    }).on('mouseenter',function(){
                        $(this).data('oncartkeep','true');
                    });
                }
            }
        }
        
})(jQuery);

 
