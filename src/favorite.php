<?php
require_once('htm.php');
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
    showJSONError(400, 1111111, 'You must specify a community ID.');
}

if($_GET['un'] === 'un') {
    $stmt = $db->prepare('DELETE FROM favorite_communities WHERE source = ? AND target = ?');
} else {
    $stmt = $db->prepare('REPLACE INTO favorite_communities (source, target) VALUES (?, ?)');
}
$stmt->bind_param('ii', $_SESSION['id'], $_GET['id']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 5000000, 'There was an error while inserting the favorite.');
}
?>