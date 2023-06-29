<?php
require_once("htm.php");
$ustmt = $db->prepare('SELECT id, username, (SELECT COUNT(*) FROM posts WHERE posted_by = users.id) AS post_count, (SELECT COUNT(*) FROM yeahs WHERE source = users.id) AS yeah_count, level FROM users WHERE username = ?');
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
renderHeader($_GET['username']);
require_once("elements/user-sidebar.php");
$type = ($_GET['type'] === 'yeahs' ? 1 : 0);
?>
<div class="main-column">
    <div class="post-list-outline">
        <h2 class="label"><?=($_SESSION['id'] === $urow['id'] ? 'Your ' : htmlspecialchars($prow['nickname']) . '\'s ') . ($type === 1 ? 'Yeahs' : 'Posts')?></h2>
        <div class="list post-list js-post-list" data-next-page-url="<?php
            $offset = (!empty($_GET['offset']) ? $_GET['offset'] : '0');
            if($type === 1) {
                $stmt = $db->prepare('SELECT posted_by, body, community, feeling, image, created_at, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id) AS yeah_count, (SELECT icon FROM communities WHERE id = posts.community) AS icon, (SELECT name FROM communities WHERE id = posts.community) AS name, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND source = ?) AS yeah_added, yeahs.id AS yeah_id, status FROM posts LEFT JOIN users ON posted_by = users.id LEFT JOIN communities ON communities.id = community LEFT JOIN yeahs ON target = posts.id WHERE source = ? AND posts.status = 0 ORDER BY yeah_id DESC LIMIT 20 OFFSET ?');
                $stmt->bind_param('iii', $_SESSION['id'], $urow['id'], $offset);
            } else {
                $stmt = $db->prepare('SELECT posted_by, body, community, feeling, image, created_at, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id) AS yeah_count, (SELECT icon FROM communities WHERE id = posts.community) AS icon, (SELECT name FROM communities WHERE id = posts.community) AS name, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND source = ?) AS yeah_added, status FROM posts WHERE posted_by = ? AND posts.status = 0 ORDER BY posts.id DESC LIMIT 20 OFFSET ?');
                $stmt->bind_param('iiii', $_SESSION['id'], $urow['id'], $offset);
            }
            $stmt->execute();
            if($stmt->error && $offset === '0') {
                echo '">';
                showNoContent('There was an error while fetching the user\'s ' . ($type === 1 ? 'Yeahs' : 'posts') . '.');
            } else {
                $result = $stmt->get_result();
                if($result->num_rows === 0) {
                    echo '">';
                    if($offset === '0') {
                        showNoContent('This user hasn\'t ' . ($type === 1 ? 'given any Yeahs' : 'posted anything') . ' yet.');
                    }
                } else {
                    echo '?offset=' . ($offset + 20) . '">';
                    while($row = $result->fetch_assoc()) {
                            $psstmt = $db->prepare('SELECT nickname, birthday, country, avatar, has_mh FROM profiles WHERE for_user = ?');
                            $psstmt->bind_param('i', $row['posted_by']);
                            $psstmt->execute();
                            if($psstmt->error) {
                                echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
                            }
                            $psresult = $psstmt->get_result();
                            $psrow = $psresult->fetch_assoc();
                            $usstmt = $db->prepare('SELECT username, level FROM users WHERE id = ?');
                            $usstmt->bind_param('i', $row['posted_by']);
                            $usstmt->execute();
                            if($usstmt->error) {
                                echo '<div class="no-content">An error occurred while grabbing user data from the database.</div>';
                            }
                            $usresult = $usstmt->get_result();
                            $usrow = $uresult->fetch_assoc();
                            if($usresult->num_rows === 0) {
                                renderHeader("Not Found");
                                exit('<div class="no-content"><p>The user could not be found.</p></div>');
                            }
                            echo $row['id'];
                        require('elements/user-post.php');
                    }
                }
            }
            ?>
        </div>
    </div>
</div>