<?php
require_once("htm.php");
$show = false;
if(isset($_SESSION['username'])) {
$show = true;
$ustmt = $db->prepare('SELECT id, username, (SELECT COUNT(*) FROM posts WHERE posted_by = users.id) AS post_count, (SELECT COUNT(*) FROM yeahs WHERE source = users.id) AS yeah_count, level, badge FROM users WHERE username = ?');
$ustmt->bind_param('s', $_SESSION['username']);
$ustmt->execute();
if($ustmt->error) {
     echo '<div class="no-content">An error occurred while grabbing user data from the database.</div>';
}
$uresult = $ustmt->get_result();
$urow = $uresult->fetch_assoc();
if($uresult->num_rows === 0) {
    renderHeader("Not Found");
    exit('<div class="no-content"><p>The user could not be found.</p></div>');
}

$pstmt = $db->prepare('SELECT nickname, birthday, country, avatar, has_mh, profile_comment, nnid FROM profiles WHERE for_user = ?');
$pstmt->bind_param('i', $urow['id']);
$pstmt->execute();
if($pstmt->error) {
     echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
}
$presult = $pstmt->get_result();
$prow = $presult->fetch_assoc();
}
$is_general = true;
renderHeader("Contact Us");
require_once("elements/user-sidebar.php");
if(isset($_POST["h-captcha-response"])){
$url = 'https://hcaptcha.com/siteverify';
$data = array('response' => $_POST["h-captcha-response"], 'secret' => $GLOBALS["h-captcha-secret"]);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$resjson = json_decode($result);
if($resjson->{'success'} == true){
    $show = true;
} else {
    $show = false;
}
}
?>
<div class="main-column" id="help">
		<div class="post-list-outline">
			<h2 class="label">Contact Us</h2>
			<div id="guide" class="help-content">
			    <p>Get in touch with staff members if you need to for whatever reason.</p>
			    <?php if($show == false AND $GLOBALS['h-captcha-secret'] != NULL) { ?>
			    <p>Since you aren't signed in, you'll have to complete a captcha, sorry.</p>
			    <form method="POST">
			        <div class="h-captcha" data-sitekey="<?=$GLOBALS['h-captcha-sitekey']?>" data-theme="<?php if($_SESSION["light_mode"] == true){ ?>light<?php } else { ?>dark<?php } ?>"></div>
			        <input type="submit" value="Submit" />
			    </form>
			    <?php } else { ?>
				<h2>Terminal (owner)</h2>
				<p>The owner of the site who came up with the idea but only did 45% of the work because Jack (bruhdude) is amazing.</p>
				<p><a href="mailto:support@community.isledelfino.net">Click to email (support@community.isledelfino.net)</a></p>
				<h2>bruhdude (co-owner)</h2>
				<p>The co-owner of the site who is epic and did most of the development.</p>
				<p><a href="mailto:exmaple@example.com">Click to email (example@example.com, did not insert email.)</a></p>
				<?php } ?>
		    </div>
	    </div>
</div>
</body>
</html>