(function($) {
"use strict";

var azuChange = function(e) {
    setTimeout(function(){  
                $('#_azu_header_mode').find('input[type="radio"]').each(function(e){
                    if(!$(this).parent().hasClass('act')){
                        $('#_azu_header_'+$(this).val()+'_slider').parent().parent().hide();
                    }
                });
    }, 50);
};

/* custom metabox */
$(function () {
	// show/hide slideshow meta boxes
	$('.rwmb-input-_azu_header_slideshow input[type="checkbox"]').on('change', azuChange);
        
	// trigger change event after meta box switcher
	$("#page_template").on('azuBoxesToggled', function(){
		var template = $(this).val();
                $('.rwmb-input-_azu_header_slideshow input[type="checkbox"]:checked').trigger('change');
                
		// show/hide meta box fields for templates
		$('.rwmb-hidden-field.hide-if-js').each(function(e){
			var $this = $(this),
				attr = $this.attr('data-show-on');
			
			if ( typeof attr !== 'undefined' && attr !== false ) {
				attr = attr.split(',');
				if ( attr.indexOf(template) > -1 ) { $this.show(); }
				else { $this.hide(); }
			}
		});
	});

	// change event for radio buttons
	$('.rwmb-radio-hide-fields').each(function() {
		var $miniContainer = $(this),
			$container = $miniContainer.parents('.rwmb-field').first();

		$miniContainer.find('input[type="radio"]').on('click', function(e){
			var $input = $(this),
				ids = $input.attr('data-hide-fields'),
				hiddenIds = jQuery.data($miniContainer, 'hiddenFields') || [],
				showIds = hiddenIds;

			if ( ids ) {
				ids = ids.split(',');
			} else {
				ids = new Array();
			}

			// hide fields
			for( var i = 0; i < ids.length; i++ ) {
				$('.rwmb-input-'+ids[i]).closest('.rwmb-field, .rwmb-flickering-field').hide();
				
				var showIndex = showIds.indexOf(ids[i]);
				if ( showIndex > -1 ) {
					showIds.splice(showIndex, 1);
				}
			}

			// show hidden fields
			for( i = 0; i < showIds.length; i++ ) {
				$('.rwmb-input-'+showIds[i]).closest('.rwmb-field, .rwmb-flickering-field').show();
			}

			// store hidden ids
			jQuery.data($miniContainer, 'hiddenFields', ids);
		});
		$miniContainer.find('input[type="radio"]:checked').trigger('click').trigger('change');
	});

	// change event for checkboxes
	$('.rwmb-checkbox-hide-fields').each(function() {
		var $miniContainer = $(this),
			$container = $miniContainer.parents('.rwmb-field').first();

		$miniContainer.find('input[type="checkbox"]').on('change', function(e){
			var $input = $(this),
				ids = $input.attr('data-hide-fields');

			if ( ids ) {
				ids = ids.split(',');
			} else {
				ids = new Array();
			}

			if ( $input.prop('checked') ) { 

				// show hidden fields
				for( i = 0; i < ids.length; i++ ) {
					$('.rwmb-input-'+ids[i]).parent().show();
				}

			} else {

				// hide fields
				for( var i = 0; i < ids.length; i++ ) {
					$('.rwmb-input-'+ids[i]).parent().hide();
				}

			}			


		});
		$miniContainer.find('input[type="checkbox"]').trigger('change').trigger('change');
	});
});
        



// Metabox js
$(document).ready(function(){

//Show content
function azu_show($_box, init){
	if (init == true){
		$_box.show();
	} else {
		$_box.animate({ opacity: '1' }, { queue: false, duration: 500 }).slideDown(500);
	}
}

//Hide content
function azu_hide($_box){
	$_box.animate({ opacity: '0' }, { queue: false, duration: 500 }).slideUp(500);
}

//Switch content
function azu_switcher($_this, init){
	var $_box = $("."+$_this.attr("data-name"));
	if ( $_this.attr("value")=="show" && $_this.is(":checked") || $_this.attr("value")=="show" && $_this.is(":hidden") ){
		azu_show($_box, init);
	} else if ($_this.attr("value")=="hide" && $_this.is(":checked") || $_this.attr("value")=="hide" && $_this.is(":hidden") ) {
		azu_hide($_box);
	}
	// add checkbox support
	if( $_this.is('input[type="checkbox"]') ) {
		if( $_this.is(":checked") ) azu_show($_box, init);
		else azu_hide($_box);
	}
}

/* Radio-image: begin */
//Highlite the active radio-image
$(".azu_radio-img label input:checked").parent("label").addClass("act").siblings('label').removeClass("act");

//Handle the click on the radio-image
$(".azu_radio-img label").on("click", function(){
	$(this).siblings('label').removeClass("act").find('input').removeAttr("checked");
	$("> input", this).attr("checked","checked").trigger('change');
	$(this).addClass("act");
});
/* Radio-image: end */

/* Radio-switcher: begin */
//Show the certain content when the page loads
$(".azu_switcher input:checked").each(function(){
	azu_switcher($(this), true);
});

//Handle the click on the switcher
$(".azu_switcher").on("change", function(e){
	azu_switcher($(" > input", e.currentTarget));
});
/* Radio-switcher: end */

/* Advanced settings toggle: begin */
//Show the certain content when the page loads
$(".azu_advanced input[value=show]").each(function(){
	$(this).parent().addClass("act");
	azu_switcher($(this), true);
});

//Handle the click on the toggle
$(".azu_advanced").on("click", function(e){
	e.preventDefault();

	var	$_this = $(e.currentTarget),
		$_check = $("> input:hidden", $_this);

	if ($_check.attr("value")=="show") {
		$_this.removeClass("act");
		$_check.attr("value", "hide");
	} else if ($_check.attr("value")=="hide") {
		$_this.addClass("act");
		$_check.attr("value", "show");
	}

	azu_switcher($_check);
});
/* Advanced settings toggle: end */

/* Tabs: begin */
//Handle the tab navigation
function azu_tabs(label){
	var	$_this = $(label),
		$_check = $("> input", $_this);
		
	$_this.siblings("label").removeClass("act").find("input").removeAttr("checked");
	$_check.attr("checked","checked");
	$_this.addClass("act");
	
	var $_tabs = $_this.parents(".azu_tabs"),
		$_tabs_content = $_tabs.next(".azu_tabs-content");

	if ($_check.attr("value")=="all") {
		$("> div", $_tabs_content).hide();
		$("> .azu_tab-all", $_tabs_content).show();
		$("> .azu_arrange-by", $_tabs).not('.hide-if-js').hide();
		$_tabs_content.removeClass("except only");
	} else if ($_check.attr("value")=="only") {
		$("> div", $_tabs_content).hide();
		$("> .azu_tab-select", $_tabs_content).show();
		$("> .azu_arrange-by", $_tabs).not('.hide-if-js').show();
		$_tabs_content.removeClass("except").addClass("only");
	} else if ($_check.attr("value")=="except") {
		$("> div", $_tabs_content).hide();
		$("> .azu_tab-select", $_tabs_content).show();
		$("> .azu_arrange-by", $_tabs).not('.hide-if-js').show();
		$_tabs_content.removeClass("only").addClass("except");
	}
	
	if ($_check.attr("value")=="category") {
		$("> .azu_tab-select > div", $_tabs_content).hide();
		$("> .azu_tab-select > .azu_tab-categories", $_tabs_content).show();
	}
}

//Highlite the active tab on the page load
$("label.azu_tab input:checked, label.azu_arrange input:checked").parent("label").addClass("act").siblings('label').removeClass("act");

//Show the active tab content on the page load
$(".azu_tabs input:checked").each(function() {
	azu_tabs($(this).parent("label"));
});

//Handle the click on the tab
$(".azu_tabs label").on("click", function(e){
	e.preventDefault();
	azu_tabs($(e.currentTarget));
});
/* Tabs: end */

/* Checkboxes: begin */
//Handle the check/uncheck action
function azu_toggle_checkbox(checkbox){
	var	$_this = $(checkbox),
		$_check = $("> input", $_this);
		
	if ($_check.attr("checked")=="checked") {
		$_check.removeAttr("checked");
		$_this.removeClass("act");
	} else {
		$_check.attr("checked", "checked");
		$_this.addClass("act");
	}
}

//Show checked checkboxes on the page load
$(".azu_checkbox").each(function(){
	var	$_this = $(this),
		$_check = $("> input", $_this);
		
	if ($_check.attr("checked")=="checked") {
		$_this.addClass("act");
	} else {
		$_this.removeClass("act");
	}
});

//Handle the click on the checkbox 
$(".azu_checkbox").on("click", function(e){
	e.preventDefault();
	azu_toggle_checkbox($(e.currentTarget));
});

//Emulate the click on the checkbox
$(".azu_item-cover, .azu_tab-categories > .azu_list-item > span").on("click", function(e){
	azu_toggle_checkbox($(e.currentTarget).parent().find(".azu_checkbox"));
});

//Emulate hover over the checkbox
$(".azu_item-cover, .azu_tab-categories > .azu_list-item > span").on("mouseenter", function(){
	$(this).parent().find(".azu_checkbox").addClass("azu_hover");
}).on("mouseleave",function(){
	$(this).parent().find(".azu_checkbox").removeClass("azu_hover");
});
/* Checkboxes: end */

$(window).resize(function(){
//	console.log("resizing")
	$(".azu_tabs-content").css({"max-height" : $(window).height() - 150});
});
$(window).trigger("resize");

});




/* metabox switcher */
$(document).ready(function(){
	var azu_boxes = new Object();
	// new!
	var azu_nonces = new Object();
	var nonce_field = null;
	function azu_find_boxes() {
		$('.postbox').each(function(){
                        
			var this_id = $(this).attr('id');
                        
			if(this_id.match(/azu_page_box-/i)){
				azu_boxes[this_id] = '#'+this_id;
				//new!
				if( typeof (nonce_field = $(this).find('input[type="hidden"][name*="nonce_"]').attr('id')) != 'undefined' ) {
					azu_nonces[this_id] = '#'+nonce_field;
				}
			}
		});
	}
	// new!
	azu_find_boxes();

	function azu_toggle_boxes() {
		var metaBoxes = arguments,
			$wpMetaBoxesSwitcher = $('#adv-settings');

		if( typeof arguments[0] == 'object' ) {
			metaBoxes = arguments[0];
		}

		for(var key in azu_boxes) {
			$wpMetaBoxesSwitcher.find(azu_boxes[key] + '-hide').prop('checked', '');
			$(azu_boxes[key]).hide();

			//new!
			if( 'azu_blocked_nonce' != $(azu_nonces[key]).attr('class') ) {
				$(azu_nonces[key]).attr('name', 'blocked_'+$(azu_nonces[key]).attr('name'));
				$(azu_nonces[key]).attr('class', 'azu_blocked_nonce');
			}
		}

		for(var i=0;i<metaBoxes.length;i++) {
			$wpMetaBoxesSwitcher.find(metaBoxes[i] + '-hide').prop('checked', true);
			$(metaBoxes[i]).show();

			// new!
			var nonce_key = metaBoxes[i].slice(1);
			if( 'azu_blocked_nonce' == $(azu_nonces[nonce_key]).attr('class') ) {
				var new_name = $(azu_nonces[nonce_key]).attr('name').replace("blocked_", "");
				$(azu_nonces[nonce_key]).attr('name', new_name);
				$(azu_nonces[nonce_key]).attr('class', '');
			}
		}
	}

	$("#page_template").change(function() {
		
		var templateName = $(this).val(),
			activeMetaBoxes = new Array();

		for( var metabox in azuMetaboxes ) {
			// choose to show or not to show
			if ( !azuMetaboxes[metabox].length || azuMetaboxes[metabox].indexOf(templateName) > -1 ) { activeMetaBoxes.push('#'+metabox); }
		}

		if ( activeMetaBoxes.length ) {
			azu_toggle_boxes(activeMetaBoxes);
		} else {
			azu_toggle_boxes();
		}
		
		$(this).trigger('azuBoxesToggled');
	});
	$("#page_template").trigger('change');

});


}(jQuery));