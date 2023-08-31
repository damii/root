(function($) {
"use strict";


function overline_do($this, $speed, $mover){
        var thisWidth = 0;
        if(azuGlobals.mobile_width < $(window).width() ){
            thisWidth = $this.outerWidth();
        }
        var pr = $this.parent();
        var thisLeftPos = pr.position().left,thisTopPos = parseInt($this.height() / 2) + pr.position().top;
        if(pr.parent().parent().attr('id') === 'azu-navbar-right'){
            thisLeftPos += parseInt(pr.css('margin-left')) + pr.parent().parent().position().left;
        }
        if($('body').hasClass('azu-nav-hover-style-though')){
            $mover.show();
            $mover.stop().animate({
                    top: thisTopPos,
                    left: thisLeftPos,
                    width: thisWidth
            }, $speed);
        }
}

$.fn.overline_reinit = function() {
        if(azuGlobals.IsMobile === '0' && ($('body').hasClass('azu-nav-hover-style-though') || typeof wp !== 'undefined')){
            $('.azu-navigation-field').addClass('azu-line-though');
            if($('.azu-mover-line').length === 0){
                $('.azu-navigation-field').prepend('<div class="azu-mover-line"></div>');
                overline_hover();
            }
                
            var $mover = $('.azu-line-though div.azu-mover-line');
            $mover.show();
            if ( $('.azu-line-though').length > 0) {
                var $currentEl = $('.azu-line-though > div > ul.nav > li.azu-act > a');

                if ( $currentEl.length ) {
                        overline_do($currentEl.first(),0,$mover.clearQueue());
                }
            }
        }
};

function overline_hover(){
        var $mover = $('.azu-line-though div.azu-mover-line');
        if($mover.length > 0){
            $('.azu-line-though > div > ul.nav > li > a').on('mouseover',function() {
                    overline_do($(this),'slow',$mover.clearQueue());
            });

            $('.azu-line-though > div > ul.nav').on('mouseout', function(event) {
                    var $currentEl = $('.azu-line-though > div > ul.nav > li.azu-act > a');
                    if ( $currentEl.length && $('body').hasClass('azu-nav-hover-style-though')) {
                        overline_do($currentEl.first(),'slow',$mover.delay( 500 ));
                    }
                    else {
                        $mover.delay( 500 ).stop().animate({left: 0,width: 0}, 'fast');
                    }
            }); 
        }
}

$(document).ready(function () {
    $(this).overline_reinit();
    //          Progress bar 
    if (typeof jQuery.fn.waypoint !== 'undefined') {

      jQuery('.azu-above-progress-bar').waypoint(function () {
        jQuery(this).find('.vc_single_bar').each(function (index) {
          var $this = jQuery(this),
            bar = $this.find('.vc_bar'),
            lab = $this.find('.vc_label_units'),
            val = bar.data('percentage-value');

          setTimeout(function () {
            lab.css({"right":(100 - val) + '%'});
          }, index * 200);
        });
      }, { offset:'85%' });
    }
});

	/*!- Custom resize function*/
	$(window).on("debouncedresize", function( e ) {
                $(this).overline_reinit();
	}).trigger( "debouncedresize" );
        /*Custom resize function:end*/
    
})(jQuery);