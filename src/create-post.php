<?php
require_once("htm.php");
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showJSONError(405, 6969696, 'You must use a POST request.');
}
if(empty($_SESSION['username'])) {
    showJSONError(401, 0000000, 'You must log in to view this page.');
}
if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
    showJSONError(400, 1234321, 'The CSRF check failed.');
}
if(!isset($_POST['body'])) {
    showJSONError(400, 1216969, 'You must add a body.');
}
$_POST['body'] = trim($_POST['body']);
if(empty($_POST['body'])) {
    showJSONError(400, 1216970, 'Your body is empty.');
}
if(mb_strlen($_POST['body']) > 2000) {
    showJSONError(400, 1219309, 'Your body is too long.');
}
if(empty($_POST['community'])) {
    $_POST['community'] = 0;
}
if(empty($_POST['feeling_id']) || $_POST['feeling_id'] > 7 || $_POST['feeling_id'] < 0) {
    $_POST['feeling_id'] = 0;
}
if(empty($_POST['spoilers']) || $_POST['spoilers'] !== '1') {
    $_POST['spoilers'] = 0;
}
if(!empty($_POST['image'])) {
    $image = uploadImage(base64_decode($_POST['image']));
    if($image === null) {
        showJSONError(500, 2310924, 'An error occurred while uploading the image.');
    }
}

$stmt = $db->prepare('SELECT COUNT(*) FROM posts WHERE posted_by = ? AND created_at > NOW() - INTERVAL 15 SECOND');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 5820194, 'There was an error while grabbing your recent posts.');
}
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if($row['COUNT(*)'] > 0) {
    showJSONError(429, 1213005, 'You\'re making too many posts in quick succession. Please try again in a moment.');
}

$stmt = $db->prepare('INSERT INTO posts (posted_by, community, feeling, body, image, spoilers) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('iiissi', $_SESSION['id'], $_POST['community'], $_POST['feeling_id'], $_POST['body'], $image, $_POST['spoilers']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 9999999, 'There was an error while inserting the post into the database.');
}
$stmt = $db->prepare('SELECT id, posted_by, body, feeling, created_at, image, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0) AS yeah_count, (SELECT COUNT(*) FROM yeahs WHERE target = posts.id AND type = 0 AND source = ?) AS yeah_added, spoilers, status FROM posts WHERE posted_by = ? ORDER BY id DESC LIMIT 1');
$stmt->bind_param('ii', $_SESSION['id'], $_SESSION["id"]);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 9999999, 'An error occurred while grabbing posts from the database.');
}
$result = $stmt->get_result();
if($result->num_rows === 0) {
    showJSONError(500, 9999999, 'Could not find your post in the database. Try refreshing.');
}
$row = $result->fetch_assoc();
$pstmt = $db->prepare('SELECT nickname, avatar, has_mh FROM profiles WHERE for_user = ?');
$pstmt->bind_param('i', $row['posted_by']);
$pstmt->execute();
if($pstmt->error) {
    showJSONError(500, 9999999, 'An error occurred while grabbing profile data from the database.');
}
$presult = $pstmt->get_result();
$prow = $presult->fetch_assoc();

$ustmt = $db->prepare('SELECT username, level, badge FROM users WHERE id = ?');
$ustmt->bind_param('i', $row['posted_by']);
$ustmt->execute();
if($ustmt->error) {
    showJSONError(500, 9999999, 'An error occurred while grabbing user data from the database.');
}
$uresult = $ustmt->get_result();
$urow = $uresult->fetch_assoc();
$_GET['type'] = null;
require('elements/post.php');
?>