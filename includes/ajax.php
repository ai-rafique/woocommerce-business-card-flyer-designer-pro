<?php
function wbfd_design_save(){
  $customData = $_REQUEST['jsonData'];
  $product_id = $_REQUEST['jsonData']['productId'];
  
  if( $customData ){
    foreach ( $customData as $key=>$value ){
      if( isset($value['imgid']) && $value['imgid'] == 1){
        update_post_meta($product_id,  '_wbfd_front_design', addslashes($value['customdata']));
      }
      if( isset($value['imgid']) && $value['imgid'] == 2){
        update_post_meta($product_id,  '_wbfd_back_design', addslashes($value['customdata']));
      }
    }
  }
  echo "added";
  exit;
}

function wbfd_get_variations_price_by_id(){
  $json_data = wbfd_get_price($_REQUEST['strAttrName'], $_REQUEST['strAttrVal'], $_REQUEST['id'], 'variations');
  echo $json_data;
  exit;
}

function wbfd_get_price($attr_name, $attr_val, $id, $type){
  $attrNameArray = array();
  $requestAttrVal = array();
  $nstr_array = array();
  $price = 0;
  $parseRequestAttrVal2 = '';
  $attrValNoMatch = 'no_match';
  $get_custom_price = 0;
   
  $attrNameArray = explode( ',', strtolower($attr_name));
  $parseRequestAttrVal = explode( ',', $attr_val);
  
  if( $parseRequestAttrVal ){
    foreach ( $parseRequestAttrVal as $vals ){
      $parseRequestAttrVal2 .= sanitize_title( $vals ) . ',';
    }
  }
  
  $requestAttrVal = explode( ',', strtolower(trim($parseRequestAttrVal2,',')) );
  
  $args = array(
               'post_type'     => 'product_variation',
               'post_status'   => array( 'private', 'publish' ),
               'numberposts'   => -1,
               'orderby'       => 'menu_order',
               'order'         => 'asc',
               'post_parent'   => $id
  );
  $variations = get_posts( $args );
  
  if( $variations ){
    foreach ( $variations as $variation ){ 
      $attrVal = array();
      $attrVals = '';
      $variation_price = 0;
      $variation_data = get_post_meta( $variation->ID );
      
      if( $attrNameArray ){
        foreach ( $attrNameArray as $attr ){
          $name = sanitize_title($attr);
          if( array_key_exists( 'attribute_' .$name, $variation_data )){
            $attrVals .= $variation_data['attribute_' . $name][0] .',';
          }
        }
      }
      
      if( trim($attrVals,',') ){
        $attrVal = explode(',', strtolower(trim($attrVals,',')));
        
        if( !array_diff( $requestAttrVal, $attrVal ) && !array_diff( $attrVal, $requestAttrVal )){
          $attrValNoMatch ='';
          $variation_price = $variation_data['_price'][0];
          break;
        }
//        else
//        {
//          if( in_array(trim($attrVals,','), $requestAttrVal) )
//          {
//            $variation_price = $variation_data['_price'][0];
//            break;
//          }
//        }
      }
    }
  }
  
  $get_custom_price = get_post_meta( $id, '_wbfd_design_custom_price', TRUE );
  
  if( $variations && $variation_price >0){
    $price = $variation_price + $get_custom_price; 
  }
  else{
    $price = $get_custom_price;
  }
  
  $nDataObj = new stdClass();
  $nDataObj->price = $price;
  $nDataObj->match_status = $attrValNoMatch;

  $nstr_array[] = $nDataObj;
  
  if( $type == 'fromAddToCart'){
    return $price;
  }
  else{
    return json_encode( $nstr_array );
  }
}

function wbfd_add_to_cart(){
  global $wpdb,$woocommerce;
  
  $final_product_id = '';
  $final_custom_variations_id = '';
  $finalImgUrl = '';
  $uIds = uniqid().rand(0,10);
  $upload_dir = wp_upload_dir();
  $path = $upload_dir['path'].'/';
  $url = $upload_dir['url'].'/';
  $img_str = '';
  $price = 0;
 
  $customScreenShot = $_REQUEST['data']['customData'];
    
  if( $customScreenShot ){
    foreach ( $customScreenShot as $key=>$screenshot ){
      $img_name = time().uniqid().rand(0,10);
      $decodedImageData = base64_decode( str_replace( 'data:image/png;base64,', '', $screenshot['screenshot'] ) );

      if( file_put_contents( $path . $img_name . ".png", $decodedImageData ) ){
        $finalImgUrl = str_replace( $upload_dir['baseurl'],'', $url. $img_name. ".png" );
        $img_str .= $finalImgUrl .',';
      }
    }
  }
  
  if(isset($_REQUEST['data']['attrName']) && isset($_REQUEST['data']['attrValue'])){
    $price = wbfd_get_price( $_REQUEST['data']['attrName'], $_REQUEST['data']['attrValue'], $_REQUEST['data']['productID'], 'fromAddToCart' );
  }
  else{
    $price = get_post_meta( $_REQUEST['data']['productID'], '_wbfd_design_custom_price', TRUE ) + get_post_meta($_REQUEST['data']['productID'], '_price', TRUE);
  }
  
  //custom variation
  //$sqls="select id from ".$wpdb->prefix."posts where post_parent='".$_REQUEST['data']['productID']."' and post_title='wbfd_custom_variation'";
		$sqls = "select id from ".$wpdb->prefix."posts where post_parent=". $_REQUEST['data']['productID'] ." and post_name='" . 'product-'.$_REQUEST['data']['productID'].'-variation' ."'";
  $get_id=$wpdb->get_row($sqls);

  if( empty( $get_id ) ){
    $post_data=array(
                    'post_author'=>1,
                    'post_status'=>'publish',
                    'post_name'=>'product-'.$_REQUEST['data']['productID'].'-variation',
                    'post_parent'=>$_REQUEST['data']['productID'],
                    'post_title'=>'wbfd_custom_variation', 
                    'post_type'=>'product_variation'
    );
    
    $post_id = wp_insert_post( $post_data );
    $final_product_id = $_REQUEST['data']['productID'];
    $final_custom_variations_id = $post_id;
    update_post_meta( $post_id, '_price', $price);
				update_post_meta( $post_id, '_regular_price', $price);
				update_post_meta( $post_id, '_sale_price', $price);
    update_post_meta( $post_id, 'attribute_scwbfd-variation', 'scwbfd-variation' );
  }
  else{
    $final_product_id = $_REQUEST['data']['productID'];
    $final_custom_variations_id = $get_id->id;
    update_post_meta( $final_custom_variations_id, '_price',  $price);
				update_post_meta( $final_custom_variations_id, '_regular_price', $price);
				update_post_meta( $final_custom_variations_id, '_sale_price', $price);
    update_post_meta( $final_custom_variations_id, 'attribute_scwbfd-variation', 'scwbfd-variation' );
  }
 
  //add to cart
  $arr = array();
  $arr['design'] = 'custom-type_' . $uIds;
  $arr['Type'] = 'custom';
  
  if( isset($_REQUEST['data']['attrName']) && isset($_REQUEST['data']['attrValue']) ){
    $attr_data = explode( ',', $_REQUEST['data']['attrName'] );	
    $attr_val =  explode( ',', $_REQUEST['data']['attrValue'] );

    foreach( $attr_data as $key => $value ){
      $arr[sanitize_title($value)] = $attr_val[sanitize_title($key)];
    }
  }
  
  $arr['wbfd_custom_price'] = $price;
  
  if($woocommerce->cart->add_to_cart( $final_product_id, $_REQUEST['data']['qty'], $final_custom_variations_id, $arr, null ) ){
    $save_custom_data = array(
                              'design_url'=>  $img_str,
                              'logo_url'=>    $_REQUEST['data']['logo'], 
                              'bg_url'=>      $_REQUEST['data']['bg'],
                              'uniqid' =>     $uIds,
    );

    $id = $wpdb->insert($wpdb->prefix."wbfd_custom_data_save", $save_custom_data);
    
    if($id == 1){
      echo 'cart_updated';
      if(!isset($_SESSION['wbfd_cart_id'])){
        $_SESSION['wbfd_cart_id'] = $uIds;
      }
    }
  }
  
  exit;
}

function wbfd_svg_data(){
  $uIds = '';
  $upload_dir = wp_upload_dir();
  
  if(isset($_SESSION['wbfd_cart_id'])){
    $uIds = $_SESSION['wbfd_cart_id'];
  }
  
  $getSVGData = $_REQUEST['svg'];
  
  if (!file_exists(WVSD_PATH. "/svg-output/" . $uIds)) {
    mkdir(WVSD_PATH. "/svg-output/" . $uIds, 0777, true);
  }
  
  if($getSVGData){
    foreach ($getSVGData as $data){
      $newID = uniqid().rand(0,10);
      
      if (!file_exists(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID)){
        mkdir(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID, 0777, true);
      }
      $img_name = time().uniqid().rand(0,10);
      
      $content = stripslashes( $data );
      $fp = fopen(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID ."/". $img_name .".svg","wb") or die("Unable to open file!");
      fwrite($fp, $content);
      
      $xdoc = new DOMDocument();
      $xdoc->load( WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID ."/". $img_name .".svg" );
      $imgCount = $xdoc->getelementsbytagname('image');
      
      if($imgCount){
        if($imgCount->length>0){
          for($i = 0; $i< $imgCount->length; $i++){
            $tagname = $xdoc->getelementsbytagname('image')->item($i);
            $attribNode = $tagname->getAttributeNode('xlink:href');
            $parse_url = str_replace( $upload_dir['baseurl'], '', $attribNode->value );
           
            $img_content = file_get_contents( $upload_dir['basedir'].$parse_url );
            
            $fp = fopen(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID ."/".  basename($attribNode->value),"wb") or die("Unable to open file!");
            fwrite($fp, $img_content);

            $tagname->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', basename($attribNode->value));
            $newFileContent = $xdoc->saveXML();
            $fp = fopen(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID ."/". $img_name .".svg","wb") or die("Unable to open file!");
            fwrite($fp, $newFileContent);
          }
        }
      }
     
      $textCount = $xdoc->getelementsbytagname('text');
      
      if($textCount){
        if($textCount->length>0){
          for($j = 0; $j<$textCount->length; $j++){
            $tagnameText = $xdoc->getelementsbytagname('text')->item($j);
            $attribNodeText = $tagnameText->getAttributeNode('font-family');
            if($attribNodeText){
              if($attribNodeText->value){
                $fontName = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $attribNodeText->value);
                 
                if(file_exists(WVSD_PATH. "fonts/". ucfirst($fontName). ".ttf")){
                  $content_font = file_get_contents( WVSD_PATH. "fonts/". ucfirst($fontName). ".ttf");
                  $fp = fopen(WVSD_PATH. "/svg-output/" . $uIds.'/'.$newID ."/". $fontName. ".ttf","wb") or die("Unable to open file!");
                  fwrite($fp, $content_font);
                }
              }
            }
          }
        }
      }
      fclose($fp);
    }
  }
  
  $the_folder = WVSD_PATH. "/svg-output/" . $uIds;
  $zip_file_name = WVSD_PATH. "/svg-output/" . $uIds . '.zip';
  $za = new FlxZipArchive;

  $res = $za->open($zip_file_name, ZipArchive::CREATE);

  if($res === TRUE) {
    $za->addDir($the_folder, basename($the_folder));
    $za->close();
    recurseRmdir( $the_folder );
  }
  
  if(isset($_SESSION['wbfd_cart_id'])){
    unset($_SESSION['wbfd_cart_id']);
    echo 'svg_added';
  }
  exit;
}

function recurseRmdir($directory, $empty=FALSE){
 if(substr($directory,-1) == '/'){
   $directory = substr($directory,0,-1);
 }
 if(!file_exists($directory) || !is_dir($directory)){
     return FALSE;
 }
 elseif(is_readable($directory)){
    $handle = opendir($directory);
     while (FALSE !== ($item = readdir($handle))){
         if($item != '.' && $item != '..'){
             $path = $directory.'/'.$item;
             if(is_dir($path)) {
                 recurseRmdir($path);
             }else{
                 unlink($path);
             }
         }
     }
     closedir($handle);
     if($empty == FALSE){
         if(!rmdir($directory)){
             return FALSE;
         }
     }
 }
 return TRUE;
}

function wbfd_save_design_by_user_id(){
  global $wpdb;
  $uniqid = uniqid().rand(0,1000);
  $front_design = '';
  $back_design = '';
    
  $customJson = $_REQUEST['data']['customData'];

  if( $customJson ){
    foreach ( $customJson as $key=>$json ){
      if($key == 1){
        $front_design = stripslashes($json['customdata']);
      }
      elseif($key == 2){
        $back_design = stripslashes($json['customdata']);
      }
    }
  }
    
  if(is_user_logged_in() && !is_super_admin()){
    $user_ID = get_current_user_id();
    $saveUserDataArray = array(
                              'design_front'  =>        $front_design,
                              'design_back'  =>         $back_design,
                              'logo_url'  =>            $_REQUEST['data']['logo'],
                              'bg_url'  =>              $_REQUEST['data']['bg'],
                              'product_id'  =>          $_REQUEST['data']['productID'],
                              'login_user_id'  =>       $user_ID,
    );
    
    if(isset($_REQUEST['data']['designID']) && empty($_REQUEST['data']['designID'])){
      if($wpdb->insert($wpdb->prefix."wbfd_design_save_by_user", $saveUserDataArray)){
        echo 'save_by_user';
      }
    }
    else{
      if($wpdb->update( $wpdb->prefix."wbfd_design_save_by_user", $saveUserDataArray, array( 'id' => $_REQUEST['data']['designID']))){
        echo 'update_by_user';
      }
    }
  }
  elseif (is_user_logged_in() && is_super_admin()){
    echo 'supur_admin_login';
  }
  elseif (!is_user_logged_in()){
    $loginUrl = get_permalink(get_option('woocommerce_myaccount_page_id'));
    
    $tempUserDataArray=array(
                            'design_front'  => $front_design,
                            'design_back'  =>  $back_design,
                            'logo_url'  =>     $_REQUEST['data']['logo'],
                            'bg_url'  =>       $_REQUEST['data']['bg'],
                            'uniqid'  =>       $uniqid,
    );
    
    if($wpdb->insert($wpdb->prefix."wbfd_temp_design_save_by_user", $tempUserDataArray)){
						
      if(isset($_SESSION['wbfd_save_id']) && isset($_SESSION['wbfd_timeout'])){
          unset($_SESSION['wbfd_save_id']);
          unset($_SESSION['wbfd_product_id']);
          unset($_SESSION['wbfd_timeout']);
          
          $_SESSION['wbfd_save_id'] = $uniqid;
          $_SESSION['wbfd_product_id'] = $_REQUEST['data']['productID'];
          $_SESSION['wbfd_timeout'] = time()+(60*30);
      }
      else{
          $_SESSION['wbfd_save_id'] = $uniqid;
          $_SESSION['wbfd_product_id'] = $_REQUEST['data']['productID'];
          $_SESSION['wbfd_timeout'] = time()+(60*30);
      }
      
      echo $loginUrl;
    }
  }
  exit;
}

function wbfd_check_is_design_html_by_user(){
		if(is_user_logged_in() && !is_super_admin()){
				global $wpdb;
				$current_user_id = get_current_user_id();
				$total = $wpdb->get_var("SELECT COUNT('id') FROM {$wpdb->prefix}wbfd_design_save_by_user where login_user_id='". $current_user_id ."'");
				echo $total;
		}
			exit;
}

add_action( 'wp_ajax_nopriv_wbfd_design_save','wbfd_design_save' );
add_action( 'wp_ajax_wbfd_design_save', 'wbfd_design_save' );
add_action( 'wp_ajax_nopriv_wbfd_get_variations_price_by_id','wbfd_get_variations_price_by_id' );
add_action( 'wp_ajax_wbfd_get_variations_price_by_id', 'wbfd_get_variations_price_by_id' );
add_action( 'wp_ajax_nopriv_wbfd_add_to_cart','wbfd_add_to_cart' );
add_action( 'wp_ajax_wbfd_add_to_cart', 'wbfd_add_to_cart' );
add_action( 'wp_ajax_nopriv_wbfd_svg_data','wbfd_svg_data' );
add_action( 'wp_ajax_wbfd_svg_data', 'wbfd_svg_data' );
add_action( 'wp_ajax_wbfd_save_design_by_user_id', 'wbfd_save_design_by_user_id' );
add_action( 'wp_ajax_nopriv_wbfd_save_design_by_user_id','wbfd_save_design_by_user_id' );
add_action( 'wp_ajax_nopriv_wbfd_check_is_design_html_by_user','wbfd_check_is_design_html_by_user' );
add_action( 'wp_ajax_wbfd_check_is_design_html_by_user', 'wbfd_check_is_design_html_by_user' );
?>