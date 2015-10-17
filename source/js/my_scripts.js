
jQuery(function( $ ){

    $(window).scroll(function(){

        scrolltop = $(window).scrollTop();
        scrollwindow = scrolltop + $(window).height();

        // Section Below Header
        $(".parallax-section.below-header").css("backgroundPosition", "0px " + -(scrolltop/6) + "px");


        // Section Above Footer
        if( scrollwindow > $(".parallax-section.above-footer").offset().top ) {

            backgroundscroll = scrollwindow - $(".parallax-section.above-footer").offset().top;
            $(".parallax-section.above-footer").css("backgroundPosition", "0px " + -(backgroundscroll/6) + "px");

        }

    });

});
