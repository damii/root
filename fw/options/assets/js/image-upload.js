(function($) {
"use strict";

/*global console, jQuery, document, wp*/
$(document).ready(function($) {
    var file_frame,root;

    // bind to click event for each upload button on the page
    $('.azu-images-upload, .azu-font-upload').live('click',
        function(event) {

            event.preventDefault();
            root = $(this).parent();
            
            // file frame already created, return
            if (file_frame) {
                file_frame.open();
                return;
            }
            var $mimetype = 'image',$multiple=false, is_font = $(this).hasClass('azu-font-upload');
            if(is_font){
                $mimetype = 'font';
                //$multiple = true;
            }
            // create the file frame
            file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).data('choose'),
                button: { text: $(this).data('update')},
                allowLocalEdits: false,
                displaySettings: true,
                multiple: $multiple,
                library: {
                    type: $mimetype
                }
            });

            // get the selected attachments
            file_frame.on('select', function() {
                var selected = file_frame.state().get('selection').first(),url_values =selected.attributes.url;
                
                if($multiple) {
                    url_values = [];
                    file_frame.state().get('selection').map( function( attachment ) {
                        url_values.push(attachment.attributes.url);
                    });
                    url_values = url_values.toString();
                }
                // get the input field that will store the image sources
                var store = root.find('input[type="hidden"].upload-uri,input[type="text"].upload-uri')
                    ,store2 = root.find('input[type="hidden"].upload-id');
                // store the new values and trigger a change
                store.val(url_values).trigger('change');
                if(typeof store2.val() !=='undefined')
                    store2.val(selected.attributes.id).trigger('change');
                var image_thumbnail = root.parent().find('.thumbnails');
                if ( selected.attributes.type == 'image' ) {
                    image_thumbnail.empty().hide().append('<img src="' + selected.attributes.url + '" class="azu-upload-img" alt="the image" >').slideDown('fast');
		}

                var c_background = root.parent().find('.azu-image-upload-bg');
                if(!$.isEmptyObject(c_background))
                    c_background.slideDown();
            });

            // open the just created file frame
            file_frame.open();
        });
        
        // click on remove button
        $('.azu-images-remove').live('click',
        function(event) {
            var root = $(this).parent();
            this.value;
            // get the input field that will store the image sources
            var store_hidden = root.find('input[type="hidden"]');
            store_hidden.val('').trigger('change');
            var image_preview = root.parent().find('img');
            image_preview.slideUp();
            var c_background = root.parent().find('.azu-image-upload-bg');
            if(!$.isEmptyObject(c_background))
                c_background.slideUp();
        });
        
        function get_font_file(str) {
            str = str.substring(str.lastIndexOf('/')+1);
            return str.toLowerCase().trim();
        }
        
        function check_exist_font(value) {
            var re = false,vals = JSON.parse($('.manual-fonts > input[type="hidden"]').first().val());
            value = get_font_file(value);
            for(var i = 0; i < vals.length; i++){
                if(get_font_file(vals[i]) === value){
                    re = true;
                    break;
                }
            }
            return re;
        }
        
        function update_font_array() {
            var $val = $('.manual-fonts > input[type="hidden"]').first(),fontDataValue = [];
            $('.azu-font-table tbody').children('tr:not(.azu-mf-no-item)').each(function () {
                fontDataValue.push($(this).children(':nth-child(2)').html().trim());
            });
            // store the new values and trigger a change
            $val.val(JSON.stringify(fontDataValue)).trigger('change');
        }
        
        $('.manual-fonts .azu-mf-delete').live('click', function(event) {
            var r = confirm("Are you sure you want to delete font?");
            if (r == true) {
                var $parent = $(this).parent().parent();
                if(typeof $parent.data('id') !== 'undefined'){
                    $parent.parent().find('#azu-mf-id-'+$parent.data('id')).remove();
                    update_font_array();
                }
            }
        });
        
        $('.manual-fonts .azu_add_font').live('click',
        function(event) {
            var $parent = $('.azu-font-table tbody'),$child=$parent.find('tr:last-child'),item_id = 0,font_value = $('.manual-fonts .upload-uri').first();
            if(font_value.val().length === 0){
                return;
            }
            else if(check_exist_font(font_value.val())){
                alert('Font is already exists');
                return;
            }

            if(typeof $child.data('id') !== 'undefined')
                item_id = parseInt($child.data('id')) + 1;
            
            if($child.hasClass('azu-mf-no-item'))
                $child.remove();
            $parent.append('<tr id="azu-mf-id-'+item_id+'" data-id="'+item_id+'"><td>'+item_id+'</td><td>'+font_value.val()+'</td><td><a class="azu-mf-delete" href="javascript:void(0)">delete</a></td></tr>');
            update_font_array();
            font_value.val('');
        });
        
});

}(jQuery));