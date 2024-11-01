<div class="wrap stf_options_page">
    <?php wp_enqueue_script('jquery'); ?>
    <?php /* header('Access-Control-Allow-Origin: *'); */?>
    <?php screen_icon(); ?>
    <h2><?php echo esc_html($title); ?></h2>
    <!-- Notifications -->
    <?php if (isset($_GET['message']) && isset($messages[$_GET['message']])) { ?>
        <div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
    <?php } ?>
    <?php if (isset($_GET['error']) && isset($errors[$_GET['error']])) { ?>
        <div id="message" class="error fade"><p><?php echo $errors[$_GET['error']]; ?></p></div>
    <?php } ?>
    <!-- [End] Notifications -->
    <?php
    /*
      //      settings_fields('SpoofProof_options');
      function SpoofProof_register_settings() {
      register_setting('SpoofProof_options','SpoofProof_Login_Override');  // , $sanitize_callback
      register_setting('SpoofProof_options','SpoofProof_Stop_JavaScript_Injection');  // , $sanitize_callback
      register_setting('SpoofProof_options','SpoofProof_Stop_SQL_Injection');  // , $sanitize_callback
      }
      //      add_action( 'admin_init', 'register_my_settings' );
      add_action( 'admin_init', 'SpoofProof_register_settings' );

      $SpoofProof_Login_Override = $_POST['SpoofProof_Login_Override'];
      $SpoofProof_Stop_JavaScript_Injection = $_POST['SpoofProof_Stop_JavaScript_Injection'];
      $SpoofProof_Stop_SQL_Injection = $_POST['SpoofProof_Stop_SQL_Injection'];
      $nonce = $_POST['nonce'];
      if (wp_verify_nonce($nonce, 'SpoofProof_plugin_settings'))
      {
      update_option('SpoofProof_Login_Override', false);//$SpoofProof_Login_Override);
      update_option('SpoofProof_Stop_JavaScript_Injection', $SpoofProof_Stop_JavaScript_Injection);
      update_option('SpoofProof_Stop_SQL_Injection', $SpoofProof_Stop_SQL_Injection);
      $msg = 'Settings have been saved.';
      //      }
      }
     */
    ?>

    <div id="nav">
        <?php
        if (!empty($navigation)) {
            echo $navigation;
        }
        ?>
    </div>
    <div class="stf_opts_wrap">
        <div class="stf_options">
            <form>
                <?php settings_fields('SpoofProof_options'); ?>
                <div id="options-tabs">
                    <!-- Tabs navigation -->
                    <ul id="tabs-navigation" class="tabs">
                        <li><a href="general"  class="general"  onclick="showcontent('general');">General</a></li>
                        <li><a href="images"   class="images"   onclick="showcontent('images');">Images</a></li>
                        <li><a href="account"  class="account"  onclick="showcontent('account');">Account</a></li>
                        <li><a href="notes"    class="notes"    onclick="showcontent('notes')">Release Notes</a></li>
                    </ul>
                    <!-- [End] Tabs Navigation -->
                    <div class="tab_container">
                        <div>  
                            <div id="general" class="tabbedcontent">
                                <div id="Instructions" class="Instructions">
                                    <p>These options allow you to turn on and off some functionality provided by this control without un-installing the control.</p>
                                    <p style="color:red;" >The login screen will <b>self disable</b> after a month of use without the service being purchased.</p>
                                    <p>It costs us money to run the service, so please pay for what you are using to protect your web site.</p>
                                </div>
                                <div id="GeneralDetail">
                                    <h1>General settings:</h1>
                                    <div id="GeneralSettingsResults"></div>
                                    <div>
                                        <input name="action" type="hidden" value="update" />
                                        <input name="page_options" type="hidden" value="SpoofProof_Login_Override,SpoofProof_Stop_JavaScript_Injection,SpoofProof_Stop_SQL_Injection" />
<?php
                                        if (get_option('SpoofProof_Login_Override') == 'true')
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Login_Override' name='SpoofProof_Login_Override' checked/>&nbsp;&nbsp;".
                                               "Override the login screen with a two factor authentication login screen. (Spoofing and Phishing cannot succed ".
                                               "without the help of a MItM attack when two factor authentication is used.)</p>";
                                        }
                                        else
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Login_Override' name='SpoofProof_Login_Override'/>&nbsp;&nbsp;".
                                               "Override the login screen with a two factor authentication login screen. (Spoofing and Phishing cannot succed ".
                                               "without the help of a MItM attack when two factor authentication is used.)</p>";
                                        }

                                        echo "<div style='display:none;'>\n ";
                                        if (get_option('SpoofProof_Stop_JavaScript_Injection') == 'true')
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Stop_JavaScript_Injection' name='SpoofProof_Stop_JavaScript_Injection' checked/>&nbsp;&nbsp;".
                                               "Intercept writes to the database and check for Javascript, SQL, C#, PHP injection. (Stops infected data from being written).</p>";
                                        }
                                        else
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Stop_JavaScript_Injection' name='SpoofProof_Stop_JavaScript_Injection'/>&nbsp;&nbsp;".
                                               "Intercept writes to the database and check for Javascript injection. (Stop infected data from being written).</p>";
                                        }
                                        echo "</div>\n";

                                        if (get_option('SpoofProof_Stop_SQL_Injection') == 'true')
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Stop_SQL_Injection' checked/>&nbsp;&nbsp;".
                                               "Intercept writes to the database and check for Javascript, SQL, C# and PHP injection. (Stop infected data from being written).</p>";
                                        }
                                        else
                                        {
                                          echo "<p><input type='checkbox' id='SpoofProof_Stop_SQL_Injection'/>&nbsp;&nbsp;".
                                               "Intercept writes to the database and check for SQL injection. (Stop infected data from being written).</p>";
                                        }
                                        echo "<div style='display:none;'>\n ";
                                        echo "<p><input type='text' id='SpoofProof_Num_Retries' name='SpoofProof_Num_Retries' maxlength='3' style='width:30px;' value='".get_option(SpoofProof_Num_Retries)."' readonly/>&nbsp;&nbsp;".
                                             "The number of retries on username password combinations before an IPAddress is locked out. for a time period.";
                                        echo "</div>\n";
/* */
?>
                                    </div>    
                                </div>
                            </div>
                            <div id="images" class="tabbedcontent">
                                <div id="Instructions" class="Instructions">
                                    <p>These images can be selected by the user when they are choosing what to personalize their login screen with.</p>
                                    <p>Choose a file and add it to the collection of images available for selection by the user.</p>
                                </div>
                                <div id="ImagesDetail" class="clearfix" style='width:calc(1005 - 260px);'>
                                    Please wait for images to load...
                                </div>
                                <output id="list"></output>
<?php                                
 /* * /
?>
                                <div id="tabs-footer" class="clearfix">
                                    <input type='file' id="New_SpoofProofImage" name="New_SpoofProofImage" accept="image/*" />&nbsp;&nbsp; <!-- multiple -->
                                    <imput type="submit" name="UploadImage"  id="UploadImage"  class="button" onClick="UploadFile(jQuery('#New_SpoofProofImage').val());">Upload Image</imput>&nbsp;&nbsp;
                                    <imput type="submit" name="RefreshImage" id="RefreshImage" class="button" onClick="RefreshSpoofProofImages();">Refresh Image</imput>
                                </div>  
<?php                                
/* */
?>
                            </div>
                            <div id="account" class="tabbedcontent">
                                <div id="Instructions" class="Instructions">
                                    <p>Since SpoofProof relies on a web-service to detect man in the middle attacks on your site, because of this we charge for the service of detecting Man in the middle attacks.</p>
                                    <p>If you do not decide to pay for this service, a two stage authentication cannot stop Spoofing and Phishing.</p>
                                </div>
                                <div id="AccountDetail" class="customerpage" >
<?php
if ($_SERVER['SERVER_NAME'] == 'ciphertooth.dev')
{ define('SERVICE_URL', 'http://ciphertooth.dev/press'); }
else
{ define('SERVICE_URL', 'http://wpsecure.convertabase.net'); }
    $URLArray = parse_url($_SERVER['HTTP_REFERER']);
    $host = str_replace("www.","",strtolower($URLArray['host']));
?>
                                    <?php echo SERVICE_URL."/?Action=get_status&host=".$host."<BR>"; ?>
                                    <iframe src="<?php echo SERVICE_URL."/?Action=get_status&host=".$host; ?>" style="border:solid 1px #999; min-height:330px; width:calc(100% - 260px); border-radius:10px;"></iframe>
                                </div>
                            </div>
                            <div id="notes" class="tabbedcontent">
                                <div id="Instructions" class="Instructions">
                                    <h1>Current Version:</h1>
                                    <h1>Version 1.0.0.2</h1>
                                    <p>Describes the notes and changes from version to version.</p>
                                    <p>SpoofProof alters the login screen to add fields to use two stage login screen so you can know you are talking to the server..</p>
                                    <p><a href="http://www.ciphertooth.com/products/downloads">This link will take you to the web-site that has the latest version.</a></p>
                                </div>
                                <div id="NotesDetail" class="clearfix">
                                    <h1><b>Version 1.0.0.2</b></h1> Released 11/13/2015
                                    <ol>
                                        <li>Fixed bugs with Brute Force detection.</li>
                                        <li>Fixed bugs in MITM detection service that mis reported some attacks.</li>
                                        <li>Fixed interface bugs which stripped colors from text that were for emphasis.</li>
                                        <li>Altered the way internal paths were read from WordPress to support more installations.</li>
                                    </ol>
                                    <h1><b>Version 1.0.0.1</b></h1> Released 11/5/2015
                                    <ol>
                                        <li>Added features to the Account screen.</li>
                                        <li>Fixed bugs in the reporting of some events to the database.</li>
                                        <li>Adding a version number to the zip file caused some issues with the correct location of image files.  Ver 1.0.0.1 has no version number in the zip files.</li>
                                    </ol>
                                    <h1><b>Version 1.0.0.0</b></h1> Release 10/10/2015
                                    <ol>
                                        <li>Count blocked users for repeated login attempts by domain.</li>
                                        <li>Override the login screen to have a two stage login screen.</li>
                                        <li>Use a Patent pending service to detect Attacks on each domain.</li>
                                        <li>Strip tags in posts that are commnly used in JavaScript, SQL, PHP and other injection attacks.</li>
                                    </ol>
                                    <p><b>SpoofProof planned enhancements:</b></p>
                                    <ol>
                                        <li>report attacks by domain and type.</li>
                                        <li>Wrong password count and blocking by IP address.</li>
                                        <li>Upload and management of images to use with SpoofProof.</li>
                                        <li>Count blocked users for repeated login attempts by domain.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div id="tabs-footer" class="clearfix">
                            <p class="submit">
                                <?php /* submit_button('Save Changes', 'primary', 'save', false); */ ?>
                                <input type="hidden" name="action" value="save" />
                                <button type="submit" class="button button-primary" OnClick="SaveGeneralSettings(); return false;">Save Changes</button>
                            </p>
                            <?php submit_button('Reset Options', 'secondary', 'reset', false); ?>
                            <input type="hidden" name="action" value="reset" />
                            <div class="copyright"><?php
                                if (!empty($footer_text)) {
                                    echo $footer_text;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script language="JavaScript">
            function RefreshSpoofProofImages()
            {
                jQuery('#ImagesDetail').html("");
                jQuery.post(ajaxurl,
                        { action: 'SpoofProof_Show_Images' },
                function(response) 
                {
                    var Stuff = jQuery.type(response);
                    var Image_Obj = jQuery.parseJSON(response);
                    if (Image_Obj.success === true)
                    { }
                    else
                    { }
                    jQuery('#ImagesDetail').html("<div style='max-width:85%;'>"+Image_Obj.data+"</div>");
                });
            }
            jQuery(document).ready(function() {
                RefreshSpoofProofImages();
            });
        </script>
        <script language="JavaScript">
/* * /
        // New_SpoofProofImage
            function handleFileSelect(evt) 
            {
              var files = evt.target.files; // FileList object
              // files is a FileList of File objects. List some properties.
              var output = [];
              for (var i = 0, f; f = files[i]; i++) 
              {
                output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
                            f.size, ' bytes, last modified: ',
                            f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
                            '</li>');
              }
              document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
            }
            document.getElementById('New_SpoofProofImage').addEventListener('change', handleFileSelect, false);
/* */
        </script>
        <script language="JavaScript">
/* * /
            // Check for the various File API support.
            if (window.File && window.FileReader && window.FileList && window.Blob) {
              // Great success! All the File APIs are supported.
            } else {
              alert('The File APIs are not fully supported in this browser.');
            }
/* http://www.html5rocks.com/en/tutorials/file/dndfiles/ * /            
            function handleFileSelect(evt) {
              var files = evt.target.files; // FileList object
              // files is a FileList of File objects. List some properties.
              var output = [];
              for (var i = 0, f; f = files[i]; i++) {
                output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
                            f.size, ' bytes, last modified: ',
                            f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
                            '</li>');
              }
              document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
            }
            document.getElementById('New_SpoofProofImage').addEventListener('change', handleFileSelect, false);
/* */            
        </script>
        <script language="JavaScript">
/* * /            
            function handleDragOver(evt) 
            {
                evt.stopPropagation();
                evt.preventDefault();
                evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
            }
            // Setup the dnd listeners.
            var dropZone = document.getElementById('drop_zone');
            dropZone.addEventListener('images', handleDragOver, false);
            dropZone.addEventListener('drop', handleFileSelect, false);
/* */            
        </script>
        <script language="JavaScript">
            function UploadFile(FileToUpload)
            {
//                alert('file to up load: ' + FileToUpload + ':'); 
                jQuery.post(ajaxurl,
                        {
                          action: 'SpoofProof_upload_Images'//,
//                          fileName: FileToUpload 
                        },
                function(response) 
                {
/*
                    var Stuff = jQuery.type(response);
                    var Image_Obj = jQuery.parseJSON(response);
                    if (Image_Obj.success === true)
                    { }
                    else
                    { }
*/                    
                    jQuery('#list').html("calling: "+ajaxurl+"<BR>Response: "+response+"<BR>File: "+FileToUpload); //Image_Obj.data);
                });
            }
        </script>
        <script language="JavaScript">
            function SaveGeneralSettings()
            {
//                alert(jQuery('#SpoofProof_Num_Retries').val());
                jQuery.post(ajaxurl,
                        { action: 'SpoofProof_Save_Global_Settings', 
                          SpoofProof_Login_Override:            (jQuery('#SpoofProof_Login_Override').attr('checked') == 'checked'),
                          SpoofProof_Stop_JavaScript_Injection: (jQuery('#SpoofProof_Stop_JavaScript_Injection').attr('checked') == 'checked'),
                          SpoofProof_Stop_SQL_Injection:        (jQuery('#SpoofProof_Stop_SQL_Injection').attr('checked') == 'checked'),
                          SpoofProof_Num_Retries:                jQuery('#SpoofProof_Num_Retries').val()
                        },
               function(response) 
                {
//                    alert("SaveGeneralSettings called!");
                    location.reload(true);
                });
            }
        </script>
        <script language="JavaScript">
            function showcontent(NameofDiv)
            {
                // hide all the tabs
                jQuery("#general").hide();
                jQuery("#images").hide();
                jQuery("#account").hide();
                jQuery("#notes").hide();
                //Show the correct tab
                jQuery("#" + NameofDiv).show();

            }
        </script>
        <script type="text/javascript">
            jQuery(document).ready(function($) 
            {
               //When page loads...
                jQuery(".tab_content").hide(); //Hide all content
                jQuery("ul.tabs li:first").addClass("active").show(); //Activate first tab
                jQuery(".tab_content:first").show(); //Show first tab content
                //On Click Event
                jQuery("ul.tabs li").click(function() 
                {
                    jQuery("ul.tabs li").removeClass("active"); //Remove any "active" class
                    jQuery(this).addClass("active"); //Add "active" class to selected tab
                    jQuery(".tab_content").hide(); //Hide all tab content
                    var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
                    jQuery(activeTab).fadeIn(); //Fade in the active ID content
                    return false;
                });
            });
        </script>

        <?php if (WP_DEBUG) { ?>
            <h3>Debug information</h3>
            <pre><?php print_r($current) ?></pre>
        <?php } ?>
    </div> 