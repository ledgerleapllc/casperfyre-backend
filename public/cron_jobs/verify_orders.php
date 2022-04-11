<?php
include_once('../../core.php');

global $helper;

$query = "
	SELECT id, deploy_hash
	FROM orders
	WHERE fulfilled = 1
	AND success = 0
	LIMIT 3
";

$selection = $db->do_select($query);

if($selection) {
	foreach($selection as $s) {
		$order_id = $s['id'];
		$deploy_hash = $s['deploy_hash'] ?? '';

		if(
			$deploy_hash && 
			strlen($deploy_hash) == 64 &&
			ctype_xdigit($deploy_hash)
		) {
			$command = "casper-client get-deploy";
			$command .= " --node-address http://".NODE_IP.":7777";
			$command .= " ".$deploy_hash;
			$stdout = shell_exec($command);
			$success = '';

			try {
				$json = json_decode($stdout);
				$execution_results = (array)($json->result->execution_results[0]->result ?? array());
				$execution_results = array_keys($execution_results)[0] ?? '';
				$success = strtolower($execution_results);
			} catch (Exception $e) {
				elog($e);
			}

			if($success == 'success') {
				$query = "
					UPDATE orders
					SET success = 1
					WHERE id = $order_id
				";

				$db->do_query($query);
			}
		}
	}
}