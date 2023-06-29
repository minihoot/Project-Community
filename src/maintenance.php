<?php
require_once('htm.php');
?>
<!DOCTYPE html>
<html class="os-win"><head>
        <meta charset="utf-8">
        <title><?=$GLOBALS["name"]?></title>
        <meta http-equiv="content-style-type" content="text/css">
        <meta http-equiv="content-script-type" content="text/javascript">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-title" content="<?=$GLOBALS["name"]?>">
        <meta name="description" content="<?=$GLOBALS['name']?> is a Miiverse clone that allows you to communicate with other users around the world.">
        <meta name="keywords" content="Miiverse,clone,Project Community,community.isledelfino.net,Terminal,bruhdude,mints,SourMints,Nintendo,Hatena">
        <meta property="og:locale" content="en_US">
        <meta property="og:title" content="<?=$GLOBALS["name"]?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="http<?=($_SERVER['HTTPS'] || HTTPS_PROXY) ? 's' : ''?>://<?=$_SERVER['SERVER_NAME']?>">
        <meta property="og:description" content="<?=$GLOBALS['name']?> is a Miiverse clone that allows you to communicate with other users around the world.">
        <meta property="og:site_name" content="<?=$GLOBALS['name']?>">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:domain" content="<?=$_SERVER['SERVER_NAME']?>">
        <title><?=$GLOBALS['name']?></title>
        <link href="/assets/css/offdevice.css" rel="stylesheet">
        <!-- add more shit here-->
        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
        <script src="/assets/js/complete.js"></script>
        <script src="/assets/js/locale/en.js"></script>
    </head>
    <body class="" data-token="">
        <div id="wrapper">
            <div id="sub-body">
                <menu id="global-menu">
                    <li id="global-menu-logo">
                        <h1><a><img src="/assets/img/menu-logo.png" alt="<?=$GLOBALS['name']?>" width="165" height="30"></a></h1>
                    </li>
                    <li id="global-menu-list">
					</li>
					</menu>
            </div>
            <div id="main-body">
<div class="warning-content warning-content-restricted track-error" data-track-error="restricted">
				<div>
                <?php if(!$_GET["login"]){ ?>
					<img src="/assets/img/error3.png" style="width:130px;height:200px;">
				    <p><b>503 Service Unavailable</b><br><?=$GLOBALS['name']?> is currently undergoing maintenance.<br>Please try again later.</p>
				<?php } else { ?>
				    <form method="POST">
				        <div class="row">
			                <input type="password" id="pass" class="form-control form-box" name="password" placeholder="Password" required>
		                </div>
		                <div class="form-buttons">
                            <button class="black-button" type="submit">Sign In</button>
                        </div>
				    </form>
				<?php }
				if(isset($_POST["password"]) AND $_POST["password"] == $GLOBALS["maintenance_pass"]){
				    $_SESSION["maintenance"] = $_POST["password"];
				    echo "Thanks, the site will now load.";
				    header("refresh:1;url=/");
				}
				?>
				</div>
			</div>
            </div>
            <div id="footer">
                <div id="footer-inner">
                    <div class="link-container"> <!--Add your contact email here-->
                        <p><a href="mailto:example@example.com">Contact Us</a></p>
                        <p><a href="/maintenance.php?login=true">Enter Password</a></p>
                        <p id="copyright"><a href="https://nintendo.com/"><?=$GLOBALS['name']?> is a nonprofit fan project based on assets from Nintendo and Hatena, and is not affiliated with either company.</a></p>
                    </div>
                </div>
            </div>
        </div>
    
</body></html>
