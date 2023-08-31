(function($) {
    
    /* Web fonts
     * Php source located in options-interface.php 'web_fonts'
     */

    $.fn.azu_is_font_web_safe = function ( font ) {
            var safeFonts = [
                            'Arial',
                            'Arial Black',
                            'Comic Sans MS',
                            'Courier New',
                            'Georgia',
                            'Impact Lucida Console',
                            'Lucida Sans Unicode',
                            'Marlett',
                            'Minion Web',
                            'Times New Roman',
                            'Tahoma',
                            'Trebuchet MS',
                            'Verdana',
                            'Webdings'
                    ];
            
            if ( -1 == safeFonts.indexOf( font ) ) {
                    var azu_custom_style = $('style[id="azu_custom_fonts_admin"]');
                    if(azu_custom_style.length>0)
                        azu_custom_style.html(azuCustomFonts.font_face);
                    else
                        $('head').first().after('<style type="text/css" id="azu_custom_fonts_admin" media="all">'+azuCustomFonts.font_face+'</style>');
                    return false;
            }

            return true;
    };
    
     $.fn.azuChildDataGet = function (element){
            listboxDataValue = {};
            // get the input field that will store the image sources
            var liststore = element.parent().find('input[type="hidden"]');
            element.children('li').each(function () {
                var data_option = {};
                var cvalue = $(this).data("azu-listbox-option");
                
                if((typeof cvalue !== 'undefined') && cvalue.toString().length>0) {
                    var p = cvalue;
                    if((typeof cvalue !== 'object'))
                          p = JSON.parse(cvalue.toString().replace('\"', '"'));
                    for (var key in p) {
                        if (p.hasOwnProperty(key)) {
                          data_option[key] = p[key];
                        }
                    }
                }
                listboxDataValue[$(this).data("azu-listbox")] = data_option;
            });
            // store the new values and trigger a change
            liststore.val(JSON.stringify(listboxDataValue)).trigger('change');
            var list_type = '';
            if(typeof liststore.attr('id') !== 'undefined')
                list_type = liststore.attr('id');
            else if(typeof liststore.attr('data-customize-setting-link') !== 'undefined')
                list_type = liststore.attr('data-customize-setting-link');
            
            if(list_type.indexOf("listbox_color") > -1)
                list_type = ['color'];
            else
                list_type = ['font'];
            $(window).trigger( "azu-less-js-compile"  , list_type );
    };
        
    $.fn.fieldChooser = function()
    {
        return this.each(function() {
            $(this).sortable({
              cancel: ".azu-listbox-child,input",
              delay: 100,
              //placeholder: ".class",
              //cancel: ".class",
              connectWith: ".azu-drag-and-drop"
            });
            
            $(this).on('sortupdate', function() {
                $(this).azuChildDataGet($(this));
                //set colors to item
                $(this).find('.azu-listbox-item').each(function() {
                    $(this).find('input[data-mode="color"].azu-listbox-child').CalcColor();
                });
            });
        });
        
    };

    
    $.fn.initListboxToggle = function (){
                var root = $(this).parent().parent();
                var cbody = root.find('.azu-listbox-body');
                if(cbody.hasClass("azu-customize-hide"))
                    cbody.removeClass("azu-customize-hide");
                else
                    cbody.addClass("azu-customize-hide");
    };
    
    function hex2rgba( colour, opocity ) {
        var r,g,b;
        if ( colour.charAt(0) == '#' ) {
            colour = colour.substr(1);
        }
        else
            return '';
        if ( colour.length == 3 ) {
            colour = colour.substr(0,1) + colour.substr(0,1) + colour.substr(1,2) + colour.substr(1,2) + colour.substr(2,3) + colour.substr(2,3);
        }
        r = colour.charAt(0) + '' + colour.charAt(1);
        g = colour.charAt(2) + '' + colour.charAt(3);
        b = colour.charAt(4) + '' + colour.charAt(5);
        r = parseInt( r,16 );
        g = parseInt( g,16 );
        b = parseInt( b,16);
        opocity = parseInt(opocity)/100;
        return 'rgba(' + r + ',' + g + ',' + b + ',' + opocity.toString() + ')';
    };   
    
    // color compute
    $.fn.CalcColor = function (selected_color){
            if(typeof $(this).data('mode') === 'undefined' || $(this).data('mode') !== 'color')
                return;
            var colorbox_number = '';
            var root = $(this).parent().parent();
            var child_opocity = $(this);
            if(child_opocity.length > 0){
                child_opocity = child_opocity.val();
                colorbox_number = root.parent().attr('id').split('listbox_color',2);
                if(typeof selected_color ==='undefined'){
                    if(colorbox_number[1]==='1')
                        selected_color = root.attr('data-std');
                    else
                        selected_color = $('#section-color'+colorbox_number[1]+', #customize-control-'+colorbox_number[0]+'-color'+colorbox_number[1]).find('input[type="text"].of-color, input[type="text"].color-picker-hex').val();
                }
                root.find('.azu-color-window').css('background-color', hex2rgba(selected_color, child_opocity));
                root.find('.azu-color-background').css('display', 'block');
            }
    };
    
    // toggle and opacity
    jQuery(document).ready(function($) {
            "use strict";
            // bind to click event for each slider on the page
            $('.azu-listbox-child').live('change',function(event) {
                
                var root = $(this).parent().parent(),option_val = {},isColor = (typeof $(this).data('mode') !== 'undefined') && $(this).data('mode') == 'color';
                if(isColor)
                    option_val['option'] = $(this).val();
                else {
                    option_val = root.data("azu-listbox-option");
                    if((typeof option_val !== 'object'))
                          option_val = JSON.parse(option_val.toString().replace('\"', '"'));
                      
                    if( $(this).attr('type') === 'range'){
                        option_val['ls'] = $(this).val()/10;
                    }
                    else if( $(this).attr('type') === 'checkbox'){
                        option_val['uc'] = $(this).is(':checked') ? 'uppercase' : 'none';
                    }
                    else
                        option_val[$(this).data("azu-select")] = $(this).val();
                }    
                root.data("azu-listbox-option", JSON.stringify(option_val).toString().replace('"', '\"'));
                $(this).azuChildDataGet(root.parent());
                if(isColor)
                    $(this).CalcColor();
            });
            
            // live to click event for each item on the page
            $('.azu-listbox-toggle').live('click',function(event) {
                $(this).initListboxToggle();
            });
            
            //set colors to item
            $('.azu-listbox-item').each(function() {
                $(this).find('input[data-mode="color"].azu-listbox-child').CalcColor();
            });

    });
    
    
    
}(jQuery));