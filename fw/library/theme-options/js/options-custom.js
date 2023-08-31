 /**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */

jQuery(document).ready(function($) {
	
	// Fade out the save message
	$('.fade').delay(1000).fadeOut(1000);
            
            var azuOptions = {   // i will change this
                    change: function(event, ui){
                        var hexcolor = $( this ).wpColorPicker( 'color' );
                        if((typeof hexcolor !== 'undefined') && hexcolor.toString().length === 7){
                            $('#fc_listbox_'+$( this ).attr('id')+' > .azu-listbox-item').each(function() {
                                $(this).find('input[type="range"].azu-listbox-child').CalcColor(hexcolor);
                            });
                        }
                    } 
            };
	$('#optionsframework .of-color').wpColorPicker(azuOptions);
	
	// Switches option sections
	$('.group').hide();
	var active_tab = '';
	if (typeof(localStorage) != 'undefined' ) {
		active_tab = localStorage.getItem("active_tab");
	}
	if (active_tab != '' && $(active_tab).length ) {
		$(active_tab).fadeIn();
	} else {
		$('.group:first').fadeIn();
	}
	$('.group .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
						return false;
					}
				$(this).filter('.hidden').removeClass('hidden');
			});
	});
	if (active_tab != '' && $(active_tab + '-tab').length ) {
		$(active_tab + '-tab').addClass('nav-tab-active');
	}
	else {
		$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}
	
	$('.nav-tab-wrapper a').click(function(evt) {
		$('.nav-tab-wrapper a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active').blur();
		var clicked_group = $(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("active_tab", $(this).attr('href'));
		}
		$('.group').hide();
		$(clicked_group).fadeIn();
		evt.preventDefault();
		
		// Editor Height (needs improvement)
		$('.wp-editor-wrap').each(function() {
			var editor_iframe = $(this).find('iframe');
			if ( editor_iframe.height() < 30 ) {
				editor_iframe.css({'height':'auto'});
			}
		});
	
	});
           					
	$('.group .collapsed input:checkbox').click(unhideHidden);
				
	function unhideHidden(){
		if ($(this).attr('checked')) {
			$(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			$(this).parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).filter('.last').length) {
					$(this).addClass('hidden');
					return false;		
					}
				$(this).addClass('hidden');
			});
           					
		}
	}
	
	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parents('.controls').find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');		
	});

	// Clear selection if image removed
	$('.section-background_img').on('click', 'a.remove-image, input.button', function(e) {
		e.preventDefault();
		$(this).parents('.controls').find('.of-radio-img-img').removeClass('of-radio-img-selected');
	});

	// radio image label onclick event handler
	$('.of-radio-img-label').on('click', function(e) {
		e.preventDefault();
		$(this).siblings('.of-radio-img-img').trigger('click');
	});

	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();
	


	// Preview
	if ( $( ".of-input.azu-web-fonts" ).length > 0 ) {

		$( ".of-input.azu-web-fonts" ).on( "change", function() {

			var _this = $( this ),
				id = _this.attr( "id" ),
				value = _this.val(),
				font_header = value.replace( / /g, "+" ),
				font_style = value.split( "&" )[0],
				_preview = _this.siblings('.azu-web-fonts-preview').first().find('span').first(),
				italic = bold = '';

			font_style = font_style.split( ":" );

			if ( font_style[1] ) {

				var vars = font_style[1].split( 'italic' );

				if ( 2 == vars.length ) { italic = "font-style: italic;"; }

				if ( '700' == vars[0] || 'bold' == vars[0] ) {

					bold = "font-weight: bold;";
				} else if ( '400' == vars[0] || 'normal' == vars[0] ) {

					bold = "font-weight: normal;";
				} else if ( vars[0] ) {

					bold = "font-weight: " + parseInt( vars[0] ) + "};";
				} else {

					bold = "font-weight: normal;";
				}

			}else {

				bold = "font-weight: normal;";
			}

			var protocol = 'http:';
			if ( typeof document.location.protocol != 'undefined' ) {
				protocol = document.location.protocol;
			}

			var linkHref = protocol + '//fonts.googleapis.com/css?family=' + font_header,
				linkStyle = 'font-family: "' + font_style[0] + '";' + italic + bold;

			var style = '<link id="' + id + '-font-preview" href="' + linkHref + '" rel="stylesheet" type="text/css">';

			_preview.hide();

			$('#' + id + '-font-preview').remove();

			if ( !_this.azu_is_font_web_safe( value ) ) {
				$('head').append( style );
			}

			_preview.attr('style', linkStyle).show();
		} );
		$( ".of-input.azu-web-fonts" ).trigger( 'change' );
	}
	/* End Web fonts */
	

    // js_hide
    jQuery('#optionsframework .of-js-hider').each(function() {
        var element = jQuery(this),
        	target = element.closest('.section').next('.of-js-hide'),
    		hideThis = jQuery( '.' + element.closest('.section').attr('id').replace('section-', '') );

        /* If checkbox */
        if ( element.is('input[type="checkbox"]') ) {
        	element.on('click', function(){
        		target.fadeToggle(400);
        	});

        	if(element.prop('checked')) {
        		target.show();
        	}
        /* If slider */
        } else if ( element.hasClass('of-slider') ) {
        	if(element.hasClass('js-hide-if-not-max')){
				var azu_range =element.next('input.of-slider-value');
        		azu_range.on('change', {max_value: azu_range.attr('data-max')}, function(e, ui){
        			var $this = jQuery(this);
        			if(this.value != e.data.max_value) {
        				target.show();
        			} else {
        				target.hide(400);
        			}
        		});
        		if(element.slider('option', 'value') != element.slider('option', 'max')) {
        			target.show();
        		}
        	}
        /* If radio */
        } else if ( element.is('input[type="radio"]') ) {

        	if ( element.attr('data-js-target') ) {
        		target = jQuery( '.' + element.attr('data-js-target') );
        	}

        	if ( target.length > 0 ) {
	         	element.on('click', function(){

	         		if ( hideThis.length > 0 ) {
	        			hideThis.hide();
	        		}

	        		if ( $(this).hasClass('js-hider-show') ) {
	        			target.show();
	        		} else {
	        			target.hide();
	        		}
	        	});

	        	if(element.prop('checked')) {
	        		element.click();
	        	}
	        }
        }
        
    });
	
	// js_hide_global
    jQuery('#optionsframework input[type="checkbox"].of-js-hider-global').click(function() {
        var element = jQuery(this);
        element.parents('.section-block_begin').next('.of-js-hide').fadeToggle(400);
    });
    
    jQuery('#optionsframework input[type="checkbox"]:checked.of-js-hider-global').each(function(){
        var element = jQuery(this);
        element.parents('.section-block_begin').next('.of-js-hide').show();
    });

    // Share buttons
    jQuery( "#optionsframework .section-social_buttons .connectedSortable" ).sortable();

    jQuery('#optionsframework .section-social_buttons .connectedSortable input[type="checkbox"]').on('change', function() {
        var $input = jQuery(this);
        if($input.is( ':checked' ))
            $input.parent().find('input[type="hidden"]').first().val(1);
        else
            $input.parent().find('input[type="hidden"]').first().val(0);
    });


    /* Theme scripts */

    // headers layout
    jQuery('#optionsframework #section-header-layout .controls input.of-radio-img-radio').on('click', function(e) {
    	var $this = jQuery(this),
    		$target = $this.parents('.section-block_begin');
		
		// hide
		$target.find('.of-js-hide.header-layout').hide();
    	
		// show
    	if ( $this.prop('checked') ) {
    		$target.find('.of-js-hide.header-layout-'+$this.val()).show();
    	}
    });
    jQuery('#optionsframework #section-header-layout .controls input:checked.of-radio-img-radio').trigger('click');

});

function azuRadioImagesSetCheckbox( target ) {
	document.getElementById(target).checked=true;
	jQuery('#'+target).trigger('click');
}

/**
 * Background image preset images.
 */
jQuery(function($){
	$('.section-background_img .of-radio-img-img').on('click', function() {
		var selector = $(this).parents('.section-background_img'),
			attachment = $(this).attr('data-full-src'),
			preview = $(this).attr('src'),
			uploadButton = selector.find('.upload-button'),
			screenshot = selector.find('.screenshot');

		selector.find('.upload').val(attachment);
		selector.find('.upload-id').val(0);

		if ( screenshot.find('img').length > 0 ) {
			// screenshot.hide();
			screenshot.find('img').attr('src', attachment);
			screenshot.show();
		} else {
			screenshot.empty().append('<img src="' + attachment + '"><a class="remove-image">Remove</a>').slideDown('fast');
		}
		// screenshot.empty().hide().append('<img src="' + attachment + '"><a class="remove-image">Remove</a>').slideDown('fast');

		if ( uploadButton.length > 0 ) {
			uploadButton.unbind().addClass('remove-file').removeClass('upload-button').val(optionsframework_l10n.remove);
			optionsframework_file_bindings(selector);
		}

		selector.find('.of-background-properties').slideDown();

	});
});


/*
 * Viewport - jQuery selectors for finding elements in viewport
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *  http://www.appelsiini.net/projects/viewport
 *
 */
(function($) {
    
    $.belowthefold = function(element, settings) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var top = $(window).scrollTop();
        return top >= $(element).offset().top + $(element).height() - settings.threshold;
    };
    
    $.rightofscreen = function(element, settings) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left - settings.threshold;
    };
    
    $.leftofscreen = function(element, settings) {
        var left = $(window).scrollLeft();
        return left >= $(element).offset().left + $(element).width() - settings.threshold;
    };
    
    $.inviewport = function(element, settings) {
        return !$.rightofscreen(element, settings) && !$.leftofscreen(element, settings) && !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
    };
    
    $.extend($.expr[':'], {
        "below-the-fold": function(a, i, m) {
            return $.belowthefold(a, {threshold : 0});
        },
        "above-the-top": function(a, i, m) {
            return $.abovethetop(a, {threshold : 0});
        },
        "left-of-screen": function(a, i, m) {
            return $.leftofscreen(a, {threshold : 0});
        },
        "right-of-screen": function(a, i, m) {
            return $.rightofscreen(a, {threshold : 0});
        },
        "in-viewport": function(a, i, m) {
            return $.inviewport(a, {threshold : 0});
        }
    });

    
})(jQuery);

jQuery(function($){
	var $wrap = $("#optionsframework"),
		$controls = $("#submit-wrap"),
		$footer = $("#wpfooter");

	function setSize() {
		$controls.css({
			"width" : $wrap.width()
		});
	};

	function setFlow() {
		var wrapBottom = $wrap.offset().top + $wrap.outerHeight(),
			viewportBottom = $(window).scrollTop() + $(window).height();

		if (viewportBottom <= wrapBottom) {
			$controls.addClass("flow");
		}
		else {
			$controls.removeClass("flow");
		};
	};


	$wrap.css({
		"padding-bottom" : $controls.height()
	});

	setSize();
	setFlow();

	$(window).on("scroll", function() {
		setFlow();
	});

	$(window).on("resize", function() {
		setSize();
	});
});

//azu drag & drop
jQuery(function($){
        //hide default color list
        $('#section-color1').hide();
        
        // init drag & drop
        $( "#optionsframework .section-listbox .azu-drag-and-drop" ).fieldChooser();
});