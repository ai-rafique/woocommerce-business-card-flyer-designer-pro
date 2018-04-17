<?php	
require_once('../../../../wp-load.php');
$filename = uniqid().'_'.$_FILES["uploadfile"]["name"];
$upload_dir = wp_upload_dir();

if(is_allowed_image($_FILES["uploadfile"]["tmp_name"])){
  if(move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $upload_dir['path'].'/'. $filename)){
    $img_url = $upload_dir['url'].'/'. $filename;
    echo $img_url;
  }	
}
else{
  echo "error";
}

function is_allowed_image($filename){
  $size = getimagesize($filename);

  switch ($size['mime']) {
    case "image/gif":
						return true;
						break;
    case "image/jpeg":
						return true;
						break;
    case "image/png":
						return true;
						break;
    default:
      return false;
  }
} 
?>