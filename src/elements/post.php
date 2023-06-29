<div id="<?=$row['id']?>" data-href<?=($row['spoilers'] && $row['posted_by'] !== $_SESSION['id'] ? '-hidden' : '') . '='?>"/posts/<?=$row['id']?>" class="post trigger<?=($row['spoilers'] && $row['posted_by'] !== $_SESSION['id'] ? ' hidden' : '')?><?php if(!empty($row['image'])) { echo ' with-image'; }?>" tabindex="0">
    <?php if(isset($row['community']) && $row['community'] !== null) { ?>
        <p class="community-container">
            <a href="/<?=$row['community'] !== 0 ? 'communities/' . $row['community'] : 'posts/' . $row['post']?>"><img src="<?=htmlspecialchars($row['icon'])?>" class="community-icon"><?=htmlspecialchars($row['name'])?></a>
        </p>
    <?php }
    if(isset($is_profile)) echo '<div class="body"><div class="post-content">'; ?>
                <a href="/users/<?=htmlspecialchars($urow['username'])?>" class="icon-container<?=$urow['badge'] == 1 ? ' official-user' : '', $urow['badge'] == 2 ? ' wiimote' : ''?>">
        <img src="<?=getAvatar($prow['avatar'], $prow['has_mh'], $row['feeling'])?>" class="icon">
                        </a>
        <p class="user-name"><a href="/users/<?=$urow['username']?>"><?=$prow['nickname']?></a></p>
    <p class="timestamp-container">
        <a class="timestamp" href="/posts/<?=$row['id']?>"><?=getTimestamp($row['created_at'])?></a>
        <span class="spoiler-status<?=$row['spoilers'] ? ' spoiler' : ''?>"> • Spoilers</span>
    </p>
    <?=!isset($is_profile) ? '<div class="body post-content">' : ''?>
    <p class="post-content-text"><?=htmlspecialchars($row['body'])?></p>
                    <br class="screenshot-container video none">
                    <?php if(!empty($row['image'])) { ?>
            <?php if(!isset($is_profile)) { ?><br class="screenshot-container video none"><?php } ?>
            <div class="screenshot-container still-image">
                <img src="<?=htmlspecialchars($row['image'])?>">
            </div>
        <?php }
        if($row['spoilers'] && $row['posted_by'] !== $_SESSION['id']) { ?>
            <div class="hidden-content">
                <p>This post may contain spoilers.</p>
                <button type="button" class="hidden-content-button">View Post</button>
            </div>
        <?php } ?>
                <div class="post-meta">
                        <button type="button" class="symbol submit empathy-button<?=$row['yeah_added'] ? ' empathy-added' : ''?>" data-feeling="<?=getFeeling($row['feeling'])?>" data-action="/posts/<?=$row['id']?>/yeah" data-url-id="<?=$row['id']?>" <?php if(!isset($_SESSION["username"])){ ?> disabled <?php } ?>>
                <span class="empathy-button-text"><?=$row['yeah_added'] ? 'Unyeah' : getFeelingText($row['feeling'])?></span>
            </button>
            <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?=$row['yeah_count']?></span></div>
            <div class="reply symbol"><span class="symbol-label">Replies</span><span class="reply-count">???</span></div>        </div>
            </div>
            <?php if(isset($is_profile)) {
            echo '</div>';
        } ?>
</div>