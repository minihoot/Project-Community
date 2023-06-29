<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    require_once("htm.php");
    session_destroy();
    header("Location: /");
} else {
    exit("You must use a POST request.");
}
?>