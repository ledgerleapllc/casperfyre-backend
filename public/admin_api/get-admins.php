<?php
/**
 *
 * GET /admin/get-admins
 *
 * HEADER Authorization: Bearer
 *
 */
include_once('../../core.php');

global $db, $helper;

require_method('GET');
$auth = authenticate_session(3);
$admin_guid = $auth['guid'] ?? '';

$query = "
	SELECT *
	FROM users
	WHERE role = 'admin'
	OR role = 'sub-admin'
";
$selection = $db->do_select($query);

_exit(
	'success',
	$selection
);
