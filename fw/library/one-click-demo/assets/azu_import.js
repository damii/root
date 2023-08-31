(function($) {
    "use strict";
    
    $(document).ready(function()
    {
            $(".azu-button-dialog").click(function(e) {
                // Define the Dialog and its properties.
                var r = confirm("Are you sure?");
                e.preventDefault();
                if (r !== false)
                {
                    if( typeof $(this).attr("data-zip") !== 'undefined')
                        $('#azu-importer-form #zip').val( $(this).attr("data-zip") );
                    $('#azu-importer-form').submit();
                }
                
            });
    });
}(jQuery)); 