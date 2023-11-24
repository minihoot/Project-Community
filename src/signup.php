<?php
require_once("htm.php");
renderHeader("Sign Up");
if(isset($_SESSION["username"])){
    header("Location: /");
    exit();
}
if(isset($_POST["username"]) AND isset($_POST["password"])){
    if($_POST["username"] == "yomom" OR $_POST["username"] == "yomomma" OR $_POST["username"] == "momma" OR $_POST["username"] == "yomomm"){
        exit("<style>body {background-color: black;} div.a {width: 96px; height:96px; background: url(/assets/img/zclose.webp); position:fixed;}</style><script>$(document).ready(function(){ animateDiv(); }); function makeNewPosition(){ var h = $(window).height() - 30; var w = $(window).width() - 30; var nh = Math.floor(Math.random() * h); var nw = Math.floor(Math.random() * w); return [nh,nw]; }; function animateDiv(){ var newq = makeNewPosition(); $('.a').animate({ top: newq[0], left: newq[1] }, function(){ animateDiv(); }); };</script><div class='a'></div><audio src='/assets/img/zclose.ogg' loop hidden autoplay></body>");
    }
    if(isset($GLOBALS["h-captcha-secret"])){
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
        goto showForm;
    }
    }
    if(empty($_POST["username"])){
        $error = "Your username cannot be empty";
        goto showForm;
    }
    if(preg_match('/^[A-Za-z0-9-._]$/', $_POST["username"])) {
        $error = "Your username can only contain alphabetical characters, numerical characters, and underscores.";
        goto showForm;
    }
    if(str_ends_with($_POST["username"], '_')){
        $error = "Username cannot begin or end with an underscore.";
        goto showForm;
    }
    if(substr($_POST["username"], 0, 0) == "_"){
        $error = "Username cannot begin or end with an underscore.";
        goto showForm;
    }
    if(empty($_POST["password"])){
        $error = "Your password cannot be empty.";
        goto showForm;
    }
    if($_POST["password"] == "password" OR $_POST["password"] == "wordpass"){
        $error = "Did you know? Your password is stupid.";
        goto showForm;
    }
    if(!empty($_POST["email"])){
        if(!str_contains($_POST["username"], "@")){
            $error = "Your email is invalid.";
            goto showForm;
        }
    }
    if(!empty($_POST["nnid"])){
        if(!preg_match('/^[A-Za-z0-9-._]{6,16}$/', $_POST["nnid"])) {
            $error = "Your Nintendo Network ID is invalid.";
            goto showForm;
        }
        $ch = curl_init('https://nnidlt.murilo.eu.org/api.php?output=hash_only&env=production&user_id=' . $_POST["nnid"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $miiHash = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if($responseCode < 200 || $responseCode > 299) {
            $error = "The Nintendo Network ID could not be found.";
            goto showForm;
        }
    } else {
        $miiHash = null;
    }
    $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Prevent multiple accounts with the same username
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    if($stmt->error){
        $error = "Unexpected error during lookup. Please try again?";
        goto showForm;
    }
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $error = "That username already exists. Try a different one!";
        goto showForm;
    }

    $stmt = $db->prepare("INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST["username"], $hash, $_POST["email"]);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occurred while trying to insert your user into the database.";
        goto showForm;
    }
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occurred while trying to look for your account in the database.";
        goto showForm;
    }
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        $error = "Account could not be found in the database. Try signing up again?";
        goto showForm;
    }
    $user = $res->fetch_assoc();
    if($miiHash == null){
        $hasmh = 0;
    } else {
        $hasmh = 1;
    }
    $stmt = $db->prepare("INSERT INTO `profiles` (`for_user`, `avatar`, `nickname`, `nnid`, `mh`, `has_mh`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user["id"], $miiHash, $_POST["username"], $_POST["nnid"], $miiHash, $hasmh);
    $stmt->execute();
    if($stmt->error){
        $error = "Yes, your account was created, but your profile wasnt, please contact an administrator to create your profile.";
        goto showForm;
    }
    $token = bin2hex(random_bytes(16));
    $stmt = $db->prepare("INSERT INTO `tokens` (`token`, `user`, `ip`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $token, $user["id"], $_SERVER["REMOTE_ADDR"]);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occurred while trying to insert a token into the database. You can probably just try signing into your account now.";
        goto showForm;
    }
    $_SESSION["id"] = $user["id"];
    $_SESSION["username"] = $_POST["username"];
    $_SESSION["token"] = $token;
    $_SESSION["avatar"] = $miiHash;
    $_SESSION["has_mh"] = $hasmh;
    $_SESSION["level"] = 0;
    header("Location: /");
    exit();
}
showForm:
?>
<div class="post-list-outline no-content center">
    <form method="post">
        <br>
        <center><h1><b>Register</b></h1></center>
        <center><p><small>Welcome to <?=$GLOBALS["name"]?>!</small></p></center>
        <img src="/assets/img/menu-logo.png" height="30" width="236">
		<br>
		<div class="row">
			<input type="text" id="user" class="form-control form-box" name="username" placeholder="Username" required>
		</div>
		<div class="row">
			<input type="password" id="pass" class="form-control form-box" name="password" placeholder="Password" required>
		</div>
		<div class="row">
			<input type="email" id="email" class="form-control form-box" name="email" placeholder="Email (optional)">
		</div>
		<div class="row">
			<input type="text" id="nnid" class="form-control form-box" name="nnid" placeholder="Nintendo Network ID (optional)">
		</div>
		<div class="row">
		<?php if($GLOBALS['h-captcha-secret'] != NULL) { ?>
		    <div class="h-captcha" data-sitekey="<?=$GLOBALS['h-captcha-sitekey']?>" data-theme="<?php if(!isset($_SESSION["light_mode"]) OR $_SESSION["light_mode"] == false){ ?>dark<?php } else { ?>light<?php } ?>"></div>
		<?php } ?>
		</div>
		<div class="form-buttons">
            <button class="black-button" type="submit">Register</button>
        </div>
        <?php if(isset($error)){ ?>
        <br>
        <p style="color:#FF0000"><?=$error?></p>
        <?php } ?>
        <br>
        <p>If you don't register an email to your account, you can add one via User Settings once logged in.</p>
        <p><small>Although, if you don't end up registering an email, and you get hacked/forget your password, we cannot help you.</small></p>
        <br>
    </form>
</div>
