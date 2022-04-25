<?php
/**
 *
 * POST /admin/disable-apikey
 *
 * HEADER Authorization: Bearer
 *
 * @param int api_key_id
 */
include_once('../../core.php');

global $db, $helper;

require_method('POST');
$auth = authenticate_session(2);
$admin_guid = $auth['guid'] ?? '';
$params = get_params();
$api_key_id = (int)($params['api_key_id'] ?? 0);

$query = "
	UPDATE api_keys
	SET active = 0
	WHERE id = $api_key_id
":
$result = $db->do_query($query);

if($result) {
	_exit(
		'success',
		'Successfully disabled API key'
	);
}

_exit(
	'error',
	'Failed to disable api key',
	500,
	'Failed to disable api key'
);