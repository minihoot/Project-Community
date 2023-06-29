<?php
require_once("htm.php");
$ustmt = $db->prepare('SELECT id, username, (SELECT COUNT(*) FROM posts WHERE posted_by = users.id) AS post_count, (SELECT COUNT(*) FROM yeahs WHERE source = users.id) AS yeah_count, level, badge FROM users WHERE id = ?');
$ustmt->bind_param('s', $_SESSION['id']);
$ustmt->execute();
if($ustmt->error) {
     echo '<div class="no-content">An error occurred while grabbing user data from the database.</div>';
}
$uresult = $ustmt->get_result();
$urow = $uresult->fetch_assoc();

$pstmt = $db->prepare('SELECT nickname, country, birthday, avatar, has_mh, profile_comment, nnid, mh FROM profiles WHERE for_user = ?');
$pstmt->bind_param('i', $urow['id']);
$pstmt->execute();
if($pstmt->error) {
     echo '<div class="no-content">An error occurred while grabbing profile data from the database.</div>';
}
$presult = $pstmt->get_result();
$prow = $presult->fetch_assoc();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
        showJSONError(400, 1234321, 'The CSRF check failed.');
    }
$edits = [];
    $sessionEdits = [];
    foreach($_POST as $key => &$value) {
        if(array_key_exists($key, $prow) && $prow[$key] !== $value) {
            switch($key) {
                case 'nickname':
                    if(mb_strlen($value) > 64) {
                        showJSONError(400, 1211337, 'Your nickname is too long.');
                    }
                    break;
                case 'country':
                    if(mb_strlen($value) > 40) {
                        showJSONError(400, 1211337, 'Your country is too long.');
                    }
                    break;
                case 'birthday':
                    if(mb_strlen($value) > 20) {
                        showJSONError(400, 1211337, 'Your birthday is too long.');
                    }
                    break;
                case 'profile_comment':
                    if(mb_strlen($value) > 2000) {
                        showJSONError(400, 1337121, 'Your profile comment is too long.');
                    }
                    break;
                case 'nnid':
                    if(!preg_match('/^[A-Za-z0-9-._]{6,16}$/', $value)) {
                        showJSONError(400, 1212121, 'Your Nintendo Network ID is invalid.');
                    }
                    $ch = curl_init('https://pf2m.com/hash/' . $value);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $miiHash = curl_exec($ch);
                    $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                    if($responseCode < 200 || $responseCode > 299) {
                        showJSONError(400, 4041337, 'The Nintendo Network ID could not be found.');
                    }
                    $edits[] = 'mh = "' . $db->real_escape_string($miiHash) . '"';
                    if((!empty($_POST['has_mh']) && $_POST['has_mh'] === '1') || (empty($_POST['has_mh']) && $prow['has_mh'] === '1')) {
                        $edits[] = 'avatar = "' . $db->real_escape_string($miiHash) . '"';
                        $sessionEdits['avatar'] = $miiHash;
                        $sessionEdits['has_mh'] = 1;
                    }
                    break;
                case 'has_mh':
                    if(!in_array($value, ['0', '1'])) {
                        showJSONError(400, 1038843, 'Your avatar setting is invalid.');
                    }
                    if($value === '1') {
                        $sessionEdits['has_mh'] = 1;
                        if(empty($_POST['nnid']) || $_POST['nnid'] === $prow['nnid']) {
                            if(empty($prow['mh'])) {
                                $edits[] = 'avatar = NULL, has_mh = 1';
                                $sessionEdits['avatar'] = null;
                            } else {
                                $edits[] = 'avatar = "' . $db->real_escape_string($prow['mh']) . '", has_mh = 1';
                                $sessionEdits['avatar'] = $prow['mh'];
                                $sessionEdits['has_mh'] = 1;
                            }
                        }
					}
					else
					{
						$sessionEdits['has_mh'] = 0;
                        if(empty($prow['mh'])) {
                            $edits[] = 'avatar = NULL, has_mh = 0';
                            $sessionEdits['avatar'] = null;
                            $sessionEdits['has_mh'] = 0;
                        } else {
                            $edits[] = 'avatar = "' . $db->real_escape_string($_POST['avatar']) . '", has_mh = 0';
                            $sessionEdits['avatar'] = $_POST['avatar'];
                            $sessionEdits['has_mh'] = 0;
                        }
					}
                default:
                    goto next;
            }
            $edits[] = $key . ' = "' . $db->real_escape_string($value) . '"';
            next:
        }
    }
    if(count($edits) > 0) {
        $stmt = $db->prepare('UPDATE profiles SET ' . implode(', ', $edits) . ' WHERE for_user = ?');
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        if($stmt->error) {
            showJSONError(500, 3928989, 'There was an error while saving your settings.');
        }
    }
    if(count($sessionEdits) > 0) {
        foreach($sessionEdits as $key => &$value) {
            $_SESSION[$key] = $value;
        }
    }
} else {
renderHeader("Profile Settings");
require_once("elements/user-sidebar.php");
?>
<div class="main-column messages">
        <div class="post-list-outline">
            <h2 class="label">Profile Settings</h2>
            <form class="setting-form" action="/settings/profile" method="post">
                <input type="hidden" name="token" value="<?=htmlspecialchars($_SESSION['token'])?>">
                <ul class="settings-list">
                    <li>
                        <p class="settings-label">Nickname</p>
                        <p class="note">Set your nickname here. It will be displayed above your username.</p>
                        <input type="text" class="url-form" name="nickname" maxlength="64" value="<?=htmlspecialchars($prow['nickname'])?>" placeholder="Nickname">
                    </li>
					<li>
                        <p class="settings-label">Country</p>
                        <p class="note">Set your country here (only if you want to, of course).</p>
                        <input type="text" class="url-form" name="country" maxlength="40" value="<?=htmlspecialchars($prow['country'])?>" placeholder="Country">
                    </li>
					<li>
                        <p class="settings-label">Birthday</p>
                        
                        <p class="note">Set your birthday here (only if you want to, of course).</p>
                        <input type="text" class="url-form" name="birthday" maxlength="20" value="<?=htmlspecialchars($prow['birthday'])?>" placeholder="Birthday">
                    </li>
                    <li class="setting-profile-comment">
                        <p class="settings-label">Profile Comment</p>
                        <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="2000" placeholder="Write about yourself here."><?=htmlspecialchars($prow['profile_comment'])?></textarea>
                        <p class="note">What you write here will appear on your profile. Feel free to write anything that doesn't violate the <a href="/guide"> rules</a>.</p>
                    </li>
                    <li>
                        <p class="settings-label">Nintendo Network ID</p>
                        <p class="note">Set your Nintendo Network ID here.</p>
                        <input type="text" class="url-form" name="nnid" minlength="6" maxlength="16" value="<?=htmlspecialchars($prow['nnid'])?>" placeholder="Nintendo Network ID">
                    </li>
                    <li>
                        <p class="settings-label">Avatar</p>
                        <label><input type="radio" name="has_mh" value="1"<?=$prow['has_mh'] === 1 ? ' checked' : ''?>> Mii</label>
                        <label><input type="radio" name="has_mh" value="0"<?=$prow['has_mh'] === 0 ? ' checked' : ''?>> Avatar</label>
                    </li>
                    <?php if($prow['has_mh'] === 0) { ?>
                    <li>
                        <p class="settings-label">Avatar URL</p>
                        <p class="note">Set your Avatar URL here.</p>
                        <input type="text" class="url-form" name="avatar" maxlength="2000" value="<?=htmlspecialchars($prow['avatar'])?>" placeholder="Avatar URL">
                    </li>
                    <?php } ?>
                </ul>
                <div class="form-buttons">
                    <input type="submit" class="black-button apply-button" value="Save Settings">
                                    </div>
            </form>
        </div>
    </div>
<?php
}
?>