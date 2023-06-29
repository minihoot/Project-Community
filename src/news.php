<?php
require_once("htm.php");
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
$is_general = true;
$butt = 'butt';
renderHeader("Notifications");
require_once("elements/user-sidebar.php");
?>
<div class="main-column messages">
    <div class="post-list-outline">
        <h2 class="label">Notifications</h2>
        <div class="list news-list">
            <?php
            $stmt = $db->prepare('SELECT id, source, target, type, seen, origin, merged, created_at FROM notifications WHERE target = ? AND type = 1 OR type = 2 ORDER BY id DESC LIMIT 255');
                $stmt->bind_param('i', $_SESSION['id']);
                $stmt->execute();
                if($stmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing notifications from the database.</div>';
                }
                $result = $stmt->get_result();
                if($result->num_rows == 0) {
                        echo '<center><br><br><div class="no-content">No notifications. How sad.</div></center>';
                } else {
                    while($row = $result->fetch_assoc()) {
                        if($row['type'] === 1) {
                            $psstmt = $db->prepare('SELECT id, body FROM posts WHERE id = ?');
                $psstmt->bind_param('i', $row['origin']);
                $psstmt->execute();
                if($psstmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing post data from the database.</div>';
                }
                $psresult = $psstmt->get_result();
                $psrow = $psresult->fetch_assoc();
                        }
                        $pstmt = $db->prepare('SELECT nickname, avatar, has_mh FROM profiles WHERE for_user = ?');
                $pstmt->bind_param('i', $row['source']);
                $pstmt->execute();
                if($pstmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
                }
                $presult = $pstmt->get_result();
                $prow = $presult->fetch_assoc();
                
                $ustmt = $db->prepare('SELECT username, level FROM users WHERE id = ?');
                $ustmt->bind_param('i', $row['source']);
                $ustmt->execute();
                if($ustmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing user data from the database.</div>';
                }
                $uresult = $ustmt->get_result();
                $urow = $uresult->fetch_assoc();?>
                        <div class="news-list-content trigger" id="<?=$row['id']?>" data-href="/posts/<?=$row['origin']?>" tabindex="0"><a href="/users/<?=htmlspecialchars($urow['username'])?>" class="icon-container official-user"><img class="icon" src="<?=getAvatar($prow['avatar'], $prow['has_mh'], 0)?>"></a><div class="body"><a href="/users/<?=htmlspecialchars($urow['username'])?>" class="nick-name"><?=htmlspecialchars($prow['nickname'])?></a> gave <a href="/posts/<?=$psrow['id']?>">your post (<?=getPreview($psrow['body'])?>)</a> a Yeah. <span class="timestamp"><?=getTimestamp($row['created_at'])?></span></div></div>
                    <?php }
                }
                $stmt = $db->prepare('DELETE FROM notifications WHERE created_at < ? AND target = ? AND merged IS NULL');
                $stmt->bind_param('si', $row['created_at'], $_SESSION['id']);
                $stmt->execute();
                $stmt = $db->prepare('UPDATE notifications SET seen = 1 WHERE target = ?');
                $stmt->bind_param('i', $_SESSION['id']);
                $stmt->execute();
            ?>
        </div>
    </div>
</div>