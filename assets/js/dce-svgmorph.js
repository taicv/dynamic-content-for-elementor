( function( $ ) {
	
	var WidgetElements_SvgMorphHandler = function( $scope, $ ) {
		//console.log( $scope );
		//alert('svg');

		var elementSettings = get_Dyncontel_ElementSettings($scope);
		var id_scope = $scope.attr('data-id');
		//alert(elementSettings.repeater_shape_polygon);
		//console.log(elementSettings);
		
		// il tipo di forma: path | polygon | circle | ECC..
		var forma = elementSettings.type_of_shape;

		var step = 0;
		var run = $('#dce-svg-'+id_scope).attr('data-run');

		var is_running = false;
		

		// ciclo il ripetitore in base alla Forma
		var ripetitore = 'repeater_shape_'+forma;
		eval('var repeaterShape = elementSettings.'+ripetitore);
		//alert('ripetitore '+ripetitore);
		var contentElemsTotal = repeaterShape.length;
		var numberOfElements = repeaterShape.length;
		var shapes = [];
		//alert(numberOfElements.length);
		//console.log(repeaterShape);

		var delayStart = 100;
		
		//console.log(repeaterShape);
		//alert(repeaterShape.length);

		
		/*
		easingSinusoidalInOut, 
		easingQuadraticInOut, 
		easingCubicInOut,
		easingQuarticInOut, 
		easingQuinticInOut, 
		easingCircularInOut,
		easingExponentialInOut.
		*/


		var dceshape = "#forma-"+id_scope;
		var dceshape_svg = "#dce-svg-"+id_scope;

		// timelinemax
		if(tl) tl.kill($(dceshape));
		var tl = null;
		tl = new TimelineMax();


		if(tlpos) tlpos.kill($(dceshape_svg));
		var tlpos = null;
		tlpos = new TimelineMax();
		
		var dceshape_delay = elementSettings.duration_morph.size || 2,
		dceshape_speed = elementSettings.speed_morph.size || 1;

		var easing_morph_ease = elementSettings.easing_morph_ease || 'Power3',
		easing_morph = elementSettings.easing_morph || 'easeInOut';
		
		var repeat_morph = elementSettings.repeat_morph || -1;

		if(transitionTl) transitionTl.kill($(dceshape));
		var transitionTl = null;

		if(transitionTl) transitionTlpos.kill($(dceshape_svg));
		var transitionTlpos = null;


		var get_data_anim = function(){
			var duration_anim = elementSettings.duration_morph.size || 3;
			var speed_anim = elementSettings.speed_morph.size || 1;
			
			easing_morph_ease = elementSettings.easing_morph_ease;
			easing_morph = elementSettings.easing_morph;

			repeat_morph = elementSettings.repeat_morph || -1;

			dceshape_delay = duration_anim;
			dceshape_speed = speed_anim;
		}
		var get_data_shape = function(){
			shapes = [];

			var ciccio = [];
			if( elementorFrontend.isEditMode()){
				ciccio = repeaterShape.models;
				//console.log('back:');
				//console.log(ciccio);
			}else{
				ciccio = repeaterShape;
				//console.log('front:');
				//console.log(ciccio);
			}
			var old_points = '';
			$.each(ciccio, function(i, el){
				var pippo = [];
				if( elementorFrontend.isEditMode()){
					pippo = repeaterShape.models[i].attributes;
				}else{
					pippo = repeaterShape[i];
				}
				//alert(pippo);
				
				var id_shape = pippo.id_shape;
				var points = pippo.shape_numbers;
				//alert(points);
				if(points == ''){
					points = old_points;
					//alert(old_points);
				}
				old_points = points; 
				//alert('!! '+points);


				// var x_position = pippo.x_position.size;
				// var y_position = pippo.y_position.size;
				// var rotate = pippo.rotate.size;
				// var scale = pippo.scale.size;
				var fillColor = pippo.fill_color;
				var strokeColor = pippo.stroke_color;

				var strokeWidth = pippo.stroke_width.size || 0;
				
				var shapeX = pippo.shape_x.size || 0;
				var shapeY = pippo.shape_y.size || 0;

				var dceshape_delay = elementSettings.duration_morph.size || 2,
				dceshape_speed = elementSettings.speed_morph.size || 1;


				//alert(strokeWidth);
				var objRep = {
					//d: points,
					points: points,

					//scaleX: scale,
					//scaleY: scale,
					//rotate: rotate,
					//tx: x_position,
					//ty: y_position,
					path: {
						duration: pippo.duration_morph.size,
						speed: pippo.speed_morph.size,
						easing: pippo.easing_morph_ease,
						morph: pippo.easing_morph,
						elasticity: 600,
					},
					fill: {
						color: fillColor,
					},
					stroke: {
						width: strokeWidth,
						color: strokeColor
					},
					svg: {
							
							x: shapeX,
							y: shapeY,
							rotation: 0,
							elasticity: 600
						
					}
				}
				shapes.push(objRep);
				//console.log(shapes[step]);
			});
			
		}
		var getCustomData_speed = function(i){
			if( shapes[i].path.speed ){
				dceshape_speed = shapes[i].path.speed;
			}else{
				dceshape_speed = elementSettings.speed_morph.size;
			}
			console.log(dceshape_speed);
			return dceshape_speed;
		}
		var getCustomData_duration = function(i){
			if( shapes[i].path.duration ){
				dceshape_delay = shapes[i].path.duration;
			}else{
				dceshape_delay = elementSettings.duration_morph.size;
			}
			return dceshape_delay;
		}
		var getCustomData_easing = function(i){

			if( shapes[i].path.easing ){
				easing_morph_ease = shapes[i].path.easing;
			}else{
				easing_morph_ease = elementSettings.easing_morph_ease;
			}
			return easing_morph_ease;
		}
		var getCustomData_morph = function(i){
			//alert(shapes[i].path.morph);
			if( shapes[i].path.morph ){
				easing_morph = shapes[i].path.morph;
			}else{
				easing_morph = elementSettings.easing_morph;
			}
			return easing_morph;
		}
		var animateShapes = function(){
			//console.log(shapes);
			//alert('animateShapes');
    		if($("#forma-"+id_scope).length){
    			//alert(repeaterShape.length);

    			var tweenSVG = 'tlpos';
				var tweenString = 'tl';
				$.each(shapes, function(i, el){

							//if( shapes[i].path.duration ) dceshape_delay = shapes[i].path.duration;
							
						if(i > 0){
							tweenString += '.to("'+dceshape+'", '+getCustomData_speed(i)+', {onComplete: myFunction1, onCompleteParams:['+i+'], morphSVG:`'+shapes[i].points+'`, ease: '+getCustomData_easing(i)+'.'+getCustomData_morph(i)+', fill:"'+shapes[i].fill.color+'", strokeWidth:'+shapes[i].stroke.width+', stroke:"'+shapes[i].stroke.color+'"}, "+='+getCustomData_duration(i)+'")';
							tweenSVG += '.to("'+dceshape_svg+'", '+getCustomData_speed(i)+', {x:'+shapes[i].svg.x+',y:'+shapes[i].svg.y+', ease: '+getCustomData_easing(i)+'.'+getCustomData_morph(i)+'}, "+='+getCustomData_duration(i)+'")';
							//alert(shapes[i].svg.x);
						}
				});
			
				tweenString += '.to("'+dceshape+'", '+getCustomData_speed(0)+', {onComplete: myFunction1, onCompleteParams:[0], morphSVG:`'+shapes[0].points+'`, ease: '+getCustomData_easing(0)+'.'+getCustomData_morph(0)+', fill:"'+shapes[0].fill.color+'", strokeWidth:'+shapes[0].stroke.width+', stroke:"'+shapes[0].stroke.color+'"}, "+='+getCustomData_duration(0)+'")';
				tweenString += ';';

				tweenSVG += '.to("'+dceshape_svg+'", '+getCustomData_speed(0)+', {x:'+shapes[0].svg.x+',y:'+shapes[0].svg.y+', ease: '+getCustomData_easing(0)+'.'+getCustomData_morph(0)+'}, "+='+getCustomData_duration(0)+'")';
				tweenSVG += ';';
			}

			//alert(tweenSVG);
			
			//TweenLite.to("#forma-"6212a99", 1, {onComplete: myFunction1, onCompleteParams:[], morphSVG:"M275.8,159.8l93.3,159.8H184.6H0l93.3-159.8L184.6,0L275.8,159.8z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M275.8,159.8l93.3,159.8H184.6H0l93.3-159.8L184.6,0L275.8,159.8z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M394.9,171l-98.7,171H98.7L0,171L98.7,0h197.5L394.9,171z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M326,326H0l1-163L0,0h326l-1,164.7L326,326z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M326,326H0c0,0,112.7-51,113.3-163C114,51,0,0,0,0h326L212.7,164.7L326,326z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M0,0l140.6,39l214.3,71.2l-234.2,43.2L52.6,354.1L27.5,191.3L0,0z"}, "+=1").to("#forma-"6212a99", 1, {morphSVG:"M458.4,235.7L225.3,463.6L111.4,347L0,0l227.9,233.1l117.7-112.7L458.4,235.7z"}, "+=1");
			eval(tweenString);
			eval(tweenSVG);

			is_running = true;
			if( run == 'paused' && elementorFrontend.isEditMode() ){
				tl.stop();
				tlpos.stop();
				is_running = false;
			}


			// $('#dce-svg-'+id_scope).attr('data-morphid',count);	

			//console.log($('#dce-svg-'+id_scope));
			


			/*$('#dce-svg-'+id_scope).attr('data-morphid').change(function() {
				  // Check input( $( this ).val() ) for validity here
				  alert('PPPPPPP');
				});*/
			/*$("body").on('DOMSubtreeModified', $('#dce-svg-'+id_scope), function() {
			    // code here
			    
			    if(step != $('#dce-svg-'+id_scope).data('morphid')) step = $('#dce-svg-'+id_scope).data('morphid');
			    //alert($('#dce-svg-'+id_scope).data('morphid')+step);
			});*/

			

			// myAnimation.eventCallback("onComplete", myFunction, ["param1","param2"]);

			// alla fine dell'intero ciclo
			//tl.eventCallback("onRepeat", myFunction, ["param1","param2"]);

			tl.repeat(repeat_morph);
			tlpos.repeat(repeat_morph);
		} // end animateShapes
		var myFunction = function(a,b){
			// ad ogni giro

		}
		var myFunction1 = function(id_step){
			// ad ogni trasformazione
			//alert(id_step);

			$('#dce-svg-'+id_scope).attr('data-morphid',id_step);	
		}
		var myFunction2 = function(id_step){
			if(transitionTl) transitionTl.kill($(dceshape));
			if(transitionTlpos) transitionTl.kill($(dceshape_svg))
			
		}


		var updateSlider = function () {
			  //$("#slider").slider("value", tl.progress());
			}

		
		var playShapeEl = function() {
			

			//if($("#dce-svg-"+id_scope).attr('data-run') == 'paused') speed_anim = 100;
			
			
			transitionTl = new TimelineMax();
			transitionTlpos = new TimelineMax();

			function repeatOften() {


				if(run != $('#dce-svg-'+id_scope).attr('data-run')){
					get_data_anim();
					//alert($('#dce-svg-'+id_scope).attr('data-run') + ' ... ' + run);
					run = $('#dce-svg-'+id_scope).attr('data-run');
					if( run == 'running'){
						tl.play();
						tlpos.play();
						is_running = true;
					}else{
						tl.stop();
						tlpos.stop();
						is_running = false;
					}
					
				}

				if(!is_running){
					if( step != $('#dce-svg-'+id_scope).attr('data-morphid')){

						step = $('#dce-svg-'+id_scope).attr('data-morphid');
						
						get_data_shape();

						
						 
						if (typeof shapes[step] !== "undefined") {
							var tweenString = 'transitionTl.to("'+dceshape+'", '+getCustomData_speed(step)+', {onComplete: myFunction2, onCompleteParams:['+step+'], morphSVG:`'+shapes[step].points+'`, ease: '+getCustomData_easing(step)+'.'+getCustomData_morph(step)+', fill:"'+shapes[step].fill.color+'", strokeWidth:'+shapes[step].stroke.width+', stroke:"'+shapes[step].stroke.color+'"});';
							var tweenStringPos = 'transitionTlpos.to("'+dceshape_svg+'", '+getCustomData_speed(step)+', {x:'+shapes[step].svg.x+', y:'+shapes[step].svg.y+', ease: '+getCustomData_easing(step)+'.'+getCustomData_morph(step)+'});';
							
							//alert(tweenStringPos);
						    eval(tweenStringPos);
						    eval(tweenString);
						    

						}
							
						
						//alert(dceshape+' '+dceshape_speed);
					}
				}
				
				
			  // Do whatever
			  requestAnimationFrame(repeatOften);

			  
			}
			requestAnimationFrame(repeatOften);
			/*setInterval(function(){ 
				
				oldCount = count;
				count = $('#dce-svg-'+id_scope).attr('data-morphid');






				if($('#dce-svg-'+id_scope).attr('data-run') == 'running'){
					
					if(count < contentElemsTotal - 1){ 
						count ++;
					}else{
						count = 0;
					}
					//cambio l'indice del morphing nell'intervallo solo se sono in running
					$('#dce-svg-'+id_scope).attr('data-morphid',count);	
				}else{

				}
				
				//alert('count: '+count+' '+shapes[count].points);
				
			}, 100);*/
	
		}
		
		
		// in frontend rendo obbligatorio l'animazione se sono con più di un elemento
		if(!elementorFrontend.isEditMode() && contentElemsTotal > 1){ 
			$('#dce-svg-'+id_scope).attr('data-run','running');
		}
		//alert('Morph '+id_scope);
		
		setTimeout(function(){
			get_data_anim();  
			get_data_shape(); 
			/*if( !elementorFrontend.isEditMode() && $('#dce-svg-'+id_scope).attr('data-run') == 'running' ) */animateShapes(); 

		},delayStart);
	
		// Comincia L'animazione ...........
		if( elementorFrontend.isEditMode() && contentElemsTotal > 1) playShapeEl();
		
	};
	// Make sure you run this code under Elementor..
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/dyncontel-svgmorphing.default', WidgetElements_SvgMorphHandler );
		
		
	} );
} )( jQuery );
