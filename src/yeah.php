<?php
ini_set('display_errors', '1');
require_once('htm.php');

$type = 0;

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    showJSONError(405, 6969696, 'You must use a POST request.');
}
if(empty($_SESSION['username'])) {
    showJSONError(401, 0000000, 'You must log in to view this page.');
}
if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
    showJSONError(400, 1234321, 'The CSRF check failed.');
}
if(empty($_GET['id'])) {
    showJSONError(400, 1111111, 'You must specify an ID.');
}

$stmt = $db->prepare('SELECT posted_by FROM posts WHERE id = ? AND status = 0');
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 9999999, 'An error occurred while checking for the posts existence.');
}
$result = $stmt->get_result();
if($result->num_rows === 0) {
    showJSONError(404, 1219999, 'The post could not be found.');
}
$row = $result->fetch_assoc();

if($_GET['delete'] === '.delete') {
    $stmt = $db->prepare('DELETE FROM yeahs WHERE source = ? AND target = ? AND type = ?');
} else {
    $stmt = $db->prepare('REPLACE INTO yeahs (source, target, type) VALUES (?, ?, ?)');
}
$stmt->bind_param('iii', $_SESSION['id'], $_GET['id'], $type);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 7654321, 'An error occurred while inserting/deleting the empathy.');
}
$stmt = $db->prepare("SELECT id FROM notifications WHERE source = ? AND origin = ? AND type = 1");
$sid = $_SESSION["id"];
$pby = $row["posted_by"];
$gid = $_GET['id'];
$stmt->bind_param("ii", $sid, $gid);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 1726354, 'An error occurred while checking if a notification was already sent.');
}
$res = $stmt->get_result();
if($res->num_rows == 0){
    
} else {
    goto end;
}

$stmt = $db->prepare('INSERT INTO `notifications` (source, target, origin, type) VALUES (?, ?, ?, 1)');
$stmt->bind_param('iii', $sid, $pby, $gid);
$stmt->execute();
if($stmt->error)
{
    showJSONError(500, 1726355, 'An error occurred while sending a notification.');
} else {
    
}
end:
?>