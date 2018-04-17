<?php
if( isset($_GET['page']) && $_GET['page'] === 'wbfd_sc_designer_design_list' && isset( $_GET['user'] )){
		require_once('../../../../wp-load.php');
  get_header();
  
  if(is_user_logged_in() && !is_super_admin()){
    global $wpdb;
    $sql = "select * from ".$wpdb->prefix."wbfd_design_save_by_user where login_user_id='".$_REQUEST['user']."'";
    $result = $wpdb->get_results($sql);
    $user_info = get_userdata($_REQUEST['user']);
    $i=1;
?>
   
<table style="width:60%;margin:150px;">
    <tr>
        <th><?php echo _e('SN', 'prowbfd');?></th>
        <th><?php echo _e('User Name', 'prowbfd') ;?></th>
    </tr>
        <?php foreach ($result as $rows){$edit_url = esc_url( add_query_arg( 'design_id', $rows->id, get_permalink( $rows->product_id )));?>
    <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo $user_info->display_name;?></td>
        <td class="edit_button"><a  href="<?php echo WVSD_URL;?>/includes/template.php?page=wbfd_design_page&type=wbfd_design&design_id=<?php echo $rows->id;?>&product_id=<?php echo $rows->product_id;?>"><?php echo _e('Edit', 'prowbfd');?></a></td>
    </tr>
        <?php $i++; }?>
</table>
    
<?php 
  }
  else{
      echo __('You do not have sufficient permissions to access this page.', 'prowbfd');
  }
  get_footer();
}  
?>