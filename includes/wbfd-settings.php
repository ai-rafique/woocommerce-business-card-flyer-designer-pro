<?php	
function wbfd_btn_upload(){
  wp_enqueue_media();
  
  if(isset($_POST['update_wbfd_settings_btn']) && $_POST['update_wbfd_settings_btn'] == 'Update Settings'){
    $bg_btn         = 0;
    $logo_btn       = 0;
    $more_text_btn  = 0;
    $save_btn       = 0;
    
    if(isset($_POST['enable_disable_bg_btn']) && $_POST['enable_disable_bg_btn'] == 'enable'){
      $bg_btn = 1;
    }
    
    if(isset($_POST['enable_disable_logo_btn']) && $_POST['enable_disable_logo_btn'] == 'enable'){
      $logo_btn = 1;
    }
    
    if(isset($_POST['enable_disable_more_text_btn']) && $_POST['enable_disable_more_text_btn'] == 'enable'){
      $more_text_btn = 1;
    }
    
    if(isset($_POST['enable_disable_save_btn']) && $_POST['enable_disable_save_btn'] == 'enable'){
      $save_btn = 1;
    }
    
    $update_settings_data = array(
                         'btn_src'  =>  array('bg_btn' => $_POST['btn_backgroun_img_upload_url'], 'logo_btn' => $_POST['btn_logo_img_upload_url'], 'more_text_btn' => $_POST['btn_more_text_img_upload_url'], 'save_btn' => $_POST['btn_save_img_upload_url'], 'add_to_cart_btn' => $_POST['btn_add_to_cart_img_upload_url']),
                         'btn_visibility' => array('bg_btn' => $bg_btn, 'logo_btn' => $logo_btn, 'more_text_btn' => $more_text_btn, 'save_btn' => $save_btn)
                                            
    );
    
    update_option( 'wbfd_new_settings_data', $update_settings_data ); 
  }
  
  $get_settings = get_option( 'wbfd_new_settings_data' );
  
 
?>
<form method="post" action="admin.php?page=wbfd-btn-upload" name="logo" enctype="multipart/form-data">
  <div class="wbfd-settings-main">
    <h3><?php echo _e('Upload Button Image', 'prowbfd');?></h3><hr>
    <table>
      <tr>
        <td>
          <?php echo _e('Upload Background Button', 'prowbfd')?>:
        </td>
        <td><input type="text" readonly="true" name="btn_backgroun_img_upload_url" id="btn_background_img_upload_url"  style="width:100%;" value="<?php echo $get_settings['btn_src']['bg_btn'];?>" /></td>  
        <td><input id="btn_background_img_uploader" name="btn_background_img_uploader" class="button-primary btn_background" style="" type="button" value="Browse"/></td>
        <td><img src="<?php echo $get_settings['btn_src']['bg_btn'];?>"/></td>
      </tr>
      <tr>
        <td>
          <?php echo _e('Upload Logo Button', 'prowbfd');?>:
        </td>
        <td><input type="text" readonly="true" name="btn_logo_img_upload_url" id="btn_logo_img_upload_url"  style="width:100%;" value="<?php echo $get_settings['btn_src']['logo_btn'];?>" /></td>  
        <td><input id="btn_logo_img_uploader" name="btn_logo_img_uploader" class="button-primary btn_logo" style="" type="button" value="Browse"/></td>
        <td><img src="<?php echo $get_settings['btn_src']['logo_btn'];?>"/></td>
      </tr>
      <tr>
        <td>
          <?php echo _e('Upload More Text Button', 'prowbfd');?>:
        </td>
        <td><input type="text" readonly="true" name="btn_more_text_img_upload_url" id="btn_more_text_img_upload_url"  style="width:100%;" value="<?php echo $get_settings['btn_src']['more_text_btn'];?>" /></td>  
        <td><input id="btn_more_text_img_uploader" name="btn_more_text_img_uploader" class="button-primary btn_more_text" style="" type="button" value="Browse"/></td>
        <td><img src="<?php echo $get_settings['btn_src']['more_text_btn'];?>"/></td>
      </tr>
      <tr>
        <td>
          <?php echo _e('Upload Save Button', 'prowbfd');?>:
        </td>
        <td><input type="text" readonly="true" name="btn_save_img_upload_url" id="btn_save_img_upload_url"  style="width:100%;" value="<?php echo $get_settings['btn_src']['save_btn'];?>" /></td>  
        <td><input id="btn_save_img_uploader" name="btn_save_img_uploader" class="button-primary btn_save" style="" type="button" value="Browse"/></td>
        <td><img src="<?php echo $get_settings['btn_src']['save_btn'];?>"/></td>
      </tr>
      <tr>
        <td>
          <?php echo _e('Upload Add To Cart Button', 'prowbfd');?>:
        </td>
        <td><input type="text" readonly="true" name="btn_add_to_cart_img_upload_url" id="btn_add_to_cart_img_upload_url"  style="width:100%;" value="<?php echo $get_settings['btn_src']['add_to_cart_btn'];?>" /></td>  
        <td><input id="btn_add_to_cart_img_uploader" name="btn_add_to_cart_img_uploader" class="button-primary btn_add_to_cart" style="" type="button" value="Browse"/></td>
        <td><img src="<?php echo $get_settings['btn_src']['add_to_cart_btn'];?>"/></td>
      </tr>
    </table>

    <br>
    <h3><?php echo _e('Button Visibility', 'prowbfd');?></h3><hr>
    <table>
      <tr>
        <td><?php echo _e('Enable/Disable Background Upload Button', 'prowbfd');?>:</td>
        <?php if($get_settings['btn_visibility']['bg_btn'] == 1) {?>
        <td><input type="checkbox" checked="checked" name="enable_disable_bg_btn" id="enable_disable_bg_btn" value="enable"></td>
        <?php } else {?>
        <td><input type="checkbox" name="enable_disable_bg_btn" id="enable_disable_bg_btn" value="enable"></td>
        <?php }?>
      </tr>
      <tr>
        <td><?php echo _e('Enable/Disable Logo Upload Button', 'prowbfd');?>:</td>
        <?php if($get_settings['btn_visibility']['logo_btn'] == 1) {?>
         <td><input type="checkbox" checked="checked" name="enable_disable_logo_btn" id="enable_disable_logo_btn" value="enable"></td>
        <?php } else {?>
         <td><input type="checkbox" name="enable_disable_logo_btn" id="enable_disable_logo_btn" value="enable"></td>
        <?php }?>
      </tr>
      <tr>
        <td><?php echo _e('Enable/Disable More Text Button', 'prowbfd');?>:</td>
        <?php if($get_settings['btn_visibility']['more_text_btn'] == 1) {?>
         <td><input type="checkbox" checked="checked" name="enable_disable_more_text_btn" id="enable_disable_more_text_btn" value="enable"></td>
        <?php } else {?>
         <td><input type="checkbox" name="enable_disable_more_text_btn" id="enable_disable_more_text_btn" value="enable"></td>
        <?php }?>
      </tr>
      <tr>
        <td><?php echo _e('Enable/Disable Save Button', 'prowbfd');?>:</td>
        <?php if($get_settings['btn_visibility']['save_btn'] == 1) {?>
         <td><input type="checkbox" checked="checked" name="enable_disable_save_btn" id="enable_disable_save_btn" value="enable"></td>
        <?php } else {?>
         <td><input type="checkbox" name="enable_disable_save_btn" id="enable_disable_save_btn" value="enable"></td>
        <?php }?>
      </tr>
      <tr>
        <td><input  name="update_wbfd_settings_btn" class="button-primary" type="submit" value="Update Settings"/></td>
      </tr>
    </table>
  </div>
</form>  
<?php
}
?>