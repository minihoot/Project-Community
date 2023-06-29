<div id="sidebar" class="<?php if($is_general === true) { ?>general <?php } else { ?>user<?php } ?>-sidebar">
<div class="sidebar-container">
      <div id="sidebar-profile-body">
          <a href="/users/<?=htmlspecialchars($urow['username'])?>" class="icon-container<?=$urow['badge'] == 1 ? ' official-user' : '', $urow['badge'] == 2 ? ' wiimote' : ''?>">
            <img src="<?=getAvatar($prow['avatar'], $prow['has_mh'], 0)?>" alt="'<?=htmlspecialchars($urow['username'])?>" class="icon">
          </a>
        <a href="/users/<?=htmlspecialchars($urow['username'])?>" class="nick-name"><?=htmlspecialchars($prow['nickname'])?></a>
        <p class="id-name"><?=htmlspecialchars($urow['username'])?></p>
      </div>
    <?php if(!empty($_SESSION['username']) && ($_SESSION['id'] == $urow['id'])) { ?>
        <div id="edit-profile-settings"><a class="button symbol" href="/settings/profile">Profile Settings</a></div>
    <?php } ?>
    <ul id="sidebar-profile-status">
        <li><a href="/users/<?=htmlspecialchars($urow['username'])?>/following"><span class="number">0</span>Following</a></li>
        <li><a href="/users/<?=htmlspecialchars($urow['username'])?>/followers"><span class="number">0</span>Followers</a></li>
      </ul>
    </div>
    <?php if($is_general === true) { ?>
<div class="sidebar-setting sidebar-container">
		  <ul>
			<li><a href="/settings/account" class="sidebar-menu-setting symbol"><span><?=$GLOBALS['name']?> Settings</span></a></li>
			<li><a href="/communities/1" class="sidebar-menu-info symbol"><span><?=$GLOBALS['name']?> Announcements</span></a></li>
	        <li><a href="/guide" class="sidebar-menu-guide symbol"><span><?=$GLOBALS['name']?> Rules</span></a></li>
		  </ul>
		</div>
		<?php } else { ?>
		<div class="sidebar-setting sidebar-container">
            <div class="sidebar-post-menu">
                <a href="/users/<?=htmlspecialchars($urow['username'])?>/posts" class="sidebar-menu-post with-count symbol">
                    <span>All Posts</span>
                    <span class="post-count">
                        <span class="test-post-count"><?=$urow['post_count']?></span>
                    </span>
                </a>
                <a href="/users/<?=htmlspecialchars($urow['username'])?>/yeahs" class="sidebar-menu-empathies with-count symbol">
                    <span>Yeahs</span>
                    <span class="post-count">
                        <span class="test-empathy-count"><?=$urow['yeah_count']?></span>
                    </span>
                </a>
                <a href="/users/<?=htmlspecialchars($urow['username'])?>/replies" class="sidebar-menu-replies with-count symbol">
                    <span>Replies</span>
                    <span class="post-count">
                        <span class="test-empathy-count">N/A</span>
                    </span>
                </a>
            </div>
        </div>
        <div class="sidebar-container sidebar-profile">
                            <?php if(!empty($prow['profile_comment'])) { ?>
                <div class="profile-comment">
                    <?php if(mb_strlen($prow['profile_comment']) > 103) { ?>
                        <p class="js-truncated-text"><?=nl2br(htmlspecialchars(mb_substr($prow['profile_comment'], 0, 100)))?>...</p>
                        <p class="js-full-text none"><?=nl2br(htmlspecialchars($prow['profile_comment']))?></p>
                        <button type="button" class="description-more-button js-open-truncated-text-button">Show More</button>
                    <?php } else { ?>
                        <p class="js-truncated-text"><?=nl2br(htmlspecialchars($prow['profile_comment']))?></p>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php
					if (empty($prow['birthday']))
					{
						$birthday = "-----";
					}
					else
					{
						$birthday = $prow['birthday'];
					}
					
					if (empty($prow['country']))
					{
						$country = "-----";
					}
					else
					{
						$country = $prow['country'];
					}
				?>
                        <div class="user-data">
				                <div class="data-content">
                    <h4><span>ID</span></h4>
                    <div class="note">#<?=htmlspecialchars($urow['id'])?></div>
                </div>
                <div class="data-content">
                    <h4><span>NNID</span></h4>
                    <div class="note"><?=htmlspecialchars($prow['nnid'])?></div>
                </div>
                <div class="data-content">
                    <h4><span>Joined</span></h4>
                    <div class="note">00/00/0000 12:00 AM</div>
                </div>
                <div class="data-content">
                    <h4><span>Last Seen</span></h4>
                    <div class="note">00/00/0000 12:00 AM</div>
                </div>
				<div class="user-main-profile data-content">
                    <h4><span>Country</span></h4>
                    <div class="note"><?=htmlspecialchars($country)?></div>
                    <h4><span>Birthday</span></h4>
                    <div class="note birthday"><?=htmlspecialchars($birthday)?></div>
                </div>
            </div>
        </div>
        <?php
        $stmt = $db->prepare('SELECT communities.id, icon FROM communities LEFT JOIN favorite_communities ON communities.id = target WHERE source = ? ORDER BY id DESC LIMIT 10');
        $stmt->bind_param('i', $urow['id']);
        $stmt->execute();
        if(!$stmt->error) {
            $result = $stmt->get_result();
            if($result->num_rows > 0) { ?>
                <div class="sidebar-container sidebar-favorite-community">
                    <h4><a href="/users/<?=htmlspecialchars($urow['username'])?>/favorites" class="favorite-community-button symbol"><span>Favorite Communities</span></a></h4>
                    <ul>
                        <?php while($frow = $result->fetch_assoc()) { ?>
                            <li class="favorite-community">
                                <a href="/communities/<?=$frow['id']?>">
                                    <span class="icon-container">
                                        <img class="icon" src="<?=htmlspecialchars($frow['icon'])?>">
                                    </span>
                                </a>
                            </li>
                        <?php }
                        for($i = $result->num_rows; $i < 10; $i++) { ?>
                            <li class="favorite-community empty">
                                <span class="icon-container empty-icon">
                                    <img class="icon" src="/assets/img/empty.png">
                                </span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php }
        }
    } ?>
		</div>