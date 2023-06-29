<?php
require_once("htm.php");
renderHeader("Sign In");
if(isset($_SESSION["username"])){
    header("Location: /");
    exit();
}
if(isset($_POST["username"]) AND isset($_POST["password"])){
    if($_POST["username"] == "yomom" OR $_POST["username"] == "yomomma" OR $_POST["username"] == "mommayo" OR $_POST["username"] == "yomomm"){
        exit("<style>body {background-color: black;} div.a {width: 96px; height:96px; background: url(/assets/img/zclose.webp); position:fixed;}</style><script>$(document).ready(function(){ animateDiv(); }); function makeNewPosition(){ var h = $(window).height() - 30; var w = $(window).width() - 30; var nh = Math.floor(Math.random() * h); var nw = Math.floor(Math.random() * w); return [nh,nw]; }; function animateDiv(){ var newq = makeNewPosition(); $('.a').animate({ top: newq[0], left: newq[1] }, function(){ animateDiv(); }); };</script><div class='a'></div><audio src='/assets/img/zclose.ogg' loop hidden autoplay></body>");
    }
    if(isset($_POST["h-captcha-secret"])){
    $url = 'https://hcaptcha.com/siteverify';
    $data = array('response' => $_POST["h-captcha-response"], 'secret' => $GLOBALS["h-captcha-secret"]);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resjson = json_decode($result);
    if($resjson->{'success'} == true){
                    
    } else {
        $error = "Please solve the hCaptcha.";
        goto form;
    }
    }
    $stmt = $db->prepare("SELECT id, password, level, badge FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        $error = "Incorrect username/password.";
        goto form;
    }
    $user = $res->fetch_assoc();
    if(password_verify($_POST["password"], $user["password"])){
        if($_SESSION["cookie"] == true){
        $token = bin2hex(random_bytes(16));
        $stmt = $db->prepare("INSERT INTO `tokens` (`token`, `user`, `ip`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $token, $user["id"], $_SERVER["REMOTE_ADDR"]);
        $stmt->execute();
        if($stmt->error){
            $error = "An error occurred. Please try again.";
        } else {
            $pstmt = $db->prepare('SELECT avatar, has_mh FROM profiles WHERE for_user = ?');
            $pstmt->bind_param('i', $user['id']);
            $pstmt->execute();
            if($pstmt->error) {
                $error = 'Unable to fetch your avatar. Please try logging in again.';
            }
            $presult = $pstmt->get_result();
            $prow = $presult->fetch_assoc();
        }
            $_SESSION["id"] = $user["id"];
            $_SESSION["username"] = $_POST["username"];
            $_SESSION["token"] = $token;
            $_SESSION["avatar"] = $prow['avatar'];
            $_SESSION["has_mh"] = $prow['has_mh'];
            $_SESSION["level"] = $user['level'];
            $_SESSION["badge"] = $user['badge'];
            exit("<script>window.location.href = '/';</script><a href=\"/\">Click here if you are not redirected...</a>");
        } else {
            exit("<br><br><br>Please click \"OK\" on the cookie banner to sign in.");
        }
    } else {
        $error = "Incorrect username/password.";
        goto form;
    }
}
form:
?>
<div class="post-list-outline no-content center">
    <form method="post">
        <br>
        <img src="/assets/img/menu-logo.png">
        <p>Sign in with a <?=$GLOBALS["name"]?> account to communicate with other users worldwide.</p>
		<br>
		<div class="row">
			<input type="text" id="user" class="form-control form-box" name="username" placeholder="Username" required>
		</div>
		<div class="row">
			<input type="password" id="pass" class="form-control form-box" name="password" placeholder="Password" required>
		</div>
		<?php if($GLOBALS['h-captcha-secret'] != NULL) { ?>
		    <div class="h-captcha" data-sitekey="<?=$GLOBALS['h-captcha-sitekey']?>" data-theme="<?php if(!isset($_SESSION["light_mode"]) OR $_SESSION["light_mode"] == false){ ?>dark<?php } else { ?>light<?php } ?>"></div>
		<?php } ?>
		<div class="form-buttons">
            <button class="black-button" type="submit">Sign In</button>
        </div>
        <?php if(isset($error)){ ?>
        <br>
        <p style="color:#FF0000"><?=$error?></p>
        <?php } ?>
        <br>
        <p>Don't have an account? No worries! You can <a href="/signup">create one here.</a></p>
        <p><small>More login methods coming soon.</small></p>
        <br>
    </form>
</div>