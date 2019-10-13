;
( function( $ ) {
	
	// Vars  ----------------------------------------
	var settings_page = {};
    var sectionsAvailable = [];
    var sezioni = '';

    is_pageScroll = false;
	// ********* Scrollify
	var is_scrollify = false,
		titleStyle = '',
		navStyle = 'default';

    // ********* ScrollEffects
    var is_scrollEffects = false;
    var currentPostId;
    var is_enable_dceScrolling,
		is_enable_scrollify,
		is_enable_scrollEffects,
    	is_enable_inertiaScroll;

    var datalax = [
        'data-lax-opacity',
        'data-lax-translate',
        'data-lax-translate-x',
        'data-lax-translate-y',
        'data-lax-scale',
        'data-lax-scale-x',
        'data-lax-scale-y',
        'data-lax-skew',
        'data-lax-skew-x',
        'data-lax-skew-y',
        'data-lax-rotate',
        'data-lax-rotate-x',
        'data-lax-rotate-y',

        'data-lax-brightness',
        'data-lax-contrast',
        'data-lax-hue-rotate',
        'data-lax-blur',
        'data-lax-invert',
        'data-lax-saturate',
        'data-lax-grayscale',

        'data-lax-bg-pos',
        'data-lax-bg-pos-x',
        'data-lax-bg-pos-y',

        'data-lax-anchor'
    ]
    // ********* InertiaScroll

    // Versione 1
    var is_inertiaScroll = false;
    var directionScroll = 'vertical';
	var coefSpeed_inertiaScroll = 0.05;
	var html = document.documentElement;
	
	var scroller = {};

	// Versione 2
	const body = document.body;
	var main = {};
	
	
	let sx = 0;
	let sy = 0;

	let dx = sx;
	let dy = sy;

	var requestId;

	// INIT ----------------------------------------
	var init_Scrollify = function( ) {
		//console.log( $scope );
		//alert('scrollify Handle');

		//sezioni = '.elementor-inner > .elementor-section-wrap > .elementor-section';
		//sezioni = '.elementor[data-elementor-type=post] > .elementor-inner > .elementor-section-wrap > .elementor-section';
		$('body').addClass('dce-scrollify dce-scrolling');
		// $("body").addClass('scrollify').append(scrollify_pagination);	

		if( settings_page.custom_class_section_sfy ){
	        $customClass = settings_page.custom_class_section_sfy;
    	}else{
    		$customClass = 'elementor-section';
    	}
    	//alert($customClass);
    	//sezioni = '.elementor-' + settings_page.scrollEffects_id_page + ' > .elementor-inner > .elementor-section-wrap > .elementor-section';
    	
    	//$target_sections = settings_page.scroll_target+' ';
    	$target_sections = '.elementor-'+currentPostId; //settings_page.scroll_id_page;
    	if(!$target_sections) $target_sections = '';

    	sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;
    	
    	// Class direction
    	$($target_sections).addClass('scroll-direction-'+settings_page.directionScroll);
    	/*if( settings_page.directionScroll == 'vertical' ){
			
		}*/

		//alert(settings_page.scrollify_id_page);
		//alert($customClass);
		//alert('count '+$(sezioni).length);
		
		// --------------------------------------------------------
		//console.log(elementor.settings.page.model.attributes);
		//alert(elementor.settings.page.model.get( 'scrollSpeed' ));
		//alert(elementor.settings.page.model.attributes.scrollSpeed.size);
		// --------------------------------------------------------
		//alert(settings_page.scrollSpeed.size);
		//$.scrollify.destroy();
		//console.log(settings_page);
		$.scrollify({
		    section : sezioni,
		    sectionName : 'id',
		    interstitialSection : settings_page.interstitialSection, //"header, footer.site-footer",
		    easing: "easeOutExpo", //settings_page.ease_scrollify || "easeOutExpo",
		    scrollSpeed: Number(settings_page.scrollSpeed.size) || 1100, //1100,
		    offset : Number(settings_page.offset.size) || 0, //0,
		    
		    scrollbars:  Boolean( settings_page.scrollBars ), //true,
		    
		    // standardScrollElements: "",
		    
		    setHeights: Boolean( settings_page.setHeights ), //true,
		    overflowScroll: Boolean( settings_page.overflowScroll ), //true,
		    updateHash: Boolean( settings_page.updateHash ), //true,
		    touchScroll: Boolean( settings_page.touchScroll ), //true,
		    // before:function() {},
		    // after:function() {},
		    // afterResize:function() {},
		    // afterRender:function() {}
		    before:function(i,panels) {
 		      var ref = panels[i].attr("data-id");
 		      
		      //
		      $(".dce-scrollify-pagination .nav__item--current").removeClass("nav__item--current");
		      $(".dce-scrollify-pagination").find("a[href=\"#" + ref + "\"]").addClass("nav__item--current");
		      

		    },
		    afterRender:function() {
		      is_scrollify = true;
		      //
		      //alert(Boolean(settings_page.enable_scrollify_nav) +' - '+ elementorFrontend.isEditMode());
		      if(settings_page.enable_scrollify_nav || elementorFrontend.isEditMode()){
		      	  //alert('pagination');
			      //
			      var scrollify_pagination = '';
			      createNavigation(settings_page.snapscroll_nav_style);

			      		      
				      
			      // al click del pallino
			      $("body").on("click",".dce-scrollify-pagination a",function() {
			        $.scrollify.move($(this).attr("href"));

			        return false;
			      });
			      
			      if(!Boolean(settings_page.enable_scrollify_nav)){
			      	handleScrollify_enablenavigation('');
			      }
			      if(Boolean(settings_page.enable_scrollEffects)){
			      	handleScrollEffects (settings_page.enable_scrollEffects);
			      }

			  }
		    }
		  });
		$.scrollify.update();
	}
	var createNavigationTitles = function($style, $reload = false){

		titleStyle = $style;

		if($reload){
	    	createNavigation(settings_page.snapscroll_nav_style);
		}
	}
	var createNavigation = function($style){
		//alert('createNavigation');
		
		navStyle = $style;

		if( $('.dce-scrollify-pagination').length > 0 ) $('.dce-scrollify-pagination').remove();

		var newPagination = '';
		var activeClass;
		
		var titleString;
		createNavigationTitles(settings_page.snapscroll_nav_title_style);
		
	    newPagination = '<ul class="dce-scrollify-pagination nav--'+$style+'">';

	    if($style == 'ayana'){
			newPagination += '<svg class="hidden"><defs><symbol id="icon-circle" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6.215"></circle></symbol></defs></svg>';
		}
		if($style == 'desta'){
			newPagination += '<svg class="hidden"><defs><symbol id="icon-triangle" viewBox="0 0 24 24"><path d="M4.5,19.8C4.5,19.8,4.5,19.8,4.5,19.8V4.2c0-0.3,0.2-0.5,0.4-0.7c0.2-0.1,0.5-0.1,0.8,0l13.5,7.8c0.2,0.1,0.4,0.4,0.4,0.7c0,0.3-0.2,0.5-0.4,0.7L5.7,20.4c-0.1,0.1-0.3,0.1-0.5,0.1C4.8,20.6,4.5,20.2,4.5,19.8z M6,5.6v12.8L17.2,12L6,5.6z"/></symbol></defs></svg>';
		}
		$(sezioni).each(function(i) {
			activeClass = '';
		    if(i===0) {
		        activeClass = "nav__item--current";
		    }

		    if(titleStyle == 'number'){
		    	var prefN = '';
		    	if(i < 9){
		    		prefN = '0';
		    	}
		    	titleString = prefN+(i+1);
		    }else if(titleStyle == 'classid'){
		    	titleString = $(this).attr("id") || 'no id';
		    	titleString = titleString.replace(/_|-|\./g, ' ');
		    }else{
		    	titleString = '';
		    }

			if($style == 'default'){		   
		    	//<span class=\"hover-text\">"+$(this).attr("data-id")+"</span>
			    newPagination += '<li><a class="' + activeClass + '" href="#' + $(this).attr("data-id") + '"></a></li>';
			    //newPagination += "<li><a class=\"" + activeClass + "\" href=\"#" + $(this).attr("data-id") + "\"><span class=\"hover-text\">" + $(this).attr("data-id").charAt(0).toUpperCase() + $(this).attr("data-id").slice(1) + "</span></a></li>";
			}else{
				$itemInner = '';
				$itemTitle = '<span class="nav__item-title">'+titleString+'</span>';
				//
				if($style == 'etefu'){
					$itemInner = '<span class="nav__item-inner"></span>';
				}else if($style == 'ayana'){
					$itemTitle = '<svg class="nav__icon"><use xlink:href="#icon-circle"></use></svg>';
				}else if($style == 'totit'){
					
					var navIcon =  settings_page.scrollify_nav_icon.value;
					if(navIcon) $itemInner = '<i class="nav__icon '+navIcon+'" aria-hidden="true"></i>';
				
				}else if($style == 'desta'){
					$itemInner = '<svg class="nav__icon"><use xlink:href="#icon-triangle"></use></svg>';
				}else if($style == 'magool' || $style == 'ayana' || $style == 'timiro'){
					$itemTitle = '';
				}
			    newPagination += '<li><a href="#'+$(this).attr("data-id")+'" class="'+ activeClass +' nav__item" aria-label="'+(i+1)+'">'+$itemInner+$itemTitle+'</a></li>';
			}
		 });	
		newPagination += "</ul>";

		$("body").append(newPagination);
	}


	var init_ScrollEffects = function( ) {
		
		$('body').addClass('dce-pageScroll dce-scrolling');

        if (settings_page.custom_class_section) {
            $customClass = settings_page.custom_class_section;
        } else {
            $customClass = 'elementor-section';
        }

        // Get the section widgets of frst level in content-page
        // settings_page.scrollEffects_id_page

        //$target_sections = settings_page.scroll_target + ' ';
        $target_sections = '.elementor-'+currentPostId; //settings_page.scroll_id_page;
        if (!$target_sections)
            $target_sections = '';

        sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;
        sectionsAvailable = $(sezioni);

        // Class direction
        $($target_sections).addClass('scroll-direction-' + settings_page.directionScroll);

        // property
        animationType = settings_page.animation_effects || ['spin']; //$scope.find('#dce_pagescroll').data('animation'),
        var animationType_string = [];

        if (animationType.length)
            animationType_string = animationType.join(' ');
        // configure
        var xx = 0;
        if(settings_page.remove_first_scrollEffects) xx = 1;
       	sectionsAvailable.each(function(){
        	if($(this).index() >= xx ) sectionsAvailable.addClass('lax');
        })
        //sectionsAvailable.addClass('lax');
        setStyleEffects(animationType_string);

        // -------------------------------
        //alert(sectionsAvailable.length);

        lax.addPreset("scaleDown", function () {
            return {

                //"data-lax-scale": "-vh 0, 0 1, vh 1.5",
                // (document.body.scrollHeight)

                "data-lax-scale": "0 1, vh 0",
                //"data-lax-translate-y": "0 0, vh 200",
            }
        });
        lax.addPreset("zoomInOut", function () {
            return {

                //"data-lax-scale": "-vh 0, 0 1, vh 1.5",
                "data-lax-scale": "-vh 0, 0 1, vh 0",
                //"data-lax-translate-y": "0 0, vh vh*0.2",
            }
        });

        lax.addPreset("leftToRight", function () {
            return {

                //"data-lax-scale": "-vh 0, 0 1, vh 1.5",
                "data-lax-translate-x": "-vh -vw,0 0, 0 1, vh vw",
            }
        });
        lax.addPreset("rightToLeft", function () {
            return {

                //"data-lax-scale": "-vh 0, 0 1, vh 1.5",
                "data-lax-translate-x": "-vh vw,0 0, 0 1, vh -vw",
            }
        });
        lax.addPreset("opacity", function () {
            return {
                "data-lax-opacity": "-vh 0, 0 1, vh 0",

            }
        });
        lax.addPreset("fixed", function () {
            return {
                "data-lax-translate-y": "-vh elh, 0 0",

            }
        });
        lax.addPreset("parallax", function () {
            return {
                "data-lax-translate-y": "0 0, elh elh",

            }
        });
        lax.addPreset("rotation", function () {
            return {
                "data-lax-rotate": "0 0, vh -30",

            }
        });
        lax.addPreset("spin", function () {
            return {
                "data-lax-rotate": "-vh 180, 0 0, vh -180",

            }
        });
        lax.setup() // init Laxxx
        const updateLax = () => {
            if (lax && typeof lax !== 'undefined')
                lax.update(window.scrollY)
            requestId = window.requestAnimationFrame(updateLax);
        }

        requestId = window.requestAnimationFrame(updateLax)

        //if(settings_page.enable_scrollify) handleScrollify (settings_page.enable_scrollify);
        is_scrollEffects = true;
	}
	var init_InertiaScroll = function($dir) {
		//alert($dir);
		main = document.querySelector(settings_page.scroll_viewport) || document.querySelector('#outer-wrap');

		$('body').addClass('dce-inertiaScroll dce-scrolling');
		//$('body').prepend('<div class="trace"></div>');
		
		if( settings_page.custom_class_section ){
    		$customClass = settings_page.custom_class_section;
    	}else{
    		$customClass = 'elementor-section';
    	}


    	// DIRECTIONS
    	if(typeof(settings_page.directionScroll) !== 'undefined') directionScroll = settings_page.directionScroll || $dir;
    	if( typeof($dir) !== 'undefined' && ($dir == 'horizontal' || $dir == 'vertical')) directionScroll = $dir;

    	// SPEED
    	if(typeof(settings_page.coefSpeed_inertiaScroll.size) !== 'undefined') coefSpeed_inertiaScroll = Number(settings_page.coefSpeed_inertiaScroll.size);

    	//$target_sections = settings_page.scroll_target+' ';
    	$target_sections = '.elementor-'+currentPostId; //settings_page.scroll_id_page;
    	if(!$target_sections) $target_sections = '';

    	// Get the section widgets of frst level in content-page
		sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;    	
		sectionsAvailable = $(sezioni);

		// Class direction
		$($target_sections).addClass('scroll-direction-'+directionScroll);

		// configure
		sectionsAvailable.addClass('inertia-scroll');

		$scrollContent = settings_page.scroll_contentScroll;
		//if(settings_page.scroll_target) $scrollContent = settings_page.scroll_target;
		
		// ************		
		
		heightAadminBar = 0;
		if ( $('body').is('.admin-bar') && !elementorFrontend.isEditMode()) {
	          heightAadminBar = 32;
	    }
		// ------------------------
		var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		var h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		var cfc = h/w;
		
		
		if( directionScroll == 'vertical' ) {
			body.style.height = (main.clientHeight-heightAadminBar) + 'px';
			main.style.width = '100%';
		}else if( directionScroll == 'horizontal' ){
	  		var totalWidth = 0;
		 	var completeWidth = 0;
		 	var count = 0;

			sectionsAvailable.each(function(i, el){
			  	//alert($(el).width());
			  	completeWidth += $(el).width();

			  	if( count > 0 ) totalWidth += $(el).width();
			  	
			  	$(el).css({'position':'absolute','width':'100%','height':'100vh','left':(i*100)+'vw'});
			  	$(el).css({'float':'left','width':(100/sectionsAvailable.length)+'%','height':'100vh'});
			  	 
			  	count++;
			});

			main.style.width = completeWidth;
			
			//totalWidth += h;
			
		    body.style.height = totalWidth + "px";
		  }

		main.style.position = 'fixed';
		
		main.style.top = 0;
		main.style.left = 0;

		window.addEventListener("resize", onResize);
		document.addEventListener("scroll", onScroll); 
		//requestAnimationFrame(render);

		requestId = window.requestAnimationFrame(render);
		//
		is_inertiaScroll = true;
	}



	function reloadScrolling(){
		if(settings_page.enable_dceScrolling){
       		handlescroll_viewport ('');
       		handlescroll_viewport ('yes');
		}
	}
	// UTIL Srollify ----------------------------------------


	// UTIL ScrollEffects ----------------------------------------
    function removeScrollEffects() {
        //$('.elementor-'+settings_page.scrollEffects_id_page).removeClass('dce-pageScroll-element');
        $('body').removeClass('dce-pageScroll');
        if (sectionsAvailable.length)
            sectionsAvailable.removeClass('lax');
        clearStyleEffects();
        //updateLax = null;
        if (lax && typeof lax !== 'undefined')
        lax.removeElement();

    	window.cancelAnimationFrame(requestId);
        is_scrollEffects = false;
    }
	function setStyleEffects(effect) {
        if (effect){
        	var xx = 0;
            if(settings_page.remove_first_scrollEffects) xx = 1;
           	sectionsAvailable.each(function(){
            	if($(this).index() >= xx ) $(this).attr('data-lax-preset', effect);
            })
            	

        }
    }
    function clearStyleEffects() {
        //alert(sectionsAvailable.length);
        for (var i = 0; i < datalax.length; i++) {
            if (sectionsAvailable.length)
                sectionsAvailable.removeAttr(datalax[i]);  
        }
        if (lax && typeof lax !== 'undefined')
                lax.updateElements();

        if(sectionsAvailable.length) sectionsAvailable.removeAttr('style');
    }
    // UTIL Inertia ----------------------------------------
    function removeInertiaScroll(){
		$('body').removeClass('dce-inertiaScroll');
		if(sectionsAvailable.length) sectionsAvailable.removeClass('inertia-scroll');
		
		
		//TweenMax.kill( scroller.target );
		/*TweenMax.set(scroller.target, {clearProps:"all"});
		TweenMax.set(scroller.viewport, {clearProps:"all"});
		sectionsAvailable.each(function(i, el){
				  	 TweenMax.set(el, {clearProps:"all"});
				});
		scroller = {
		  endY: 0,
		  y: 0,
		  resizeRequest: 1,
		  scrollRequest: 0,
		};*/
		
		sectionsAvailable.each(function(i, el){
				  	 $(el).removeAttr('style');
				});

		if (requestId) {
	       window.cancelAnimationFrame(requestId);
	       
	       requestId = undefined;
	    }
		window.removeEventListener("resize", onResize);
		document.removeEventListener("scroll", onScroll);

		is_inertiaScroll = false;
		
		$(main).removeAttr('style').css('transform','translate(0,0)');

	}
	// EVENTS - Util InertiaScroll
	function onScroll() {
	  // We only update the scroll position variables
	  sx = window.pageXOffset;
	  sy = window.pageYOffset;
	}
	function onResize() {
		body.style.height = main.clientHeight + 'px';
		  if (!requestId) {
		    requestId = requestAnimationFrame(render);
		  }
	}

	function render() {

	  dx = lerp(dx, sx, coefSpeed_inertiaScroll);
	  dy = lerp(dy, sy, coefSpeed_inertiaScroll);
	  
	  dx = Math.floor(dx * 100) / 100;
	  dy = Math.floor(dy * 100) / 100;
	  
	  // Finally we translate our container to its new positions.
	  // Don't forget to add a minus sign because the container need to move in 
	  // the opposite direction of the window scroll.
	  main.style.transform = `translate(-${dx}px, -${dy}px)`;
	  
	  // And we loop again.
	  requestId = window.requestAnimationFrame(render);
	}

	// This is our Linear Interpolation method.
	function lerp(a, b, n) {
	  return (1 - n) * a + n * b;
	}

	// Change CallBack - - - - - - - - - - - - - - - - - - - - - - - - -
	function handlescroll_viewport ( newValue ) {

		if(newValue){
			// SI
			is_pageScroll = true;

		}else{
			// NO	
			is_pageScroll = false;
		}
		//alert(settings_page.enable_scrollEffects);
		if(settings_page.enable_scrollEffects) handleScrollEffects(newValue);
		if(settings_page.enable_scrollify) handleScrollify (newValue);
		if(settings_page.enable_inertiaScroll) handleInertiaScroll (newValue);
	}

	// Change CallBack SCROLLIFY - - - - - - - - - - - - - - - - - - - - - - - - -
	function handleScrollify ( newValue ) {
		
		//elementor.reloadPreview();

		if(newValue){
			// SI;
			if(is_scrollify){
				$.scrollify.enable();
			}

			init_Scrollify();
			handleScrollify_enablenavigation(settings_page.enable_scrollify_nav);
		}else{
			// NO
			//$.scrollify.isDisabled();
			$.scrollify.destroy();
			if(sectionsAvailable.length){
				sectionsAvailable.removeAttr('style');
			}
			handleScrollify_enablenavigation('');
			
			is_scrollify = false;
		}
	}
	function handleScrollify_speed ( newValue ) {
		$.scrollify.setOptions({scrollSpeed: newValue.size});
	}
	function handleScrollify_interstitialSection ( newValue ) {
		$.scrollify.setOptions({scrollSpeed: newValue});
	}
	function handleScrollify_offset ( newValue ) {
		$.scrollify.setOptions({offset: newValue.size});
	}
	function handleScrollify_ease ( newValue ) {
		$.scrollify.setOptions({easing: newValue});
	}
	function handleScrollify_setHeights ( newValue ) {
		//alert(newValue ? true : false);
		$.scrollify.setOptions({setHeights: newValue ? true : false });
	}
	function handleScrollify_overflowScroll ( newValue ) {
		$.scrollify.setOptions({overflowScroll: newValue ? true : false });
	}
	function handleScrollify_updateHash ( newValue ) {
		$.scrollify.setOptions({updateHash: newValue ? true : false });
	}
	function handleScrollify_touchScroll ( newValue ) {
		$.scrollify.setOptions({touchScroll: newValue ? true : false });
	}
	function handleScrollify_scrollBars ( newValue ) {
		$.scrollify.setOptions({scrollbars: newValue ? true : false });
	}
	function handleScrollify_enablenavigation ( newValue ) {
		if(newValue){
			$('body').addClass('dce-scrollify').find('.dce-scrollify-pagination').show();
		}else{
			$('body').removeClass('dce-scrollify').find('.dce-scrollify-pagination').hide();
		}
	}
	function handleScrollify_navstyle( newValue ) {
		if(newValue){
			createNavigation(newValue);
		}
	}
	function handleScrollify_titlestyle( newValue ) {
		if(newValue){
			createNavigationTitles(newValue,true);
		}
	}
	// Change CallBack SCROLL-EFFECTS - - - - - - - - - - - - - - - - - - - - - - - - -
	function handleScrollEffects(newValue) {
        if (newValue) {
            // SI
            if (is_scrollEffects) {
                removeScrollEffects();

            }
            setTimeout(function () {
                init_ScrollEffects();
            }, 500);
        } else {
            // NO
            removeScrollEffects();

        }
       
    }
    function handleScrollEffects_animations(newValue) {
        //clearStyleEffects();
        var animationType_string = newValue.join(' ');
        if (newValue.length) {

            removeScrollEffects();

            init_ScrollEffects();
            
            setStyleEffects(animationType_string);
            lax.updateElements();
        }
        lax.updateElements();

       	reloadScrolling();
        //if(settings_page.enable_scrollEffects) handleScrollify (settings_page.scollEffects);
    	//if(settings_page.enable_scrollify) handleScrollify (settings_page.enable_scrollify);
    }
    function handleScrollEffects_removefirst( newValue){
    	if (newValue) {
            // SI
            
        }
        removeScrollEffects();
        init_ScrollEffects();
    }

	// Change CallBack INERTIA-SCROLL - - - - - - - - - - - - - - - - - - - - - - - - -
	function handleInertiaScroll ( newValue ) {

		//elementor.reloadPreview();

		if(newValue){
			// SI
			if(is_inertiaScroll){
				removeInertiaScroll();
			}

			setTimeout(function(){
				if( settings_page.enable_inertiaScroll ) init_InertiaScroll(newValue);	
			},100);
		}else{
			// NO
			removeInertiaScroll();
			
		}
	}


	$( window ).on( 'elementor/frontend/init', function() {
		

	} );

 	$( document ).on( 'ready', function() {


		if( typeof elementorFrontendConfig.settings.page !== 'undefined' ){
			settings_page = elementorFrontendConfig.settings.page;
			currentPostId = elementorFrontendConfig.post.id;

			is_enable_dceScrolling = settings_page.enable_dceScrolling;
			is_enable_scrollify = settings_page.enable_scrollify;
			is_enable_scrollEffects = settings_page.enable_scrollEffects;
            is_enable_inertiaScroll = settings_page.enable_inertiaScroll;
			
			var responsive_scrollEffects = settings_page.responsive_scrollEffects;
			var responsive_snapScroll = settings_page.responsive_snapScroll;
			var responsive_inertiaScroll = settings_page.responsive_inertiaScroll;

			var deviceMode = $('body').attr('data-elementor-device-mode');

            if (is_enable_scrollEffects && is_enable_dceScrolling && $.inArray(deviceMode,responsive_scrollEffects) >= 0) {
                    init_ScrollEffects();
                }
            if( is_enable_scrollify && is_enable_dceScrolling && $.inArray(deviceMode,responsive_snapScroll) >= 0){
					init_Scrollify();
				}
			
			if( is_enable_inertiaScroll && is_enable_dceScrolling && $.inArray(deviceMode,responsive_inertiaScroll) >= 0){
					init_InertiaScroll(); 
			}


			if ( elementorFrontend.isEditMode() ){
				settings_page = elementor.settings.page.model.attributes;

				/*elementor.once( 'preview:loaded', function() {
					// questo è il callBack di fine loading della preview

				} );*/
				elementor.settings.page.addChangeCallback( 'enable_dceScrolling', handlescroll_viewport );
				
				// Scrollfy
				elementor.settings.page.addChangeCallback( 'enable_scrollify', handleScrollify );
				elementor.settings.page.addChangeCallback( 'scrollSpeed', handleScrollify_speed );
				elementor.settings.page.addChangeCallback( 'offset', handleScrollify_offset );
				elementor.settings.page.addChangeCallback( 'ease_scrollify', handleScrollify_ease );
				elementor.settings.page.addChangeCallback( 'setHeights', handleScrollify_setHeights );
				elementor.settings.page.addChangeCallback( 'overflowScroll', handleScrollify_overflowScroll );
				elementor.settings.page.addChangeCallback( 'updateHash', handleScrollify_updateHash );
				elementor.settings.page.addChangeCallback( 'scrollBars', handleScrollify_scrollBars );
				elementor.settings.page.addChangeCallback( 'touchScroll', handleScrollify_touchScroll );
				elementor.settings.page.addChangeCallback( 'enable_scrollify_nav', handleScrollify_enablenavigation );
				elementor.settings.page.addChangeCallback( 'snapscroll_nav_style', handleScrollify_navstyle );
				elementor.settings.page.addChangeCallback( 'snapscroll_nav_title_style', handleScrollify_titlestyle );
				
				// ScrollEffects
				elementor.settings.page.addChangeCallback('enable_scrollEffects', handleScrollEffects);
                elementor.settings.page.addChangeCallback('animation_effects', handleScrollEffects_animations);
                elementor.settings.page.addChangeCallback('remove_first_scrollEffects', handleScrollEffects_removefirst);

                // InertiaScroll
				elementor.settings.page.addChangeCallback( 'enable_inertiaScroll', handleInertiaScroll );
				elementor.settings.page.addChangeCallback( 'directionScroll', handleInertiaScroll );
				//elementor.settings.page.addChangeCallback( 'scroll_target', handleInertiaScroll );
				elementor.settings.page.addChangeCallback( 'coefSpeed_inertiaScroll', handleInertiaScroll );
				

			}
			
		}
		
		

	} );
} )( jQuery );