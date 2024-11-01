<?php
/*
 * Plugin Name: SpoofProof
 * Description: Real security for Word Press we stop Spoofing, Phishing, redirection attacks and MItM attacks.
 * Plugin URI: http://ciphertooth.com/spoofproof/
 * Author: CipherTooth Inc.
 * Author URI: http://ciphertooth.com
 * Version: 1.0
 * License: GPL2
 */

defined('ABSPATH') or die('No script kiddies please!');

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

//$URLArray = parse_url ($_SERVER['HTTP_HOST']);
if ($_SERVER['SERVER_NAME'] == 'ciphertooth.dev')
{ define('SERVICE_URL', 'http://ciphertooth.dev/press'); }
else
{ define('SERVICE_URL', 'http://wpsecure.convertabase.net'); }

// See more at: http://findnerd.com/list/view/Create-a-Wordpress-Plugin/1404/#sthash.6NhmrApE.dpuf
if (is_admin()) {
    new SpoofProof();
}

class SpoofProof 
{
  /* Constructor will create the menu item */
  public function __construct() 
  { add_action('admin_menu', array($this, 'SpoofProof_Settings')); }

  function SpoofProof_Settings() 
  { add_menu_page('SpoofProof', 'SpoofProof', 'manage_options', 'SpoofProof-options', array($this, 'SpoofProof_option_page')); }

  public function SpoofProof_option_page() 
  {
    global $options, $current;
    $title = "SpoofProof Options";
    wp_enqueue_script('jquery');
    wp_enqueue_style("dropdown-admin", plugins_url('/css/SpoofProof.css', __FILE__), false, "1.0", "all");
    include_once( "spoofproof-page-options.php" );
  }
}

/*******************************************************************************
                Functions to save settings from the options page
*******************************************************************************/
//  settings_fields('SpoofProof_options');
  function SpoofProof_register_settings() 
  {
    register_setting('SpoofProof_options','SpoofProof_Login_Override');  // , $sanitize_callback 
    register_setting('SpoofProof_options','SpoofProof_Stop_JavaScript_Injection');  // , $sanitize_callback 
    register_setting('SpoofProof_options','SpoofProof_Stop_SQL_Injection');  // , $sanitize_callback 
    register_setting('SpoofProof_options','SpoofProof_Num_Retries');  // , $sanitize_callback 
  }  
  if (is_admin()) { add_action( 'admin_init', 'SpoofProof_register_settings' ); }
      
function plugin_admin_init(){
  register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
  
//  add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
//  add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
}

/*******************************************************************************
                              Save global settings
*******************************************************************************/
function SpoofProof_Save_Global_Settings()
{
/* */
  $SpoofProof_Login_Override            = $_POST['SpoofProof_Login_Override'];
  $SpoofProof_Stop_JavaScript_Injection = $_POST['SpoofProof_Stop_JavaScript_Injection'];
  $SpoofProof_Stop_SQL_Injection        = $_POST['SpoofProof_Stop_SQL_Injection'];
  $SpoofProof_Num_Retries               = $_POST['SpoofProof_Num_Retries'];
/* */  
    update_option('SpoofProof_Login_Override',            $SpoofProof_Login_Override); // false);//
    update_option('SpoofProof_Stop_JavaScript_Injection', $SpoofProof_Stop_JavaScript_Injection);
    update_option('SpoofProof_Stop_SQL_Injection',        $SpoofProof_Stop_SQL_Injection);
    update_option('SpoofProof_Num_Retries',               $SpoofProof_Num_Retries);  // , $sanitize_callback 
    $ResponseData = 'Settings have been saved.';
    echo json_encode(array('success'=>true, 'data'=>$ResponseData, 'SpoofProof_Num_Retries'=>$SpoofProof_Num_Retries));
/* */
}
add_action( 'wp_ajax_SpoofProof_Save_Global_Settings', 'SpoofProof_Save_Global_Settings' );

/******************************************************************************
                              Functions to call on install
*******************************************************************************/
// Add a function to add a table to the database if it does not exist.
function SpoofProof_install() 
{
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
  add_option("SpoofProof_db_version", "1.0");
  // Create a table for the users to store their data in
  $table_name = $wpdb->prefix . 'users_suppliment';
  $sql = "CREATE TABLE " . $table_name . "(ID bigint(20) NOT NULL,pass_phrase varchar(100) NOT NULL DEFAULT 'No image or phrase set.  Login and change for added security.',image INT NOT NULL DEFAULT '1', PRIMARY KEY (`ID`))$charset_collate;";
  dbDelta($sql);
  // Create a table to store images and track them with.
  $table_name = $wpdb->prefix . 'users_images';
  $sql = "CREATE TABLE " . $table_name . "(ID bigint(20) NOT NULL AUTO_INCREMENT,image_name text NOT NULL DEFAULT '', PRIMARY KEY (`ID`))$charset_collate;";
  dbDelta($sql);
}
register_activation_hook(__FILE__, 'SpoofProof_install');

// Add a function to add data to the table if it does not exist.
function SpoofProof_install_data() 
{
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  global $wpdb;
  // Create options
  if ( get_option('SpoofProof_Login_Override' ) == false )
  { add_option('SpoofProof_Login_Override', true, null, 'no'); }  
  if ( get_option('Spoo3fProof_Stop_JavaScript_Injection' ) == false )
  { add_option('Spoo3fProof_Stop_JavaScript_Injection', true, null, 'no'); }  
  if ( get_option('SpoofProof_Stop_SQL_Injection' ) == false )
  { add_option('SpoofProof_Stop_SQL_Injection', true, null, 'no'); }  

  // Insert data into users suppliments
  $table_name = $wpdb->prefix . 'users_suppliment';
  $user_table_name = $wpdb->prefix . 'users';
  $user_image_table_name = $wpdb->prefix . 'users_images';
  $sql = "INSERT INTO " . $table_name . "(ID) SELECT ID FROM " . $user_table_name . " WHERE ID NOT IN (Select ID from " . $table_name . ");";
  $wpdb->query($sql);
  // Insert default data into users_images
  $sql = "Select count(*) as imagecount from `".$user_image_table_name."`";
  $thedata = $wpdb->get_row($sql, ARRAY_A);
  // Insert default records if they don't already exist      
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'default.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'default.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'pi.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'pi.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'smile.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'smile.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'steak.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'steak.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'umbrealla.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'umbrealla.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'puppy.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'puppy.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'kitten.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'kitten.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'snowflake.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'snowflake.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'corn.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'corn.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'tomato.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'tomato.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'car.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'car.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'horse.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'horse.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'band-aid.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'band-aid.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'flower.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'flower.jpg') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'masks.png' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'masks.png') LIMIT 1)";
  $wpdb->query($sql);
  $sql = "INSERT INTO `".$user_image_table_name."`(image_name) (SELECT 'glass.jpg' FROM dual WHERE 0 = (SELECT COUNT( * ) FROM `".$user_image_table_name."` WHERE image_name =  'glass.jpg') LIMIT 1)";
  $wpdb->query($sql);
/* SQL to create a trigger in MYSQL * /
  $sql  = "DELIMITER // \n";
  $sql .= "DROP TRIGGER IF EXISTS SPNoInsertion \n";
  $sql .= "CREATE TRIGGER SPNoInsertion BEFORE INSERT ON wp_mzwl_posts \n";
  $sql .= "FOR EACH ROW BEGIN \n";
  $sql .= "  DECLARE x INT; \n";
  $sql .= "  SET x = (SELECT @SpoofProofStatus); \n";
  $sql .= "  IF (1 = x) THEN \n\n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<script type=\"text/javascript\">', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<script type='text/javascript'>', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '</script>', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<script', ''); \n\n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<?php', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '?>', ''); \n\n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%@', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%#', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%=', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%$', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%:', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '<%', ''); \n";
  $sql .= "    SET NEW.post_content = REPLACE(NEW.post_content, '%>', ''); \n";
  $sql .= "  END IF; \n";
  $sql .= "END \n\n";
  $sql .= "DELIMITER ; \n";
  $wpdb->query($sql);
/* */
}
register_activation_hook(__FILE__, 'SpoofProof_install_data');

// Settings link
function SpoofProof_add_settings_link($links) 
{
// $settings_link = '<a href="options-general.php?page=dropdown-menu">Settings</a>';
  $settings_link = '<a href="admin.php?page=SpoofProof-options">Settings</a>';
  array_push($links, $settings_link);
  return $links;
}
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'SpoofProof_add_settings_link');

// Capture insertion of Malicious code here ************************************************************************************************************
function SpoofProof_InjectionStopper($content)
{
  if (get_option('SpoofProof_Stop_SQL_Injection') == 'true')
  {
    $Tags = array('<script type=\"text/javascript\">','<script type=\'text/javascript\'>','</script>','<?php','?>','<%@','<%#','<%=','<%$','<%:','<%','%>');  
    $content = str_replace($Tags, "", $content);
    $content = str_replace('Create procedure', "", $content);
    $content = str_replace('UNION SELECT', "", $content);
    $content = str_replace('UNION (SELECT', "", $content);
    $content = str_replace('SELECT *', "", $content);
    $content = str_replace('SELECT id', "", $content);
  }
  return $content;
}
add_action('publish_post', 'SpoofProof_InjectionStopper' );
//add_filter ( 'hook_name', 'your_filter', [priority], [accepted_args] );

// Add functions here to call for Images editing. ******************************************************************************************************
function SpoofProof_Show_Images()
{
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  require_once(ABSPATH . 'wp-includes/ms-functions.php' );
  global $wpdb;
  // generate the response
  $user_table_name =           $wpdb->prefix.'users';
  $users_image_table_name =    $wpdb->prefix.'users_images';
  $sql = "Select * From `".$users_image_table_name."`";
  $allposts = $wpdb->get_results($sql, ARRAY_A);
  $ResponseData = "";
  foreach ($allposts as $singlepost) 
  { 
    $ResponseData .= "<div class=\"photo\">\n";
//    $ResponseData .= "<img float=right src='".get_current_site()."/wp-content/plugins/spoofproof/img/".$singlepost['image_name']."' style='float: right; margin: 0.5em; border: 1px solid #ccc; border-radius: 10px;width: 100px; height: 100px; font-size: 10px;'>";
    $ResponseData .= "<img float=right src='".plugins_url()."/SpoofProof/img/".$singlepost['image_name']."' style='margin: 0.5em; border: 1px solid #ccc; border-radius: 10px;width: 100px; height: 100px; font-size: 10px;'><BR>\n";
//    $ResponseData .= $singlepost['image_name'].': '.$singlepost['ID'].': '.$singlepost['image_name'].'<BR>\n';      
    $ResponseData .= $singlepost['ID'].': '.$singlepost['image_name'].'<BR>';      
    $ResponseData .= "</div>\n";
  }
    
//  $thedata = $wpdb->get_row($sql, ARRAY_A);
//  if ($thedata === null)
//  { $response = json_encode(array('success'=>false, 'data'=>'<p><b>Did you type your user name correctly?</b></p><BR><small>(If you try too often you will be blocked)</small> '.$sql)); }
//  else
//  {
  
//    $ResponseData="TEST<div style='display: block; min-height:120px;'><img float=right src='".get_current_site()."/wp-content/plugins/SpoofProof/img/".$thedata['image_name']."' style='float: right; margin: 0.5em; border: 1px solid #ccc; border-radius: 10px;width: 100px; height: 100px; font-size: 10px;'><p>".$thedata['pass_phrase']."</p></div>";
    $response = json_encode(array('success'=>true, 'data'=>$ResponseData));
//  }  
 echo $response;
 
 // IMPORTANT: don't forget to "exit"
  wp_die(); 
//  exit;
}  // SpoofProof_Show_Images
add_action( 'wp_ajax_SpoofProof_Show_Images', 'SpoofProof_Show_Images' );

function SpoofProof_upload_Images()
{
    $ResponseData = "Help!";
/* * /
//  if ( user_can_save( $post_id, plugin_basename( __FILE__ ), 'example-jpg-nonce' ) ) 
  {
//    if ( has_files_to_upload( 'example-jpg-file' ) ) 
    {
//      if ( isset( $_FILES['example-jpg-file'] ) ) 
      {
//        $file = wp_upload_bits( $_FILES['example-jpg-file']['name'], null, @file_get_contents( $_FILES['example-jpg-file']['tmp_name'] ) );
        $response = json_encode(array('success'=>true, 'data'=>$ResponseData));
      }
    }
  }
/* */
//   echo $response+" Not kidding!";
   echo "Help Not kidding!";
}
add_action( 'wp_ajax_SpoofProof_Upload_Images', 'SpoofProof_upload_Images' );

/******************************************************************************
                              User Profile fields functions
*******************************************************************************/
function extra_user_profile_fields($user) 
{
  wp_enqueue_script('jquery');
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  require_once(ABSPATH . 'wp-includes/ms-functions.php' );
  global $wpdb;
  $user_table_name = $wpdb->prefix.'users';
  $user_image_table_name = $wpdb->prefix.'users_images';
  $user_supliment_table_name = $wpdb->prefix.'users_suppliment';
  $sql = "Select s.pass_phrase, s.image, i.image_name FROM `".
         $user_supliment_table_name."` s, `".$user_image_table_name."` i ".
         "WHERE s.ID = '".get_current_user_id()."' and i.id=s.image";
  $thedata = $wpdb->get_row($sql, ARRAY_A);
/* */
  if ($thedata === null)
  { 
    $sql = "INSERT INTO " . $user_supliment_table_name . "(ID) SELECT ID FROM " . $user_table_name . " WHERE ID NOT IN (Select ID from " . $user_supliment_table_name . ");";
    $wpdb->query($sql);
    $image       = 1;
    $image_name  = "default.jpg";
    $pass_phrase = "No image or phrase set.  Login and change for added security.";
  }
  else
  { 
    $image       = $thedata['image'];
    $image_name  = $thedata['image_name'];
    $pass_phrase = $thedata['pass_phrase'];
  }  
/* */
?>
  <script type="text/javascript">
    function LoginImageChange()
    {
      jQuery('#DisplayThumb').attr('src', '<?php echo plugins_url(); ?>/SpoofProof/img/'+jQuery('#LoginImage option:selected').text());
    }
  </script>
    <h3><?php _e("More secure login information", "blank"); ?></h3>
    <span class="description"><?php _e("This information will be displayed on the login screen and will show you are talking to the website, and not a copy."); ?></span>

    <table class="form-table">
        <tr>
            <th><label for="PassPhrase"><?php _e("Pass Phrase"); ?></label></th>
            <td>
                <input type="text" name="PassPhrase" id="PassPhrase" value="<?php echo esc_attr($pass_phrase); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your Pass Phrase (something uniquely yours)."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="LoginImage"><?php _e("Login Image".$image); ?></label></th>
            <td>
                <!--    
                  <input type="text" name="LoginImage" id="LoginImage" value="<?php echo esc_attr(get_the_author_meta('LoginImage', $user->ID)); ?>" class="regular-text" /><br />
                -->
<?PHP /* $SelectedLoginImage = get_the_author_meta( 'LoginImage', $user->ID); */ ?>
                <img id="DisplayThumb" float=right src='<?php echo plugins_url(); ?>/SpoofProof/img/<?PHP echo $image_name; ?>' style='float: right; margin: 0.5em; border: 1px solid #ccc; border-radius: 10px; width: 100px; height: 100px; font-size: 10px;'>
                <Select name="LoginImage" id="LoginImage" onChange="LoginImageChange();">
<?php
    $sql = "Select * from ".$wpdb->prefix."users_images";
    $allposts = $wpdb->get_results($sql, ARRAY_A);
    foreach ($allposts as $singlepost) 
//    { echo '<option value="'.$singlepost['image_name'].'" title="'.get_current_site().'/wp-content/plugins/SpoofProof/img/'.$singlepost['image_name'].'" '.selected($singlepost['image_name'], $SelectedLoginImage).'>'.$singlepost['image_name'].'</option>'; }
//    { echo '<option style="background-image:url('.get_current_site().'/wp-content/plugins/SpoofProof/img/'.$singlepost['image_name'].'); ". "value="'.$singlepost['ID'].'" '.selected($singlepost['image_name'], $SelectedLoginImage).'>'.$singlepost['image_name'].'</option>'; }
    { 
      if ($singlepost['ID'] === $image) 
        {$Selected=" selected";}
      else
        {$Selected="";}
//      echo '<option style="background-image:url('.plugins_url().'/SpoofProof/img/'.$singlepost['image_name'].'); " value="'.$singlepost['ID'].'"'.$Selected.'>'.$singlepost['image_name'].'</option>';      
      echo '<option value="'.$singlepost['ID'].'"'.$Selected.'>'.$singlepost['image_name'].'</option>';      
    }
?>
                </Select><br/>
                <span class="description"><?php _e("Please select an image to use during login."); ?></span>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

function save_extra_user_profile_fields($user_id) 
{
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  require_once(ABSPATH . 'wp-includes/ms-functions.php' );
  global $wpdb;
  if (!current_user_can('edit_user', $user_id)) { return false; }
  
  $table_name = $wpdb->prefix . 'users_suppliment';
  
  $sql = "UPDATE ".$table_name." Set pass_phrase='".$_POST['PassPhrase']."', image='".$_POST['LoginImage']."' WHERE ID = '".get_current_user_id()."'";
  
  $wpdb->query($sql);
// Do not use standard method of storing these options since that will not be available if you are not logged in yet.
//  update_user_meta($user_id, 'PassPhrase', $_POST['PassPhrase']);
//  update_user_meta($user_id, 'LoginImage', $_POST['LoginImage']);
}
add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

/******************************************************************************
                               User Registration Fields
*******************************************************************************/

// Add fields to the register form using the same code as extra_user_profile_fields()
add_action('register_form','signup_extra_fields');

function SpoofProof_registration_errors( $errors, $sanitized_user_login, $user_email ) 
{
  if ( empty( $_POST['PassPhrase'] ) || ! empty( $_POST['PassPhrase'] ) && trim( $_POST['PassPhrase'] ) == '' ) 
  { $errors->add( 'PassPhrase_error', __( '<strong>ERROR</strong>: You must include a pass phrase.', 'mydomain' ) ); }
  return $errors;
}
add_filter( 'registration_errors', 'SpoofProof_registration_errors', 10, 3 );

function SpoofProof_user_register( $user_id ) 
{
  if ( ! empty( $_POST['PassPhrase'] ) ) 
  {
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  require_once(ABSPATH . 'wp-includes/ms-functions.php' );
  global $wpdb;
  $table_name = $wpdb->prefix . 'users_suppliment';
  $sql = "UPDATE ".$table_name." Set pass_phrase='".$_POST['PassPhrase']."', image='".$_POST['LoginImage']."' WHERE ID = '".get_current_user_id()."'";
  $wpdb->query($sql);
// Do not use standard method of storing these options since that will not be available if you are not logged in yet.
//    update_user_meta( $user_id, 'PassPhrase', trim( $_POST['PassPhrase'] ) ); 
//    update_user_meta( $user_id, 'LoginImage', trim( $_POST['LoginImage'] ) ); 
  }
}
add_action( 'user_register', 'SpoofProof_user_register' );

/******************************************************************************
                               System Variable code
******************************************************************************* /
function spoofproof_session() 
{
  //do stuff
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  global $wpdb;
  $sql = "SET @SpoofProofStatus = NULL";
  $wpdb->query($sql);
  if (current_user_can('edit_user', $user_id)) 
  { 
    $sql = "SET @SpoofProofStatus = 1";
    $wpdb->query($sql);
  }
}
add_action('wp_login', 'spoofproof_session');

/******************************************************************************
                           Modify the login screen code
*******************************************************************************/
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
// This needs localization code, but not right now. (Get it to work, then localize it)
function SpoofProof_added_login_functions() 
{
  if (get_option('SpoofProof_Login_Override') == 'true')
  {
    require_once(ABSPATH . 'wp-includes/ms-functions.php' );
    // wp_localize_script( $handle, $namespace, $variable_array ); format of creating an object in jquery with Wordpress
    // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
    // wp_localize_script( 'my-ajax-request', 'MyAjax', array('ajaxurl'=>admin_url('admin-ajax.php')));
    // embed the javascript file that makes the AJAX request
    wp_enqueue_script('jquery');
    wp_enqueue_script('SpoofProof_Scripts', plugin_dir_url(__FILE__).'js/SpoofProof.js');
    wp_enqueue_script('SpoofProof-Token', SERVICE_URL.'/?Action=get_token', array());
    if (strpos($_SERVER['HTTP_REFERER'],"www."))
    { $admin_url = admin_url('admin-ajax.php'); }
    else
    { $admin_url = str_replace("www.","",admin_url('admin-ajax.php')); }
/* */        
?>
    <style type="text/css"> 
       #login {padding-top:10px;} 
       .login h1 a {background-image: none, url('<?php echo plugins_url(); ?>/SpoofProof/img/wordpress-logo-plus.png')} 
    </style>
    <script type="text/javascript">
      var ajaxurl      = "<?php echo $admin_url; ?>";  
/*      var pagelocation = "< ?php echo get_current_site(); ? >"; plugins_url() */
      var service_url  = "<?php echo SERVICE_URL; ?>";
    </script>
<?php
/*        url: "<?php echo get_current_site(); ?>/wp-content/plugins/SpoofProof/admin-ajax.php", */
  }
}
add_action('login_head', 'SpoofProof_added_login_functions');

function SpoofProof_added_login_field()
{
  if (get_option('SpoofProof_Login_Override') == 'true')
  { 
?>
    <input type='checkbox' id='SpoofProof_revert' name='SpoofProof_revert' onClick="RevertScreen();"/><label for="SpoofProof_revert" id="SpoofProof_revert_label" >Revert Screen.</label>
    <input type='checkbox' id='SpoofProof_simulate' name='SpoofProof_simulate'/><label for="SpoofProof_simulate" id="SpoofProof_simulate_label" >Simulate attack.</label><Span id="spoofproof_space" >&nbsp;</span>
    <BR><BR>
    <div id='SpoofProofResults' style='padding-bottom:10px;' >
        Enter your user name, hit next to validate your connection to server.<BR><BR>
        You should see the text and the image you selected earlier, if not, do not continue.
        <BR>
    </div>
    <input type='button' name='sp-next' id='sp-next' class='button button-primary button-large' value='Next' onclick='GetSpoofProofResults();'>
<?php
/* * /
    <p>
        <label for="my_extra_field">My extra field<br>
          <input type="text" tabindex="20" size="20" value="" class="input" id="my_extra_field" name="my_extra_field_name">
        </label>
    </p>
 /* */
  }
}
add_action('login_form','SpoofProof_added_login_field');

function get_web_page($url)
{
  $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
  $requestnumber = 0; // Initialize to zero
  $fields = '';
  $FieldCount = 0;
  foreach ($_REQUEST as $key => $value) // Parse each filed passed in and set in aray
  {  
    $fields .= $key.'='.$value."&";
    $FieldCount++;
  } 
  $fields = Rtrim($fields,"&"); //Remove trailing '&' if it exists.
        
  $options = array(
    CURLOPT_HTTPHEADER     => array('Authorization: Basic user:pass',
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                    'Cache-Control: no-cache'),
    CURLOPT_HTTPHEADER     => array('Content-Length: '.strlen($fields)),
    CURLOPT_POST           => sizeof($_REQUEST),//true,         //set to POST
    CURLOPT_POSTFIELDS     => $fields,                          //Add Post Fields
    CURLOPT_RETURNTRANSFER => true,                             // return web page
    CURLOPT_CONNECTTIMEOUT => 120,                              // timeout on connect
    CURLOPT_TIMEOUT        => 120,                              // timeout on response
    CURLOPT_MAXREDIRS      => 10,                               // stop after 10 redirects
    CURLOPT_USERPWD        => base64_encode(USER.":".PASSWORD)  // Set username and password
    );
  $ch      = curl_init( $url );
  curl_setopt_array( $ch, $options );
  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  $header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $header;
}
    
function SpoofProof_Next() 
{
  require_once(ABSPATH . 'wp-includes/wp-db.php' );
  require_once(ABSPATH . 'wp-includes/ms-functions.php' );
  global $wpdb;
  $succeed = true;
  $ResponseData = "";
  // Validate the token
  /* */
  $Token = SpoofProof_InjectionStopper($_REQUEST['spoofproof_token']);
  if ($_POST['Simulate_Attack'] == "True")
  { $url = SERVICE_URL.'/?Action=validate_token&Token='.$Token."&IPAddress=0.0.0.0&host=".$_SERVER['HTTP_HOST']; }
  else 
  { $url = SERVICE_URL.'/?Action=validate_token&Token='.$Token."&IPAddress=".$_SERVER['REMOTE_ADDR']."&host=".$_SERVER['HTTP_HOST']; }
  $result = get_web_page($url);
  $TokenValidation = rtrim($result['content']);  

  if ($TokenValidation === "Validated")
  {
  /* */ 
    // generate the response
    $user_table_name           = $wpdb->prefix.'users';
    $users_image_table_name    = $wpdb->prefix.'users_images';
    $user_supliment_table_name = $wpdb->prefix.'users_suppliment';
    $sql = "Select pass_phrase,image_name From `".$user_table_name."` u, `".$user_supliment_table_name."` s, `".$users_image_table_name."` i".
           " WHERE u.user_login= '".$_REQUEST['user_login']."' and s.ID=u.ID and i.ID=s.image";
    $thedata = $wpdb->get_row($sql, ARRAY_A);
    if ($thedata === null)
    {
      $ResponseData = "<p><b>Did you type your user name correctly?</b></p><BR><small>(If you try too often you will be blocked)</small> ";
      $succeed = false;
    }
    else
    {
       $ResponseData="<b>Make sure the following text and picture are yours, if they are not, do not log in!</b><BR><BR>".
                    "<div style='display: block; min-height:120px;'><img float=right src='".plugins_url().
                    "/SpoofProof/img/".$thedata['image_name']."' style='float: right; margin: 0.5em;".
                    " border: 1px solid #ccc; border-radius: 10px;width: 100px; height: 100px; font-size: 10px;'><p>".
                    $thedata['pass_phrase']."</p></div>"; 
    }  
  }    
  else
  {
    $ResponseData=$TokenValidation; 
    $succeed = false;
  }
    $response = json_encode(array('success'=>$succeed, 'data'=>$ResponseData, 'Time'=>time()));
    echo $response;
  exit;  // IMPORTANT: don't forget to "exit"
}
// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action( 'wp_ajax_nopriv_SpoofProof_Next', 'SpoofProof_Next' );
add_action( 'wp_ajax_SpoofProof_Next', 'SpoofProof_Next' );

// Not sure if I need this
// Add a shortcode so I can call this from my getuserdetail.
function SpoofProof_GetUserLoginDetail( $atts ) 
{
    $a = shortcode_atts( array('user_login' => ''), $atts );
    return "User not found.";
}
add_shortcode( 'spoofproof_login', 'SpoofProof_GetUserLoginDetail' );
/* */

/******************************************************************************
                           Run at the end of registration
*******************************************************************************/
// Redirects to options page on activate
function SpoofProof_activate() 
{ add_option('SpoofProof_plugin_do_activation_redirect', true); }

function SpoofProof_plugin_redirect()
{
  if (get_option('SpoofProof_plugin_do_activation_redirect', false)) 
  {
    delete_option('SpoofProof_plugin_do_activation_redirect');
    if(!isset($_GET['activate-multi']))
    { wp_redirect("options-general.php?page=SpoofProof-options"); }
  }
}
register_activation_hook(__FILE__, 'SpoofProof_activate');
add_action('admin_init', 'SpoofProof_plugin_redirect');
?>