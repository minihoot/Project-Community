<?php
require_once("htm.php");
$stmt = $db->prepare('SELECT name, description, icon, banner, type, permissions FROM communities WHERE id = ?');
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error) {
exit("<div class=\"no-content\"><p>An error occurred while trying to get the community's data.</p></div>");
}
$result = $stmt->get_result();
if($result->num_rows === 0) {
    exit("<div class=\"no-content\"><p>The community could not be found.</p></div>");
}
$row = $result->fetch_array();
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
                                    <button type="button" class="symbol button favorite-button" data-action-favorite="/communities/<?=$_GET['id']?>/favorite" data-action-unfavorite="/communities/66/unfavorite.json">
                    <span class="favorite-button-text">Favorite</span>
                </button>
                <?php } ?>
                                <div class="sidebar-setting">
      <div class="sidebar-post-menu">
          <a href="/communities/<?=$_GET['id']?>/topic" class="sidebar-menu-topic symbol selected">
            <span>Discussions</span>
          </a>
          <a href="/communities/<?=$_GET['id']?>" class="sidebar-menu-post symbol">
            <span>Posts</span>
          </a>
      </div>
    </div>
        </section>
</div>
<div class="main-column">
    <div class="post-list-outline">
        <h2 class="symbol label label-topic">
        New Discussions
    </h2>
                    <div class="body-content" id="community-post-list">
            <div class="no-content"><p>Discussions have not been implemented yet.<br>In the meantime, why don't you check out other features of the website?</p></div>            
        </div>
    </div>
</div>