<?php
require_once("htm.php");
if(isset($_SESSION['username'])) {
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
renderHeader("Rules");
require_once("elements/user-sidebar.php");
?>
<div class="main-column" id="help">
		<div class="post-list-outline">
			<h2 class="label">Rules</h2>
			<div id="guide" class="help-content">
                      		<div class="num1">
            <h2><?=$GLOBALS['name']?> Rules</h2>
            <p>To help keep the site in order, there are some rules we'd like you to follow.</p>
            <h3>Age</h3>
            <p>In accordance with COPPA laws, people under the age of 13 (or the age that applies in your country) may not join the site. If someone is found to be under said age, the user will be banned until their age is above the minimum required.</p>
            <h3>Inappropriate Content</h3>
            <p>Please do not post any of these things on the website:</p>
            <ul>
                <li>NSFW. If you've come here to post this, please leave.</li>
                <li>Depictions or pictures of gore. (NSFL) This is not allowed as it may scare some people.</li>
            </ul>
            <h3>Common Decency</h3>
            <p>Here at <?=$GLOBALS['name']?>, we try to be polite and want users of all kinds to be comfortable on our site. To keep this standard up, we ask that you follow a few common decency rules:</p>
            <ul>
                <li>Harassment, hate speech, and raiding are not allowed anywhere on <?=$GLOBALS['name']?>, and action will be taken accordingly. What constitutes hate speech will be decided by the admins.</li>
                <li>Hacking and/or doxxing is also against the rules, and will be punished with a ban if necessary.</li>
                <li>Do not spam. Spam can be considered doing multiple actions repeately in a short period of time, such as posting, replying, yeahbombing, etc.</li>
            </ul>
            </div>
            <p>That should be about all the rules. Only 3 in total, albeit rather verbose ones. Next, we'll cover some things that aren't rules, but rather general information you may want to know.</p>
            <div class="num2">
            <h2><?=$GLOBALS['name']?>'s Cookie Policy</h2>
            <p><small>"their" corrosponds to the user.</small></p>
            <p><?=$GLOBALS['name']?> handles cookies very strictly. The only information <?=$GLOBALS['name']?> holds is their Username, Token, User ID, Avatar settings, level and badge.</p>
            <p><?=$GLOBALS['name']?> refuses to and will not collect information from the user that is not necessary for the site to work properly.</p>
            <p><b>Any third party cookies handled by other sites are not managed by <?=$GLOBALS['name']?>, and <?=$GLOBALS['name']?> cannot be held responsible for those cookies.</b></p>
        </div>
        </div>
		    </div>
	    </div>
</div>
</body>
</html>