/* 
 * DCE EDITOR
 * dynamic.ooo
 */

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
function getUrlParam(parameter, defaultvalue){
    var urlparameter = defaultvalue;
    if(window.location.href.indexOf(parameter) > -1){
        urlparameter = getUrlVars()[parameter];
    }
    return urlparameter;
}

function dce_disable_save_button() {
    // enable save buttons
    jQuery('#elementor-panel-saver-button-publish, #elementor-panel-saver-button-save-options, #elementor-panel-saver-menu-save-draft').addClass('elementor-saver-disabled').prop('disabled', true);
    return true;
}

function dce_enable_save_button() {
    // enable save buttons
    //console.log('enable save button');
    jQuery('#elementor-panel-saver-button-publish, #elementor-panel-saver-button-save-options, #elementor-panel-saver-menu-save-draft').removeClass('elementor-saver-disabled').removeClass('elementor-disabled').prop('disabled', false).removeProp('disabled');
    return true;
}

function dce_get_setting_name(einput) {
    if (einput.hasClass('elementor-input')) {
        if (einput.data('setting') == 'url') {
            //console.log(einput.closest('.elementor-control').attr('class'));
            var settingName = '';
            jQuery.each(einput.closest('.elementor-control').attr('class').split(' '), function( index, element ){
                //console.log(index);
                //console.log(element);
                if (index == 1) {
                    settingName =  element.replace('elementor-control-', '');
                    return false;
                }
            });
            //console.log(settingName);
            if (settingName) {
                return settingName;
            }
        }
    }
    return einput.data('setting');
}

/*
function dce_popup_toggle(cid, navigator) {
    var settings = elementorFrontend.config.elements.data[cid].attributes;
    if (change_data) {
        if (settings['show_popup_editor']) {
            elementorFrontend.config.elements.data[cid].attributes['show_popup_editor'] = '';
        } else {
            elementorFrontend.config.elements.data[cid].attributes['show_popup_editor'] ='yes';
        }
    }
    //dce_menu_list_item_toggle(cid);
    if (navigator) {
        elementor_navigator_element_toggle(cid);
    }
    var eid = dce_get_element_id_from_cid(cid);
    return true;
}
*/

/******************************************************************************/

// RAW PHP
jQuery(window).load(function() {
    var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
    //console.log(elementor);
    if (jQuery('#elementor-preview-iframe').length) {
        setInterval(function(){
            if (iFrameDOM.find("div.elementor-widget-dce-rawphp").length) { 
                if (iFrameDOM.find("div.elementor-widget-dce-rawphp.elementor-loading").length) { 
                    //&& iFrameDOM.find("div[data-id=<?php echo $this->get_id(); ?>]").hasClass('elementor-loading')) {
                    dce_disable_save_button();
                    jQuery('#elementor-panel-saver-button-publish').addClass('elementor-saver-disabled-dce');
                    jQuery('.dce-notice-phpraw').slideDown();
                    //console.log('errore');
                } else {
                    if (jQuery('#elementor-panel-saver-button-publish').hasClass('elementor-saver-disabled-dce')) {
                        dce_enable_save_button();
                        jQuery('#elementor-panel-saver-button-publish').removeClass('elementor-saver-disabled-dce');
                    }
                    jQuery('.dce-notice-phpraw').slideUp();
                }
            }
            //console.log('controllato php_raw');
        }, 1000);
    }
});

jQuery(document).ready(function() {

    jQuery(document).on('mousedown','.elementor-control-repeater_shape_path .elementor-repeater-fields, .elementor-control-repeater_shape_polyline .elementor-repeater-fields',function(){
        var repeater_index = $(this).index();
        //alert('shape'+repeater_index);
        // ------------
        var eid = dce_get_element_id_from_cid(dce_model_cid);
        var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
        var morphed = iFrameDOM.find('.elementor-element[data-id='+eid+'] svg.dce-svg-morph');
        // ------------
        //morphed.trigger('changeDataMorph',[repeater_index]);
        if(morphed.attr('data-run') == 'paused') morphed.attr('data-morphid',repeater_index);
        //scambiaSVGmorphing();
        //morphed.data('changeDataMorph')();
        //morphed.data("morphid", repeater_index).trigger('changeDataMorph');

        //alert(morphed.attr('class')+repeater_index);
        //alert(eid);
        //alert( $(this).index() );
    });
    jQuery(document).on('change','.elementor-control-playpause_control',function(){
            var runAnimation = elementorFrontend.config.elements.data[dce_model_cid].attributes['playpause_control'];

            // ------------
            var eid = dce_get_element_id_from_cid(dce_model_cid);
            var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
            var morphed = iFrameDOM.find('.elementor-element[data-id='+eid+'] #dce-svg-'+eid);
            // ------------
            morphed.attr('data-run',runAnimation);

            //morphed.data("run", runAnimation).trigger('changeData'); 
            //alert(morphed.attr('class')+repeater_index);
            //alert(eid);
            //alert( runAnimation );
        });
});

/*
// DYNAMIC TAGS with TOKENS support
jQuery(document).ready(function() {
    //var global = __webpack_require__(5);
    //var $JSON = global.JSON;
    //var _stringify = $JSON && $JSON.stringify;

    jQuery(document).on('change','textarea.elementor-control-tag-area, .elementor-control-tag-area[type="text"], .elementor-control-tag-area.elementor-input',function(){
        var cid = dce_model_cid;
        var eid = dce_get_element_id_from_cid(cid);
        var tagValue = jQuery(this).val();
        var tokens = tagValue.match(/\[(.*?)\]/);
        if (tokens) {
            var settingKey = dce_get_setting_name(jQuery(this));
            //var settingValue = '[dce-token value="' + tokens.shift() + '"]';
            var tokenValue = tokens.shift();
            tokenValue = eid;
            tagSettings  = { 'eid': eid, 'cid': cid, 'dynamic': 'ooo'},
            tagSettings['name'] = settingKey;
            tagSettings['sub'] = jQuery(this).data('setting');
            //tagSettings['value'] = tagValue;
            //console.log(tagSettings);
            tagSettings = encodeURIComponent(JSON.stringify(tagSettings) || {});
            var settingValue = '[elementor-tag id="' + eid + '" name="post-id" settings="'+ tagSettings +'"]';
            
            var dynamicSettings = {};        
            if (elementorFrontend.config.elements.data[cid].attributes.__dynamic__) {    
               dynamicSettings = elementorFrontend.config.elements.data[cid].attributes.__dynamic__;
            }
            var newSetting = {
                'dynamic': 'ooo',
                //settingKey: { settingValue },
            };
            newSetting[settingKey] = settingValue;
            jQuery.extend(dynamicSettings, newSetting);
            elementorFrontend.config.elements.data[cid].attributes.__dynamic__ = dynamicSettings;
            
            var dynamicActive = {};        
            if (elementorFrontend.config.elements.data[cid].attributes.dynamic) {    
               dynamicActive = elementorFrontend.config.elements.data[cid].attributes.dynamic;
            }
            newActive = { 'active': true };
            jQuery.extend(dynamicActive, newActive);
            elementorFrontend.config.elements.data[cid].attributes.dynamic = dynamicActive;
            //elementor.reloadPreview();
        }
        console.log(elementorFrontend.config.elements.data[cid].attributes);
    });

});
jQuery(window).on( 'load', function() { 
    setInterval(function(){
        jQuery('.elementor-control-dynamic.elementor-control-dynamic-value').each(function(){
            var tagInput = jQuery(this).find('.elementor-control-tag-area').first();
            var settingKey = tagInput.data('setting');
            //var cid = tagInput.attr('id').split('-').pop();
            var cid = dce_model_cid;
            //console.log(cid);
            if (elementorFrontend.config.elements.data[cid].attributes.__dynamic__) {
                if (elementorFrontend.config.elements.data[cid].attributes.__dynamic__.dynamic) {
                    if (elementorFrontend.config.elements.data[cid].attributes.__dynamic__.dynamic = 'ooo') {
                        //console.log(settingKey);
                        tagInput.show();
                        jQuery(this).find('.elementor-dynamic-cover').hide();
                    }
                }
            }
        });
    }, 1000);
});
*/

// FILEBROWSER
jQuery(window).on( 'load', function() {     
    jQuery(document).on("click", ".elementor-control-medias .remove_media", function() { 
        //alert("add3"); 
        var editorId = jQuery(this).data('editor');
        tinyMCE.editors[editorId].setContent('');
    });
    setInterval(function(){
        // add navigator element toggle
        jQuery(".elementor-control-medias .add_media").not('.has-remove-media').each(function(){
            jQuery(this).after('<button type="button" id="remove-media-button" class="elementor-button elementor-button-warning button remove_media" data-editor="'+jQuery(this).data('editor')+'"><span class="wp-media-buttons-icon dashicons dashicons-no-alt"></span> <small>Remove Media</small></button>');
            jQuery(this).addClass('has-remove-media');
        });
    }, 1000);
});

/******************************************************************************/


// SELECT2 everywhere
jQuery(window).on( 'load', function() {     
    elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
        jQuery('.elementor-control-type-select select').select2();
    } );
    elementor.hooks.addAction( 'panel/open_editor/section', function( panel, model, view ) {
        jQuery('.elementor-control-type-select select').select2();
    } );
    elementor.hooks.addAction( 'panel/open_editor/column', function( panel, model, view ) {
        jQuery('.elementor-control-type-select select').select2();
    } ); 
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
        jQuery('.elementor-control-type-select select').select2();
    } );
    
    setInterval(function(){
        // add navigator element toggle
        jQuery('.elementor-control-type-select select').not('.select2-hidden-accessible').each(function(){
            jQuery(this).select2();
        });
    }, 1000);
});