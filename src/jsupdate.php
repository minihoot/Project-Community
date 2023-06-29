<?php
require_once('htm.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$ip = $_SERVER['REMOTE_ADDR'];
    print(json_encode(['success' => 1]));
}
else
{
	if (empty($_SESSION['username']))
	{
		http_response_code(401);
	
		exit(
			json_encode(
				[
					'success' => 0,
					'errors' => ['You must be signed in to access this page.']
				]
			)
		);
	}

	$notifications = ['success' => 1];

	if (!empty($_GET['news']) && $_GET['news'] === '{}')
	{
		$stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE target = ? AND merged IS NULL AND seen = 0');
	
		if (!$stmt)
		{
			http_response_code(500);
			$notifications['success'] = 0;
			$notifications['errors'] = ['There was an error while preparing to fetch the notification count.'];
			exit(json_encode($notifications));
		}
	
		$stmt->bind_param('s', $_SESSION['id']);
		$stmt->execute();
	
		if ($stmt->error)
		{
			http_response_code(500);
			$notifications['success'] = 0;
			$notifications['errors'] = ['There was an error while fetching the notification count.'];
			exit(json_encode($notifications));
		}
	
		$result = $stmt->get_result();
		$row = $result->fetch_array();
		$notifications['news'] = ['unread_count' => $row['COUNT(*)']];
	}

	if (!empty($_GET['admin_message']) && $_GET['admin_message'] === '{}')
	{
		$notifications['admin_message'] = ['unread_count' => 0];
	}

	if (!empty($_GET['mission']) && $_GET['mission'] === '{}')
	{
		$notifications['mission'] = ['unread_count' => 0];
	}

	print(json_encode($notifications));
}
?>