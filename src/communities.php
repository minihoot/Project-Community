<?php
require_once("htm.php");
if($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"){
    $offset = $_GET["offset"];
                $stmt = $db->prepare('SELECT id, posted_by, body, feeling, created_at, image, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0) AS yeah_count, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0 AND source = ?) AS yeah_added, spoilers, status FROM posts WHERE community = ? ORDER BY id DESC LIMIT 20, ?');
                $stmt->bind_param('iii', $_SESSION['id'], $_GET['id'], $offset);
                $stmt->execute();
                if($stmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing posts from the database.</div>';
                }
                $result = $stmt->get_result();
                if($result->num_rows === 0) {
                    if($_GET['offset'] == 0) {
                        echo '<div class="no-content">No posts were found.</div>';
                    }
                } else {
                    while($row = $result->fetch_assoc()) {
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
                        require('elements/post.php');
                    }
                }
                exit();
}

$stmt = $db->prepare('SELECT name, description, icon, banner, type, (SELECT COUNT(*) FROM favorite_communities WHERE source = ? AND target = communities.id) AS favorite_given, permissions FROM communities WHERE id = ?');
$stmt->bind_param('ii', $_SESSION['id'], $_GET['id']);
$stmt->execute();
if($stmt->error) {
exit("<div class=\"no-content\"><p>An error occurred while trying to get the community's data.</p></div>");
}
$result = $stmt->get_result();
if($result->num_rows === 0) {
    renderHeader("Not Found");
    exit("<div class=\"no-content\"><p>The community could not be found.</p></div>");
}
$row = $result->fetch_array();
$selected = 'community';
renderHeader(htmlspecialchars($row['name']));
?>
<div id="sidebar">
    <section class="sidebar-container" id="sidebar-community">
                    <span id="sidebar-cover">
                <a href="/communities/<?=$_GET['id']?>"><img src="<?=htmlspecialchars($row['banner'])?>"></a>
            </span>
                <header id="sidebar-community-body">
            <span id="sidebar-community-img">
                <span class="icon-container">
                    <a href="/communities/<?=$_GET['id']?>"><img src="<?=htmlspecialchars($row['icon'])?>" class="icon"></a>
                </span>
                <span class="platform-tag"><?=getCommunityType($row['type'])?></span>            </span>
                        <h1 class="community-name"><a href="/communities/<?=$_GET['id']?>"><?=htmlspecialchars($row['name'])?></a></h1>
        </header>
                    <div class="community-description js-community-description">
                                    <p class="text js-truncated-text"><?=htmlspecialchars($row['description'])?></p>
                            </div>
                            <?php if(isset($_SESSION['username'])) { ?>
                                    <button type="button" class="symbol button favorite-button<?=$row['favorite_given'] === 1 ? ' checked' : ''?>" data-action-favorite="/communities/<?=$_GET['id']?>/favorite.json" data-action-unfavorite="/communities/<?=$_GET['id']?>/unfavorite.json">
                    <span class="favorite-button-text">Favorite</span>
                </button>
                <?php } ?>
                                <div class="sidebar-setting">
      <div class="sidebar-post-menu">
          <a href="/communities/<?=$_GET['id']?>/topic" class="sidebar-menu-topic symbol">
            <span>Discussions</span>
          </a>
          <a href="/communities/<?=$_GET['id']?>" class="sidebar-menu-post symbol selected">
            <span>Posts</span>
          </a>
      </div>
    </div>
        </section>
</div>
<div class="main-column">
    <div class="post-list-outline">
        <h2 class="label">Posts</h2>
        <?php if(!empty($_SESSION['username'])) { ?>
                        <form id="post-form" method="post" action="/posts" class="for-identified-user folded" data-post-subtype="default">
                    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                    <input type="hidden" name="community" value="<?=$_GET['id']?>">
                    <div class="feeling-selector js-feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label></div>
                    <div class="textarea-with-menu">
                        <div class="textarea-container">
                            <textarea name="body" class="textarea-text textarea" maxlength="2000" placeholder="Share your thoughts in a post to this community." data-open-folded-form="" data-required=""></textarea>
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
                        <input type="submit" class="black-button post-button disabled" value="Send" data-community-id="1" data-post-content-type="text" data-post-with-screenshot="nodata" disabled="">
                    </div>
                </form>
                <?php } ?>
                    <div class="body-content" id="community-post-list">
            <?php
            if(empty($_GET['offset'])) {
                    $_GET['offset'] = 0;
                }
                $stmt = $db->prepare('SELECT id, posted_by, body, feeling, created_at, image, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0) AS yeah_count, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0 AND source = ?) AS yeah_added, spoilers, status FROM posts WHERE community = ? ORDER BY id DESC LIMIT 20 OFFSET ?');
                $stmt->bind_param('iii', $_SESSION['id'], $_GET['id'], $_GET['offset']);
                $stmt->execute();
                if($stmt->error) {
                    echo '<div class="no-content">An error occurred while grabbing posts from the database.</div>';
                }
                $result = $stmt->get_result();
                if($result->num_rows === 0) {
                    if($_GET['offset'] == 0) {
                        echo '<div class="no-content">No posts were found.</div>';
                    }
                } else {
                    echo '<div class="list post-list js-post-list" data-next-page-url="?offset=' . ($_GET['offset'] + 20) . '">';
                    while($row = $result->fetch_assoc()) {
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
                        require('elements/post.php');
                    }
                    echo '</div>';
                }
                ?>
                
        </div>
    </div>
</div>