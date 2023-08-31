/***********************************************
 * AzuMenu Administrative JavaScript
 * 
 * @author Damii
 * @version 1.0.0.0
 * Last modified 2014-08-05
 * 
 ***********************************************/

jQuery(document).ready(function($){

	var DEBUG = false;

	var mega_inputs_selector = '.azumega-custom input, .azumega-custom textarea, .azumega-custom select';
	$megaInputs = $( mega_inputs_selector );
	
	function processMegaAtts(){
		$( '.azumega-atts.azumega-unprocessed' ).each( function(){
			$( this ).removeClass( 'azumega-unprocessed' );
			var $inputs = $( this ).find( ':input:not( .azu_options_input )' );
			$inputs.each( function(){
				var name = $( this ).attr( 'name' );
				name = name.substring( 0 , name.indexOf( '[' ) );
				$( this ).attr( 'data-name' , name ).attr( 'name' , name ); //.removeAttr( 'name' );
			});
			var options = $inputs.serialize();
			$( this ).find( '.azu_options_input' ).val( options );
			$inputs.removeAttr( 'name' );
		});
	}
	processMegaAtts();
	
	var $menu_management = $( '#menu-management' );
	$menu_management.on( 'change' , mega_inputs_selector , function(){		
		var $attGroup = $( this ).parents( '.azumega-atts' );
		var $inputs = $attGroup.find( ':input:not( .azu_options_input )' );
		$inputs.each( function(){
			$( this ).attr( 'name' , $( this ).attr( 'data-name' ) );
		});
		var options = $inputs.serialize();

		$attGroup.find( '.azu_options_input' ).val( options );
		$inputs.removeAttr( 'name' );
	});

	
	/* MENU ITEMS */
	/** Menu Panel Add New Item Override **/
	/* This overrides the normal addItemToMenu Function, in order to call a different callback which invokes the custom walker */
	if(typeof wpNavMenu != 'undefined'){
		wpNavMenu.addItemToMenu = function(menuItem, processMethod, callback) {
			var menu = $('#menu').val(),
			nonce = $('#menu-settings-column-nonce').val();
		
			processMethod = processMethod || function(){};
			callback = callback || function(){};
		
			params = {
				//'action': 'add-menu-item',
				'action': 'azumega-add-menu-item',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'menu-item': menuItem
			};

			$.post( ajaxurl, params, function(menuMarkup) {
				var ins = $('#menu-instructions');
				processMethod(menuMarkup, params);
				// Make it stand out a bit more visually, by adding a fadeIn
				$( 'li.pending' ).hide().fadeIn('slow');
				$( '.drag-instructions' ).show();
				if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length )
					ins.addClass( 'menu-instructions-inactive' );
				callback();
				processMegaAtts();
			});
		};
	}

	/** Menu Panel Choosing Images **/

	// Uploading files
	var azu_media_frame;
	var $menu_item_button;
	var um_item_id;

	$('.menu').on( 'click', '.set-menu-item-thumb' , function( e ){
	 
		e.preventDefault();
		$menu_item_button = $(this);
		um_item_id = $menu_item_button.data( 'menu-item-id' );
	 
		// If the media frame already exists, reopen it.
		if( azu_media_frame ) {
			//azu_media_frame.uploader.uploader.param( 'um_item_id', um_item_id );
			azu_media_frame.open();
			return;
		}
	 
		// Create the media frame.
		azu_media_frame = wp.media.frames.azu_media_frame = wp.media({
			className: 'azumenu-media-frame media-frame',
			library: {
				type: 'image'
			},
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		azu_media_frame.on( 'select', function() {
		
			// We set multiple to false so only get one image from the uploader
			attachment = azu_media_frame.state().get('selection').first().toJSON();

			// Do something with attachment.id and/or attachment.url here
			$menu_item_button.html( '<img src="'+attachment.url + '" />' );

			$.ajax({
				type:	'POST',
				cache:	false,
				url:	ajaxurl,
				data:	{
					"action" : "azumenu_getMenuImage",
					"menu_item_id" : um_item_id,
					"thumbnail_id" : attachment.id
				},
				error:	function(req, status, errorThrown){
					if(DEBUG) console.log('Error: '+status+' | '+errorThrown);
				},
				success: function(data, status, req){
					if( data == '' || data.image == '' ){
						$menu_item_button.html( 'Set Thumbnail' );
					}
					else{
						$menu_item_button.html( data.image );
						$('a#remove-post-thumbnail-'+data.id).remove();
						
						$menu_item_button.after(
								'<div class="remove-item-thumb" id="remove-item-thumb-'+data.id+'">'+
									'<a href="#" id="remove-post-thumbnail-'+data.id+'" '+
										'onclick="azumega_remove_thumb(\''+ data.remove_nonce +'\', '+	data.id+'); return false; ">'+
										'Remove image</a></div>');
						
					}
				}
			});
			
		});

		// Finally, open the modal
		azu_media_frame.open();
	});
	
	
	
	//For WordPress 3.3
	var menuItemID = 0;
	WPSetThumbnailID = function(id){
		menuItemID = id;
		var field = jQuery('input[value="_thumbnail_id"]', '#list-table');
		if ( field.size() > 0 ) {
			jQuery('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(id);
		}
	};
	
	
	//For WordPress 3.3
	WPSetThumbnailHTML = function(html){
		
		var $item = $('#'+megaMenuAdminItemID);
		$item.addClass('azumega-loading-img');
		$.ajax({
			type:	'POST',
			cache:	false,
			url:	ajaxurl,
			data:	{ "action" : "azumenu_getMenuImage",	"id" : megaMenuAdminItemID },
			error:	function(req, status, errorThrown){
				if(DEBUG) console.log('Error: '+status+' | '+errorThrown);
			},
			success: function(data, status, req){
				$item.removeClass('azumega-loading-img');
				if(data == '' || data.image == ''){
					$item.text('Set Thumbnail');
				}
				else{
					$item.html(data.image);
					$('a#remove-post-thumbnail-'+data.id).remove();
					$item.after(
							'<div class="remove-item-thumb" id="remove-item-thumb-'+data.id+'">'+
								'<a href="#" id="remove-post-thumbnail-'+data.id+'" '+
									'onclick="azumega_remove_thumb(\''+ data.remove_nonce +'\', '+	data.id+'); return false; ">'+
									'Remove image</a></div>');
				}
			}
		});
	};
	
	//For WordPress 3.3
	WPRemoveThumbnail = function(nonce){};
	
	//For WordPress 3.2
	WPSetAsThumbnail = function(a,b){};
	
	
	$( '.appearance_page_azu-menu .updated, .appearance_page_azu-menu .error' ).prependTo('.wrap');
		
});

function azumega_remove_thumb(nonce, id){
	jQuery.post(ajaxurl, {
		action:"set-post-thumbnail", post_id: id, thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	}, function(str){
		if ( str == '0' ) {
			alert( setPostThumbnailL10n.error );
		} else {
			if(str != '-1'){
				jQuery('a#set-post-thumbnail-'+id).html('Set Thumbnail');
				jQuery('a#remove-post-thumbnail-'+id).remove();
			}
		}
	});
};


