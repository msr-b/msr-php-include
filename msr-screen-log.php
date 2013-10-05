<?php

	require_once __INCLUDE_DIR__.'/msr-log.php';

	define('SCREEN_LOG_ON' , true );
	define('SCREEN_LOG_OFF', false);

	$ScreenLogStatus = SCREEN_LOG_OFF;
	$ScreenLogCount = 0;

	function SetScreenLogStatus($status) {
		global $ScreenLogStatus;
		if ($ScreenLogStatus == SCREEN_LOG_ON || $ScreenLogStatus == SCREEN_LOG_OFF) {
			$ScreenLogStatus = $status;
		}
	}

	function ScreenLogStatusIsOn() {
		global $ScreenLogStatus;
		return $ScreenLogStatus;
	}

	function ScreenLog($var1                   ,
	                   $var2 = NULL            ,
	                   $var3 = STATUS_DEFAULT  ,
	                   $var4 = SECURITY_DEFAULT,
	                   $var5 = NULL            ,
	                   $var6 = NULL            ) {
		// ScreenLog(Log      $log     )
		// ScreenLog(string   $message ,
		//           string   $type    ,
		//           STATUS   $status  ,
		//           SECURITY $security,
		//           string   $date    ,
		//           string   $time    )
		global $ScreenLogStatus;
		if ($ScreenLogStatus == SCREEN_LOG_OFF) {
			return;
		}
		if (is_object($var1) && get_class($var1) == 'Log') {
			$array = json_decode($var1, true);
		} else if (is_string($var1) &&
		          (is_string($var2) || is_null($var2)) &&
		          is_int($var3) &&
		          is_int($var4) &&
		          (is_string($var5) || is_null($var5)) &&
		          (is_string($var6) || is_null($var6))) {
			$array = json_decode(new Log($var1, $var2, $var3, $var4, $var5, $var6), true);
		} else {
			return;
		}
		global $ScreenLogCount;
		$ScreenLogCount++;
		$date           = $array['date'   ];
		$time           = $array['time'   ];
		$type           = $array['type'   ];
		$message        = $array['message'];
		$status         = $array['status' ];
		$statusString   = str_replace('STATUS_'  , '', StatusString  ($status  ));
		$security       = $array['security'];
		$securityString = str_replace('SECURITY_', '', SecurityString($security));
		switch ($status) {
			case STATUS_SUCCESS:
				$statusClass = 'success';
				break;
			case STATUS_WARNING:
				$statusClass = 'warning';
				break;
			case STATUS_ERROR:
				$statusClass = 'danger';
				break;
			default:
				$statusClass = '';
				break;
		}
		switch ($security) {
			case SECURITY_SAFE:
				$securityClass = 'success';
				break;
			case SECURITY_WARNING:
				$securityClass = 'warning';
				break;
			case SECURITY_DANGER:
				$securityClass = 'danger';
				break;
			default:
				$securityClass = '';
				break;
		}
		echo "<tr><td class=\"active\">$ScreenLogCount</td><td>$date</td><td>$time</td><td>$type</td><td class=\"$statusClass\">$statusString</td><td class=\"$securityClass\">$securityString</td><td>$message</td></tr>";
	}

?>