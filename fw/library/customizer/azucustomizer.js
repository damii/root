;(function ($, window, undefined) {
$(function() {
        function azu_get_collect_color(){
            var out ='@azu-layout-mobile-width: 870px;';
            $('#accordion-section-colors .accordion-section-content .customize-control-azu_listbox').each(function(i, e){
                var arr = $(this).children('input[type="hidden"]').val(),
                    el_id = $(this).attr('id').replace( "listbox_color", "color" ),
                    full_id = $(this).children('input[type="hidden"]').attr('data-customize-setting-link'),
                    id = full_id.match(/\[(.*?)\]/g)[0].replace(/\]|\[/g, ""),
                    default_colors={};
                
                arr = JSON.parse(arr.toString().replace('\"', '"'));
               
                if( id === 'listbox_color1'){
                    $(this).find('.azu-listbox-item').each(function(){
                        default_colors[$(this).attr('data-azu-listbox')] = $(this).attr('data-std');
                    });
                }

                var hex = $('#'+el_id +' .color-picker-hex').wpColorPicker( 'color' );
                for (var key in arr)
                {
                   var myhex = hex ? hex : '#ffffff';
                   if( id === 'listbox_color1'){
                       myhex = default_colors[key];
                   }
                   if (arr.hasOwnProperty(key))
                   {
                      var opacity = typeof arr[key].option !== 'undefined' ? arr[key].option: 100;
                      out +='@'+ key + ': '+ azu_convertHex(myhex,opacity) + ';';
                   }
                }
                
            });
            out += '@brand-primary: @base-brand-color;';
            //console.log(out);
            return out;
        }
        function azu_get_collect_font(){
            var out ='@azu-layout-mobile-width: 870px;';
            $('#accordion-section-typography .accordion-section-content .customize-control-azu_listbox').each(function(i, e){
                var arr = $(this).children('input[type="hidden"]').val(),
                    el_id = $(this).attr('id').replace( "listbox_font", "azu-font-family" );
                
                arr = JSON.parse(arr.toString().replace('\"', '"'));

                var font = $('#'+el_id +' select').val();
                for (var key in arr)
                {
                   if (arr.hasOwnProperty(key))
                   {
                      var size = typeof arr[key].Size !== 'undefined' ? arr[key].Size: 'normal';
                      var weight = typeof arr[key].Weight !== 'undefined' ? arr[key].Weight: 400;
                      var ls = typeof arr[key].ls !== 'undefined' ? arr[key].ls: 0;
                      var uc = typeof arr[key].uc !== 'undefined' ? arr[key].uc: 'none';
                      out +='@'+ key + ": '"+ font + "';";
                      out +='@'+ key + '_size: '+ size + ';';
                      out +='@'+ key + '_weight: '+ weight + ';';
                      out +='@'+ key + '_ls: '+ ls + ';';
                      out +='@'+ key + '_uc: '+ uc + ';';
                   }
                }
            });
            
            $('#accordion-section-typography .accordion-section-content .customize-control-range input[type="range"]').each(function(i, e){
                     var full_id = $(this).attr('data-customize-setting-link'),
                     value = $(this).val(),
                     id = full_id.match(/\[(.*?)\]/g)[0].replace(/\]|\[/g, "");
                     out +='@'+ id + ': '+ value + 'px;';
            });
            //console.log(out);
            return out;
        }
        
        function azu_convertHex(hex,opacity){
            if(typeof hex !== "string")
                return '#ffffff';
            hex = hex.replace('#','');
            var r = parseInt(hex.substring(0,2), 16),
            g = parseInt(hex.substring(2,4), 16),
            b = parseInt(hex.substring(4,6), 16);

            var result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
            return result;
        }
    
    
    
        var global_id = 0; 
        function azu_throttle (callback, limit,id) {
                    global_id = id;
                    setTimeout(function () {  
                                if(global_id === id)
                                    azu_callback.call();      
                            }, limit);
                }
                
        var input;
        function azu_callback(){
                //console.log(wp.customize.settings); //activated
                var theme_name = wp.customize.settings.theme.stylesheet.toLowerCase().replace("-child", ""), 
                        less_url = wp.customize.settings.url.home+"wp-content/themes/"+theme_name+"/ui/"+theme_name+"/less/",
                        src = less_url+"typography.less";
                if(input === 'color')
                     src = less_url+"color.less";
                $.get(src, function(data) {
                    var input_css ='';
                    if(input === 'color')
                        input_css = azu_get_collect_color();
                    else
                        input_css = azu_get_collect_font();
                    //console.log(input_css);
                    input_css += data;
                    less.render(input_css, {rootpath: less_url})
                        .then(function(output) {
                            //console.log(output.css);
                            var azu_iframe = $('#customize-preview').find('iframe').first();
                            azu_iframe[0].contentWindow.azu_include_style(input + '-theme-inline-css','link[id="azu-custom.less-css"],style[id="bootstrap-theme-inline-css"]',output.css);
                        },
                        function(error) {
                            console.log(error.message);
                        });
                 });
        }
        
        $(window).on("azu-less-js-compile", function( e, tp ) {
                input = tp;
                azu_throttle(azu_callback,500,Math.floor(Math.random() * 10000));  
        });
        
        $('#accordion-section-typography .accordion-section-content .customize-control-range input[type="range"]').on('change',function(){ $(window).trigger( "azu-less-js-compile" , ['font'] ); });
        
        // init drag & drop
        $( ".accordion-section-content .azu-drag-and-drop" ).fieldChooser();
        
        // Share buttons
        $( "#customize-theme-controls .customize-control-social_buttons .connectedSortable" ).sortable();

        $( "#customize-theme-controls .customize-control-social_buttons .connectedSortable" ).on('sortupdate', function() {
            share_array_update($(this));
        });
        
        function share_array_update($this){
            var array_val={};
            $this.find('input[type="checkbox"]').each(function (i, e) {
                array_val[$(this).data('name')]=$(this).val();
            });
            $this.parent().find('input[type="hidden"]').val(JSON.stringify(array_val)).trigger('change');
        }
        
        $('#customize-theme-controls .customize-control-social_buttons .connectedSortable input[type="checkbox"]').on('change', function() {
            var $input = $(this);
            if($input.is( ':checked' ))
                $input.val(1);
            else
                $input.val(0);
            share_array_update($input.parent().parent());
        });
        
  });
  


        function azu_webfont_view(e){
                    var _preview = $(this).parent(),
                            id = $(this).attr( "data-customize-setting-link" ),
                            font_style=$(this).val();
                    var protocol = 'http:';
                    if ( typeof document.location.protocol != 'undefined' ) {
                            protocol = document.location.protocol;
                    }

                    var linkHref = protocol + '//fonts.googleapis.com/css?family=' + font_style.replace( / /g, "+" ),
                            linkStyle = 'font-family: "' + font_style + '";';

                    var style = '<link id="' + id + '-font-preview" href="' + linkHref + '" rel="stylesheet" type="text/css">';

                    _preview.hide();

                    $('link[id="'+ id + '-font-preview"]').remove();

                    if ( !$(this).azu_is_font_web_safe( font_style ) ) {
                            $('head').append( style );
                    }

                    _preview.attr('style', linkStyle).show();
                    if(e.type =='change'){
                        $(window).trigger( "azu-less-js-compile" , ['font'] );
                        if ( !$(this).azu_is_font_web_safe( font_style ) ) {
                            var azu_iframe = $('#customize-preview').find('iframe').first();
                            azu_iframe[0].contentWindow.azu_include_link( id,linkHref);
                        }
                        
                    }
        }
  
  

        //customizer reload
        $( document ).ready(function() {
                var azuOptions = {   // i will change this
                        change: function(event, ui){
                            var hexcolor = ui.color.toString(),//$( this ).wpColorPicker( 'color' ),
                            id = $( this ).parent().parent().parent().parent().parent().attr('id').replace("-color", "-listbox_color");
                            if((typeof hexcolor !== 'undefined') && hexcolor.toString().length === 7){
                                    $('#'+id+' .azu-listbox-item').each(function() {
                                        $(this).find('input[type="range"].azu-listbox-child').CalcColor(hexcolor);
                                    });
                                    var key = $('#'+id+' > input[type="hidden"]').attr('data-customize-setting-link').replace("listbox_", "");
                                    wp.customize(key, function(obj) {
                                            obj.set(hexcolor);
                                    });
                                    $(window).trigger( "azu-less-js-compile" , ['color']  );
                                } 
                            },
                       clear: function() {
                            var id = $( this ).parent().parent().parent().parent().parent().attr('id').replace("-color", "-listbox_color");
                            var key = $('#'+id+' > input[type="hidden"]').attr('data-customize-setting-link').replace("listbox_", "");
                            wp.customize(key, function(obj) {
                                    obj.set(false);
                            });
                            $(window).trigger( "azu-less-js-compile" , ['color']  );
                       }
                };
                $('#accordion-section-colors .wp-picker-input-wrap > .color-picker-hex').wpColorPicker(azuOptions);
                
                var font_dropdown = '#accordion-section-typography .customize-control-select > label > select';  
                $(font_dropdown).each(azu_webfont_view);
                $(font_dropdown).on('change',azu_webfont_view);
        });
  
})(jQuery, this);




  
 
  
  