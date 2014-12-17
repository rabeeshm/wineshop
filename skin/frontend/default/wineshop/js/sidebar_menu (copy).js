jQuery(document).ready(function($) {
    //If there is NO active Item in the category nav, lets expand the first item
    if ($('#nav li.active').length == 0) {
        $('#nav li:first').addClass('active');
        $('#nav li:first').children('ul').slideDown('slow', function() {
            is_playing = false
        });
    }
    jQuery('#nav li').hover(
            function() {
                jQuery(this).addClass("hover");
                //jQuery(this).css("display","block");
            },
            function() {
                jQuery(this).removeClass("hover");
            }
    );
    jQuery('#nav ul').each(function() {
        jQuery(this).parent().addClass('items');
    });
    var is_playing = false;
    var counter = 30;
    function slide_check(this_button) {

        if (counter > 0) {
            if (this_button.hasClass('hover')) {
                if (is_playing == false) {
                    is_playing = true;
                    this_button.children('ul').slideDown('slow', function() {
                        is_playing = false
                    });
                    counter = counter - 10;
                }
            } else {
                if (is_playing == false) {
                    is_playing = true;
                    this_button.children('ul').slideUp('normal', function() {
                        is_playing = false
                    });
                }

            }
            counter--;
            setTimeout(function() {
                slide_check(this_button)
            }, 200);
        } else {
            setTimeout(function() {
                setSelected()
            }, 200);
        }
    }

//    jQuery('#nav > li').not('.active').each(function() {
//        jQuery(this).on("mouseenter mouseleave", function(e) {
//            if (jQuery(this).hasClass('items')) {
//                counter = 30;
//                slide_check(jQuery(this));
//            }
//            //setSelected();
//        });
//    });
//    
//    jQuery('#nav > li > ul > li').not('.active').each(function() {
//        jQuery(this).on("mouseenter mouseleave", function(e) {
//            if (jQuery(this).hasClass('items')) {
//                counter = 30;
//                slide_check(jQuery(this));
//            }
//            //setSelected();
//        });
//    });
    setSelected();

    jQuery('#nav > li > ul > li, #nav > li').not('.active').each(function() {
        var me = jQuery(this);
        me.click(function(e) {
            if (me.children('ul').css('display') == 'none') {
                me.data('slide-down-inprogress', true);
                me.children('ul').slideDown('slow', function() {
                    me.data('slide-down-inprogress', false);
                    var slideDownCalled = me.data('slide-down-called');
                    if (slideDownCalled) {
                        me.data('slide-down-called', false);
                        me.children('ul').slideUp('normal', function() {
                            selectActiveItem();
                        });
                    }
                });
            } else if ((me.hasClass('items') && me.find('li.active').length == 0 && me.parents().css("display") == 'none') ||
                    !me.find("li").hasClass('items') || me.find("li").find('ul').css('display') == 'none') {
                var progressing = me.data('slide-down-inprogress');
                if (!progressing) {
                    me.children('ul').slideUp('normal', function() {
                        selectActiveItem();
                    });
                } else {
                    //selectActiveItem();
                    //me.data('slide-down-called', true);
                }
            }
        });


//        me.on("mouseenter", function(e) {
//            if (me.hasClass('items')) {
//                me.data('slide-down-inprogress', true);
//                me.children('ul').slideDown('slow', function() {
//                    me.data('slide-down-inprogress', false);
//                    var slideDownCalled = me.data('slide-down-called');
//                    if (slideDownCalled) {
//                        me.data('slide-down-called', false);
//                        me.children('ul').slideUp('normal', function() {
//                            selectActiveItem();
//                        });
//                    }
//                });
//            }
//        });

//        me.click(function(e){
//            //e.stopPropagation();
//            if (me.hasClass('items') && me.find('li.active').length == 0) {
//                var progressing = me.data('slide-down-inprogress');
//                if (!progressing) { alert(2);
//                    //me.children('ul').slideUp('normal', function() {
//                        selectActiveItem();
//                    //});
//                } else {
//                    //selectActiveItem();
//                    //me.data('slide-down-called', true);
//                }
//            }
//        });
    });
});

function setSelected() {
    jQuery('li').each(function(i) {
        if (jQuery(this).is('.active')) {
            jQuery(this).parent().css("display", "block");
            jQuery(this).parents().css("display", "block");
            jQuery(this).children().css("display", "block");
            jQuery(this).parents().closest("ul").addClass("active items");
            jQuery(this).css("display", "block");
        }
    });
}





function selectActiveItem() {
    jQuery('#nav li.active').each(function() {
        if (jQuery(this).find('ul').length == 0) {
            jQuery(this).parents().css("display", "block");
            jQuery(this).children().css("display", "block");
            jQuery(this).parents().closest("ul").addClass("active items");
            jQuery(this).css("display", "block");
        }
    });
}

