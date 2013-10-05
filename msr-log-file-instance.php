<?php

	require_once __INCLUDE_DIR__.'/msr-log-file.php';
	require_once __INCLUDE_DIR__.'/msr-screen-log.php';

	function MSRLog($message                    ,
					$type     = NULL            ,
					$status   = STATUS_DEFAULT  ,
					$security = SECURITY_DEFAULT) {
		$file = new LogFile(LOG_FILE_DIR);
		if ($file -> open(LOG_FILE_WRITE)) {
			return $file -> write($message, $type, $status, $security);
		}
		return false;
	}

	function MSRPrintAllLogs() {
		$file = new LogFile(LOG_FILE_DIR);
		if ($file -> open(LOG_FILE_READ)) {
			while ($log = $file -> readLine()) {
				ScreenLog($log);
			}
			return true;
		}
		return false;
	}

?>