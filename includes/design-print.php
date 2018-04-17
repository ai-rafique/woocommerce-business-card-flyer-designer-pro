<?php require_once('../../../../wp-load.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <style type="text/css">
    .wbfd_bg_img img,.wbfd_design_img img{
      width: 400px;
    }
    .wbfd_bg_img,.wbfd_logo_img,.wbfd_design_img{
      display: inline-block;
      margin-right: 20px;
    }
    .wbfd_logo_img img{
      width:100px;
    }
    
  </style>
</head>
  
<body onload="window.focus();window.print();">
<?php 
global $wpdb;
$sqls = "select * from ".$wpdb->prefix ."wbfd_custom_data_save where uniqid='".$_REQUEST['id']."'";
$get_results = $wpdb->get_row($sqls);
$upload_dir = wp_upload_dir();
?>
  
<?php if($_REQUEST['type'] == 'bg' && $get_results->bg_url){
  $parseBG = explode('##', $get_results->bg_url);
  foreach($parseBG as $urlBG){
?>
    <div class="wbfd_bg_img"><img src="<?php echo $upload_dir['baseurl'].$urlBG;?>" alt="bg_img"></img></div>  
<?php }}?>

<?php if($_REQUEST['type'] == 'logo' && $get_results->logo_url){
  $parseLogo = explode('##', $get_results->logo_url);
  foreach($parseLogo as $urlLogo){
?>
    <div class="wbfd_logo_img"><img src="<?php echo $upload_dir['baseurl'].$urlLogo;?>" alt="logo_img"></img></div>  
<?php }}?>    
    
<?php if($_REQUEST['type'] == 'design' && $get_results->design_url){
  $parseDesign = explode(',', $get_results->design_url);
  foreach($parseDesign as $urlDesign){
    if($urlDesign){
?>
    <div class="wbfd_design_img"><img src="<?php echo $upload_dir['baseurl'].$urlDesign;?>" alt="design_img"></img></div>  
    <?php }}}?>        
</body>
</html>