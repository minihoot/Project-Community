<?php
require_once("htm.php");
$ustmt = $db->prepare('SELECT id, username, (SELECT COUNT(*) FROM posts WHERE posted_by = users.id) AS post_count, (SELECT COUNT(*) FROM yeahs WHERE source = users.id) AS yeah_count, level, badge FROM users WHERE username = ?');
$ustmt->bind_param('s', $_GET['username']);
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
$is_profile = 1;
renderHeader("User");
require_once("elements/user-sidebar.php");
?>
<div class="main-column">
    <div class="post-list-outline">
        <h2 class="label">Recent Posts</h2>
        <?php
        $stmt = $db->prepare('SELECT id, posted_by, body, community, feeling, image, spoilers, created_at, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0) AS yeah_count, (SELECT icon FROM communities WHERE id = posts.community) AS icon, (SELECT name FROM communities WHERE id = posts.community) AS name, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0 AND source = ?) AS yeah_added, status FROM posts WHERE posted_by = ? ORDER BY id DESC LIMIT 3');
        $stmt->bind_param('ii', $_SESSION['id'], $urow['id']);
        $stmt->execute();
        if($stmt->error) {
            echo '<div class="post-list empty"><p>There was an error while fetching posts.</p></div>';
        } else {
            $result = $stmt->get_result();
            if($result->num_rows === 0) {
                echo '<div class="post-list empty"><p>This user hasn\'t posted anything yet.</p></div>';
            } else {
                ?><div class="post-body">
                    <div class="list multi-timeline-post-list">
                        <?php
                        while($row = $result->fetch_assoc()) {
                            require('elements/post.php');
                        }
                        ?>
                    </div>
                </div><?php
            }
        }
        ?>
    </div>
    <?php if($result->num_rows > 0) { ?><a href="/users/<?=htmlspecialchars($urow['username'])?>/posts" class="big-button">View Posts</a><?php } ?>
</body>
</html>