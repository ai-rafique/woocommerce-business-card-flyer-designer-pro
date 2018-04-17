var canvasObj;
var prev_canvobj = 0;
var canvDataobj = {};
var cartItemobj = {};
var logoAry = [];
var bgAry = [];
var svgObj = {};
var src = '';
var count = 1;
var undoCount = 0;
var undoFrontData = {};
var undoBackData = {};
var frontObjCount = 1;
var backObjCount = 1;
var copyFrontControlHtml = '';
var copyBackControlHtml  = '';

jQuery(document).ready(function()
{
	if(jQuery('ul.wc-item-meta').length>0){
		jQuery('ul.wc-item-meta li').each(function(){
			if(jQuery(this).find('.wc-item-meta-label').html() == 'design:' || jQuery(this).find('.wc-item-meta-label').html() == 'wbfd_custom_price:'){
				jQuery(this).css('display', 'none');
			}
		});
	}
	
	if(jQuery('#wbfd_card_designer_pro').length>0){
		jQuery('aside.widget-area').remove();
	}
	
  if(jQuery('#bg_image').length>0)
  {
    canvasObj = new fabric.Canvas('bg_image');
    canvasObj.on( 'selection:cleared', wbfd_clearPanel );
    canvasObj.on( 'object:selected', wbfd_viewObject );
    canvasObj.on( 'object:moving', wbfd_objectMoving );
    canvasObj.on( 'object:scaling', wbfd_objectScaling );
    canvasObj.on( 'object:rotating', wbfd_objectRotating );

    if(jQuery('#jsonDataForFront').val() && jQuery('#jsonDataForFront').val() != '' )
    {
      var htmlObj = '';
      canvasObj.loadFromJSON(jQuery('#jsonDataForFront').val(), function(){
        
        jQuery('.wbfd_text_area').find('.front_side').each(function()
        {
          htmlObj = jQuery(this).clone();
          jQuery(this).remove();
        });

        canvasObj.forEachObject(function(obj)
        {
          if(obj.type == 'text')
          {
            if(jQuery('.wbfd_text_area').find('.front_side').length == 0)
            {
              htmlObj.appendTo('.wbfd_text_area');
            }
            else if(jQuery('.wbfd_text_area').find('.front_side').length>0)
            {
              var newObj = jQuery('.wbfd_text_area').find('.front_side:last').clone();
              jQuery('.wbfd_text_area').find('.front_side:last').after(newObj);
            }

            jQuery('.wbfd_text_area').find('.front_side:last').attr('data-id', obj.id);
            jQuery('.wbfd_text_area').find('.front_side:last .dynamic_text').attr('id', 'custom_text_' + obj.id);
            jQuery('.wbfd_text_area').find('.front_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + obj.id);
            jQuery('#custom_text_' + obj.id).val( obj.getText() );

            dynamic_text();
            add_dynamic_text();
            change_text_size();
	    change_text_lockSystem();
            change_text_align();
            change_text_color();
            change_text_font(obj.id);
            change_text_style();
            
            obj.bringToFront();
           
          }
          
          if(obj.type == 'image' && obj.itemName && (obj.itemName == 'art'))
          {
            obj.bringToFront();
          }
        });
        canvasObj.renderAll();
				
				if(jQuery('.wbfd_text_remove_icon').length>0)
				{	
					remove_dynamic_text();
				}
      });
    }
    else
    {
      if( jQuery('#front_bg', window.parent.document).val() )
      {
        wbfd_load_bg( jQuery('#front_bg', window.parent.document).val() );
      }
    }
  }
  
  if(jQuery('.wbfd_front_bg').length || jQuery('.wbfd_back_bg').length )
  {
    if(jQuery('#jsonDataForFront').val() && jQuery('#jsonDataForFront').val() != '' )
    {
      var parseFront = JSON.parse( jQuery('#jsonDataForFront').val() );
      
      if(parseFront.objects.length>0)
      {
        for(var count1 = 0; count1< parseFront.objects.length; count1++)
        {
          if(parseFront.objects[count1].itemName == 'front')
          {
            jQuery('.wbfd_front_bg img').attr('src', parseFront.objects[count1].src);
          }
        }
      }
    }
    else
    {
      if(jQuery('#front_bg', window.parent.document).val())
      {
        jQuery('.wbfd_front_bg img').attr('src', jQuery('#front_bg', window.parent.document).val());
      }
    }
    prev_canvobj = jQuery('.wbfd_front_bg').data('id');
    
    if(jQuery('#jsonDataForBack').val() && jQuery('#jsonDataForBack').val() != '' )
    {
      var parseBack = JSON.parse( jQuery('#jsonDataForBack').val() );
      
      if(parseBack.objects.length>0)
      {
        for(var count2 = 0; count2< parseBack.objects.length; count2++)
        {
          if(parseBack.objects[count2].itemName == 'back')
          {
            jQuery('.wbfd_back_bg img').attr('src', parseBack.objects[count2].src);
          }
        }
      }
    }
    else
    {
      if(jQuery('#back_bg', window.parent.document).val())
      {
        jQuery('.wbfd_back_bg img').attr('src', jQuery('#back_bg', window.parent.document).val());
      }
    }

    //jQuery('#bg_back_type').prop('disabled', true);
    jQuery('.wbfd_front_bg,.wbfd_back_bg').click(function()
    {
      var product_id = jQuery(this).data('id');
      canvasObj.deactivateAll().renderAll();
      
      if(jQuery(this).hasClass('wbfd_front_bg'))
      {
        jQuery('#bg_track').val('front');
      }
      else if( jQuery(this).hasClass('wbfd_back_bg') )
      {
        jQuery('#bg_track').val('back');
      }
      
      if(Object.keys(canvDataobj).length == 0 && jQuery('#jsonDataForBack').val() != '' && jQuery('#jsonDataForBack').val())
      {
        if(jQuery('#admin_track').length>0 && jQuery('#admin_track').val() == 'fromAdmin')
        {
          canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']))};
        }
        else if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd')
        {
          canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls'])), screenshot:canvasObj.toDataURL({format: 'png', multiplier: 2})};
          svgObj[prev_canvobj] = canvasObj.toSVG();
        }
        
        prev_canvobj = product_id;
        canvasObj.loadFromJSON(jQuery('#jsonDataForBack').val(), function(){
          
          var htmlObj = '';

          jQuery('.wbfd_text_area').find('.back_side').each(function()
          {
            htmlObj = jQuery(this).clone();
            jQuery(this).remove();
          });

          canvasObj.forEachObject(function(obj)
          {
            if(obj.type == 'text')
            {
              if(jQuery('.wbfd_text_area').find('.back_side').length == 0)
              {
                htmlObj.appendTo('.wbfd_text_area');
              }
              else if(jQuery('.wbfd_text_area').find('.back_side').length>0)
              {
                var newObj = jQuery('.wbfd_text_area').find('.back_side:last').clone();
                jQuery('.wbfd_text_area').find('.back_side:last').after(newObj);
              }

              jQuery('.wbfd_text_area').find('.back_side:last').attr('data-id', obj.id);
              jQuery('.wbfd_text_area').find('.back_side:last .dynamic_text').attr('id', 'custom_text_' + obj.id);
              jQuery('.wbfd_text_area').find('.back_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + obj.id);
              jQuery('#custom_text_' + obj.id).val( obj.getText() );

              dynamic_text();
              add_dynamic_text();
              change_text_size();
							change_text_lockSystem();
              change_text_align();
              change_text_color();
              change_text_font(obj.id);
              change_text_style();
              
              obj.bringToFront();
            }
            
            if(obj.type == 'image' && obj.itemName && (obj.itemName == 'art'))
            {
              obj.bringToFront();
            }
          });
          
          canvasObj.renderAll();
        });
      }
      else
      {
        if(jQuery('#admin_track').length>0 && jQuery('#admin_track').val() == 'fromAdmin')
        {
          canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']))};
        }
        else if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd')
        {
          canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls'])), screenshot:canvasObj.toDataURL({format: 'png', multiplier: 2})};
          svgObj[prev_canvobj] = canvasObj.toSVG();
        }

        canvasObj.clear();
        prev_canvobj = product_id;

        if(typeof(canvDataobj[product_id])!='undefined')
        {
          canvasObj.loadFromJSON(canvDataobj[product_id].customdata,canvasObj.renderAll.bind(canvasObj));
        }
        else
        {
          wbfd_load_bg( jQuery(this).find('img').attr('src') );
        }
      }        
      wbfd_clearPanel();
      
      if(jQuery(this).hasClass('wbfd_front_bg'))
      {
        jQuery('.wbfd_back_bg').css('border', '');
        jQuery(this).css('border', '1px solid #2EA2CC');
        //jQuery('#bg_front_type').prop('checked', true);
        //jQuery('#bg_back_type').prop('checked', false);
        //jQuery('#bg_back_type').prop('disabled', true);
        jQuery('.show_front_title').show();
        jQuery('.show_back_title').hide();
        jQuery('.front_side').show();
        jQuery('.back_side').hide();
        
        jQuery('.wbfd_text_area').find('.front_side .wbfd_all_control').each(function()
        {
          jQuery(this).hide();
        });
      }
      else if( jQuery(this).hasClass('wbfd_back_bg') )
      {
        jQuery('.wbfd_front_bg').css('border', '');
        jQuery(this).css('border', '1px solid #2EA2CC');
        //jQuery('#bg_front_type').prop('checked', false);
        //jQuery('#bg_back_type').prop('checked', true); 
        //jQuery('#bg_front_type').prop('disabled', true);
        jQuery('.show_front_title').hide();
        jQuery('.show_back_title').show();
        jQuery('.front_side').hide();
        jQuery('.back_side').show();
        
      
        jQuery('.wbfd_text_area').find('.back_side .wbfd_all_control').each(function()
        {
          jQuery(this).hide();
        });
        
      }
    });

    jQuery('.wbfd_front_bg').css('border', '1px solid #2EA2CC');
  }

  function wbfd_load_bg( url )
  {
    var bg_img = new Image();
    bg_img.onload = function()
    {
      var image = new fabric.Image( bg_img );
      image.setWidth( this.width );
      image.setHeight( this.height );

      image.set({
        itemName: jQuery('#bg_track').val(),
        hasRotatingPoint: false
      });

      canvasObj.add( image );
      canvasObj.centerObject( image );
      image.setCoords();
      canvasObj.calcOffset();
      canvasObj.renderAll();
        
//      canvasObj.setBackgroundImage(bg_img.src, canvasObj.renderAll.bind(canvasObj), {
//        originX: 'left',
//        originY: 'top',
//        left: 0,
//        top: 0,
//      });
    };
    bg_img.src = url;
  }

  function wbfd_viewObject()
  {
    var getActiveObj = canvasObj.getActiveObject();
    
		if(getActiveObj)
		{
			getActiveObj.set({hasRotatingPoint:false, padding:10});
			getActiveObj.customiseCornerIcons( {
					settings: {
							borderColor: '#cccccc',
							cornerSize: 20,
							cornerShape: 'rect',
							cornerBackgroundColor: '#cccccc',
							cornerPadding: 8
					},
					bl: {
							icon: jQuery('#plugin_url').val() + '/includes/js/icons/rotate.png'
					},
//					tr: {
//
//							icon: jQuery('#plugin_url').val() + '/includes/js/icons/remove.png'
//					},
					tr: {
							icon: jQuery('#plugin_url').val() + '/includes/js/icons/resize.png'
					}
			}, function() {
					canvasObj.renderAll();
			} );
		}
		
    if(getActiveObj && getActiveObj.type == 'text')
    {
      jQuery('.wbfd_text_area').find('.wbfd_control_panel').each(function()
      {
        jQuery(this).find('.wbfd_all_control').hide();
        jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
        
        if( getActiveObj.getTextAlign() == 'left' && getActiveObj.id == jQuery(this).data('id') )
        {
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_left').css('background-image', 'none');
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_left').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_left_hover.png)');
        }
        else if( getActiveObj.getTextAlign() == 'center' && getActiveObj.id == jQuery(this).data('id'))
        {
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_center').css('background-image', 'none');
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_center').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_center_hover.png)');
        }
        else if( getActiveObj.getTextAlign() == 'right' && getActiveObj.id == jQuery(this).data('id') )
        {
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_right').css('background-image', 'none');
          jQuery(this).find('.wbfd_text_align_control .wbfd_text_align_right').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_right_hover.png)');
        }
        
        jQuery(this).find('.txt_color').val( getActiveObj.getFill().split('#')[1] );
      });
      
			jQuery('#wbfd_change_font_name_' + getActiveObj.id).val( getActiveObj.getFontFamily() );
      jQuery('#custom_text_'+ getActiveObj.id).css('border', '1px solid #faa523');
      jQuery('#custom_text_'+ getActiveObj.id).parents('.wbfd_control_panel').find('.wbfd_all_control').show();
      jQuery('#wbfd_change_font_name_' + getActiveObj.id).val( getActiveObj.getFontFamily() ).attr('selected', true);
			
			if( (typeof getActiveObj.lockMovementX != 'undefined' && getActiveObj.lockMovementX == true ) && (typeof getActiveObj.lockMovementY != 'undefined' && getActiveObj.lockMovementY == true) && (typeof getActiveObj.lockScalingX != 'undefined' && getActiveObj.lockScalingX == true) && (typeof getActiveObj.lockScalingY != 'undefined' && getActiveObj.lockScalingY == true) && (typeof getActiveObj.lockRotation != 'undefined' && getActiveObj.lockRotation == true) && (typeof getActiveObj.hasBorders != 'undefined' && getActiveObj.hasBorders == false) && (typeof getActiveObj.hasControls != 'undefined' && getActiveObj.hasControls == false)){
				jQuery('.wbfd_text_movement_control').find('.wbfd_text_lock').hide();
				jQuery('.wbfd_text_movement_control').find('.wbfd_text_unlock').css({'display' : 'inline-block'});
				jQuery('.wbfd_text_movement_control').find('.unlock-label').show();
				jQuery('.wbfd_text_movement_control').find('.lock-label').hide();
			}
			else{
				jQuery('.wbfd_text_movement_control').find('.wbfd_text_unlock').hide();
				jQuery('.wbfd_text_movement_control').find('.wbfd_text_lock').css({'display' : 'inline-block'});
				jQuery('.wbfd_text_movement_control').find('.unlock-label').hide();
				jQuery('.wbfd_text_movement_control').find('.lock-label').show();
			}
			
			jQuery('.wbfd_image_control').hide();
    }
    
    if( getActiveObj && getActiveObj.type == 'image' ){
      jQuery('.wbfd_image_control').show();
			
			jQuery('.wbfd_text_area').find('.wbfd_control_panel').each(function(){
				jQuery(this).find('.wbfd_all_control').hide();
				jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
			});
    }
  }
  
  function wbfd_clearPanel()
  {
		jQuery('.wbfd_image_control').hide();
    jQuery('.wbfd_text_area').find('.wbfd_control_panel').each(function(){
      jQuery(this).find('.wbfd_all_control').hide();
      jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
    });
  }
  
  if(jQuery('.wbfd_logo_remove').length>0)
  {
    jQuery('.wbfd_logo_remove').click(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();
      
      if( getActiveObj && getActiveObj.itemName == 'art')
      {
        if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd')
        {
          var id = getActiveObj.id;
          
          jQuery.each(logoAry, function(j)
          {
            if( logoAry[j].id === id ) 
            {
             logoAry.splice(j,1);
	return false;
            }
          });
        }
        
        canvasObj.remove( getActiveObj );
        canvasObj.renderAll();
      }
    });
  }
  
  change_text_size();
	change_text_lockSystem();
  change_text_align();
  change_text_color();
  change_text_font(1);
  change_text_font(2);
  change_text_style();
  
  if(jQuery('.wbfd_save_design').length>0)
  {
    jQuery('.wbfd_save_design').click(function()
    {
      canvasObj.deactivateAll().renderAll();
      canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']))};
      
      jQuery('.wbfd_loading_save_design').css('display', 'inline-block');
      canvDataobj.productId = jQuery('#product_id', window.parent.document).val();
      
      jQuery.ajax ({
	      type: "POST",
	      url:  jQuery('#admin-ajax').val(),
	      data: {action:'wbfd_design_save', jsonData:canvDataobj},
	      success: function( data )
	      {
	        if( data == 'added' )
	        {
	          jQuery('.wbfd_loading_save_design').css('display', 'none');
	          window.location.href = window.location.href;
	        }
	      },
	      error: function(){}
      });
    });
  }
  
  //upload logo
  if(jQuery('.wbfd_logo_uploader').length>0 || jQuery('.wbfd_admin_logo_uploader').length>0)
  {
		var btnUploadLogo;
		if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd'){
			btnUploadLogo = jQuery('.wbfd_logo_uploader');
		}
		else{
			btnUploadLogo = jQuery('.wbfd_admin_logo_uploader');
		}
   
    new AjaxUpload(btnUploadLogo, 
    {
        action: jQuery('#plugin_url').val() + '/includes/upload.php',
        name: 'uploadfile',
        onSubmit: function(file, ext)
        {
          if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
            alert('Supported files are jpg,png,jpeg,gif');
            return false;
          }

          jQuery('.wbfd_loading_logo').css('display', 'inline-block');
          
          createUndoJsonString();
            
        },
        onComplete: function(file, response)
        {
          jQuery('.wbfd_loading_logo').css('display', 'none');

          if(response == 'error')
          {
            alert("Please try again");
          }
          else
          {
            var customwWidth = 100;
            var customHeight = 100;
            var logoObj = {};
            var id = wbfd_makeid();
            var imgObj = new Image();

            imgObj.src = response;

            imgObj.onload = function () 
            {
							var image = new fabric.Image( imgObj );
							
							image.setWidth( this.width );
							image.setHeight( this.height );

							image.set({
							itemName:'art',
							id:id
							});

							canvasObj.add( image );
							canvasObj.centerObject( image );
							canvasObj.setActiveObject( image );
							image.setCoords();
							canvasObj.calcOffset();
							canvasObj.renderAll();
            }

            if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd')
            {
							logoObj.id = id;
							logoObj.url = response;

							logoAry.push( logoObj );
            }
          }
        }
    });
  }
  
  if(jQuery('.wbfd_bg_uploader').length>0 || jQuery('.wbfd_admin_bg_uploader').length>0)
  {
		var btnUploadBg;
		if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd'){
			btnUploadBg = jQuery('.wbfd_bg_uploader');
		}
		else{
			btnUploadBg = jQuery('.wbfd_admin_bg_uploader');
		}
    
    new AjaxUpload(btnUploadBg, 
    {
        action: jQuery('#plugin_url').val() + '/includes/upload.php',
        name: 'uploadfile',
        onSubmit: function(file, ext)
        {
          if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
            alert('Supported files are jpg,png,jpeg,gif');
            return false;
          }
          
          createUndoJsonString();
          if(jQuery('#bg_track').val() == 'front')
          {
            jQuery('.wbfd_loading').show();
            jQuery('.wbfd_loading').css('left', '30px');
          }
          else if(jQuery('#bg_track').val() == 'back')
          {
            jQuery('.wbfd_loading').show();
            jQuery('.wbfd_loading').css('left', '125px');
          }
        },
        onComplete: function(file, response)
        {
          jQuery('.wbfd_loading').hide();

          if(response == 'error')
          {
            alert("Please try again");
          }
          else
          {
            var bgObject = {};
            if(jQuery('#bg_track').val() == 'front')
            {
              jQuery('.wbfd_back_bg').css('border', '');
              jQuery('.wbfd_front_bg').css('border', '1px solid #2EA2CC');
              jQuery('.wbfd_front_bg img').attr('src', response);

              if(typeof(canvDataobj[1]) != 'undefined')
              {
                var parseJson1 = JSON.parse(canvDataobj[1].customdata);
                //parseJson1.backgroundImage = response;
                
                canvasObj.loadFromJSON(JSON.stringify(parseJson1), function(){
                  canvasObj.renderAll();
                  canvasObj.forEachObject(function(obj){
                    if(obj.itemName)
                    {
                      if(obj.itemName == jQuery('#bg_track').val())
                      {
                        var img = new Image();
                        img.onload=function(){
                            obj.setElement(img);
                            obj.set({
                              hasRotatingPoint: false
                            });
                            canvasObj.renderAll();
                        }
                        img.src=response;
                        return false;
                      }
                    }
                  });
                });
              }
              else
              {
                canvasObj.forEachObject(function(obj){
                  
                  if(obj.itemName)
                  {
                    if(obj.itemName == jQuery('#bg_track').val())
                    {
                      var img = new Image();
                      img.onload=function(){
                          obj.setElement(img);
                          obj.set({
                              hasRotatingPoint: false,
                            });
                          canvasObj.renderAll();
                      }
                      img.src=response;
                      return false;
                    }
                  }
                });
              }
            }

            if(jQuery('#bg_track').val() == 'back')
            {
              jQuery('.wbfd_front_bg').css('border', '');
              jQuery('.wbfd_back_bg').css('border', '1px solid #2EA2CC');
              jQuery('.wbfd_back_bg img').attr('src', response);

              if(typeof(canvDataobj[2]) != 'undefined')
              {
                var parseJson2 = JSON.parse(canvDataobj[2].customdata);
                //parseJson2.backgroundImage = response;

                canvasObj.loadFromJSON(JSON.stringify(parseJson2), function(){
                  canvasObj.renderAll();
                  canvasObj.forEachObject(function(obj){
                    if(obj.itemName)
                    {
                      if(obj.itemName == jQuery('#bg_track').val())
                      {
                        var img = new Image();
                        img.onload=function(){
                            obj.setElement(img);
                            obj.set({
                              hasRotatingPoint: false,
                            });
                            canvasObj.renderAll();
                        }
                        img.src=response;
                        return false;
                      }
                    }
                  });                  
                  
                });
              }
              else
              {
                canvasObj.forEachObject(function(obj){
                  if(obj.itemName)
                  {
                    if(obj.itemName == jQuery('#bg_track').val())
                    {
                      var img = new Image();
                      img.onload=function(){
                          obj.setElement(img);
                          obj.set({
                              hasRotatingPoint: false,
                            });
                          canvasObj.renderAll();
                      }
                      img.src=response;
                      return false;
                    }
                  }
                });
              }
            }

            if(jQuery('#frontend_track').length>0 && jQuery('#frontend_track').val() == 'fromFrontEnd')
            {
              bgObject.id = wbfd_makeid();
              bgObject.url = response;

              bgAry.push( bgObject );
            }
          }
        }
    });
  }
  
//  if(jQuery('.wbfd_custom_bg_upload').length>0)
//  {
//    jQuery('.wbfd_custom_bg_upload input[type=radio]').click(function()
//    {
//      if(jQuery(this).attr('id') == 'bg_front_type')
//      {
//        jQuery('.wbfd_front_bg').css('border', '1px solid #2ea2cc');
//        jQuery('.wbfd_back_bg').css('border', 'none');
//        
//        if(typeof(canvDataobj[1])!='undefined')
//        {
//          canvasObj.loadFromJSON(canvDataobj[1].customdata,canvasObj.renderAll.bind(canvasObj));
//        }
//        else
//        {
//          wbfd_load_bg( jQuery('.wbfd_front_bg').find('img').attr('src') );
//          canvDataobj[jQuery('.wbfd_front_bg').data('id')] = {imgid:jQuery('.wbfd_front_bg').data('id'), customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'radiusVal', 'spacingVal', 'textFliping']))};
//        }
//      }
//      else if(jQuery(this).attr('id') == 'bg_back_type')
//      {
//        jQuery('.wbfd_front_bg').css('border', 'none');
//        jQuery('.wbfd_back_bg').css('border', '1px solid #2ea2cc');
//        
//        
//        if(typeof(canvDataobj[2])!='undefined')
//        {
//          canvasObj.loadFromJSON(canvDataobj[2].customdata,canvasObj.renderAll.bind(canvasObj));
//        }
//        else
//        {
//          wbfd_load_bg( jQuery('.wbfd_back_bg').find('img').attr('src') );
//          canvDataobj[jQuery('.wbfd_back_bg').data('id')] = {imgid:jQuery('.wbfd_back_bg').data('id'), customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'radiusVal', 'spacingVal', 'textFliping']))};
//          console.log(canvDataobj);
//        }
//      }
//    });
//  }

  if(jQuery('.wbfd_design_add_to_cart').length>0)
  {
    jQuery('.wbfd_design_add_to_cart').click(function()
    {
      var hasVariationsValue;
      console.log(jQuery('.wbfd_option_all').length);
      if( jQuery('.wbfd_option_all').length >0 )
      {
        var getSelectedValue = '';
        var getAttrVal = '';
        
        jQuery('.wbfd_variation').each(function()
        {
          getSelectedValue += jQuery( '#' + jQuery( this ).attr('id') + ' option:selected').val() + ',';
        });
        
        jQuery('.wbfd_option_title').each(function()
        {
          getAttrVal += jQuery( this ).data('name') + ',';
        });
        
        var modifiedString = getSelectedValue.replace(/,\s*$/, '');
        var modifiedStringAttr = getAttrVal.replace(/,\s*$/, '');
        cartItemobj.attrName = modifiedStringAttr;
        cartItemobj.attrValue = modifiedString;
        hasVariationsValue = cartItemobj.attrValue.indexOf('wbfdSelect');
      }
      else
      {
        hasVariationsValue = 'no_variation_data';
      }
      
      if( hasVariationsValue == -1 || hasVariationsValue == 'no_variation_data' )
      {
        //jQuery('.wbfd_option_content').fadeOut();
        //jQuery('.wbfd_option_content').css('border-bottom', '1px solid #e1e1e1');
        //jQuery('.wbfd_option_content').css('border-top', '1px solid #e1e1e1');
        //jQuery('.wbfd_option_content').css('border-right', '1px solid #e1e1e1');
        
        //jQuery('.wbfd_loading_save_design').css("display", "inline-block");
        
        canvasObj.deactivateAll().renderAll();
        
        canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls'])), screenshot:canvasObj.toDataURL({format: 'png', multiplier: 5})};
        svgObj[prev_canvobj] = canvasObj.toSVG();
        
        var qtys = jQuery('#wbfd_qty').val();
        if( qtys >0 && qtys )
        {
          qtys = qtys;
        }
        else
        {
          qtys = 1;
        }
        
        var logoStr = '';
        if( logoAry.length>0 )
        {
          for( var k=0; k<logoAry.length; k++ )
          {
            var logoUrl = logoAry[k].url.replace( jQuery('#wbfd_upload_url').val(),'');
            logoStr += logoUrl + '##';
          }
        }
        
        var bgStr = '';
        if( bgAry.length>0 )
        {
          for( var l=0; l<bgAry.length; l++ )
          {
            var bgUrl = bgAry[l].url.replace( jQuery('#wbfd_upload_url').val(),'');
            bgStr += bgUrl + '##';
          }
        }
        
        for(var key in canvDataobj)
        {
          var getCustomObjData = JSON.parse( canvDataobj[key].customdata );
          var getImgID = canvDataobj[key].imgid

          if( getCustomObjData )
          {
            delete canvDataobj[key].customdata;
          }
          if( getImgID )
          {
            delete canvDataobj[key].imgid;
          }
        }
        
        cartItemobj.productID = jQuery('#product_id').val();
        cartItemobj.qty = qtys;
        cartItemobj.logo = logoStr.replace(/##\s*$/, '');
        cartItemobj.bg = bgStr.replace(/##\s*$/, '');
        cartItemobj.customData = canvDataobj;
        
        jQuery.ajax ({
          type:"POST",
          url:jQuery('#admin-ajax').val(),
          data: {action:'wbfd_add_to_cart', data:cartItemobj},
          success: function( data )
          {
            if( data == 'cart_updated' )
            {
							jQuery.ajax ({
											type:"POST",
											url:jQuery('#admin-ajax').val(),
											data: {action:'wbfd_svg_data', svg:svgObj},
											success: function( data )
											{
												if( data == 'svg_added' )
												{
													window.location.href = jQuery('#wbfd_cart_url').val();
												}
											},
											error: function(){}
							});
            }
          },
          error: function(){}
        });
      }
      else
      {
        //jQuery('.wbfd_option_content').fadeIn();
        //jQuery('.wbfd_option_content').css('border-bottom', '1px solid #FF0000');
        //jQuery('.wbfd_option_content').css('border-top', '1px solid #FF0000');
        //jQuery('.wbfd_option_content').css('border-right', '1px solid #FF0000');
      }
      
    });
  }
  
  if(jQuery('.wbfd_close_popup').length>0)
  {
    jQuery('.wbfd_close_popup').click(function()
    {
      jQuery(this).parent().fadeOut();
    });
  }
  
  if(jQuery('.wbfd_design_option').length>0)
  {
    jQuery('.wbfd_design_option').click(function()
    {
      jQuery('.wbfd_option_content').fadeIn();
      jQuery('.wbfd_option_content').css('border-bottom', '1px solid #e1e1e1');
      jQuery('.wbfd_option_content').css('border-right', '1px solid #e1e1e1');
      jQuery('.wbfd_option_content').css('border-top', '1px solid #e1e1e1');
    });
  }
  
  function wbfd_makeid()
  {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  }
  
  if(jQuery('.wbfd_save_by_user').length>0)
  {
    jQuery('.wbfd_save_by_user').click(function()
    {
      canvasObj.deactivateAll().renderAll();
        
      canvDataobj[prev_canvobj] = {imgid:prev_canvobj,customdata:JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']))};
      
      var logoStr = '';
      if( logoAry.length>0 )
      {
        for( var k=0; k<logoAry.length; k++ )
        {
          var logoUrl = logoAry[k].url.replace( jQuery('#wbfd_upload_url').val(),'');
          logoStr += logoUrl + '##';
        }
      }

      var bgStr = '';
      if( bgAry.length>0 )
      {
        for( var l=0; l<bgAry.length; l++ )
        {
          var bgUrl = bgAry[l].url.replace( jQuery('#wbfd_upload_url').val(),'');
          bgStr += bgUrl + '##';
        }
      }
      
      for(var key in canvDataobj)
      {
        var getCustomObjData = canvDataobj[key].screenshot;
        var getImgID = canvDataobj[key].imgid

        if( getCustomObjData )
        {
          delete canvDataobj[key].screenshot;
        }
        if( getImgID )
        {
          delete canvDataobj[key].imgid;
        }
      }
      
      var design_id = '';
      
      if(jQuery('#wbfd_is_url_from_edit_panel').length>0 && jQuery('#wbfd_is_url_from_edit_panel').val() >0)
      {
        design_id = jQuery('#wbfd_is_url_from_edit_panel').val();
      }
      
      cartItemobj.productID =  jQuery('#product_id').val();
      cartItemobj.designID =   design_id;
      cartItemobj.logo =	   logoStr.replace(/##\s*$/, '');
      cartItemobj.bg =	   bgStr.replace(/##\s*$/, '');
      cartItemobj.customData = canvDataobj;
      
        
      jQuery('.wbfd_loading_save_design').css('display', 'inline-block');
      
      jQuery.ajax ({
	      type:"POST",
	      url:wbfdAjax.ajaxurl,
	      data: {action:'wbfd_save_design_by_user_id', data:cartItemobj},
				
	      success: function( data )
	      {
	        if( data == 'supur_admin_login' )
	        {
	          jQuery('.wbfd_loading_save_design').css('display', 'none');
	          jQuery('.wbfd_overlay').show();
	          jQuery('.wbfd_msg_content').show();
	        }
	        else if( data == 'save_by_user' || data == 'update_by_user' )
	        {
	          jQuery('.wbfd_loading_save_design').css('display', 'none');
	          jQuery('.wbfd_overlay').show();
	          jQuery('.wbfd_success_msg_content').show();
	        }
	        else if(data && data !='')
	        {
	          window.location.href = data;
	        }
	      },
	      error: function(){}
      });
    });
  }
  
  if(jQuery('.wbfd_close_msg_content').length>0)
  {
    jQuery('.wbfd_close_msg_content').click(function()
    {
      jQuery(this).parent().hide();
      jQuery('.wbfd_overlay').hide();
    });
  }
  
  if(jQuery('.wbfd_reset_design').length>0)
  {
    jQuery('.wbfd_reset_design').click(function()
    {
      var elFront = jQuery('.wbfd_front_bg[style*="border"]');
      var elBack  = jQuery('.wbfd_back_bg[style*="border"]');
      
      if(elFront.length)
      {
        if(jQuery('#jsonDataForFront').val() && jQuery('#jsonDataForFront').val() != '' && jQuery('#frontend_track').length>0)
        {
          canvasObj.clear();
          canvasObj.loadFromJSON(jQuery('#jsonDataForFront').val(), canvasObj.renderAll.bind(canvasObj));
          
          var parseFront = JSON.parse( jQuery('#jsonDataForFront').val() );
          jQuery('.wbfd_front_bg img').attr('src', parseFront.backgroundImage);
        }
        else if(jQuery('#admin_track').length>0)
        {
          if( jQuery('#front_bg', window.parent.document).val() )
          {
            canvasObj.clear();
            wbfd_load_bg( jQuery('#front_bg', window.parent.document).val() );
            
           jQuery('.wbfd_front_bg img').attr('src', jQuery('#front_bg', window.parent.document).val());
          }
        }
        
        if(jQuery('.wbfd_text_area').find('.front_side').length>0)
        {
          jQuery('.wbfd_text_area').find('.front_side').each(function()
          {
            jQuery(this).remove();
          });
        }
        if(jQuery('.wbfd_text_area').find('.dynamic_text').length>0)
        {
          jQuery('.wbfd_text_area').find('.front_side .dynamic_text').each(function()
          {
            jQuery(this).val('');
          });
        }
        
        copyFrontControlHtml.appendTo('.wbfd_text_area');
        change_text_font(1);
     
      }
      else if (elBack.length)
      {
        if(jQuery('#jsonDataForBack').val() && jQuery('#jsonDataForBack').val() != '' && jQuery('#frontend_track').length>0)
        {
          canvasObj.clear();
          canvasObj.loadFromJSON(jQuery('#jsonDataForBack').val(), canvasObj.renderAll.bind(canvasObj));
          
          var parseFront = JSON.parse( jQuery('#jsonDataForBack').val() );
          jQuery('.wbfd_back_bg img').attr('src', parseFront.backgroundImage);
        }
        else if(jQuery('#admin_track').length>0)
        {
          if( jQuery('#back_bg', window.parent.document).val() )
          {
            canvasObj.clear();
            wbfd_load_bg( jQuery('#back_bg', window.parent.document).val() );
            
           jQuery('.wbfd_back_bg img').attr('src', jQuery('#back_bg', window.parent.document).val());
          }
        }
        
        if(jQuery('.wbfd_text_area').find('.back_side').length>0)
        {
          jQuery('.wbfd_text_area').find('.back_side').each(function()
          {
            jQuery(this).remove();
          });
        }
        
        if(jQuery('.wbfd_text_area').find('.dynamic_text').length>0)
        {
          jQuery('.wbfd_text_area').find('.back_side .dynamic_text').each(function()
          {
            jQuery(this).val('');
          });
        }
        copyBackControlHtml.appendTo('.wbfd_text_area');
        jQuery('.wbfd_text_area').find('.back_side:last').show();
        change_text_font(2);
      }
      
      dynamic_text();
      add_dynamic_text();
      change_text_size();
			change_text_lockSystem();
      change_text_align();
      change_text_color();
      change_text_style();
      wbfd_clearPanel();
    });
  }
  
  if(jQuery('.wbfd_undo_design').length>0)
  {
    jQuery('.wbfd_undo_design').click(function()
    {
      var elFront = jQuery('.wbfd_front_bg[style*="border"]');
      var elBack  = jQuery('.wbfd_back_bg[style*="border"]');
      
      var getUndoFrontObj = undoFrontData;
      var getUndoBackObj =  undoBackData;
      
      if(elFront.length && Object.keys(getUndoFrontObj).length>0)
      {
        if(getUndoFrontObj[parseInt(Object.keys(getUndoFrontObj).length) - parseInt(frontObjCount)])
        {
          canvasObj.clear();
          canvasObj.loadFromJSON(getUndoFrontObj[parseInt(Object.keys(getUndoFrontObj).length) - parseInt(frontObjCount)], canvasObj.renderAll.bind(canvasObj));
          
          canvasObj.forEachObject(function(obj)
          {
            if(obj.type == 'text')
            {
	if(jQuery('#custom_text_' + obj.id).length>0)
	{
	  jQuery('#custom_text_' + obj.id).val(obj.getText());
	}
            }
          });

          var parseFront = JSON.parse( getUndoFrontObj[parseInt(Object.keys(getUndoFrontObj).length) - parseInt(frontObjCount)] );
          jQuery('.wbfd_front_bg img').attr('src', parseFront.backgroundImage);
          
          frontObjCount ++;
        }
      }
      else if(elBack.length && Object.keys(getUndoBackObj).length>0)
      {
        if(getUndoBackObj[parseInt(Object.keys(getUndoBackObj).length) - parseInt(backObjCount)])
        {
          canvasObj.clear();
          canvasObj.loadFromJSON(getUndoBackObj[parseInt(Object.keys(getUndoBackObj).length) - parseInt(backObjCount)], canvasObj.renderAll.bind(canvasObj));
          
          canvasObj.forEachObject(function(obj)
          {
            if(obj.type == 'text')
            {
	if(jQuery('#custom_text_' + obj.id).length>0)
	{
	  jQuery('#custom_text_' + obj.id).val(obj.getText());
	}
            }
          });

          var parseFront = JSON.parse( getUndoBackObj[parseInt(Object.keys(getUndoBackObj).length) - parseInt(backObjCount)] );
          jQuery('.wbfd_back_bg img').attr('src', parseFront.backgroundImage);
          
          backObjCount ++;
        }
      }
      
      wbfd_clearPanel();
    });
  }
  
  if(jQuery('.wbfd_control_panel').length>0)
  {
    dynamic_tooltip();
  }
  
  if(jQuery('.wbfd_add_more_text').length>0 || jQuery('.wbfd_add_more_text_frontend').length>0)
  {
    copyFrontControlHtml = jQuery('.wbfd_text_area').find('.front_side:first').clone();
    copyBackControlHtml  = jQuery('.wbfd_text_area').find('.back_side:first').clone();
    
    jQuery('.wbfd_add_more_text,.wbfd_add_more_text_frontend').click(function()
    {
      var elFront = jQuery('.wbfd_front_bg[style*="border"]');
      var elBack  = jQuery('.wbfd_back_bg[style*="border"]');
      var uid = wbfd_make_unique_id();
      
      if(elFront.length)
      {
        if(jQuery('.wbfd_text_area').find('.front_side:first').length)
        {
          jQuery('.wbfd_text_area').find('.front_side:first').clone().appendTo('.wbfd_text_area');
          jQuery('.wbfd_text_area').find('.front_side:last').attr('data-id', uid);
          jQuery('.wbfd_text_area').find('.front_side:last .dynamic_text').attr('id', 'custom_text_' + uid);
          jQuery('.wbfd_text_area').find('.front_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + uid);
        }
        else
        {
          copyFrontControlHtml.appendTo('.wbfd_text_area');
          jQuery('.wbfd_text_area').find('.front_side:last').attr('data-id', uid);
          jQuery('.wbfd_text_area').find('.front_side:last .dynamic_text').attr('id', 'custom_text_' + uid);
          jQuery('.wbfd_text_area').find('.front_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + uid);
        }
				
				jQuery('.wbfd_text_area').find('.front_side #custom_text_' + uid).val('');
				
				jQuery('.color').click(function(){
						var obj = jQuery(this)[0];
						if (!obj.hasPicker) {
							var picker = new jscolor.color(obj, {});
							obj.hasPicker = true;
							picker.showPicker();
						}
				});
				
        
        jQuery('.wbfd_text_area').find('.front_side').each(function()
        {
          if(jQuery(this).find('.wbfd_all_control').css('display') == 'block')
          {
            jQuery(this).find('.wbfd_all_control').hide();
          }

          jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
          
        });
      }
      else if(elBack.length)
      {
        if(jQuery('.wbfd_text_area').find('.back_side:first').length)
        {
          jQuery('.wbfd_text_area').find('.back_side:first').clone().appendTo('.wbfd_text_area');
          jQuery('.wbfd_text_area').find('.back_side:last').attr('data-id', uid);
          jQuery('.wbfd_text_area').find('.back_side:last .dynamic_text').attr('id', 'custom_text_' + uid);
          jQuery('.wbfd_text_area').find('.back_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + uid);
        }
        else
        {
          copyBackControlHtml.appendTo('.wbfd_text_area');
          jQuery('.wbfd_text_area').find('.back_side:last').show();
          jQuery('.wbfd_text_area').find('.back_side:last').attr('data-id', uid);
          jQuery('.wbfd_text_area').find('.back_side:last .dynamic_text').attr('id', 'custom_text_' + uid);
          jQuery('.wbfd_text_area').find('.back_side:last .wbfd_change_font_name').attr('id', 'wbfd_change_font_name_' + uid);
        }
				
				jQuery('.wbfd_text_area').find('.back_side #custom_text_' + uid).val('');
				
				jQuery('.color').click(function(){
						var obj = jQuery(this)[0];
						if (!obj.hasPicker) {
							var picker = new jscolor.color(obj, {});
							obj.hasPicker = true;
							picker.showPicker();
						}
				});
        
        jQuery('.wbfd_text_area').find('.back_side').each(function()
        {
          if(jQuery(this).find('.wbfd_all_control').css('display') == 'block')
          {
            jQuery(this).find('.wbfd_all_control').hide();
          }

          jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
        });
      }
      
      if(jQuery('.wbfd_text_remove_icon').length>0)
      {
        remove_dynamic_text();
      }
      
      dynamic_text();
      add_dynamic_text();
      change_text_size();
			change_text_lockSystem();
      change_text_align();
      change_text_color();
      change_text_font(uid);
      change_text_style();
      
      canvasObj.deactivateAll().renderAll();
    });
  }
  
  if(jQuery('.dynamic_text').length>0)
  {
    dynamic_text();
  }
	
	if(jQuery('.wbfd_text_remove_icon').length>0)
	{	
		remove_dynamic_text();
	}
  
  add_dynamic_text();
	
	//controls
	fabric.Object.prototype.setControlsVisibility( {
		ml: false,
		mr: false,
		br: false,
		mb: false,
		mt: false,
		tl: false
	} );

	fabric.Canvas.prototype.customiseControls( {
		bl: {
				action: 'rotate',
				cursor: 'crosshair'
		},
		tr: {
				action: 'scale'
		}
//		bl: {
//				action: 'remove',
//				cursor: 'pointer'
//		}
	} );
	
	if(jQuery('.wbfd_logo_movement_control').length>0){
		jQuery('.wbfd_logo_movement_control div').on('click', function(){
			
				var getActiveObj = canvasObj.getActiveObject();
			
				if(jQuery(this).hasClass('wbfd_logo_lock')){
					if(getActiveObj && getActiveObj.type == "image"){
						//lock movement
						getActiveObj.lockMovementX = getActiveObj.lockMovementY = true;

						//lock scaling 
						getActiveObj.lockScalingX = getActiveObj.lockScalingY = true; 

						//lock rotation
						getActiveObj.lockRotation = true;

						//remove border
						getActiveObj.hasBorders  = false;
						getActiveObj.hasControls = false;

						//canvasObj.discardActiveObject(); 

						jQuery('.wbfd_logo_movement_control').find('.wbfd_logo_lock').hide();
						jQuery('.wbfd_logo_movement_control').find('.wbfd_logo_unlock').css({'display' : 'inline-block'});
					}
				}
				else if(jQuery(this).hasClass('wbfd_logo_unlock')){
					if(getActiveObj && getActiveObj.type == "image"){
						//unlock movement
						getActiveObj.lockMovementX = getActiveObj.lockMovementY = false;

						//unlock scaling 
						getActiveObj.lockScalingX = getActiveObj.lockScalingY = false; 

						//unlock rotation
						getActiveObj.lockRotation = false;

						//add border
						getActiveObj.hasBorders  = true;
						getActiveObj.hasControls = true;

						jQuery('.wbfd_logo_movement_control').find('.wbfd_logo_lock').css({'display' : 'inline-block'});
						jQuery('.wbfd_logo_movement_control').find('.wbfd_logo_unlock').hide();
					}
				}
			
				canvasObj.renderAll();
			
				//jQuery(this).hide();
				//jQuery(this).parents('.wbfd_logo_movement_control').find('.wbfd_logo_unlock').show();
		});
	}
	
	if(jQuery('.wbfd_object_bring_to_front').length>0){
		jQuery('.wbfd_object_bring_to_front').click(function(){
			var getActiveObj = canvasObj.getActiveObject();
			canvasObj.bringToFront( getActiveObj );
			canvasObj.renderAll();
		});
	}
	
	if(jQuery('.wbfd_object_send_to_back').length>0){
		jQuery('.wbfd_object_send_to_back').click(function(){
			var getActiveObj = canvasObj.getActiveObject();
			canvasObj.sendToBack( getActiveObj );
			canvasObj.renderAll();
		});
	}
});

function wbfd_set_variations_price()
{
  if( jQuery('.wbfd_option_all').length >0 )
  {
    var getSelectedValue = '';
    var getAttrVal = '';
    
    jQuery('.wbfd_variation').each(function()
    {
      getSelectedValue += jQuery( '#' + jQuery( this ).attr('id') + ' option:selected').val() + ',';
    });
    
    jQuery('.wbfd_option_title').each(function()
    {
      getAttrVal += jQuery( this ).data('name') + ',';
    });
    
    var modifiedString = getSelectedValue.replace(/,\s*$/, '');
    var modifiedStringAttr = getAttrVal.replace(/,\s*$/, '');
    var hasVariationsValue = modifiedString.indexOf('wbfdSelect');
    
    //ajax request for variation price
    jQuery.ajax ({
      type:"POST",
      dataType: "json",
      url:jQuery('#admin-ajax').val(),
      data: {action:'wbfd_get_variations_price_by_id',strAttrName:modifiedStringAttr,strAttrVal: modifiedString,id:jQuery('#product_id').val()},
      success: function( data )
      {
       if( data )
       {
         if( data[0].match_status && data[0].match_status === 'no_match' && hasVariationsValue == 'wbfdSelect' )
         {
           jQuery('.wbfd_option_all').find('.wbfd_variation').each(function()
           {
             jQuery('#' + jQuery(this).attr('id')).val('wbfdSelect').attr('selected',true);
           });
         }
	 
         if(data[0].price)
         {
            jQuery('.wbfd_changable_price').html( data[0].price );
         }
         else
         {
            jQuery('.wbfd_changable_price').html( 0 );
         }
       }
      },
      error: function(){}
    });
  }
}


function createUndoJsonString()
{
  var elFront = jQuery('.wbfd_front_bg[style*="border"]');
  var elBack  = jQuery('.wbfd_back_bg[style*="border"]');

  if(elFront.length)
  {
    undoFrontData[undoCount] = JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']));
  }
  else if(elBack.length)
  {
    undoBackData[undoCount] = JSON.stringify(canvasObj.toJSON(['id', 'name', 'itemName', 'zIndex', 'hasRotatingPoint', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'hasBorders', 'hasControls']));
  }

  undoCount ++;
}

function wbfd_make_unique_id()
{
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for( var i=0; i < 5; i++ )
      text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text + (new Date().getTime()).toString(36);
}

function dynamic_text()
{
  dynamic_tooltip();
  jQuery('.dynamic_text').click(function()
  {
    jQuery(this).parents('.wbfd_text_area').find('.wbfd_control_panel').each(function()
    {
      jQuery(this).find('.wbfd_all_control').hide();
      jQuery(this).find('.dynamic_text').css('border', '1px solid #e1e1e1');
    });
		
    jQuery(this).parents('.wbfd_control_panel').find('.wbfd_all_control').show();
    jQuery(this).css('border', '1px solid #FAA523');
    
    var selected_id = jQuery(this).parents('.wbfd_control_panel').data('id'); 
    canvasObj.forEachObject(function(obj){
      if(obj.id === selected_id && obj.type == 'text')
      {
        canvasObj.setActiveObject( obj );
        obj.setCoords();
        canvasObj.calcOffset();
        canvasObj.renderAll();
      }
    });
		
		jQuery('.wbfd_image_control').hide();
  });
}

function dynamic_tooltip()
{
  jQuery( '.wbfd_text_increase,.wbfd_text_deincrease,.wbfd_text_align_left,.wbfd_text_align_center,.wbfd_text_align_right,.wbfd_text_color_control,.wbfd_text_font_control,.wbfd_text_style_bold,.wbfd_text_style_italic,.wbfd_text_style_underline,.wbfd_text_remove_icon, .wbfd_logo_lock, .wbfd_logo_unlock, .wbfd_object_bring_to_front, .wbfd_object_send_to_back, .wbfd_logo_remove, .wbfd_text_lock, .wbfd_text_unlock' ).tooltip({
    position: {
      my: "center bottom-20",
      at: "center top",
      using: function( position, feedback ) {
        jQuery( this ).css( position );
        jQuery( "<div>" )
          .addClass( "arrow" )
          .addClass( feedback.vertical )
          .addClass( feedback.horizontal )
          .appendTo( this );
      }
    }
  });
}

function remove_dynamic_text()
{
  jQuery('.wbfd_text_remove_icon').on('click', function()
  {
    createUndoJsonString();
    var getActiveObj = canvasObj.getActiveObject();
    
    if(getActiveObj && getActiveObj.type == 'text')
    {
      var selected_id = jQuery(this).parents('.wbfd_control_panel').data('id');
      canvasObj.forEachObject(function(obj)
      {
        if(obj.id === selected_id)
        {
          canvasObj.remove( obj );
          canvasObj.renderAll();
        }
      });
    }
    
    jQuery(this).parents('.wbfd_control_panel').remove();
  });
}

function add_dynamic_text()
{
  if(jQuery('.dynamic_text').length>0)
  {
    jQuery('.dynamic_text').keyup(function(e)
    {
      var code =  e.keyCode || e.which;
			var selected_id = jQuery(this).parents('.wbfd_control_panel').data('id'); 
			
			if(code == 9 || code == 40){
				canvasObj.forEachObject(function(obj){
					if(obj.id === selected_id && obj.type == 'text')
					{
						canvasObj.setActiveObject( obj );
						obj.setCoords();
						canvasObj.calcOffset();
						canvasObj.renderAll();
					}
				});
			}
	
			if(code != 9 && code != 40){
				wbfd_create_text_obj(jQuery(this).val(), jQuery(this).parents('.wbfd_control_panel').data('id'));
			}
			else{
				return false;
			}
    });
  }
}

function wbfd_create_text_obj( str, id )
{
  var getActiveObj = canvasObj.getActiveObject();
  
  if(getActiveObj && getActiveObj.type == 'text' && getActiveObj.id == id)
  {
    getActiveObj.setText( str );
    getActiveObj.setCoords();
    canvasObj.calcOffset();
    canvasObj.renderAll();
  }
  else
  {
    var strObj = new fabric.Text( str ,{
			fontFamily: "arial", 
      fontSize:20,
      fill:'#3B5998',
      id: id
    });
  
    canvasObj.add( strObj );
    canvasObj.centerObject( strObj );
    canvasObj.setActiveObject( strObj );
    strObj.setCoords();
    canvasObj.calcOffset();
    canvasObj.renderAll();
  }
}

function change_text_size()
{
  if(jQuery('.wbfd_text_size_control').length>0)
  {
    jQuery('.wbfd_text_size_control div').click(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();
      
      if(jQuery(this).hasClass('wbfd_text_increase'))
      {
        if(getActiveObj && getActiveObj.type == "text")
        {
          if(getActiveObj.getFontSize()>0)
          {
            getActiveObj.setFontSize( parseInt( getActiveObj.getFontSize() + 1 ));
          }
        }
      }
      else if(jQuery(this).hasClass('wbfd_text_deincrease'))
      {
        if(getActiveObj && getActiveObj.type == "text")
        {
          if(getActiveObj.getFontSize()>0 && getActiveObj.getFontSize()>5)
          {
            getActiveObj.setFontSize( parseInt( getActiveObj.getFontSize() - 1 ));
          }
        }
      }
      
      canvasObj.renderAll();
    });
  }
}

function change_text_lockSystem()
{
	if(jQuery('.wbfd_text_movement_control').length>0)
  {
		jQuery('.wbfd_text_movement_control div').click(function()
    {
      var getActiveObj = canvasObj.getActiveObject();
			
			if(jQuery(this).hasClass('wbfd_text_lock'))
      {
				if(getActiveObj && getActiveObj.type == "text")
        {
					//lock movement
          getActiveObj.lockMovementX = getActiveObj.lockMovementY = true;
          
          //lock scaling 
          getActiveObj.lockScalingX = getActiveObj.lockScalingY = true; 
          
          //lock rotation
          getActiveObj.lockRotation = true;
					
					//remove border
					getActiveObj.hasBorders  = false;
					getActiveObj.hasControls = false;
          
					//canvasObj.discardActiveObject(); 
					
					jQuery('.wbfd_text_movement_control').find('.wbfd_text_lock').hide();
					jQuery('.wbfd_text_movement_control').find('.wbfd_text_unlock').css({'display' : 'inline-block'});
					jQuery('.wbfd_text_movement_control').find('.unlock-label').show();
					jQuery('.wbfd_text_movement_control').find('.lock-label').hide();
				}
			}
			else if(jQuery(this).hasClass('wbfd_text_unlock'))
      {
				if(getActiveObj && getActiveObj.type == "text")
        {
					//unlock movement
					getActiveObj.lockMovementX = getActiveObj.lockMovementY = false;

					//unlock scaling 
					getActiveObj.lockScalingX = getActiveObj.lockScalingY = false; 

					//unlock rotation
					getActiveObj.lockRotation = false;

					//add border
					getActiveObj.hasBorders  = true;
					getActiveObj.hasControls = true;
					
					jQuery('.wbfd_text_movement_control').find('.wbfd_text_lock').css({'display' : 'inline-block'});
					jQuery('.wbfd_text_movement_control').find('.wbfd_text_unlock').hide();
					jQuery('.wbfd_text_movement_control').find('.unlock-label').hide();
					jQuery('.wbfd_text_movement_control').find('.lock-label').show();
				}
			}
			
			canvasObj.renderAll();
		});
	}
}

function change_text_align()
{
  if(jQuery('.wbfd_text_align_control').length>0)
  {
    jQuery('.wbfd_text_align_control div').click(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();
      
      if(jQuery(this).hasClass('wbfd_text_align_left'))
      {
        if(getActiveObj)
        {
          getActiveObj.setTextAlign ('left');
        }
        
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_center.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_right.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_left_hover.png)');
      }
      else if(jQuery(this).hasClass('wbfd_text_align_center'))
      {
        if(getActiveObj)
        {
          getActiveObj.setTextAlign ('center');
        }
        
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_center_hover.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_right.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_left.png)');
      }
      else if(jQuery(this).hasClass('wbfd_text_align_right'))
      {
        if(getActiveObj)
        {
          getActiveObj.setTextAlign ('right');
        }
        
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_center').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_center.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_right').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_right_hover.png)');
        
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'none');
        jQuery(this).parent().find('.wbfd_text_align_left').css('background-image', 'url(' + jQuery('#plugin_url').val() + '/includes/images/text_align_left.png)');
        
      }
      
      canvasObj.renderAll();
    });
  }
}

function change_text_color()
{
  if(jQuery('.txt_color').length>0)
  {
    jQuery('.txt_color').change(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();
      var getColor = '#' + jQuery( this ).val();
      
      if(getActiveObj && getActiveObj.type == 'text')
      {
        getActiveObj.setFill( getColor );
        canvasObj.renderAll();
      }
    });
  }
}

function change_text_font(id)
{
  if(jQuery('.wbfd_change_font_name').length>0)
  {
    jQuery('#wbfd_change_font_name_' + id).change(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();
      var getFontName = jQuery( '#' + jQuery( this ).attr('id') + ' option:selected' ).val();
       
      if(getActiveObj && getActiveObj.type == 'text')
      {
        getActiveObj.setFontFamily( getFontName );
        canvasObj.renderAll();
      }
    });
  }
}

function change_text_style()
{
  if(jQuery('.wbfd_text_style_control').length>0)
  {
    jQuery('.wbfd_text_style_control div').click(function()
    {
      createUndoJsonString();
      var getActiveObj = canvasObj.getActiveObject();

      if(jQuery(this).hasClass('wbfd_text_style_bold'))
      {
        if( getActiveObj.getFontWeight() === 'normal' || getActiveObj.getFontWeight() === 'italic' )
        {
          getActiveObj.setFontWeight( 'bold' );
        }
        else
        {
          getActiveObj.setFontWeight( 'normal' );
        }
      }
      else if(jQuery(this).hasClass('wbfd_text_style_italic'))
      {
        if( getActiveObj.getFontWeight() === 'normal' || getActiveObj.getFontWeight() === 'bold' )
        {
          getActiveObj.setFontWeight( 'italic' );
        }
        else
        {
          getActiveObj.setFontWeight( 'normal' );
        }
      }
      else if(jQuery(this).hasClass('wbfd_text_style_underline'))
      {
        if( (getActiveObj.getFontWeight() === 'normal' || getActiveObj.getFontWeight() === 'bold' || getActiveObj.getFontWeight() === 'italic' ) && getActiveObj.getTextDecoration() == '' )
        {
          getActiveObj.setTextDecoration( 'underline' );
        }
        else
        {
          getActiveObj.setTextDecoration( '' );
        }
      }
      
      canvasObj.renderAll();
    });
  }
}

function wbfd_objectMoving()
{
  createUndoJsonString();
}

function wbfd_objectScaling()
{
  createUndoJsonString();
}

function wbfd_objectRotating()
{
  createUndoJsonString();
}