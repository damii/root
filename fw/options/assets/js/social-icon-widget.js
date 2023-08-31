(function($) {
"use strict";

function azuWidgetSwitcherListShowHide ( element ) {
        if ( element.length != 1 ) return;

        var container = element.parents('.azu-widget-switcher').next('div.hide-if-js');
        if( 'all' == element.val() ) {
                container.hide();
        }else {
                container.show();
        }
}


// do some stuff on widget save
$(document).ajaxSuccess(function(e, xhr, settings) {
	if ( (typeof settings.data !=='undefined') && settings.data.search( 'action=save-widget' ) != -1 )
	{
        // do some stuff
		var settingsArray = settings.data.split( '&' ),
			sidebar = '',
			widgetId = '';
		for ( var i = settingsArray.length - 1; i >= 0 ; i-- ) {
			if ( sidebar && widgetId ) { break; }
			
			if ( settingsArray[ i ].search( 'sidebar=' ) != -1 ) {
				sidebar = '#' + settingsArray[ i ].split( '=' )[1] + ' ';
			} else if ( settingsArray[ i ].search( 'widget-id=' ) != -1 ) {
				widgetId = 'div.widget[id$="' + settingsArray[ i ].split( '=' )[1] + '"] ';
			}

		}
		azuWidgetSwitcherListShowHide( $( sidebar + widgetId + '.azu-widget-switcher input:checked' ) );
	}
} );



$(document).ready(function($) {
    

    $('.azu-widget-switcher input').live( 'click', function() {
        if( $(this).attr('name').search('__i__') == -1 ) {
			azuWidgetSwitcherListShowHide( $(this) );
        }
    } );
	
    $('.azu-widget-switcher input:checked').each( function() {
            azuWidgetSwitcherListShowHide( $(this) );
    } );
    
    /* start field generator
     * of_fields_generator script
     *  
     */
        $('.nav-menus-php .menu-item-handle').live('click',function(event) {
            var parent = $(this).parent().parent();
            if ( !parent.hasClass('ui-sortable') && parent.length > 0 ) {
                    parent.sortable();
            }
        });

	// add button
    $('button.of_fields_gen_add').live('click',function(e) {
    	e.preventDefault();
        var container = $(this).parent().prev('.of_fields_gen_list'),
        	layout = $(this).parents('div.of_fields_gen_controls'),
        	del_link = '<div class="submitbox"><a href="#" class="of_fields_gen_del submitdelete">Delete</a></div>';

        if ( !layout.find('.of_fields_gen_title').val() ) return false;
        
		var size = 0;
		container.find('div.of_fields_gen_title').each( function(){
			var index = parseInt($(this).attr('data-index'));
			if( index >= size )
				size = index;
		});
		size += 1;

        var new_block = layout.clone();
        new_block.find('button.of_fields_gen_add').detach();
        new_block
            .attr('class', '')
            .addClass('of_fields_gen_data menu-item-settings description')
			.append(del_link);
        
		var title = $('<span class="azu-menu-item-title">').text( $('.of_fields_gen_title', layout).val() );
		var div_title = $('<div class="of_fields_gen_title menu-item-handle" data-index="' + size + '"><span class="item-controls"><a class="item-edit"></a></span></div>');
		
        new_block.find('input, textarea, select').each(function(){
            var name = $(this).attr('name').toString();
            
            // this line must be awful, simple horror
            $(this).val(layout.find('input[name="'+name+'"], textarea[name="'+name+'"], select[name="'+name+'"]').val());

            name = name.replace(/\[(?![\s\S]*\]\[)/, "["+ size +"][");
            $(this).attr('name', name);
			
			var hidden_desc = $(this).next('.azu-hidden-desc');

			if( 'checkbox' == $(this).attr('type') && $(this).attr('checked') && hidden_desc ) {
				div_title.prepend( hidden_desc.clone().removeClass('azu-hidden-desc') );
			}
        });
        container.append(new_block);
		
		div_title.prepend(title);
		
        new_block
            .wrap('<li class="nav-menus-php"></li>')
            .before(div_title);
        
		new_block.hide();
	
    });

	var del_button = function() {
			var title_container = $(this).parents('li').find('div.of_fields_gen_title');
			title_container.next('div.of_fields_gen_data').hide().detach();
			title_container.hide('slow').detach();
			return false;
	};
		
	var toggle_button = function(event) {
			if( $(event.target).parents('.of_fields_gen_title').is('div.of_fields_gen_title') ) {
				$(event.target).parents('.of_fields_gen_title').next('div.of_fields_gen_data').toggle();
			}
	};
    
	var checkbox_check = function() {
			var this_ob = $(this);
			var hidden_desc = this_ob.next('.azu-hidden-desc');
			if( !hidden_desc.length ) return true;
			hidden_desc = hidden_desc.clone().removeClass('azu-hidden-desc');
			
			var div_title = $(event.target)
                .parents('div.of_fields_gen_data')
                .prev('div.of_fields_gen_title')
				.children('.azu-menu-item-title');
			
			if( this_ob.attr('checked') ) {
				div_title.after( hidden_desc );
			}else {
				div_title.parent().find('.' + hidden_desc.attr('class')).remove();
			}
	};
        
        var change_title = function(event) {
            if( $(event.target).not('div').is('.of_fields_gen_title') ) {
                var title = $(event.target)
                    .parents('div.of_fields_gen_data')
                    .prev('div.of_fields_gen_title')
                    .children('.azu-menu-item-title');

                            if( title ) {
                                    title.text( $(event.target).val() );
                            }
            }
        };
        
        function social_init() {
                   $('.menu-item-handle .item-edit').live('click',toggle_button);
                   $('.of_fields_gen_del').live('click', del_button );
                   $('.of_fields_gen_data input[type="checkbox"]').live('change',checkbox_check);
                   $('div.widget-content, .rwmb-input-_azu_teammate_options_social').live('change',change_title);
        }
        social_init();
        
	// on load indication
	$('.widget-inside .nav-menus-php').each( function() {
		var title = $('.azu-menu-item-title', $(this));
		
		$('input[type="checkbox"]:checked', $(this)).each( function() {
			var hidden_desc = $(this).next('.azu-hidden-desc');
			if( hidden_desc.length ) {
				var new_desc = hidden_desc.clone();
				title.after( new_desc.removeClass('azu-hidden-desc') );
			}
		});
	});


    // of_fields_generator end
    
});

}(jQuery));