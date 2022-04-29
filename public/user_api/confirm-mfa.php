<?php
include_once('../../core.php');
/**
 *
 * POST /user/confirm-mfa
 *
 * HEADER Authorization: Bearer
 *
 * @param string  $mfa_code
 *
 */
class UserConfirmMfa extends Endpoints {
	function __construct(
		$mfa_code = ''
	) {
		global $helper;

		require_method('POST');

		$auth = authenticate_session();
		$guid = $auth['guid'] ?? '';
		$mfa_code = parent::$params['mfa_code'] ?? '';
		$response = $helper->verify_mfa($guid, $mfa_code);

		if($response == 'incorrect') {
			_exit(
				'error',
				'Incorrect MFA code',
				401,
				'Incorrect MFA code'
			);
		} elseif($response == 'expired') {
			_exit(
				'error',
				'MFA code is expired',
				401,
				'MFA code is expired'
			);
		} elseif($response == 'success') {
			_exit(
				'success',
				'MFA code accepted'
			);
		}

		_exit(
			'error',
			'Failed to confirm MFA code',
			400,
			'Failed to confirm MFA code'
		);
	}
}
new UserConfirmMfa();