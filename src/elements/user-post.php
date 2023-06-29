<div id="<?=$row['id']?>" data-href="/posts/<?=$row['id']?>" class="post trigger" tabindex="0">
    <?php if(isset($row['community']) && $row['community'] !== null) { ?>
        <p class="community-container">
            <a href="/<?=$row['community'] !== 0 ? 'communities/' . $row['community'] : 'posts/' . $row['post']?>"><img src="<?=htmlspecialchars($row['icon'])?>" class="community-icon"><?=htmlspecialchars($row['name'])?></a>
        </p>
    <?php }
    if(isset($is_profile)) echo '<div class="body"><div class="post-content">'; ?>
                <a href="/users/<?=$usrow['username']?>" class="icon-container<?=$usrow['level'] > 0 ? ' official-user' : ''?>">
        <img src="<?=getAvatar($psrow['avatar'], $psrow['has_mh'], $row['feeling'])?>" class="icon">
                        </a>
        <p class="user-name"><a href="/users/<?=$usrow['username']?>"><?=$psrow['nickname']?> (<?=$usrow['level']?>)</a></p>
    <p class="timestamp-container">
        <a class="timestamp" href="/posts/<?=$row['id']?>"><?=getTimestamp($row['created_at'])?></a>
        <span class="spoiler-status"><?=htmlspecialchars($row['image'])?></span>
    </p>
    <?=!isset($is_profile) ? '<div class="body post-content">' : ''?>
    <p class="post-content-text"><?=htmlspecialchars($row['body'])?></p>
                    <br class="screenshot-container video none">
                    <?php if(!empty($row['image'])) { ?>
            <?php if(!isset($is_profile)) { ?><br class="screenshot-container video none"><?php } ?>
            <div class="screenshot-container still-image">
                <img src="<?=htmlspecialchars($row['image'])?>">
            </div>
        <?php } ?>
                <div class="post-meta">
                        <button type="button" class="symbol submit empathy-button<?=$row['yeah_added'] ? ' empathy-added' : ''?>" data-feeling="<?=getFeeling($row['feeling'])?>" data-action="/posts/<?=$row['id']?>/yeah" data-url-id="<?=$row['id']?>">
                <span class="empathy-button-text"><?=$row['yeah_added'] ? 'Unyeah' : getFeelingText($row['feeling'])?></span>
            </button>
            <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?=$row['yeah_count']?></span></div>
            <div class="reply symbol"><span class="symbol-label">Replies</span><span class="reply-count">???</span></div>        </div>
            </div>
            <?php if(isset($is_profile)) {
            echo '</div>';
        } ?>
</div>