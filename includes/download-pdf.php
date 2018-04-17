<?php 
		require_once('../../../../wp-load.php');
		global $wpdb;
	
		if(!isset($_REQUEST['id'])){
				return false;
		}
		
		$sqls = "select * from ".$wpdb->prefix ."wbfd_custom_data_save where uniqid='". $_REQUEST['id'] ."'";
		$get_results = $wpdb->get_row($sqls);
		
		$upload_dir = wp_upload_dir();
  
		if (!empty($get_results->design_url)) {
				$uIds = uniqid().rand(0,10);
				
				if (!file_exists($upload_dir['path'].'/'.$uIds)) {
						mkdir($upload_dir['path'].'/'.$uIds, 0777, true);
				}
				
				$get_img_url = explode(',',	trim($get_results->design_url, ','));
				
				$count = 1;
				if(count($get_img_url) > 0){
						foreach($get_img_url as $url){
								shell_exec("convert ".$upload_dir['basedir']. $url . ' '.$upload_dir['path'].'/'.$uIds . "/". $count .".pdf");
								
								$count ++ ;
						}
				}
				
				$the_folder = $upload_dir['path'].'/'.$uIds;
				$zip_file_name = $upload_dir['path'].'/'.$uIds . '.zip';
				$za = new FlxZipArchive;

				$res = $za->open($zip_file_name, ZipArchive::CREATE);

				if($res === TRUE) {
						$za->addDir($the_folder, basename($the_folder));
						$za->close();
						recurseRmPdfdir( $the_folder );
				}
				
				header('Content-Description: File Transfer');
				header('Content-Type: image/png');
				header('Content-Disposition: attachment; filename=' . basename($upload_dir['url'].'/'.$uIds. '.zip'));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: public');
				header('Pragma: public');

				readfile( $upload_dir['path']. '/' .$uIds . '.zip' );
				
				if (file_exists($upload_dir['path']. '/' .$uIds . '.zip')) {
						unlink($upload_dir['path']. '/' .$uIds . '.zip'); 
				}
		}
		
function recurseRmPdfdir($directory, $empty=FALSE){
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
?>