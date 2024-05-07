/*-------------------------------------------------------------------------------------------------------------------------------*/
/*This is main JS file that contains custom style rules used in this template*/
/*-------------------------------------------------------------------------------------------------------------------------------*/
/* Template Name: "Modesto"*/
/* Version: 1.0 Initial Release*/
/* Build Date: 06-02-2016*/
/* Author: LionStyle*/
/* Website: http://moonart.net.ua/modesto/ 
/* Copyright: (C) 2016 */
/*-------------------------------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------*/
/* TABLE OF CONTENTS: */
/*--------------------------------------------------------*/
/* 01 - VARIABLES */
/* 02 - page calculations */
/* 03 - function on document ready */
/* 04 - function on page load */
/* 05 - function on page resize */
/* 06 - function on page scroll */
/* 07 - swiper sliders */
/* 08 - buttons, clicks, hovers */


$(function() {

    "use strict";

    /*================*/
    /* 01 - VARIABLES */
    /*================*/
    var swipers = [], winW, winH, winScr, footerTop, _isresponsive, _ismobile = navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i);

    /*========================*/
    /* 02 - page calculations */
    /*========================*/
    function pageCalculations(){
        winW = $(window).width();
        winH = $(window).height();
        footerTop = ($('footer').length)?$('footer').offset().top : 0;
        if($('.portfolio-detail-related-entry').length) footerTop = $('.portfolio-detail-related-entry').offset().top;
        if($('.is-mobile').is(':visible')) _isresponsive = true;
        else _isresponsive = false;
        $('.page-height').css({'height':winH, 'min-height':(winH<480)?480:winH});
        if(winH<=600) $('body').addClass('min-height');
        else $('body').removeClass('min-height');
        $('.rotate').each(function(){
            $(this).css({'width':$(this).parent().height()});
        });
    }

    /*=================================*/
    /* 03 - function on document ready */
    /*=================================*/
    setTimeout(function(){
        //loader
        $('#loader-wrapper').fadeOut();

    },2000);


    $('.input').each(function(){
        if($(this).val()!=='') $(this).parent().addClass('focus');
    });
    if(_ismobile) $('body').addClass('mobile');
    pageCalculations();

    /*============================*/
    /* 04 - function on page load */
    /*============================*/
    $(window).load(function(){

        setTimeout(function () {
            initSwiper();
        }, 1000);

        izotopInit();
        
        $('.fixed-menu .nav-fix-a').each(function(){
             var index_start = $('.fixed-menu .nav-fix-a').index(this);
             if($(this).attr('data-link') ===  window.location.hash){
                $('.fixed-menu .nav-fix-a').eq(index_start).click();
             }
        });

        if ($('.baner-bg').length){
            $('header').addClass('bg-header');
        }
        
        $('body').addClass('loaded');
        setTimeout(function(){
            setTimeout(function(){
                pageCalculations();
                scrollCall();
            },0);
        },400);

        /*lightbox*/
        if($('.lightbox').length > 0){
            $(function(){
                var lightbox = $('.lightbox').simpleLightbox({
                    disableScroll: false
                });
            });
        }


    });


    /*==============================*/
    /* 05 - function on page resize */
    /*==============================*/
    function resizeCall(){
        pageCalculations();
    }
    if(!_ismobile){
        $(window).resize(function(){
            resizeCall();
        });
    } else{
        window.addEventListener("orientationchange", function() {
            resizeCall();
        }, false);
    }

    /*==============================*/
    /* 06 - function on page scroll */
    /*==============================*/
    /*header scroll*/
    $(window).scroll(function() {
        scrollCall();
    });

    function scrollCall(){
        winScr = $(window).scrollTop();
        if (winScr>30){  
            $('header').addClass('scrolled');
            $('.popup-container.right').addClass('scrolled');
        }
        else{
            $('header').removeClass('scrolled');
            $('.popup-container.right').removeClass('scrolled');
        }
    }

    /*=====================*/
    /* 07 - swiper sliders */
    /*=====================*/

    function initSwiper(){
        var initIterator = 0;
        $('.swiper-container').each(function(){                               
            var $t = $(this);                                 

            var index = 'swiper-unique-id-'+initIterator;

            $t.addClass('swiper-'+index+' initialized').attr('id', index);
            $t.find('.swiper-pagination').addClass('swiper-pagination-'+index);

            if($t.find('.swiper-button').length>=1){
                $t.find('.swiper-button-prev').addClass('swiper-button-prev-'+index);
                $t.find('.swiper-button-next').addClass('swiper-button-next-'+index);
            }else if($t.parent().find('.swiper-button').length>=1){
                $t.parent().find('.swiper-button-prev').addClass('swiper-button-prev-'+index);
                $t.parent().find('.swiper-button-next').addClass('swiper-button-next-'+index);
            }

            if($t.find('.swiper-slide').length<=1) $('.slider-click[data-pagination-rel="'+$t.data('pagination-rel')+'"]').addClass('disabled');

            var slidesPerViewVar = ($t.data('slides-per-view'))?$t.data('slides-per-view'):1,
                loopVar = ($t.data('loop'))?parseInt($t.data('loop'), 10):0;
            if(slidesPerViewVar!='auto') slidesPerViewVar = parseInt(slidesPerViewVar, 10);

            swipers['swiper-'+index] = new Swiper('.swiper-'+index,{
                pagination: '.swiper-pagination-'+index,
                paginationClickable: true,
                nextButton: '.swiper-button-next-'+index,
                prevButton: '.swiper-button-prev-'+index,
                slidesPerView: slidesPerViewVar,
                autoHeight: ($t.data('auto-height'))?parseInt($t.data('auto-height'), 10):0,
                loop: loopVar,
                autoplay: ($t.data('autoplay'))?parseInt($t.data('autoplay'), 10):0,
                centeredSlides: ($t.data('center'))?parseInt($t.data('center'), 10):0,
                breakpoints: ($t.data('breakpoints'))? { 767: { slidesPerView: parseInt($t.attr('data-xs-slides'), 10) }, 991: { slidesPerView: parseInt($t.attr('data-sm-slides'), 10) }, 1199: { slidesPerView: parseInt($t.attr('data-md-slides'), 10) } } : {},
                initialSlide: ($t.data('ini'))?parseInt($t.data('ini'), 10):0,
                watchSlidesProgress: true,
                speed: ($t.data('speed'))?parseInt($t.data('speed'), 10):500,
                parallax: ($t.data('parallax'))?parseInt($t.data('parallax'), 10):0,
                slideToClickedSlide: true,
                keyboardControl: true,
                mousewheelControl: ($t.data('mousewheel'))?parseInt($t.data('mousewheel'), 10):0,
                mousewheelReleaseOnEdges: true,
                spaceBetween: ($t.data('space'))?parseInt($t.data('space'), 10):0,
                direction: ($t.data('direction'))?$t.data('direction'):'horizontal',
                onProgress: function(swiper, progress){
                    watchSwiperProgress($t,swiper,swiper.activeIndex);
                },
                onSlideChangeStart: function(swiper){
                    var activeIndex = (loopVar===1)?swiper.activeLoopIndex:swiper.activeIndex;
                    watchSwiperProgress($t,swiper,activeIndex);
                },
                onTransitionEnd: function(swiper){
                    var activeIndex = (loopVar===1)?swiper.activeLoopIndex:swiper.activeIndex;
                    if($('.slider-click[data-pagination-rel="'+$t.data('pagination-rel')+'"]').length){
                        var updateLeftNum = $('.slider-click.left[data-pagination-rel="'+$t.data('pagination-rel')+'"]'),
                            updateRightNum = $('.slider-click.right[data-pagination-rel="'+$t.data('pagination-rel')+'"]');
                        if(loopVar!=1){
                            if(activeIndex<1) updateLeftNum.addClass('disabled');
                            else updateLeftNum.removeClass('disabled').find('.left').text(activeIndex);
                            if(activeIndex+2>swiper.slides.length) updateRightNum.addClass('disabled');
                            else updateRightNum.removeClass('disabled').find('.left').text(activeIndex+2);
                            updateLeftNum.find('.preview-entry.active').removeClass('active');
                            updateLeftNum.find('.preview-entry[data-rel="'+(activeIndex-1)+'"]').addClass('active');
                            updateRightNum.find('.preview-entry.active').removeClass('active');
                            updateRightNum.find('.preview-entry[data-rel="'+(activeIndex+1)+'"]').addClass('active');
                        }
                    }
                }
            });
            swipers['swiper-'+index].update();
            initIterator++;
        });
        $('.swiper-container.swiper-control-top-js').each(function(){
            swipers['swiper-'+$(this).attr('id')].params.control = swipers['swiper-'+$(this).closest('.swipers-couple-wrapper').find('.swiper-control-bottom-js').attr('id')];
        });
        $('.swiper-container.swiper-control-bottom-js').each(function(){
            swipers['swiper-'+$(this).attr('id')].params.control = swipers['swiper-'+$(this).closest('.swipers-couple-wrapper').find('.swiper-control-top-js').attr('id')];
        });

    }


    function watchSwiperProgress(container, swiper, index){
        if($('.homepage-1-backgrounds[data-pagination-rel="'+container.data('pagination-rel')+'"]').length){
            $('.homepage-1-backgrounds .entry.active').removeClass('active');
            $('.homepage-1-backgrounds .entry[data-rel='+index+']').addClass('active');
        }
        if($('.slider-click-label[data-pagination-rel="'+container.data('pagination-rel')+'"]').length){
            $('.slider-click-label[data-pagination-rel="'+container.data('pagination-rel')+'"]').removeClass('active prev next');
            $('.slider-click-label[data-pagination-rel="'+container.data('pagination-rel')+'"][data-slide-to="'+index+'"]').addClass('active');
        }
        if($('.pagination-slider-wrapper[data-pagination-rel="'+container.data('pagination-rel')+'"]').length){
            var foo = $('.pagination-slider-wrapper[data-pagination-rel="'+container.data('pagination-rel')+'"]');
            foo.css({'top':(-1)*parseInt(foo.find('.active').attr('data-slide-to'), 10)*foo.parent().height()});
        }        
    }

    var slide_index = 1;
    $('.all-slides').text($('.left-right .swiper-container.my-bg-swiper .swiper-slide').length);
    $('.prev-slide').text(slide_index);
    $('.next-slide').text(slide_index+1);

    $('.slider-click.left').on('click', function(){
        if($(this)[0].hasAttribute('data-pagination-rel')){
            swipers['swiper-'+$('.swiper-container[data-pagination-rel="'+$(this).data('pagination-rel')+'"]').attr('id')].slidePrev();
            $(this).find('.preview-entry').removeClass('active');
        }
    });

    $('.slider-click.right').on('click', function(){
        if($(this)[0].hasAttribute('data-pagination-rel')){
            swipers['swiper-'+$('.swiper-container[data-pagination-rel="'+$(this).data('pagination-rel')+'"]').attr('id')].slideNext();
            $(this).find('.preview-entry').removeClass('active');
        }
    });

    $('.slider-click-label').on('click', function(){
        swipers['swiper-'+$('.swiper-container[data-pagination-rel="'+$(this).data('pagination-rel')+'"]').attr('id')].slideTo($(this).data('slide-to'));
    });




    /*==============================*/
    /* 08 - buttons, clicks, hovers */
    /*==============================*/

    //open overlay popup
    $('.open-overlay').on('click', function(){
        var $this = $(this);
        $('.overlay[data-rel="'+$(this).data('rel')+'"]').addClass('active');
        if($(this).hasClass('open-video')) setTimeout(function(){$('.overlay[data-rel="'+$this.data('rel')+'"] iframe').attr('src', $this.data('src'));}, 500);
        if(_ismobile) setTimeout(function(){$('html').addClass('overflow-hidden');}, 500);
    });

    //close overlay popup
    $('.overlay .button-close').on('click', function(){
        $(this).closest('.video-popup').find('iframe').attr('src', '');
        if($('.overlay.active').length===1) $('html').removeClass('overflow-hidden');
        $(this).closest('.overlay').removeClass('active');
    });



    //input animations on focus
    $('.input').on('focus', function(){
        $(this).parent().addClass('focus');
    });

    $('.input').on('blur', function(){
        if($(this).val()==='') $(this).parent().removeClass('focus');
    });



    /*==============================*/
    /* 09 - New */
    /*==============================*/
    var scroll_index = 0;

//portfolio menu
    var nav_index  = 0;
    $('.nav-a').on('click', function(){
        nav_index = $('.nav-a').index(this);
        $('.nav-a').removeClass('active');
        $(this).addClass('active');
        $('.my-multi-swiper').removeClass('active');
        $('.my-multi-swiper').eq(nav_index).addClass('active');
        return false;
    });

//portfolio drop-menu
     $('.drop-menu').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $('.drop-list').removeClass('active');
            $('#portfolioCaret').addClass('fa-angle-down');
            $('#portfolioCaret').addClass('fa-angle-up');
        }else{
            $(this).addClass('active');
            $('.drop-list').addClass('active');
            $('#portfolioCaret').addClass('fa-angle-up');
            $('#portfolioCaret').removeClass('fa-angle-down');
        }
        return false;
    });
    
//portfolio drop-menu: active nav-a
    $('.nav-a').on('click', function(){
        if($('.drop-menu').hasClass('active')){ //for responsive only
            $('.drop-menu').html($(this).html()+' <i id="portfolioCaret" class="fa fa-angle-up" aria-hidden="true"></i></a>');
        } 
    });



    //overlay hamburger-icon
    $('.hamburger-icon').on('click', function(){
        $('.overlay-wrapper').addClass('active');
    });
    //overlay btn-close
    $('.btn-close').on('click', function(){
        $('.overlay-wrapper').removeClass('active');
    });
    
    //overlay plus
   $('.dropdown-plus span').on('click', function(){
        $(this).toggleClass('clicked').parent().find('ul').slideToggle(300);
    });

   /*----*/
    /*SHOP*/
    /*----*/
    
    /*SumoSelect*/
    if($('.select-box').length > 0){   
        $('.select-box').SumoSelect();
    };

    /*slider-range*/
    if($('#slider-range').length > 0){  
        $( function() {
            $( "#slider-range" ).slider({
              range: true,
              min: 10,
              max: 500,
              values: [ 10, 300 ],
              slide: function( event, ui ) {
                $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
              }
            });
            $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
              " - $" + $( "#slider-range" ).slider( "values", 1 ) );
        } );
    };


    /*view-btn*/
    $('.shop .view-btn').click(function(){
        $('.view-btn').removeClass('active');
        $(this).addClass('active');
        if($('.view-btn-2').hasClass('active')){
            $('.prod-item-wrapper').addClass('view-2');
        } else $('.prod-item-wrapper').removeClass('view-2');
    });

    /*shop-category-menu*/
    $('.category-shop').on('click', function(){
        var toggle_text = $(this).attr('data-text');
        $('.category-toggle span').text(toggle_text);
        $('.category-shop').removeClass('active');
        $(this).addClass('active');
        return false;
    });

    /*category-toggle*/
    $('.category-toggle').on( 'click', function() {
        $(".category").slideToggle(300);
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        } else $(this).addClass('active');
    });

    // hamburger-icon-2
    $('.hamburger-icon-2').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        } else $(this).addClass('active');
        if($('.header-style-2').hasClass('active')){
            $('.header-style-2').removeClass('active bg-white');
        } else $('.header-style-2').addClass('active bg-white');
    });

    //open and close popup
    $('.open-popup').on( 'click', function() {
        $('.popup-content').removeClass('active');
        $('.popup-wrapper, .popup-content[data-rel="'+$(this).data('rel')+'"]').addClass('active');
        $('html').addClass('overflow-hidden');
        return false;
    });

    $('.popup-wrapper .button-close, .popup-wrapper .layer-close').on( 'click', function() {
        $('.popup-wrapper, .popup-content').removeClass('active');
        $('html').removeClass('overflow-hidden');
        setTimeout(function(){
            $('.ajax-popup').remove();
        },300);
        return false;
    });

    /*shop color*/
    $('.shop .detail-item .description .color span').on('click', function(){
        $('.shop .detail-item .description .color span').removeClass('active');
        $(this).addClass('active');
    });    

    //tabs
    var tabsFinish = 0;
    $('.tab-menu').on('click', function() {
        if($(this).hasClass('active') || tabsFinish) return false;
        tabsFinish = 1;
        var tabsWrapper = $(this).closest('.tabs-block'),
            tabsMenu = tabsWrapper.find('.tab-menu'),
            tabsItem = tabsWrapper.find('.tab-entry'),
            index = tabsMenu.index(this);
        
        tabsItem.filter(':visible').fadeOut(function(){
            tabsItem.eq(index).fadeIn(function(){
                tabsFinish = 0;
            });
        });
        tabsMenu.removeClass('active');
        $(this).addClass('active');
         setTimeout(function () {
            initSwiper();
        }, 1000);
    });

    //basket color, z-index
    $('.basket.open-popup').on( 'click', function() {
        $('.basket.open-popup').addClass('active');
        $('.popup-wrapper').addClass('z-lower');
        return false;
    });
    $('.popup-wrapper .button-close, .popup-wrapper .layer-close').on( 'click', function() {
        if($('.basket.open-popup').hasClass('active')){
            $('.basket.open-popup').removeClass('active');
        }
        if($('.popup-wrapper').hasClass('z-lower')){
            $('.popup-wrapper').removeClass('z-lower');
        }
        return false;
    });

    $('.img-preview img').on('click', function(){
        var img_src = $(this).attr('data-src');
        $('.img-main').attr('src', img_src);
    });
    
    $('.scroll-down').on('click', function(){
      var scrollOffset = $('.baner-bg').next().offset().top - $('header').height();    
       $('body, html').animate({
           scrollTop: scrollOffset
       },800);
    });

    // item quantity
    $('.quantity .fa-caret-right').on('click', function(){
        var q_plus = $(this).closest('.quantity').find('input').attr('value');
        $(this).closest('.quantity').find('input').attr('value', +q_plus+1);
    });
    $('.quantity .fa-caret-left').on('click', function(){
        var q_minus = $(this).closest('.quantity').find('input').attr('value');
        $(this).closest('.quantity').find('input').attr('value', +q_minus-1);
        if(q_minus<=1){
            $(this).closest('.quantity').find('input').attr('value', 1);
        }
    });
    
    /*==============================*/
    /* 10 - Isotope */
    /*==============================*/
    
    function izotopInit() {
	  if ($('.izotope-container').length) {
	    var $container = $('.izotope-container');
	  	  $container.isotope({
			itemSelector: '.item',
			layoutMode: 'masonry',
			masonry: {
			  columnWidth: '.grid-sizer'
			}
		  });
            $('.sorting-menu li').on('click',function(){
                if($(this).hasClass('active')) return false;
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');
                var filterValue = $(this).attr('data-filter');
                $container.isotope({ filter: filterValue });
                var sorting_menu_btn = $('.sorting-menu .button-drop a span');
                sorting_menu_btn.text($(this).data('name'));
                return false;
            });
	  }
	}
    
    $('.sorting-menu .button-drop').on('click', function(){
        var list = $('.sorting-menu ul');
            if($(this).hasClass('active')){
            $(this).removeClass('active');
            list.removeClass('active');
        }else{
            $(this).addClass('active');
            list.addClass('active');
        }
        return false;
    });
 
    
   
    
     if ($(window).width()<992){
          $('.txt-replace').on('click', function(){
                $(this).parent().find('.tabs-mobile-style').slideToggle(325);  
           });
          $('.tabs-mobile-style .tab-menu').on('click', function(){
            $('.txt-replace span').text($(this).text()); 
            $(this).parent().slideUp(325);  
         });    
     }
     

});