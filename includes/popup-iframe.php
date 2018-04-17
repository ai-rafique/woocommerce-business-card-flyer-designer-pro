<?php require_once('../../../../wp-load.php');

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

$jsonStrFront = html_entity_decode(stripcslashes(get_post_meta($_REQUEST['id'],  '_wbfd_front_design', TRUE)));
$jsonStrBack =  html_entity_decode(stripcslashes(get_post_meta($_REQUEST['id'],  '_wbfd_back_design', TRUE)));

$get_front_bg_img =           esc_html( get_post_meta($_REQUEST['id'], '_wbfd_front_background_image', true ) );
$get_back_bg_img =            esc_html( get_post_meta($_REQUEST['id'], '_wbfd_back_background_image', true ) );

$front_display_mode = 'style="display:none;"'; 
$back_display_mode  = 'style="display:none;"'; 

if($get_front_bg_img){
  $front_display_mode = 'style="display:inline-block;"'; 
}

if($get_back_bg_img){
  $back_display_mode = 'style="display:inline-block;"'; 
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo WVSD_URL;?>/includes/js/fabric-1.5.0.min.js"></script>
        <script type="text/javascript" src="<?php echo WVSD_URL;?>/includes/js/customiseControls.min.js"></script>
        <script type="text/javascript" src="<?php echo WVSD_URL;?>/includes/js/ajaxupload.3.5.js"></script>
        <script type="text/javascript" src="<?php echo WVSD_URL;?>/includes/js/jscolor/jscolor.js"></script>
        <script type="text/javascript" src="<?php echo WVSD_URL;?>/includes/js/design.js"></script>
        <link rel="stylesheet" href="<?php echo WVSD_URL;?>/includes/css/stickers-design.css">
        <link rel="stylesheet" href="<?php echo WVSD_URL;?>/includes/css/stickers-design-ui.css">
        <style type="text/css">
          body
          {
              font-family: "Open Sans",sans-serif;
              font-size: 13px;
          }
          <?php foreach ($fontArray as $file){ $parseFont=  explode('.ttf', strtolower($file));?>
          @font-face 
          {
              font-family:<?php echo $parseFont[0];?>; src: url('<?php echo WVSD_URL;?>/fonts/<?php echo $file?>');
          }
          <?php }?>
        </style>
    </head>
    <body>
        <div class="wbfd_div_1">
          <div class="canvas_container" style="width:<?php echo $_REQUEST['width'] + 5;?>px;height:<?php echo $_REQUEST['height'] + 5;?>px;">
            <div class="canvas-main" style="width:<?php echo $_REQUEST['width'];?>px;height:<?php echo $_REQUEST['height'];?>px;"><canvas id="bg_image" width="<?php echo $_REQUEST['width'];?>" height="<?php echo $_REQUEST['height'];?>" style="border:1px solid #dddddd;"></canvas></div>
          </div>
        </div>
        <div class="wbfd_div_2">
          <div class="wbfd_admin_top_left">
            <div class="wbfd_custom_bg_upload">
              <div class="wbfd_admin_bg_uploader"></div>
  <!--            <div class="wbfd_bg_front"><input type="radio" name="bg_type" checked="checked" id="bg_front_type" value="front">&nbsp;Front</div>
              <div class="wbfd_bg_back"><input type="radio" name="bg_type" id="bg_back_type" value="back">&nbsp;Back</div>-->
            </div> 
            <div class="wbfd_custom_logo_upload">
              <div class="wbfd_admin_logo_uploader"></div>
              <div class="wbfd_loading_logo"></div>
  <!--            <div class="wbfd_undo_design"></div>
              <div class="wbfd_reset_design"></div>-->
            </div>
          </div> 
          <div class="wbfd_bg_icon_area">
            <div class="wbfd_loading"></div>
            <div class="wbfd_front_bg" data-id="1" <?php echo $front_display_mode;?>><img id=""></div>
            <div class="wbfd_back_bg" data-id="2" <?php echo $back_display_mode;?>><img id=""></div>
          </div>
          
          <div class="wbfd_clear"></div>
          
          <div class="wbfd_image_control">
            <fieldset>
              <legend><?php echo _e('Image Control', 'prowbfd');?></legend>
              <ul>
                <li>
                  <div class="wbfd_logo_movement_control">
                    <div class="wbfd_logo_lock" style="display:block;" data-target="lock" title="<?php echo _e('image lock', 'prowbfd');?>"></div>
                    <div class="wbfd_logo_unlock" style="display: none;" data-target="unlock" title="<?php echo _e('image unlock', 'prowbfd');?>"></div>
                  </div>
                </li>
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
            </fieldset>       
          </div>
            <div class="side_name">
                <h3 class="show_front_title"><?php echo _e('Customize Text ( Front Side )', 'prowbfd');?></h3>
                <h3 class="show_back_title"><?php echo _e('Customize Text ( Back Side )', 'prowbfd');?></h3>
            </div>  
          <div class="wbfd_text_area">
              <div class="wbfd_control_panel front_side" data-id="1">
                  <div class="wbfd_all_control">
                    <div class="wbfd_text_size_control">
<!--                        <div class="control-label">Size</div>-->
                        <div class="wbfd_text_increase" title="<?php echo _e('font size increase', 'prowbfd');?>"></div>
                        <div class="wbfd_text_deincrease" title="<?php echo _e('font size decrease', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_align_control">
<!--                        <div class="control-label">Alignments</div>-->
                        <div class="wbfd_text_align_left" title="<?php echo _e('align left', 'prowbfd');?>"></div>
                        <div class="wbfd_text_align_center" title="<?php echo _e('align center', 'prowbfd');?>"></div>
                        <div class="wbfd_text_align_right" title="<?php echo _e('align right', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_color_control">
<!--                      <div class="control-label">Color</div>-->
                    <div title="<?php echo _e('text color', 'prowbfd');?>"><input type="text" name="txt_color" id="txt_color" class="txt_color color"></div>
                    </div>
                    <div class="wbfd_text_font_control">
<!--                      <div class="control-label">Fonts</div>-->
                      <div title="<?php echo _e('text font', 'prowbfd');?>"><select name="wbfd_change_font_name" id="wbfd_change_font_name_1" class="wbfd_change_font_name">
                        <?php 
                        foreach ($fontArray as $fontName){
                          $parseFontName=  explode('.ttf', strtolower($fontName));
                        ?>
                          <option style="font-family:<?php echo $parseFontName[0];?>;" value="<?php echo $parseFontName[0];?>"><?php echo ucfirst($parseFontName[0]);?></option>
                        <?php }?>
                      </select></div>
                    </div>
                    <div class="wbfd_text_style_control">
<!--                      <div class="control-label">Style</div>-->
                        <div class="wbfd_text_style_bold" title="<?php echo _e('text bold', 'prowbfd');?>"></div>
                        <div class="wbfd_text_style_italic" title="<?php echo _e('text italic', 'prowbfd');?>"></div>
                        <div class="wbfd_text_style_underline" title="<?php echo _e('text underline', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_movement_control">
<!--                        <div class="control-label"><span class="lock-label">Lock</span><span class="unlock-label">Unlock</span></div>-->
                        <div class="wbfd_text_lock" title="<?php echo _e('text lock', 'prowbfd');?>"></div>
                        <div class="wbfd_text_unlock" title="<?php echo _e('text unlock', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_remove" title="<?php echo _e('text remove', 'prowbfd');?>">
<!--                      <div class="control-label">Remove</div>-->
                       <div class="wbfd_text_remove_icon"></div>
                    </div>
                 </div>        
                <div class="wbfd_textarea_box">
                    <textarea id="custom_text_1" class="dynamic_text" name="custom_text" placeholder="<?php echo _e('Enter Your Text', 'prowbfd');?>"></textarea>
                </div>   
              </div>
              <div class="wbfd_control_panel back_side" data-id="2">
                  <div class="wbfd_all_control">
                    <div class="wbfd_text_size_control">
<!--                      <div class="control-label">Size</div>-->
                        <div class="wbfd_text_increase" title="<?php echo _e('font size increase', 'prowbfd');?>"></div>
                        <div class="wbfd_text_deincrease" title="<?php echo _e('font size decrease', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_align_control">
<!--                      <div class="control-label">Alignments</div>-->
                        <div class="wbfd_text_align_left" title="<?php echo _e('align left', 'prowbfd');?>"></div>
                        <div class="wbfd_text_align_center" title="<?php echo _e('align center', 'prowbfd');?>"></div>
                        <div class="wbfd_text_align_right" title="<?php echo _e('align right', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_color_control">
<!--                      <div class="control-label">Color</div>-->
                      <div title="<?php echo _e('text color', 'prowbfd');?>"><input type="text" name="txt_color" id="txt_color" class="txt_color color"></div>
                    </div>
                    <div class="wbfd_text_font_control">
<!--                      <div class="control-label">Fonts</div>-->
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
<!--                      <div class="control-label">Style</div>-->
                        <div class="wbfd_text_style_bold" title="<?php echo _e('text bold', 'prowbfd');?>"></div>
                        <div class="wbfd_text_style_italic" title="<?php echo _e('text italic', 'prowbfd');?>"></div>
                        <div class="wbfd_text_style_underline" title="<?php echo _e('text underline', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_movement_control">
<!--                      <div class="control-label"><span class="lock-label">Lock</span><span class="unlock-label">Unlock</span></div>-->
                      <div class="wbfd_text_lock" title="<?php echo _e('text lock', 'prowbfd');?>"></div>
                      <div class="wbfd_text_unlock" title="<?php echo _e('text unlock', 'prowbfd');?>"></div>
                    </div>
                    <div class="wbfd_text_remove">
<!--                      <div class="control-label">Remove</div>-->
                        <div class="wbfd_text_remove_icon" title="<?php echo _e('text remove', 'prowbfd');?>"></div>
                    </div>
                 </div>        
                <div class="wbfd_textarea_box">
                    <textarea id="custom_text_2" class="dynamic_text" name="custom_text" placeholder="<?php echo _e('Enter Your Text', 'prowbfd');?>"></textarea>
                </div>   
              </div>
          </div>
          <div class="wbfd_design_save_panel">
            <div class="wbfd_add_more_text"></div>  
            <div class="wbfd_save_design"></div>
            <div class="wbfd_loading_save_design"></div>
          </div>
        </div>
        <div class="wbfd_clear"></div>
        <input type="hidden" name="plugin_url" id="plugin_url" value="<?php echo WVSD_URL;?>">
        <input type="hidden" name="admin_track" id="admin_track" value="fromAdmin">
        <input type="hidden" name="admin-ajax" id="admin-ajax" value="<?php echo admin_url( 'admin-ajax.php' ); ?>"/>
        <input type="hidden" name="jsonDataForFront" id="jsonDataForFront" value='<?php echo $jsonStrFront;?>'>
        <input type="hidden" name="jsonDataForBack" id="jsonDataForBack" value='<?php echo $jsonStrBack;?>'>
        <input type="hidden" name="bg_track" id="bg_track" value="front">
    </body>
</html>