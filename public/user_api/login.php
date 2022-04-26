<?php
/**
 *
 * POST /user/login
 *
 * @param email     string
 * @param password  string
 *
 */
include_once('../../core.php');

global $db, $helper;

require_method('POST');
$params = get_params();
$email = $params['email'] ?? null;
$password = $params['password'] ?? null;

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	_exit(
		'error',
		'Invalid email address',
		400,
		'Invalid email address'
	);
}

if(!$password) {
	_exit(
		'error',
		'Please provide a password',
		400,
		'No password provided'
	);
}

$query = "
	SELECT guid, email, first_name, last_name, role, password, twofa, verified, last_ip, company, admin_approved, deny_reason
	FROM users
	WHERE email = '$email'
";

$result = $db->do_select($query);
$result = $result[0] ?? null;
$guid = $result['guid'] ?? '';
$twofa = (int)($result['twofa'] ?? 0);
$fetched_password_hash = $result['password'] ?? '';
$password_hash = hash('sha256', $password);
$created_at = $helper->get_datetime();
$expires_at = $helper->get_datetime(86400); // one day from now

if(!hash_equals($fetched_password_hash, $password_hash)) {
	_exit(
		'error',
		'Invalid email or password',
		401,
		'Invalid email or password'
	);
}

/* check MFA */
if($twofa == 1) {
	$code = $helper->generate_hash(6);

	$helper->schedule_email(
		'twofa',
		$email,
		'Multi Factor Authentication',
		'Please find your MFA code below to login to CasperFYRE. This code expires in 10 minutes.',
		$code
	);

	$query = "
		DELETE FROM twofa
		WHERE guid = '$guid'
	";
	$db->do_query($query);

	$query = "
		INSERT INTO twofa (
			guid,
			created_at,
			code
		) VALUES (
			'$guid',
			'$created_at',
			'$code'
		)
	";
	$db->do_query($query);

	_exit(
		'success',
		array(
			'twofa' => true,
			'guid' => $guid
		)
	);
}

$bearer = $helper->generate_session_token();

$query1 = "
	DELETE FROM sessions
	WHERE guid = '$guid'
";

$query2 = "
	INSERT INTO sessions (
	guid,
	bearer,
	created_at,
	expires_at
	) VALUES (
	'$guid',
	'$bearer',
	'$created_at',
	'$expires_at'
	)
";

$db->do_query($query1);
$db->do_query($query2);

_exit(
	'success',
	array(
		'bearer' => $bearer,
		'guid' => $guid,
		'user' => $result
	)
);
