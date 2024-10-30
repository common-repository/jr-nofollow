<?php
/*
Plugin Name: JR NoFollow
Plugin URI: http://www.jakeruston.co.uk/2009/11/wordpress-plugin-jr-nofollow/
Description: This plugin allows you to enable/disable the NoFollow attribute on your blog.
Version: 1.5.1
Author: Jake Ruston
Author URI: http://www.jakeruston.co.uk
*/

/*  Copyright 2010 Jake Ruston - the.escapist22@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$pluginname="nofollow";

// Hook for adding admin menus
add_action('admin_menu', 'jr_nofollow_add_pages');

if (get_option("mt_nofollow_author")=="Yes") {
add_filter('get_comment_author_link', 'enable_nofollow_link');
}

if (get_option("mt_nofollow_text")=="Yes") {
add_filter('comment_text', 'enable_nofollow_text');
}

if (get_option("mt_nofollow_type")=="Yes") {
add_filter('get_comment_type', 'enable_nofollow_type');
}

if (get_option("mt_nofollow_posts")=="Yes") {
add_filter('the_content', 'enable_nofollow_posts');
}

if (get_option("mt_nofollow_plugin_support")=="Yes" || get_option("mt_nofollow_plugin_support")=="") {
add_action('comment_form', 'nofollow_plugin_support');
}

if (!function_exists("_iscurlinstalled")) {
function _iscurlinstalled() {
if (in_array ('curl', get_loaded_extensions())) {
return true;
} else {
return false;
}
}
}

if (!function_exists("jr_show_notices")) {
function jr_show_notices() {
echo "<div id='warning' class='updated fade'><b>Ouch! You currently do not have cURL enabled on your server. This will affect the operations of your plugins.</b></div>";
}
}

if (!_iscurlinstalled()) {
add_action("admin_notices", "jr_show_notices");

} else {
if (!defined("ch"))
{
function setupch()
{
$ch = curl_init();
$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
return($ch);
}
define("ch", setupch());
}

if (!function_exists("curl_get_contents")) {
function curl_get_contents($url)
{
$c = curl_setopt(ch, CURLOPT_URL, $url);
return(curl_exec(ch));
}
}
}

if (!function_exists("jr_nofollow_refresh")) {
function jr_nofollow_refresh() {
update_option("jr_submitted_nofollow", "0");
}
}

register_activation_hook(__FILE__,'nofollow_choice');

function nofollow_choice () {
if (get_option("jr_nofollow_links_choice")=="") {

if (_iscurlinstalled()) {
$pname="jr_nofollow";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_nofollow", "1");
wp_schedule_single_event(time()+172800, 'jr_nofollow_refresh'); 
} else {
$content = "Powered by <a href='http://arcade.xeromi.com'>Free Online Games</a> and <a href='http://directory.xeromi.com'>General Web Directory</a>.";
}

if ($content!="") {
$content=utf8_encode($content);
update_option("jr_nofollow_links_choice", $content);
}
}

if (get_option("jr_nofollow_link_personal")=="") {
$content = curl_get_contents("http://www.jakeruston.co.uk/p_pluginslink4.php");

update_option("jr_nofollow_link_personal", $content);
}
}

// action function for above hook
function jr_nofollow_add_pages() {
    add_options_page('JR NoFollow', 'JR NoFollow', 'administrator', 'jr_nofollow', 'jr_nofollow_options_page');
}

// jr_nofollow_options_page() displays the page content for the Test Options submenu
function jr_nofollow_options_page() {

    // variables for the field and option names 
    $opt_name_2 = 'mt_nofollow_text';
    $opt_name_3 = 'mt_nofollow_author';
	$opt_name_4 = 'mt_nofollow_sites';
	$opt_name_5 = 'mt_nofollow_ip';
    $opt_name_6 = 'mt_nofollow_plugin_support';
	$opt_name_7 = 'mt_nofollow_posts';
    $hidden_field_name = 'mt_nofollow_submit_hidden';
    $data_field_name_2 = 'mt_nofollow_text';
    $data_field_name_3 = 'mt_nofollow_author';
	$data_field_name_4 = 'mt_nofollow_sites';
	$data_field_name_5 = 'mt_nofollow_ip';
    $data_field_name_6 = 'mt_nofollow_plugin_support';
	$data_field_name_7 = 'mt_nofollow_posts';

    // Read in existing option value from database
    $opt_val_2 = get_option( $opt_name_2 );
    $opt_val_3 = get_option( $opt_name_3 );
	$opt_val_4 = get_option( $opt_name_4 );
	$opt_val_5 = get_option( $opt_name_5 );
    $opt_val_6 = get_option( $opt_name_6 );
	$opt_val_7 = get_option( $opt_name_7 );
	
if (!$_POST['feedback']=='') {
$my_email1="the.escapist22@gmail.com";
$plugin_name="JR NoFollow";
$blog_url_feedback=get_bloginfo('url');
$user_email=$_POST['email'];
$user_email=stripslashes($user_email);
$subject=$_POST['subject'];
$subject=stripslashes($subject);
$name=$_POST['name'];
$name=stripslashes($name);
$response=$_POST['response'];
$response=stripslashes($response);
$category=$_POST['category'];
$category=stripslashes($category);
if ($response=="Yes") {
$response="REQUIRED: ";
}
$feedback_feedback=$_POST['feedback'];
$feedback_feedback=stripslashes($feedback_feedback);
if ($user_email=="") {
$headers1 = "From: feedback@jakeruston.co.uk";
} else {
$headers1 = "From: $user_email";
}
$emailsubject1=$response.$plugin_name." - ".$category." - ".$subject;
$emailmessage1="Blog: $blog_url_feedback\n\nUser Name: $name\n\nUser E-Mail: $user_email\n\nMessage: $feedback_feedback";
mail($my_email1,$emailsubject1,$emailmessage1,$headers1);
?>

<div class="updated"><p><strong><?php _e('Feedback Sent!', 'mt_trans_domain' ); ?></strong></p></div>

<?php
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val_2 = $_POST[ $data_field_name_2 ];
        $opt_val_3 = $_POST[ $data_field_name_3 ];
		$opt_val_4 = $_POST[ $data_field_name_4 ];
		$opt_val_5 = $_POST[ $data_field_name_5 ];
        $opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];

        // Save the posted value in the database
        update_option( $opt_name_2, $opt_val_2 );
        update_option( $opt_name_3, $opt_val_3 );
		update_option( $opt_name_4, $opt_val_4 );
		update_option( $opt_name_5, $opt_val_5 );
        update_option( $opt_name_6, $opt_val_6 );  
		update_option( $opt_name_7, $opt_val_7 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Settings saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'JR NoFollow Plugin Options', 'mt_trans_domain' ) . "</h2>";
$blog_url_feedback=get_bloginfo('url');
	$donated=curl_get_contents("http://www.jakeruston.co.uk/p-donation/index.php?url=".$blog_url_feedback);
	if ($donated=="1") {
	?>
		<div class="updated"><p><strong><?php _e('Thank you for donating!', 'mt_trans_domain' ); ?></strong></p></div>
	<?php
	} else {
	if ($_POST['mtdonationjr']!="") {
	update_option("mtdonationjr", "444");
	}
	
	if (get_option("mtdonationjr")=="") {
	?>
	<div class="updated"><p><strong><?php _e('Please consider donating to help support the development of my plugins!', 'mt_trans_domain' ); ?></strong><br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ULRRFEPGZ6PSJ">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form></p><br /><form action="" method="post"><input type="hidden" name="mtdonationjr" value="444" /><input type="submit" value="Don't Show This Again" /></form></div>
<?php
}
}

    // options form
    
    $change5 = get_option("mt_nofollow_plugin_support");
    $change6 = get_option("mt_nofollow_text");
    $change7 = get_option("mt_nofollow_author");
	$change8 = get_option("mt_nofollow_sites");
	$change9 = get_option("mt_nofollow_ip");
	$change10 = get_option("mt_nofollow_posts");

if ($change5=="Yes" || $change5=="") {
$change5="checked";
$change51="";
} else {
$change5="";
$change51="checked";
}

if ($change6=="Yes" || $change6=="") {
$change6="checked";
$change61="";
} else {
$change6="";
$change61="checked";
}

if ($change7=="Yes" || $change7=="") {
$change7="checked";
$change71="";
} else {
$change7="";
$change71="checked";
}

if ($change10=="Yes" || $change10=="") {
$change10="checked";
$change101="";
} else {
$change10="";
$change101="checked";
}
    ?>
	<iframe src="http://www.jakeruston.co.uk/plugins/index.php" width="100%" height="20%">iframe support is required to see this.</iframe>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Links in the comment body are...", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_2; ?>" value="Yes" <?php echo $change6; ?>>DoFollow
<input type="radio" name="<?php echo $data_field_name_2; ?>" value="No" <?php echo $change61; ?>>NoFollow
</p><hr />

<p><?php _e("Links in the Website URL field are...", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_3; ?>" value="Yes" <?php echo $change7; ?>>DoFollow
<input type="radio" name="<?php echo $data_field_name_3; ?>" value="No" <?php echo $change71; ?>>NoFollow
</p><hr />

<p><?php _e("Links in Posts are...", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="Yes" <?php echo $change10; ?>>DoFollow
<input type="radio" name="<?php echo $data_field_name_7; ?>" value="No" <?php echo $change101; ?>>NoFollow
</p><hr />

<p><?php _e("(DoFollow Must Be Enabled, Comments Only) Only these sites are DoFollow (Leave blank to disable), one per line:", 'mt_trans_domain' ); ?> 
<textarea name="<?php echo $data_field_name_4; ?>" rows="5" cols="50"><?php echo $change8; ?></textarea>
</p><hr />

<p><?php _e("(DoFollow Must Be Enabled, Comments Only) Only these IPs are DoFollow (Leave blank to disable), one per line:", 'mt_trans_domain' ); ?> 
<textarea name="<?php echo $data_field_name_5; ?>" rows="5" cols="50"><?php echo $change9; ?></textarea>
</p><hr />

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="Yes" <?php echo $change5; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="No" <?php echo $change51; ?> id="Please do not disable plugin support - This is the only thing I get from creating this free plugin!" onClick="alert(id)">No
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>

<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validateEmail(ctrl){

var strMail = ctrl.value
        var regMail =  /^\w+([-.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (regMail.test(strMail))
        {
            return true;
        }
        else
        {

            return false;

        }
					
	}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(subject,"Subject must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(email,"E-Mail must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(feedback,"Feedback must be filled out!")==false)
  {email.focus();return false;}
  if (validateEmail(email)==false)
  {
  alert("E-Mail Address not valid!");
  email.focus();
  return false;
  }
 }
}
</script>
<h3>Submit Feedback about my Plugin!</h3>
<p><b>Note: Only send feedback in english, I cannot understand other languages!</b><br /><b>Please do not send spam messages!</b></p>
<form name="form2" method="post" action="" onsubmit="return validate_form(this)">
<p><?php _e("Your Name:", 'mt_trans_domain' ); ?> 
<input type="text" name="name" /></p>
<p><?php _e("E-Mail Address (Required):", 'mt_trans_domain' ); ?> 
<input type="text" name="email" /></p>
<p><?php _e("Message Category:", 'mt_trans_domain'); ?>
<select name="category">
<option value="General">General</option>
<option value="Feedback">Feedback</option>
<option value="Bug Report">Bug Report</option>
<option value="Feature Request">Feature Request</option>
<option value="Other">Other</option>
</select>
<p><?php _e("Message Subject (Required):", 'mt_trans_domain' ); ?>
<input type="text" name="subject" /></p>
<input type="checkbox" name="response" value="Yes" /> I want e-mailing back about this feedback</p>
<p><?php _e("Message Comment (Required):", 'mt_trans_domain' ); ?> 
<textarea name="feedback"></textarea>
</p>
<p class="submit">
<input type="submit" name="Send" value="<?php _e('Send', 'mt_trans_domain' ); ?>" />
</p><hr /></form>
</div>
<?php
}

if (get_option("jr_nofollow_links_choice")=="") {
nofollow_choice();
}


function enable_nofollow_link($comment2) {
$allowedsites2=get_option("mt_nofollow_sites");
$option_nofollow2=get_option("mt_nofollow_on");
$plugin_support2=get_option("mt_nofollow_plugin_support");
$option_ip2=get_option("mt_nofollow_ip");

if (!$option_ip2=="") {
$authorip=get_comment_author_IP();

if ($authorip=="") {
$authorip="584848484";
}

$pos=strpos($option_ip2, $authorip);

if (is_int($pos)) {
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment2);
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment2);
} else {
}
}

if (!$allowedsites2=="") {
preg_match('/http:\/\/[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+/', $comment2, $domainname2);
preg_match('@^(?:http://)?([^/]+)@i', $domainname2[0], $matches2);
$pos2=strpos($allowedsites2, $matches2[1]);

if ($pos2=="" || $pos2=="false") {
} else {
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment2);
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment2);
}
}

if ($allowedsites2=="" && $option_ip2=="") {
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment2);
$comment2 = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment2);
}

return $comment2;
}

function enable_nofollow_posts($comment) {
$allowedsites=get_option("mt_nofollow_sites");
$option_nofollow=get_option("mt_nofollow_on");
$plugin_support=get_option("mt_nofollow_plugin_support");

if (!$allowedsites=="") {
$i=preg_match_all('/http:\/\/[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+/', $comment, $domainname);
$j=0;

while ($j<$i) {
$pos=strpos($allowedsites, $domainname[0][$j]);
if ($pos=="") {
} else {
$comment = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment);
$comment = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment);
}
$j ++;
}
}

if ($allowedsites=="") {
$comment = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment);
$comment = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment);
}

return $comment;
}

function enable_nofollow_text($comment) {
$allowedsites=get_option("mt_nofollow_sites");
$option_nofollow=get_option("mt_nofollow_on");
$plugin_support=get_option("mt_nofollow_plugin_support");
$option_ip=get_option("mt_nofollow_ip");

if (!$option_ip=="") {
$authorip=get_comment_author_IP();

if ($authorip=="") {
$authorip="5857474";
}

$pos=strpos($option_ip, $authorip);

if (is_int($pos)) {
$comment = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment);
$comment = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment);
} else {
}
}

if (!$allowedsites=="") {
$i=preg_match_all('/http:\/\/[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+/', $comment, $domainname);
$j=0;

while ($j<$i) {
$pos=strpos($allowedsites, $domainname[0][$j]);
if ($pos=="") {
} else {
$comment = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment);
$comment = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment);
}
$j ++;
}
}

if ($allowedsites=="" && $option_ip=="") {
$comment = preg_replace("/(<a[^>]*[^\s])(\s*nofollow\s*)/i", "$1", $comment);
$comment = preg_replace("/(<a[^>]*[^\s])(\s*rel=[\"\']\s*[\"\'])/i", "$1", $comment);
}

return $comment;
}

function nofollow_plugin_support() {
global $single, $feed, $post;

if (!$feed && $single) {
$linkper=utf8_decode(get_option('jr_nofollow_link_personal'));

if (get_option("jr_nofollow_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_nofollow_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_nofollow_links_choice", $new);
update_option("jr_nofollow_link_newcheck", "444");
}

if (get_option("jr_submitted_nofollow")=="0") {
$pname="jr_nofollow";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_nofollow", "1");
update_option("jr_nofollow_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_nofollow_refresh'); 
} else if (get_option("jr_submitted_nofollow")=="") {
$pname="jr_nofollow";
$url=get_bloginfo('url');
$current=get_option("jr_nofollow_links_choice");
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname."&override=".$current);
update_option("jr_submitted_nofollow", "1");
update_option("jr_nofollow_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_nofollow_refresh'); 
}

echo "<p style='font-size:x-small'>NoFollow Plugin created by ".$linkper." - ".stripslashes(get_option('jr_nofollow_links_choice'))."</p>";
}
}

?>