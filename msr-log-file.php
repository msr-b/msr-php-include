<?php

/*
	A log file class for php.
	Created by Darren Liu (MSR.B, msr-b)
*/

	require_once __INCLUDE_DIR__.'/msr-log.php';
	require_once __INCLUDE_DIR__.'/msr-file.php';

	define('LOG_FILE_READ'     , FILE_READONLY  | FILE_HEADER);
	define('LOG_FILE_WRITE'    , FILE_WRITEONLY | FILE_END   );
	define('LOG_FILE_READWRITE', FILE_READWRITE | FILE_END   );

	class LogFile extends File {

		public function open($mode = LOG_FILE_READWRITE) {
		/*
			3 modes:
				LOG_FILE_READ
				LOG_FILE_WRITE
				LOG_FILE_READWRITE(default)
		*/
			if ($mode == LOG_FILE_READ || $mode == LOG_FILE_WRITE || $mode == LOG_FILE_READWRITE) {
				return parent::open($mode);
			}
			return false;
		}

		public function write($var1, $var2 = NULL, $var4 = STATUS_DEFAULT, $var3 = SECURITY_DEFAULT) {
		/*
			2 overloads:
				write(string $message, string $type, SECURITY $security, STATUS $status)
				write(Log $var1)
					See msr-log.php
		*/
			if (is_object($var1) && get_class($var1) == 'Log') {
				$log = $var1;
				return parent::write($log."\n");
			} else if (is_string($var1) && (is_string($var2) || is_null($var2)) && is_int($var3) && is_int($var4)) {
				$log = new Log($var1, $var2, $var3, $var4);
				return parent::write($log."\n");
			}
			return false;
		}

		public function readLine() {
		/*
			Get a log.
			return a log object
		*/
			$array = json_decode(parent::readLine(), true);
			if (!empty($array)) {
				return new Log($array['message'], $array['type'], $array['security'], $array['status'], $array['date'], $array['time']);
			}
			return null;
		}

		public function getLog() {
		/*
			Same as readLine().
			return a log object
		*/
			return self::readLine();
		}

		public function getAllLogs() {
		/*
			return all logs in an array
		*/
			$result = array();
			while ($log = self::readLine()) {
				array_push($result, $log);
			}
			return $result;
		}

	}

?>
