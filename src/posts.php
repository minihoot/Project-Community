<?php
require_once("htm.php");
renderHeader("Post");
$stmt = $db->prepare('SELECT id, posted_by, body, community, created_at, image, feeling, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0) AS yeah_count, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0 AND source = ?) AS yeah_added, spoilers, status FROM posts WHERE id = ?');
$stmt->bind_param('ii', $_SESSION['id'], $_GET['id']);
$stmt->execute();
if($stmt->error) {
    renderHeader("Not Found");
exit("<div class=\"no-content\"><p>An error occurred while trying to get the post's data.</p></div>");
}
$result = $stmt->get_result();
if($result->num_rows === 0) {
    renderHeader("Not Found");
    exit("<div class=\"no-content\"><p>The post could not be found.</p></div>");
}
$row = $result->fetch_array();

$pstmt = $db->prepare('SELECT nickname, avatar, has_mh FROM profiles WHERE for_user = ?');
$pstmt->bind_param('i', $row['posted_by']);
$pstmt->execute();
if($pstmt->error) {
    echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
}
$presult = $pstmt->get_result();
$prow = $presult->fetch_assoc();

$ustmt = $db->prepare('SELECT username, level, badge FROM users WHERE id = ?');
$ustmt->bind_param('i', $row['posted_by']);
$ustmt->execute();
if($ustmt->error) {
    echo '<div class="no-content">An error occurred while grabbing user data from the database.</div>';
}
$uresult = $ustmt->get_result();
$urow = $uresult->fetch_assoc();

$cstmt = $db->prepare('SELECT id, name, description, icon, banner, type, permissions FROM communities WHERE id = ?');
$cstmt->bind_param('i', $row['community']);
$cstmt->execute();
if($cstmt->error) {
exit("<div class=\"no-content\"><p>An error occurred while trying to get the community's data.</p></div>");
}
$cresult = $cstmt->get_result();
$crow = $cresult->fetch_array();
?>
<div id="post-permlink" class="main-column">
        <div class="post-list-outline">
        <section id="post-content" class="post post-subtype-default">
                            <p class="community-container">
                    
                    <a href="/communities/<?=htmlspecialchars($crow['id'])?>"><img src="<?=htmlspecialchars($crow['icon'])?>" class="community-icon"><?=htmlspecialchars($crow['name'])?></a>
                </p>
                        <div class="user-content">
                <a href="/users/<?=htmlspecialchars($urow['username'])?>" class="icon-container<?=$urow['badge'] == 1 ? ' official-user' : '', $urow['badge'] == 2 ? ' wiimote' : ''?>">
                    <img src="<?=getAvatar($prow['avatar'], $prow['has_mh'], $row['feeling'])?>" class="icon">
                                    </a>
                <div class="user-name-content">
                    <p class="user-name">
                        <a href="/users/<?=htmlspecialchars($urow['username'])?>"><?=htmlspecialchars($prow['nickname'])?></a>
                        <span class="user-id"><?=htmlspecialchars($urow['username'])?></span>
                    </p>
                    <p class="timestamp-container">
                        <span class="timestamp"><?=getTimestamp($row['created_at'])?></span>
                        <span class="spoiler-status<?=$row['spoilers'] ? ' spoiler' : ''?>"> • Spoilers</span>
                    </p>
                </div>
            </div>
            <div class="body">
                                    <div class="post-content-text"><?=$row['body']?></div>
                                    <?php if(!empty($row['image'])) { ?>
                        <div class="screenshot-container still-image">
                            <img src="<?=htmlspecialchars($row['image'])?>">
                        </div>
                    <?php } ?>
                                            <div class="post-meta">
                            <button type="button" class="symbol submit empathy-button<?=$row['yeah_added'] ? ' empathy-added' : ''?>" data-feeling="<?=getFeeling($row['feeling'])?>" data-action="/posts/<?=$row['id']?>/yeah" data-url-id="<?=$row['id']?>" <?php if(!isset($_SESSION["username"])){ ?> disabled <?php } ?>>
                <span class="empathy-button-text"><?=$row['yeah_added'] ? 'Unyeah' : getFeelingText($row['feeling'])?></span>
            </button>
            <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?=$row['yeah_count']?></span></div>
                            <div class="reply symbol"><span class="symbol-label">Replies</span><span class="reply-count">0</span></div></div>                </div>
            </section>
                    <div id="empathy-content"<?=$row['yeah_count'] === 0 ? ' class="none"' : ''?>>
                <a href="/users/<?=$_SESSION['username']?>" class="post-permalink-feeling-icon visitor"<?=$row['yeah_added'] === 0 ? ' style="display: none;"' : ''?>><img src="<?=getAvatar($_SESSION['avatar'], $_SESSION['has_mh'], $row['feeling'])?>" class="user-icon"></a>
                <?php
                $type = 0;
                $stmt = $db->prepare('SELECT users.id, username, level FROM users LEFT JOIN yeahs ON source = users.id WHERE target = ? AND type = ? ORDER BY yeahs.id DESC LIMIT 14');
                $stmt->bind_param('ii', $row['id'], $type);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while($empathy = $result->fetch_assoc()) {
                    if($empathy['id'] !== $_SESSION['id']) {
                        $pstmt = $db->prepare('SELECT avatar, has_mh FROM profiles WHERE for_user = ?');
$pstmt->bind_param('i', $empathy['id']);
$pstmt->execute();
if($pstmt->error) {
    echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
}
$presult = $pstmt->get_result();
$prow = $presult->fetch_assoc();
                        echo '<a href="/users/' . htmlspecialchars($empathy['username']) . '" class="post-permalink-feeling-icon"><img src="' . getAvatar($prow['avatar'], $prow['has_mh'], $row['feeling']) . '" class="user-icon"></a>';
                    }
                }
                ?>
                </div>
                            <br>
                <div class="buttons-content">
                    
                    
                    
                                            <div class="edit-buttons-content">
                            <button type="button" class="symbol button edit-button edit-post-button" data-modal-open="#edit-post-page">
                                <span class="symbol-label">Edit</span>
                            </button>
                        </div>
                                        </div>
                            <div id="reply-content">
                    <h2 class="reply-label">Comments</h2>
                    <div class="no-reply-content">
                        <p>This post has no comments.</p>
                    </div>
                                        <div class="list reply-list"></div>
                </div>
                <h2 class="reply-label">Add a Comment</h2>
                <?php if(empty($_SESSION['username'])) { ?>
                <div class="no-content">
                        <p>You must sign in to post a comment.<br><br>Sign in with a <?=$GLOBALS["name"]?> account to connect to users around the world by writing posts and comments and by giving Yeahs to other people's posts. You can sign up for a <?=$GLOBALS["name"]?> account <a href="/signup">here</a>.</p>
                    </div>
                <?php } else { ?>
                                    <form id="reply-form" method="post" action="/posts/id/replies" class="for-identified-user" data-post-subtype="default">
                        <input type="hidden" name="token" value="token">
                        <div class="feeling-selector js-feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label></div>
                        <div class="textarea-with-menu">
                            <div class="textarea-container">
                                <textarea name="body" class="textarea-text textarea" maxlength="2000" placeholder="Add a comment here." data-required=""></textarea>
                            </div>
                        </div>
                        <label class="file-button-container">
                            <span class="input-label">Image
                                <span>PNG, JPEG and GIF files are allowed.</span>
                            </span>
                            <input accept="image/*" type="file" class="file-button">
                            <input type="hidden" name="image">
                        </label>
                        <div class="post-form-footer-options">
                            <div class="post-form-footer-option-inner post-form-spoiler js-post-form-spoiler">
                                <label class="spoiler-button symbol"><input type="checkbox" name="spoilers" value="1"> Spoilers</label>
                            </div>
                        </div>
                        <div class="form-buttons">
                            <input type="submit" class="black-button post-button disabled" value="Send" data-post-content-type="text" data-post-with-screenshot="nodata" disabled="">
                        </div>
                    </form>
                <?php } ?>               
                </div>
                        <div id="edit-post-page" class="dialog none" data-modal-types="edit-post">
                <div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Edit Post</h1>
                        <div class="window-body">
                        <form method="post" class="edit-post-form">
                            <p class="select-button-label">Select an action:</p>
                            <select name="edit-type">
                                <option value="" selected="">Select an option.</option>
                                <option value="spoiler" data-action="/posts/id/spoiler">Set as Spoiler</option>                                                         <option value="delete" data-action="/posts/703.delete" data-track-action="deletePost" data-track-category="post">Delete</option>
                            </select>
                            <div class="form-buttons">
                                <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
                                <input type="submit" class="post-button black-button" value="Confirm">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
                <div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Report Violation to <?=$GLOBALS["name"]?> Administrators</h1>
                        <div class="window-body">
                            <p class="description">You are about to report a post with content which violates the (Insert Name) Code of Conduct. This report will be sent to the (Insert Name) administrators and not to the creator of the post.</p>
                            <form method="post" action="/posts/id/report">
                                <input type="hidden" name="token" value="token">
                                <select name="type" class="can-report-spoiler">
                                    <option value="">Select who should see the report.</option>
                                    <option value="1" data-body-required="1"><?=$GLOBALS["name"]?> Staff</option>
                                </select>
                                <textarea name="body" class="textarea" maxlength="100" data-placeholder="Enter a reason for the report here." placeholder="Enter a reason for the report here."></textarea>
                                <div class="form-buttons">
                                    <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
                                    <input type="submit" class="post-button black-button disabled" value="Submit Report" disabled="">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>