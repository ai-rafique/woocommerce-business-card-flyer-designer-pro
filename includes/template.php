<?php 



if( isset($_GET['page']) && isset( $_GET['product_id']) && $_GET['page'] == 'wbfd_design_page'  && $_GET['type'] == 'wbfd_design'){

require_once('../../../../wp-load.php');

header('HTTP/1.1 200 OK');

get_header();

		

global $post, $product, $woocommerce;

$product_id = $_REQUEST['product_id'];



$hasCustomization =     get_post_meta( $product_id, '_wbfd_designer_enable_checkbox_on_metabox', true );

$get_custon_width=      esc_html( get_post_meta($product_id, '_wbfd_design_width', true ));

$get_custon_height=     esc_html( get_post_meta($product_id, '_wbfd_design_height', true ));



if( $product_id && $hasCustomization == 'on' ){

  $is_url_from_edit_panel = 0;

  //load fonts

  $dir = WVSD_PATH.'fonts/';

  $fontArray = array();

  $strFont = '';

  $i = 0;



  if (is_dir($dir)) {

    if ($dh = opendir($dir)) {

      while (($file = readdir($dh)) !== false) {

        if (!in_array($file, array('.', '..'))){

          $fontArray[$i]= $file;

          $parseExtraFont=  explode('.ttf', strtolower($file));

          $strFont .=$parseExtraFont[0] .',';

          $i++;

        }

      }

      closedir($dh);

    }

  }



  $jsonStrFront = html_entity_decode(stripcslashes(get_post_meta($product_id,  '_wbfd_front_design', TRUE)));

  $jsonStrBack =  html_entity_decode(stripcslashes(get_post_meta($product_id,  '_wbfd_back_design', TRUE)));

  

  $price = 0;

  $get_variations_data = get_post_meta( $product_id, '_product_attributes',true );

  

  if($get_variations_data){

    $price = get_post_meta( $product_id, '_wbfd_design_custom_price', TRUE );

  }

  else{

    $price = get_post_meta( $product_id, '_price', TRUE ) + get_post_meta( $product_id, '_wbfd_design_custom_price', TRUE );

  }

  

  $upload_url = wp_upload_dir();

  

  global $wpdb;

  $logo_url = '';

  $bg_url = '';

  

  if (isset($_SESSION['wbfd_save_id']) && isset($_SESSION['wbfd_product_id']) && isset($_SESSION['wbfd_timeout']) && !isset($_REQUEST['design_id'])){

    $entries = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wbfd_temp_design_save_by_user WHERE uniqid = '". $_SESSION['wbfd_save_id'] ."'" );

    

    $jsonStrFront =   $entries->design_front;

    $jsonStrBack =    $entries->design_back;

    $logo_url =       $entries->logo_url;

    $bg_url =         $entries->bg_url;

    

    unset($_SESSION['wbfd_save_id']);

    unset($_SESSION['wbfd_product_id']);

    unset($_SESSION['wbfd_timeout']);

  }

  else if(!isset($_SESSION['wbfd_save_id']) && !isset($_SESSION['wbfd_product_id']) && !isset($_SESSION['wbfd_timeout']) && isset($_REQUEST['design_id'])){

    

    $entries = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wbfd_design_save_by_user WHERE id = '". $_REQUEST['design_id'] ."'" );

    

    $jsonStrFront =   $entries->design_front;

    $jsonStrBack =    $entries->design_back;

    $logo_url =       $entries->logo_url;

    $bg_url =         $entries->bg_url;

    

    $is_url_from_edit_panel = $_REQUEST['design_id'];

  }

  

  $get_front_bg_img =           esc_html( get_post_meta($product_id, '_wbfd_front_background_image', true ) );

  $get_back_bg_img =            esc_html( get_post_meta($product_id, '_wbfd_back_background_image', true ) );



  $front_display_mode = 'style="display:none;"'; 

  $back_display_mode  = 'style="display:none;"'; 



  if($get_front_bg_img){

    $front_display_mode = 'style="display:inline-block;"'; 

  }



  if($get_back_bg_img){

    $back_display_mode = 'style="display:inline-block;"'; 

  }



$get_settings = get_option( 'wbfd_new_settings_data' );



?>

<style type="text/css">

  body

  {

    font-family: "Open Sans",sans-serif;

    font-size: 13px;

  }

  input

  {

    border:1px solid #e1e1e1 !important;

    padding: 0px !important;

  }

  <?php foreach ($fontArray as $file){ $parseFont=  explode('.ttf', strtolower($file));?>

  @font-face 

  {

      font-family:<?php echo $parseFont[0];?>; src: url('<?php echo WVSD_URL;?>/fonts/<?php echo $file?>');

  }

  <?php }?>

  .content-area

  {

    width:100% !important;

  }

  

  .wbfd_bg_uploader{

    background: url("<?php echo $get_settings['btn_src']['bg_btn'];?>") no-repeat !important;

  }

  

  .wbfd_logo_uploader{

    background: url("<?php echo $get_settings['btn_src']['logo_btn'];?>") no-repeat !important;

  }

  

  .wbfd_add_more_text_frontend{

    background: url("<?php echo $get_settings['btn_src']['more_text_btn'];?>") no-repeat !important;

  }

  

  .wbfd_save_by_user{

    background: url("<?php echo $get_settings['btn_src']['save_btn'];?>") no-repeat !important;

  }

  

  .wbfd_design_add_to_cart{

    background: url("<?php echo $get_settings['btn_src']['add_to_cart_btn'];?>") no-repeat !important;

  }

</style>



<div id="wbfd_card_designer_pro" class="wbfd_designer_container">

  <div class="wbfd_designer_sub_container">

    <div class="wbfd_div_1_frontend">

      <div class="side_name">

          <h3 class="show_front_title"><?php echo _e('Customize Your Product', 'prowbfd');?></h3>

          <h3 class="show_back_title"><?php echo _e('Customize Your Product', 'prowbfd');?></h3>

      </div>
      <div class="canvas_container" style="width:<?php echo $get_custon_width + 5;?>px; height:<?php echo $get_custon_height + 5;?>px;">

        <div class="canvas-main" style="width:<?php echo $get_custon_width;?>px; height:<?php echo $get_custon_height;?>px;"><canvas id="bg_image" width="<?php echo $get_custon_width;?>" height="<?php echo $get_custon_height;?>" style="border:1px solid #e1e1e1;"></canvas></div>

      </div>

    </div>

    <div class="wbfd_div_2_frontend">

       <div class="rl_bg_control_panel">
      <div class="wbfd_bg_icon_area">

        <div class="wbfd_loading"></div>

        <div class="wbfd_front_bg" data-id="1" <?php echo $front_display_mode;?>><img id=""></div>

        <div class="wbfd_back_bg" data-id="2" <?php echo $back_display_mode;?>><img id=""></div>

      </div>

       <?php if($get_settings['btn_visibility']['save_btn'] == 1){?>

        <div class="wbfd_save_by_user rl_save_button"></div>

         <?php }?>
      <div class="wbfd_clear"></div>

      <div class="wbfd_image_control image-control-frontend">

        <label><?php echo _e('Image Control', 'prowbfd');?></label>

        <div class="imgae-control-list">

          <ul>

            <li>

              <div class="wbfd_object_bring_to_front" title="<?php echo _e('bring to front', 'prowbfd');?>"></div>

            </li>

            <li>

              <div class="wbfd_object_send_to_back" title="<?php echo _e('send to back', 'prowbfd');?>"></div>

            </li>

            <li>

              <div class="wbfd_logo_remove" title="<?php echo _e('remove item', 'prowbfd');?>"></div>

            </li>

          </ul>  

        </div>  

      </div>

      </div>

      <div class="wbfd_top_left rl_imagepanel_upload">

        <?php if($get_settings['btn_visibility']['bg_btn'] == 1){?>

        <div class="wbfd_custom_bg_upload">

          <div class="wbfd_bg_uploader"></div>

  <!--        <div class="wbfd_bg_front"><input type="radio" name="bg_type" checked="checked" id="bg_front_type" value="front">&nbsp;Front</div>

          <div class="wbfd_bg_back"><input type="radio" name="bg_type" id="bg_back_type" value="back">&nbsp;Back</div>-->

        </div> 

        <?php }?>

        

        <?php if($get_settings['btn_visibility']['logo_btn'] == 1){?>

        <div class="wbfd_custom_logo_upload">

          <div class="wbfd_logo_uploader"></div>

          <div class="wbfd_loading_logo"></div>

  <!--        <div class="wbfd_undo_design"></div>

          <div class="wbfd_reset_design"></div>-->

        </div>

        <?php }?>

      </div>  

      <div class="side_name">

          <h3 class="show_front_title"><?php echo _e('Customize Text ( Front Side )', 'prowbfd');?></h3>

          <h3 class="show_back_title"><?php echo _e('Customize Text ( Back Side )', 'prowbfd');?></h3>

      </div>    

      <div class="wbfd_text_area">

        <div class="wbfd_control_panel front_side" data-id="1">

            <div class="wbfd_all_control">

              <div class="wbfd_text_size_control">

                  <div class="wbfd_text_increase" title="<?php echo _e('font size increase', 'prowbfd');?>"></div>

                  <div class="wbfd_text_deincrease" title="<?php echo _e('font size decrease', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_align_control">

                  

                  <div class="wbfd_text_align_left" title="<?php echo _e('align left', 'prowbfd');?>"></div>

                  <div class="wbfd_text_align_center" title="<?php echo _e('align center', 'prowbfd');?>"></div>

                  <div class="wbfd_text_align_right" title="<?php echo _e('align right', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_color_control">

                  <div title="<?php echo _e('text color', 'prowbfd');?>"><input type="text" name="txt_color" id="txt_color" class="txt_color color"></div>

              </div>

              <div class="wbfd_text_font_control">

                

                <div title="<?php echo _e('text font', 'prowbfd');?>"><select name="wbfd_change_font_name" id="wbfd_change_font_name_1" class="wbfd_change_font_name">

                  <?php 

                  foreach ($fontArray as $fontName){

                    $parseFontName=  explode('.ttf', strtolower($fontName));

                  ?>

                    <option style="font-family:<?php echo $parseFontName[0];?>;" value="<?php echo $parseFontName[0];?>"><?php echo ucfirst($parseFontName[0]);?></option>

                  <?php }?>

                </select>

                </div>

              </div>

              <div class="wbfd_text_style_control">

                

                  <div class="wbfd_text_style_bold" title="<?php echo _e('text bold', 'prowbfd');?>"></div>

                  <div class="wbfd_text_style_italic" title="<?php echo _e('text italic', 'prowbfd');?>"></div>

                  <div class="wbfd_text_style_underline" title="<?php echo _e('text underline', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_remove">

                

                <div class="wbfd_text_remove_icon" title="<?php echo _e('text remove', 'prowbfd');?>"></div>

              </div>

           </div>        

          <div class="wbfd_textarea_box">

              <textarea id="custom_text_1" class="dynamic_text" name="custom_text" placeholder="<?php echo _e('Enter Your Text', 'prowbfd');?>"></textarea>

          </div>   

        </div>

        <div class="wbfd_control_panel back_side" data-id="2">

            <div class="wbfd_all_control">

              <div class="wbfd_text_size_control">

                

                  <div class="wbfd_text_increase" title="<?php echo _e('font size increase', 'prowbfd');?>"></div>

                  <div class="wbfd_text_deincrease" title="<?php echo _e('font size decrease', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_align_control">

                

                  <div class="wbfd_text_align_left" title="<?php echo _e('align left', 'prowbfd');?>"></div>

                  <div class="wbfd_text_align_center" title="<?php echo _e('align center', 'prowbfd');?>"></div>

                  <div class="wbfd_text_align_right" title="<?php echo _e('align right', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_color_control">

                  

                      <div title="<?php echo _e('text color', 'prowbfd');?>"><input type="text" name="txt_color" id="txt_color" class="txt_color color"></div>

              </div>

              <div class="wbfd_text_font_control">

                

                <div title="<?php echo _e('text font', 'prowbfd');?>"><select name="wbfd_change_font_name" id="wbfd_change_font_name_2" class="wbfd_change_font_name">

                  <?php 

                  foreach ($fontArray as $fontName){

                    $parseFontName=  explode('.ttf', strtolower($fontName));

                  ?>

                    <option style="font-family:<?php echo $parseFontName[0];?>;" value="<?php echo $parseFontName[0];?>"><?php echo ucfirst($parseFontName[0]);?></option>

                  <?php }?>

                </select>

                  </div>

              </div>

              <div class="wbfd_text_style_control">

                

                  <div class="wbfd_text_style_bold" title="<?php echo _e('text bold', 'prowbfd');?>"></div>

                  <div class="wbfd_text_style_italic" title="<?php echo _e('text italic', 'prowbfd');?>"></div>

                  <div class="wbfd_text_style_underline" title="<?php echo _e('text underline', 'prowbfd');?>"></div>

              </div>

              <div class="wbfd_text_remove">

               

                  <div class="wbfd_text_remove_icon" title="<?php echo _e('text remove', 'prowbfd');?>"></div>

              </div>

           </div>        

          <div class="wbfd_textarea_box">

              <textarea id="custom_text_2" class="dynamic_text" name="custom_text" placeholder="<?php echo _e('Enter Your Text', 'prowbfd');?>"></textarea>

          </div>   

        </div>

      </div>

      

        

      <div class="wbfd_design_add_to_cart_panel">

        <?php if($get_settings['btn_visibility']['more_text_btn'] == 1){?>

        <div class="wbfd_add_more_text_frontend"></div>  

        <?php }?>

        

        <?php if($get_variations_data){?>  

        <div class="wbfd_design_option"></div>

        <?php }?>


        

        <div class="wbfd_design_add_to_cart"></div>

        <div class="wbfd_loading_save_design"></div>

      </div>

      <div class="wbfd_option_content">

        <div class="wbfd_close_popup"></div>

        <div class="wbfd_option_area">

        <?php

        if( $get_variations_data )

        {

        ?>

        <div class="wbfd_option_all">

          <div class="wbfd_option_header"><?php echo _e('Products Options', 'prowbfd');?></div>

            <?php 

            $variations_data_array = array();

            $str_array = array();	

            $attr_name = '';

            $attr_value = '';

            $var_str = '';

            $var_opt_multi_load = '';



            if( $get_variations_data ){

              foreach( $get_variations_data as $attr ){

                $attr_name = $attr['name'];

                $attr_value = $attr['value'];

                $data = new stdClass();

                $data->names = $attr_name;

                $data->values_attr = $attr_value;

                $variations_data_array[] = $data;

              }

            }



            if( $get_variations_data ){

              $i = 0;

              $opt = 1;



              foreach( $variations_data_array as $data ){

                $var_opt = '';

                $var_str .= '<div class="option_'. sanitize_title(strtolower( $data->names )).'">';

                $var_str .= '<div class="wbfd_option_title" data-name="'. strtoupper( $data->names ) .'" id="attr_name_'. $i .'"> <span style="color:#58A1EC;">'. strtoupper( $data->names ).'</span></div>';



                $att_values = $data->values_attr;

                $search_char = strpos( $att_values,'|' );



                if( $search_char ){

                  $parse_search = explode( '|',$att_values );

                  $var_str .= '<div style="padding:8px 0px;width:90px;display:inline-block;" id="attr_value_'. $i .'">';

  //                  echo '<div class="cwd_layer_for_select"></div>';

                  $var_str .= '<select class="wbfd_variation" onchange="wbfd_set_variations_price(this);" id="variation_'. $i .'">';

                  if($opt == 1){

                    $var_opt_multi_load .= '<option value="wbfdSelect">--select--</option>';

                  }

                  $var_opt .= '<option value="wbfdSelect">--select--</option>';

                  foreach( $parse_search as $rows ){

                    $var_opt .= '<option value="'. sanitize_title( $rows ) .'">'. $rows .'</option>';

                    if($opt == 1){

                      $var_opt_multi_load .= '<option value="'. sanitize_title( $rows ) .'">'. $rows .'</option>';

                    }

                  }

                  $var_str .= $var_opt;

                  $var_str .= '</select>';

                  $var_str .= '</div>';

                }

                else{

                  $var_str .= '<div style="padding:10px 0px;" id="attr_value_'. $i .'">';

                  $var_str .= '<select class="wbfd_variation" onchange="wbfd_set_variations_price(this);" id="variation_'. $i .'" style="height:23px;font-size:12px;width:120px;">';

                  $var_str .= '<option value="wbfdSelect">--select--</option>';

                  $var_str .= '<option value="'. sanitize_title( $att_values ) .'">'. $att_values .'</option>';

                  $var_str .= '</select>';

                  $var_str .= '</div>';

                }

                $var_str .= '</div>';

                $i++;

                $opt++;

              }

              echo $var_str;

            }

            ?>

        </div>

        <?php

        }

        ?>
        
        
        </div>


      </div>



    </div>

    <div class="wbfd_qty_price_panel">

          <div class="wbfd_price"><span><?php echo _e('Total Price', 'prowbfd');?>:&nbsp;</span><span class="wbfd_changable_price"><?php echo $price;?></div></span>

          <div class="wbfd_qty"><input type="text" name="wbfd_qty" id="wbfd_qty" value="1" placeholder="quantity"></div><?php echo get_woocommerce_currency_symbol();?>

    </div>

    <div class="wbfd_clear"></div>
     

    <div class="wbfd_msg_content">

        <div class="wbfd_close_msg_content"></div>

        <div class="wbfd_msg_header"><?php echo _e('Login Message', 'prowbfd');?></div>

        <p><?php echo _e('You have logged in as super admin ! . Super admin can not save the design from frontend.', 'prowbfd');?></p>

        <p><?php echo _e('Please login as a frontend user to save the design', 'prowbfd');?></p>

    </div>

    <div class="wbfd_success_msg_content">

        <div class="wbfd_close_msg_content"></div>

        <div class="wbfd_msg_header"><?php echo _e('Success Message', 'prowbfd');?></div>

        <p><?php echo _e('Your design have been successfully saved !', 'prowbfd');?></p>

    </div>

    <div class="wbfd_overlay"></div>

    <input type="hidden" name="plugin_url" id="plugin_url" value="<?php echo WVSD_URL;?>">

    <input type="hidden" name="frontend_track" id="frontend_track" value="fromFrontEnd">

    <input type="hidden" name="admin-ajax" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ); ?>"/>

    <input type="hidden" name="jsonDataForFront" id="jsonDataForFront" value='<?php echo $jsonStrFront;?>'>

    <input type="hidden" name="jsonDataForBack" id="jsonDataForBack" value='<?php echo $jsonStrBack;?>'>

    <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id;?>">

    <input type="hidden" name="wbfd_upload_url" id="wbfd_upload_url" value="<?php echo $upload_url['baseurl'];?>"/>

    <input type="hidden" name="wbfd_cart_url" id="wbfd_cart_url" value="<?php echo $woocommerce->cart->get_cart_url();?>"/>

    

    <input type="hidden" name="wbfd_logo_url" id="wbfd_logo_url" value="<?php echo $logo_url;?>"/>

    <input type="hidden" name="wbfd_bg_url" id="wbfd_bg_url" value="<?php echo $bg_url;?>"/>

    

    <input type="hidden" name="wbfd_is_url_from_edit_panel" id="wbfd_is_url_from_edit_panel" value="<?php echo $is_url_from_edit_panel;?>"/>

    <input type="hidden" name="bg_track" id="bg_track" value="front">

  </div>

</div>

<?php

get_footer();

}

}

?>