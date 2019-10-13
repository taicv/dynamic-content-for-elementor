( function( $ ) {
	
	// Vars  ----------------------------------------
	var settings_page = {};
    var sectionsAvailable = [];

    is_pageScroll = false;
	// ********* Scrollify
	var is_scrollify = false;

    // ********* ScrollEffects
    var is_scrollEffects = false;

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
    var is_inertiaScroll = false;
    var directionScroll = 'vertical';
	var coefSpeed_inertiaScroll = 0.05;
	var html = document.documentElement;
	var body = document.body;
	var scroller = {};



	// INIT ----------------------------------------
	var init_Scrollify = function( ) {
		//console.log( $scope );
		//alert('scrollify Handle');

		//var sezioni = '.elementor-inner > .elementor-section-wrap > .elementor-section';
		//var sezioni = '.elementor[data-elementor-type=post] > .elementor-inner > .elementor-section-wrap > .elementor-section';
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
    	$target_sections = '.elementor-'+settings_page.scroll_id_page;
    	if(!$target_sections) $target_sections = '';

    	var sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;
    	
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
		    //easing: settings_page.ease_scrollify || "easeOutExpo",
		    scrollSpeed: Number(settings_page.scrollSpeed.size) || 1100, //1100,
		    offset : Number(settings_page.offset.size) || 0, //0,
		    
		    //scrollbars:  'yes' === settings_page.scrollbars, //true,
		    
		    // standardScrollElements: "",
		    
		    setHeights: 'yes' === settings_page.setHeights, //true,
		    overflowScroll: 'yes' === settings_page.overflowScroll, //true,
		    updateHash: 'yes' === settings_page.updateHash, //true,
		    touchScroll: 'yes' === settings_page.touchScroll, //true,
		    // before:function() {},
		    // after:function() {},
		    // afterResize:function() {},
		    // afterRender:function() {}
		    before:function(i,panels) {
 		      var ref = panels[i].attr("data-id");
 		      //
		      $(".dce-scrollify-pagination .active").removeClass("active");
		      $(".dce-scrollify-pagination").find("a[href=\"#" + ref + "\"]").addClass("active");
		      //

		    },
		    afterRender:function() {
		      is_scrollify = true;
		      //
		      //alert(settings_page.enable_scrollify_nav);
		      if(settings_page.enable_scrollify_nav){
		      	  //alert('pagination');
			      var scrollify_pagination = "<ul class=\"dce-scrollify-pagination\">";
			      var activeClass = "";
			      $(sezioni).each(function(i) {
			        activeClass = "";
			        if(i===0) {
			          activeClass = "active";
			        }
			        //<span class=\"hover-text\">"+$(this).attr("data-id")+"</span>
			        scrollify_pagination += "<li><a class=\"" + activeClass + "\" href=\"#" + $(this).attr("data-id") + "\"></a></li>";
			        //scrollify_pagination += "<li><a class=\"" + activeClass + "\" href=\"#" + $(this).attr("data-id") + "\"><span class=\"hover-text\">" + $(this).attr("data-id").charAt(0).toUpperCase() + $(this).attr("data-id").slice(1) + "</span></a></li>";
			      });
			      scrollify_pagination += "</ul>";

			      $("body").append(scrollify_pagination);		      

			      //Tip: The two click events below are the same:
			      
			      $("body").on("click",".dce-scrollify-pagination a",function() {
			        $.scrollify.move($(this).attr("href"));
			      });
			      if(settings_page.enable_scrollEffects) handleScrollEffects (settings_page.enable_scrollEffects);


			  }
		    }
		  });
		$.scrollify.update();
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
        //alert(settings_page.scroll_target);
        $target_sections = settings_page.scroll_target + ' ';
        $target_sections = '.elementor-' + settings_page.scroll_id_page;
        if (!$target_sections)
            $target_sections = '';

        var sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;
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
            window.requestAnimationFrame(updateLax);
        }

        window.requestAnimationFrame(updateLax)

        //if(settings_page.enable_scrollify) handleScrollify (settings_page.enable_scrollify);
        is_scrollEffects = true;
	}
	var init_InertiaScroll = function($dir) {
		//alert($dir);
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
    	//alert('sett: ' + directionScroll);


    	// SPEED
    	if(typeof(settings_page.coefSpeed_inertiaScroll) !== 'undefined') coefSpeed_inertiaScroll = Number(settings_page.coefSpeed_inertiaScroll.size);


    	//$target_sections = settings_page.scroll_target+' ';
    	$target_sections = '.elementor-'+settings_page.scroll_id_page;
    	if(!$target_sections) $target_sections = '';

    	// Get the section widgets of frst level in content-page
		var sezioni = $target_sections + '.elementor > .elementor-inner > .elementor-section-wrap > .' + $customClass;    	
		sectionsAvailable = $(sezioni);

		// Class direction
		$($target_sections).addClass('scroll-direction-'+directionScroll);

		// configure
		sectionsAvailable.addClass('inertia-scroll');
		

		$scrollContent = settings_page.scroll_contentScroll;
		if(settings_page.scroll_target) $scrollContent = settings_page.scroll_target;
		
		scroller = {
		  viewport: document.querySelector(settings_page.scroll_viewport) || document.querySelector('#outer-wrap'),
		  target: document.querySelector($scrollContent) || document.querySelector('#wrap'),
		  ease:  coefSpeed_inertiaScroll || 0.05, // <= scroll speed ...  
		  endY: 0,
		  endX: 0,
		  y: 0,
		  x: 0,
		  resizeRequest: 1,
		  scrollRequest: 0,
		};
		//alert(coefSpeed_inertiaScroll);
		var requestId = undefined;

		TweenMax.set(scroller.target, {
		  rotation: 0.01,
		  force3D: true
		});
			
		// Viewoport 
		/*TweenMax.set(scroller.viewport, {
		  overflow: hidden;
		  position: fixed;
		  height: '100%';
		  width: '100%';
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		});*/
		TweenMax.set(scroller.viewport, {
												  overflow: 'hidden',
												  position: 'fixed',
												  height: '100%',
												  width: '100%',
												  top: 0,
												  left: 0,
												  right: 0,
												  bottom: 0,
												});
		 updateScroller();  
		 window.focus();
		 window.addEventListener("resize", onResize);
		 document.addEventListener("scroll", onScroll); 

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
        lax.removeElement();

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

            if (lax && typeof lax !== 'undefined')
                lax.updateElements();
        }
        if(sectionsAvailable.length) sectionsAvailable.removeAttr('style');
    }
    // UTIL Inertia ----------------------------------------
    function removeInertiaScroll(){
		$('body').removeClass('dce-inertiaScroll');
		if(sectionsAvailable.length) sectionsAvailable.removeClass('inertia-scroll');
		
		
		//TweenMax.kill( scroller.target );
		TweenMax.set(scroller.target, {clearProps:"all"});
		TweenMax.set(scroller.viewport, {clearProps:"all"});
		sectionsAvailable.each(function(i, el){
				  	 TweenMax.set(el, {clearProps:"all"});
				});
		scroller = {
		  endY: 0,
		  y: 0,
		  resizeRequest: 1,
		  scrollRequest: 0,
		};

		if (requestId) {
	       window.cancelAnimationFrame(requestId);
	       
	       requestId = undefined;
	    }
		window.removeEventListener("resize", onResize);
		document.removeEventListener("scroll", onScroll);

		is_inertiaScroll = false;
	}
	// EVENTS - Util InertiaScroll
	function updateScroller() {
	  
		  var resized = scroller.resizeRequest > 0;
		  
		  var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		  var h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		  var cfc = h/w;



		  // qui sto elaborando la [ Y ]  -----------------------------------------------
		  if( directionScroll == 'vertical' ) {

			  
			  if (resized) {    
			    var height = scroller.target.clientHeight;
			    body.style.height = height + "px";
			    scroller.resizeRequest = 0;
			  }

			  var scrollY = window.pageYOffset || html.scrollTop || body.scrollTop || 0;

			  scroller.endY = scrollY;
			  scroller.y += (scrollY - scroller.y) * scroller.ease;

			  if (Math.abs(scrollY - scroller.y) < 0.05 || resized) {
			    scroller.y = scrollY;
			    scroller.scrollRequest = 0;
			  }
			  // ------------------------
			  heightAadminBar = 0;
			  if ( $('body').is('.admin-bar') && !elementorFrontend.isEditMode()) {
		            heightAadminBar = 32;
		        }
			  // ------------------------
			  TweenMax.set(scroller.target, { 
			    y: -scroller.y+heightAadminBar 
			  });

		  }else if( directionScroll == 'horizontal' ){
	  		  // qui invece elaboro la [ X ] -----------------------------------------------
			  if (resized) {    
			    var width = scroller.target.clientWidth;
			    body.style.width = width + "px";
			    scroller.resizeRequest = 0;
			  }



			  
			  var scrollX = window.pageYOffset || html.scrollTop || body.scrollTop || 0;

			  scroller.endX = scrollY;
			  scroller.y += (scrollX - scroller.x) * scroller.ease;

			  if (Math.abs(scrollX - scroller.x) < 0.05 || resized) {
			    scroller.x = scrollX;
			    scroller.scrollRequest = 0;
			  }
			  // ------------------------
			  heightAadminBar = 0;
			  if ( $('body').is('.admin-bar') && !elementorFrontend.isEditMode()) {
		            heightAadminBar = 32;
		        }
			  // ------------------------
			  TweenMax.set(scroller.target, { 
			    x: -scroller.x+heightAadminBar 
			  });
		  }

		  //$('.trace').text(window.scrollY); //scroller.x
		  requestId = scroller.scrollRequest > 0 ? requestAnimationFrame(updateScroller) : null;

	}

	function onScroll() {
		  scroller.scrollRequest++;
		  if (!requestId) {
		    requestId = requestAnimationFrame(updateScroller);
		  }

	}

	function onResize() {
		  scroller.resizeRequest++;
		  if (!requestId) {
		    requestId = requestAnimationFrame(updateScroller);
		  }
	}

	// Change CallBack - - - - - - - - - - - - - - - - - - - - - - - - -
	function handlescroll_viewport ( newValue ) {
		//elementor.reloadPreview();
		settings_page = elementor.settings.page.model.attributes;
		//alert('ssssss '+newValue);
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

				
			}else{
				settings_page = elementor.settings.page.model.attributes;
				
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
	function handleScrollify_enablenavigation ( newValue ) {
		if(newValue){
			$('body').addClass('dce-scrollify').find('.dce-scrollify-pagination').show();
		}else{
			$('body').removeClass('dce-scrollify').find('.dce-scrollify-pagination').hide();
		}
	}

	// Change CallBack SCROLL-EFFECTS - - - - - - - - - - - - - - - - - - - - - - - - -
	function handleScrollEffects(newValue) {
        if (newValue) {
            // SI
            if (is_scrollEffects) {
                removeScrollEffects();

            } else {
                settings_page = elementor.settings.page.model.attributes;
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

            settings_page = elementor.settings.page.model.attributes;

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
            
        } else {
            // NO
        }
        settings_page = elementor.settings.page.model.attributes;
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
			}else{
				settings_page = elementor.settings.page.model.attributes;
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

	window.onload = function() {
		
	}
 	$( document ).on( 'ready', function() {


		if( typeof elementorFrontendConfig.settings.page !== 'undefined' ){
			settings_page = elementorFrontendConfig.settings.page;

			is_enable_dceScrolling = settings_page.enable_dceScrolling;
			is_enable_scrollify = settings_page.enable_scrollify;
			is_enable_scrollEffects = settings_page.enable_scrollEffects;
            is_enable_inertiaScroll = settings_page.enable_inertiaScroll;
			
            if (is_enable_scrollEffects && is_enable_dceScrolling) {
                    init_ScrollEffects();
                }
            if( is_enable_scrollify && is_enable_dceScrolling ){
					init_Scrollify();
				}
			
			if( is_enable_inertiaScroll && is_enable_dceScrolling ){
					init_InertiaScroll(); 
			}

			//alert(settings_page.enable_scrollEffects);
			//console.log($('.elementor').attr('data-elementor-settings'));
			//alert(elementSettings.enable_scrollEffects);
			if( settings_page ){

				

				if ( elementorFrontend.isEditMode() ){
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
					elementor.settings.page.addChangeCallback( 'touchScroll', handleScrollify_touchScroll );
					elementor.settings.page.addChangeCallback( 'enable_scrollify_nav', handleScrollify_enablenavigation );

					// ScrollEffects
					elementor.settings.page.addChangeCallback('enable_scrollEffects', handleScrollEffects);
                    elementor.settings.page.addChangeCallback('animation_effects', handleScrollEffects_animations);
                    elementor.settings.page.addChangeCallback('remove_first_scrollEffects', handleScrollEffects_removefirst);

                    // InertiaScroll
					elementor.settings.page.addChangeCallback( 'enable_inertiaScroll', handleInertiaScroll );
					elementor.settings.page.addChangeCallback( 'directionScroll', handleInertiaScroll );
					elementor.settings.page.addChangeCallback( 'scroll_target', handleInertiaScroll );
					elementor.settings.page.addChangeCallback( 'coefSpeed_inertiaScroll', handleInertiaScroll );
					

				}
			}
		}
		
		

	} );
} )( jQuery );