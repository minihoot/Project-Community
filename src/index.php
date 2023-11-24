<?php
require_once("htm.php");
if(isset($_GET["light-mode"])){
    if($_GET["light-mode"]){
        $_SESSION["light_mode"] = false;
    } else {
        $_SESSION["light_mode"] = true;
    }
}
$selected = 'community';
renderHeader("Home");
?>
<div class="body-content" id="community-top" data-region="USA">
        <div class="community-top-sidebar">
        <form method="GET" action="/search" class="search">
            <input type="text" name="query" placeholder="Search" maxlength="255"><input type="submit" value="q" title="Search">
        </form>
        <div class="post-list-outline index-memo">
			<h2 class="label">Welcome to <?=$GLOBALS['name']?>!</h2>
			<div style="width: 90%; display: inline-block; padding-bottom: 10px;">
				<p><br><?=$GLOBALS['name']?> is the revolutionary Miiverse Clone Experience. (Or at least, we tried to make it revolutionary.)</p>
			<h2 class="memo-head">this is fucking amazing</h2>
			<p>oh wait, it isnt</p>
			<h2 class="memo-head">i don't know why</h2>
			<p>but its obvious</p>
			<h2 class="memo-head">why am i doing this..</h2>
			<p>im so fucking burnt out of rehosting mvcs, to be honest i may quit</p>
			<h2 class="memo-head">chances are i wont</h2>
			<p>time is valuable</p>
			<h2 class="memo-head">and im wasting it every single fucking day</h2>
			<p>when will i use my time wisely</p>
				<p>this is currently in beta, expect broken and non-implemented shit</p>
			<a href="/">eh, im done talking, here is a random button</a>
			</div>
		</div>
        </div>
     <div class="community-main">
        <?php
        if(isset($_SESSION["username"])){ ?>
        <h3 class="community-title symbol community-favorite-title">Favorite Communities</h3>
        <?php
            $stmt = $db->prepare('SELECT communities.id, icon FROM communities LEFT JOIN favorite_communities ON communities.id = target WHERE source = ? ORDER BY id DESC LIMIT 8');
            $stmt->bind_param('i', $_SESSION['id']);
            $stmt->execute();
            if(!$stmt->error) {
                $result = $stmt->get_result();
                if($result->num_rows > 0) { ?>
                    <div class="card" id="community-favorite">
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
                            for($i = $result->num_rows; $i < 8; $i++) { ?>
                                <li class="favorite-community empty">
                                    <span class="icon-container empty-icon">
                                        <img class="icon" src="/assets/img/empty.png">
                                    </span>
                                </li>
                            <?php } ?>
                            <li class="read-more">
                                <a href="/communities/favorites" class="favorite-community-link symbol"><span class="symbol-label">Show More</span></a>
                            </li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <div class="no-content no-content-favorites">
            		    <div>
            		        <p>Tap the &#9734; button on a community's page to have it show up as a favorite community here.</p>
            		        <a href="/communities/favorites" class="favorite-community-link symbol"><span class="symbol-label">Show More</span></a>
                        </div>
                    </div>
                <?php }
            }
        } ?>
        <?php
        $result = $db->query('SELECT communities.id, name, icon, banner, type FROM communities WHERE is_featured = 1 GROUP BY communities.id DESC LIMIT 4');
        if(!$db->error && $result->num_rows > 0) { ?>
            <h3 class="community-title symbol">Featured Communities</h3>
            <div>
                <ul class="list community-list community-card-list">
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <li class="trigger" data-href="/communities/<?=$row['id']?>" tabindex="0">
                            <?php if(!empty($row['banner'])) { ?><img src="<?=htmlspecialchars($row['banner'])?>" class="community-list-cover"><?php } ?>
                            <div class="community-list-body">
                                <span class="icon-container"><img src="<?=htmlspecialchars($row['icon'])?>" class="icon"></span>
                                <div class="body">
                                    <a class="title" href="/communities/<?=$row['id']?>" tabindex="-1"><?=htmlspecialchars($row['name'])?></a>
                                    <?php
            echo getCommunityTypeAlt($row['type']);
            ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php }
        $result = $db->query('SELECT communities.id, name, icon, type FROM communities ORDER BY communities.id DESC LIMIT 6');
        if(!$db->error && $result->num_rows > 0) { ?>
            <h3 class="community-title symbol">All Communities</h3>
            <div>
                <ul class="list community-list community-card-list device-new-community-list">
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <li class="trigger" data-href="/communities/<?=$row['id']?>" tabindex="0">
                            <div class="community-list-body">
                                <span class="icon-container"><img src="<?=htmlspecialchars($row['icon'])?>" class="icon"></span>
                                <div class="body">
                                    <a class="title" href="/communities/<?=$row['id']?>" tabindex="-1"><?=htmlspecialchars($row['name'])?></a>
                                    <?php
            echo getCommunityTypeAlt($row['type']);
            ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php }
        ?>
        <a href="/communities/all" class="big-button">Show More</a>
        <div id="community-guide-footer">
            <div id="guide-menu">
                <a href="/guide" class="arrow-button"><span><?=$GLOBALS['name']?> Rules</span></a>
                <a href="/guide/contact" class="arrow-button"><span>Contact Us</span></a>
            </div>
        </div>
    </div>
</div>