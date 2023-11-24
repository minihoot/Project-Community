One second please..
<?php
// SETTINGS
$GLOBALS['name'] = "Project Community"; // name 
$GLOBALS['ssl'] = false; // force ssl
$GLOBALS['vpn'] = false; // allow VPNs
$GLOBALS['cloudflare'] = false; // only enable if youre using cloudflare
$GLOBALS['cname'] = "reverb"; // cloudinary cloudname (for img uploads.)
// you won't have to fill cloudinary stuff out unless you want to use your cloudinary account
$GLOBALS['cpreset'] = "reverb-mobile"; // cloudinary unsigned preset (for img uploads.)
$GLOBALS['cendpoint'] = "https://api.cloudinary.com/v1_1/"; // idk.. do you have a custom cloudinary instance or smthn?
$GLOBALS['mii_cdn_url'] = "https://mii-secure.cdn.nintendo.net/"; // What, do you have a custom mii CDN or something?
$GLOBALS['h-captcha-sitekey'] = "1fe5521c-205a-42b1-accc-16ce4f6df756"; // h-captcha. set secret value to null to disable. used for login, signup and contact info.
$GLOBALS['h-captcha-secret'] = "ES_752cbae791f24c48941ecffc365d8ddf"; // h-captcha. set this value to null to disable. used for login, signup and contact info.
$GLOBALS['maintenance'] = false; // Activate maintenance mode
$GLOBALS['maintenance_pass'] = "changeme"; // The password to give users a cookie to access the site
$GLOBALS['EOS'] = false; // End of service.
$db = @mysqli_connect("localhost", "root", "root", "communityphp"); // mysqli info
//——————————--—No DB connection?———————————
//⠀⣞⢽⢪⢣⢣⢣⢫⡺⡵⣝⡮⣗⢷⢽⢽⢽⣮⡷⡽⣜⣜⢮⢺⣜⢷⢽⢝⡽⣝
//⠸⡸⠜⠕⠕⠁⢁⢇⢏⢽⢺⣪⡳⡝⣎⣏⢯⢞⡿⣟⣷⣳⢯⡷⣽⢽⢯⣳⣫⠇
//⠀⠀⢀⢀⢄⢬⢪⡪⡎⣆⡈⠚⠜⠕⠇⠗⠝⢕⢯⢫⣞⣯⣿⣻⡽⣏⢗⣗⠏⠀
//⠀⠪⡪⡪⣪⢪⢺⢸⢢⢓⢆⢤⢀⠀⠀⠀⠀⠈⢊⢞⡾⣿⡯⣏⢮⠷⠁⠀⠀  
//⠀⠀⠀⠈⠊⠆⡃⠕⢕⢇⢇⢇⢇⢇⢏⢎⢎⢆⢄⠀⢑⣽⣿⢝⠲⠉⠀⠀⠀⠀
//⠀⠀⠀⠀⠀⡿⠂⠠⠀⡇⢇⠕⢈⣀⠀⠁⠡⠣⡣⡫⣂⣿⠯⢪⠰⠂⠀⠀⠀⠀
//⠀⠀⠀⠀⡦⡙⡂⢀⢤⢣⠣⡈⣾⡃⠠⠄⠀⡄⢱⣌⣶⢏⢊⠂⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⠀⢝⡲⣜⡮⡏⢎⢌⢂⠙⠢⠐⢀⢘⢵⣽⣿⡿⠁⠁⠀⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⠀⠨⣺⡺⡕⡕⡱⡑⡆⡕⡅⡕⡜⡼⢽⡻⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⠀⣼⣳⣫⣾⣵⣗⡵⡱⡡⢣⢑⢕⢜⢕⡝⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⣴⣿⣾⣿⣿⣿⡿⡽⡑⢌⠪⡢⡣⣣⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⡟⡾⣿⢿⢿⢵⣽⣾⣼⣘⢸⢸⣞⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
//⠀⠀⠀⠀⠁⠇⠡⠩⡫⢿⣝⡻⡮⣒⢽⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
//———————————————————————————————————————
if (!$db)
{
    http_response_code(500);
    header("Content-Type: text/html");
	require("500.php");
	exit();
}


/*
DO NOT TOUCH BELOW UNLESS YOU KNOW WHAT YOU ARE DOING.
the only reason why loadcss is separate from renderHeader is so you can use the CSS without having to require the header.
*/

if(session_status() === PHP_SESSION_NONE){
    session_start();
}
if($GLOBALS["cloudflare"]) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}   
/*
if($_SERVER["REMOTE_ADDR"] != "IP ADDRESS 1" AND $_SERVER["REMOTE_ADDR"] != "IP ADDRESS 2"){
    exit("Sorry Link. I don't give credit. Come back when you're a little.. Mmm.. Richer!");
}
*/
if($GLOBALS["maintenance"]){
    if(isset($_SESSION["maintenance"]) AND $_SESSION["maintenance"] == $GLOBALS["maintenance_pass"]){
        goto skipmaint;
    } else {
        http_response_code(503);
        header("Content-Type: text/html");
	    require("maintenance.php");
	    exit();
    }
}
skipmaint:
if($GLOBALS['ssl']){ if($_SERVER["HTTPS"] != "on"){ header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]); exit(); } }
if(!isset($_SESSION["last_ip"]) OR $_SESSION["last_ip"] != $_SERVER["REMOTE_ADDR"]){
if(!$GLOBALS['vpn']){ 
    $_SESSION["last_ip"] = $_SERVER["REMOTE_ADDR"];
    $json = file_get_contents("https://proxycheck.io/v2/".$_SERVER["REMOTE_ADDR"]."?vpn=1");
    $vpn = json_decode($json);
    if($vpn->{$_SERVER["REMOTE_ADDR"]}->{'type'} == "VPN"){
        if($_SERVER["REQUEST_URI"] != "/403.php"){
            header("Location: /403.php");
        }
   }
}
}
if(isset($_SESSION["token"])){
    $stmt = $db->prepare("SELECT * FROM tokens WHERE token = ?");
    $stmt->bind_param("s", $_SESSION["token"]);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        session_destroy();
        header("Location: /");
        exit();
    }
    $row = $res->fetch_assoc();
    if($row["user"] != $_SESSION["id"]){
        session_destroy();
        header("Location: /");
        exit();
    }
    if($row["ip"] != $_SERVER["REMOTE_ADDR"]){
        session_destroy();
        header("Location: /");
        exit();
    }
}
function loadcss($title){
    ?>
    <head>
        <meta charset="utf-8">
        <title><?php if(isset($title)){ echo $title." - "; } ?><?=$GLOBALS["name"]?></title>
        <meta http-equiv="content-style-type" content="text/css">
        <meta http-equiv="content-script-type" content="text/javascript">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-title" content="<?=$GLOBALS["name"]?>">
        <meta name="description" content="<?=$GLOBALS['name']?> is a Miiverse clone that allows you to communicate with other users around the world.">
        <meta name="keywords" content="Miiverse,clone,Project Community,community.isledelfino.net,Terminal,bruhdude,mints,SourMints,Nintendo,Hatena">
        <meta property="og:locale" content="en_US">
        <meta property="og:title" content="<?php if(isset($title)){ echo $title." - "; } ?><?=$GLOBALS["name"]?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="http<?=((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) || (isset($_SERVER['HTTPS_PROXY']) && $_SERVER['HTTPS_PROXY'])) ? 's' : ''?>://<?=$_SERVER['SERVER_NAME']?>">
        <meta property="og:description" content="<?=$GLOBALS['name']?> is a Miiverse clone that allows you to communicate with other users around the world.">
        <meta property="og:site_name" content="<?=$GLOBALS['name']?>">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:domain" content="<?=$_SERVER['SERVER_NAME']?>">
        <title><?=$title?> - <?=$GLOBALS['name']?></title>
        <link href="/assets/css/offdevice.css" rel="stylesheet">
        <?php
        if(!($_SESSION["light_mode"] ?? true)){ ?>
        <link href="/assets/css/offdevice.css" rel="stylesheet">
        <?php } ?>
        <!-- add more shit here-->
        <link rel="stylesheet" type="text/css" href="/assets/css/login.css">
        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
        <script src="https://js.hcaptcha.com/1/api.js?hl=en" async defer></script>
        <script src="/assets/js/complete.js"></script>
        <script src="/assets/js/locale/en.js"></script>
    </head>
    <?php
    return(0);
}
function renderHeader($title){
    loadcss($title);
    if(!isset($_SESSION["username"])){
    ?>
    <body class="guest" data-token="">
        <div id="wrapper">
            <div id="sub-body">
                <menu id="global-menu">
                    <li id="global-menu-logo">
                        <h1><a href="/"><img src="/assets/img/menu-logo.png" alt="<?=$GLOBALS['name']?>" width="165" height="30"></a></h1>
                    </li>
                    <li id="global-menu-login">
                            <form id="login_form" action="/login" method="post">
                                <input type="image" alt="Login" src="/assets/img/en/signin_base.png">
                            </form>
                        </li>
                </menu>
                <?php if(!isset($_SESSION["cookie"])){ ?>
                <div id="cookie-policy-notice" class="cookie-content">
                <center>
                    <p>This site uses cookies. Cookies are handled very strictly on <?=$GLOBALS["name"]?>. For more info, please review the cookie policy. We assume you accept this if you continue using the site.</p>
                    <button class="cookie-policy-notice button" onClick="window.location.href = '/accept/cookie'">Ok</button>
                    <button class="cookie-policy-notice" id="cookie-setting" data-modal-open="#cookie-policy-dialog">Cookie Policy</button>
                </center>
                </div>
                <?php } ?>
            </div>
            <div id="main-body">
                <div id="cookie-policy-dialog" class="dialog none" data-modal-types="cookie-policy-dialog">
                <div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Cookie Policy</h1>
                        <div class="window-body">
                            <center>
                                <h2><?=$GLOBALS['name']?>'s Cookie Policy</h2>
                                <p><small>"their" corrosponds to the user.</small></p>
                                <p><?=$GLOBALS['name']?> handles cookies very strictly. The only information <?=$GLOBALS['name']?> holds is their Username, Token, User ID, Avatar settings, level and badge.</p>
                                <p><?=$GLOBALS['name']?> refuses to and will not collect information from the user that is not necessary for the site to work properly.</p>
                                <p><b>Any third party cookies handled by other sites are not managed by <?=$GLOBALS['name']?>, and <?=$GLOBALS['name']?> cannot be held responsible for those cookies.</b></p>
                            </center>
                            <div class="form-buttons">
                                <input type="button" class="olv-modal-close-button black-button" value="Close" data-modal>
                            </div>
                    </div>
                </div>
            </div>
            </div>
    <?php
    } else { 
    ?>
        <body class="is-autopagerized" data-token="<?=$_SESSION["token"]?>">
        <div id="wrapper">
            <div id="sub-body">
                <menu id="global-menu">
                    <li id="global-menu-logo">
                        <h1><a href="/"><img src="/assets/img/menu-logo.png" alt="<?=$GLOBALS['name']?>" width="165" height="30"></a></h1>
                    </li>
                    <li id="global-menu-list">
                        <ul>
                            <li id="global-menu-mymenu"<?php if(!empty($title) && $title == $_SESSION["username"]) echo ' class="selected"'; ?>>
                                <a href="/users/<?=$_SESSION["username"]?>">
                                    <span class="icon-container<?=$_SESSION['badge'] == 1 ? ' official-user' : '', $_SESSION['badge'] == 2 ? ' wiimote' : ''?>">
                                        <img src="<?=getAvatar($_SESSION['avatar'], $_SESSION['has_mh'], 0)?>" class="icon">
                                                        </span>
                                    <span>User Page</span>
                                </a>
                            </li>
                            <li id="global-menu-feed" <?php if(!empty($title) && $title === 'Activity Feed') echo ' class="selected"'; ?>>
                                <a href="/activity" class="symbol">
                                    <span>Activity Feed</span>
                                </a>
                            </li>
							<li id="global-menu-community"<?php if(!empty($title) && $title === 'Home') echo ' class="selected"'; ?>>
                                <a href="/" class="symbol">
                                    <span>Communities</span>
                                </a>
                            </li>
                            <li id="global-menu-news"<?php if(!empty($title) && $title === 'Notifications') echo ' class="selected"'; ?>>
                                <a href="/news/my_news" class="symbol">
									<span></span>
									<?php
									global $db;
                                    $stmt = $db->prepare('SELECT id, COUNT(*) FROM notifications WHERE seen = 0 AND target = ?');
                                    $stmt->bind_param('i', $_SESSION['id']);
                                    $stmt->execute();
                                    $mcres = $stmt->get_result();
                                    $mcrow = $mcres->fetch_array();
                                    if($mcrow['COUNT(*)'] > 0) { ?>
                                        <span class="badge" style="display: block;"><?=$mcrow['COUNT(*)']?> </span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li id="global-menu-my-menu">
                            
                                <button class="symbol js-open-global-my-menu open-global-my-menu" id="my-menu-btn"></button>

                                <menu id="global-my-menu" class="invisible none">
                                    <li><a href="/settings/profile" class="symbol my-menu-profile-setting"><span>Profile Settings</span></a></li>
                                    <li><a href="/settings/account" class="symbol my-menu-miiverse-setting"><span>Account Settings</span></a></li>
                                    <li><a class="symbol my-menu-info" href="/communities/1"><span><?=$GLOBALS['name']?> Announcements</span></a></li>
                                    <li><a href="/guide/contact" class="symbol my-menu-guide"><span>Contact Us</span></a></li>
                                    <li><a href="/guide" class="symbol my-menu-guide"><span><?=$GLOBALS['name']?> Rules</span></a></li>
                                    <li><a href="/guide/faq" class="symbol my-menu-guide"><span>Frequently Asked Questions (FAQ)</span></a></li>
                                                                        <li>
                                        <form action="/logout" method="post" id="my-menu-logout" class="symbol">
                                            <input type="submit" value="Log Out">
                                        </form>
                                    </li>
                                </menu>
                            </li>
                        </ul>
                    </li>
                </menu>
                <?php if(!isset($_SESSION["cookie"])){ ?>
                <div id="cookie-policy-notice" class="cookie-content">
                <center>
                    <p>This site uses cookies. Cookies are handled very strictly on <?=$GLOBALS["name"]?>. For more info, please review the cookie policy. We assume you accept this if you continue using the site.</p>
                    <button class="cookie-policy-notice button" onClick="window.location.href = '/accept/cookie'">Ok</button>
                    <button class="cookie-policy-notice" id="cookie-setting" data-modal-open="#cookie-policy-dialog">Cookie Policy</button>
                </center>
                </div>
                <?php } ?>
            </div>
            <div id="main-body">
                <div id="cookie-policy-dialog" class="dialog none" data-modal-types="cookie-policy-dialog">
                <div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Cookie Policy</h1>
                        <div class="window-body">
                            <center>
                                <h2><?=$GLOBALS['name']?>'s Cookie Policy</h2>
                                <p><small>"their" corrosponds to the user.</small></p>
                                <p><?=$GLOBALS['name']?> handles cookies very strictly. The only information <?=$GLOBALS['name']?> holds is their Username, Token, User ID, Avatar settings, level and badge.</p>
                                <p><?=$GLOBALS['name']?> refuses to and will not collect information from the user that is not necessary for the site to work properly.</p>
                                <p><b>Any third party cookies handled by other sites are not managed by <?=$GLOBALS['name']?>, and <?=$GLOBALS['name']?> cannot be held responsible for those cookies.</b></p>
                            </center>
                            <div class="form-buttons">
                                <input type="button" class="olv-modal-close-button black-button" value="Close" data-modal>
                            </div>
                    </div>
                </div>
            </div>
            </div>
            <?php }
            function printcommunitycard($communities)
            {
                }
                ?>
    <?php }
    return(0);

function showJSONError($responseCode, $errorCode, $message) {
    http_response_code($responseCode);
    header('Content-Type: application/json');
    exit(json_encode(['success' => 0, 'errors' => [['error_code' => $errorCode, 'message' => $message]]]));
}

// feel
function getFeeling($feeling)
{
    switch($feeling)
	{
        case 1:
            return 'happy';
        case 2:
            return 'like';
        case 3:
            return 'surprised';
        case 4:
            return 'frustrated';
        case 5:
            return 'puzzled';
        default:
            return 'normal';
	}
}
// avatar
function getAvatar($avatar, $has_mh, $feeling)
{
    if($has_mh == 0)
	{
        if(empty($avatar))
		{
            return('/assets/img/no_avatar.png');
        }
		
        return(htmlspecialchars($avatar));
    }
	
    return(htmlspecialchars($GLOBALS['mii_cdn_url'] . urlencode($avatar) . '_' . urlencode(getFeeling($feeling)) . '_face.png'));
}
//feel
function getFeelingText($feeling)
{
    switch($feeling)
	{
	    case 0:
            return 'Yeah!';
        case 1:
            return 'Yeah!';
        case 2:
            return 'Yeah&#10084;';
        case 3:
            return 'Yeah!?';
        case 4:
            return 'Yeah...';
        case 5:
            return 'Yeah...';
        default:
            return 'olv.portal.miitoo';
    }
}
// get get
function getTimestamp($datetime)
{
    $timeSincePost = time() - strtotime($datetime);
	
    if($timeSincePost < 1)
	{
        return 'Less than a second ago';
    }
	elseif($timeSincePost < 2)
	{
        return '1 second ago';
    }
	elseif($timeSincePost < 60)
	{
        return strtok($timeSincePost, '.') . ' seconds ago';
    }
	elseif($timeSincePost < 120)
	{
        return '1 minute ago';
    }
	elseif($timeSincePost < 3600)
	{
        return strtok($timeSincePost / 60, '.') . ' minutes ago';
    }
	elseif($timeSincePost < 7200)
	{
        return '1 hour ago';
    }
	elseif($timeSincePost < 86400)
	{
        return strtok($timeSincePost / 3600, '.') . ' hours ago';
    }
	elseif($timeSincePost < 172800)
	{
        return '1 day ago';
    }
	elseif($timeSincePost < 341600)
	{
        return strtok($timeSincePost / 86400, '.') . ' days ago';
    }
	else
	{
        return date('m/d/Y g:i A', strtotime($datetime));
    }
}
//type
function getCommunityTypeAlt($type)
{
    switch($type)
	{
        case 1:
            return '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu.png"></span><span class="text">Wii U Games</span>';
        case 2:
            return '<span class="platform-tag"><img src="/assets/img/platform-tag-3ds.png"></span><span class="text">3DS Games</span>';
        case 3:
            return '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu-3ds.png"></span><span class="text">Wii U + 3DS</span>';
        case 4:
            return '<span class="platform-tag"><img src="/assets/img/platform-tag-switch.png"></span><span class="text">Switch Games</span>>';
        default:
            return '';
	}
}
//type
function getCommunityType($type)
{
    switch($type)
	{
        case 1:
            return '<img src="/assets/img/platform-tag-wiiu.png">';
        case 2:
            return '<img src="/assets/img/platform-tag-3ds.png">';
        case 3:
            return '<img src="/assets/img/platform-tag-wiiu-3ds.png">';
        case 4:
            return '<img src="/assets/img/platform-tag-switch.png">';
        default:
            return '';
	}
}
//doun your mom
function getPreview($body)
{
    if(mb_strlen($body) > 18)
	{
        return htmlspecialchars(mb_substr($body, 0, 15)) . '...';
    }
	else
	{
        return htmlspecialchars($body);
    }
}

//imag
function uploadImage($file, $width = null, $height = null)
{
    if($width !== null && $height !== null && extension_loaded('imagick'))
	{
        $imagick = new Imagick(); // imagick dick
        $imagick->readImageBlob($file);
		
        if ($imagick->getImageFormat() === 'GIF')
		{
            $imagick = $imagick->coalesceImages();
            $imagick->cropThumbnailImage($width, $height);
			
            while($imagick->nextImage())
			{
                $imagick->cropThumbnailImage($width, $height);
            }
			
            $imagick = $imagick->deconstructImages();
        }
		else
		{
            $imagick->cropThumbnailImage($width, $height);
        }
		
        $file = $imagick->getImagesBlob();
    }
		if(empty($GLOBALS['cname']) || empty($GLOBALS['cpreset']))
		{
			return null;
		}
		else
		{
			$mime = finfo_buffer(finfo_open(), $file, FILEINFO_MIME_TYPE);
			$ch = curl_init($GLOBALS['cendpoint'] . urlencode($GLOBALS['cname']) . '/image/upload');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['upload_preset' => $GLOBALS['cpreset'], 'file' => 'data:' . $mime . ';base64,' . base64_encode($file)]));
			$response = curl_exec($ch);
			$responseJSON = json_decode($response);
			$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			
			if($responseCode > 299 || $responseCode < 200)
			{
				return null;
			}
			
			curl_close($ch);
			
			return $responseJSON->secure_url;
		}
}
?>
