var canvasObj;
jQuery(document).ready(function()
{
	//image upload
	if( jQuery( '#btn_background_img_uploader' ).length >0 || jQuery( '#btn_logo_img_uploader' ).length >0 || jQuery( '#btn_more_text_img_uploader' ).length >0 || jQuery( '#btn_save_img_uploader' ).length >0 || jQuery( '#btn_add_to_cart_img_uploader' ).length >0 )
  {
    var custom_uploader;
    jQuery( '#btn_background_img_uploader, #btn_logo_img_uploader, #btn_more_text_img_uploader, #btn_save_img_uploader, #btn_add_to_cart_img_uploader' ).click(function(e) 
    {
      e.preventDefault();
      var obj = jQuery(this);
      
      custom_uploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
              text: 'Add Image'
          },
          multiple: false
      });
      custom_uploader.on('select', function() 
      {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
       
        if(attachment.url)
        {
          if(jQuery(obj).hasClass('btn_background')){
						jQuery('#btn_background_img_upload_url').val( attachment.url );
					}
					else if(jQuery(obj).hasClass('btn_logo')){
						jQuery('#btn_logo_img_upload_url').val( attachment.url );
					}
					else if(jQuery(obj).hasClass('btn_more_text')){
						jQuery('#btn_more_text_img_upload_url').val( attachment.url );
					}
					else if(jQuery(obj).hasClass('btn_save')){
						jQuery('#btn_save_img_upload_url').val( attachment.url );
					}
					else if(jQuery(obj).hasClass('btn_add_to_cart')){
						jQuery('#btn_add_to_cart_img_upload_url').val( attachment.url );
					}
        }
      });
      custom_uploader.open();
    });
  }
	
  if(jQuery('#bg_image').length>0)
  {
    canvasObj = new fabric.Canvas('bg_image');
  }
  
  if(jQuery('#front_background_img_uploader').length>0 || jQuery('#back_background_img_uploader').length>0)
  {
    jQuery('#front_background_img_uploader, #back_background_img_uploader').click(function()
    {
      var custom_uploader;
      var getType = jQuery(this).data('side');
      
      custom_uploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
	text: 'Choose Image'
          },
          multiple: false
      });
      custom_uploader.on('select', function() 
      {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        if(getType == 'front')
        {
          jQuery('#front_background_img_upload_url').val( attachment.url );
        }
        else if(getType == 'back')
        {
          jQuery('#back_background_img_upload_url').val( attachment.url );
        }
      });
      custom_uploader.open();
    });
  }
  
  if(jQuery('#wbfd_design_popup_open').length>0)
  {
    jQuery('#wbfd_design_popup_open').click(function()
    {
      var parm = 'width=' + jQuery('#wbfd_design_width').val() + '&height=' + jQuery('#wbfd_design_height').val() + '&id=' + jQuery('#product_id').val();
      jQuery('.wbfd_overlay').show();
      jQuery('.wbfd_design_popup_content').show();
      jQuery('#iframe_popup').attr('src', jQuery('#plugin_url').val() + '/includes/popup-iframe.php?' + parm);
    });
  }
  
  if(jQuery('.wbfd_close_popup').length>0)
  {
    jQuery('.wbfd_close_popup').click(function()
    {
      jQuery('#iframe_popup').attr('src', '');
      jQuery(this).parent().hide();
      jQuery('.wbfd_overlay').hide();
    });
  }
});