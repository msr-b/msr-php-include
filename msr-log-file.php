<?php

	require_once __INCLUDE_DIR__.'/msr-log.php';
	require_once __INCLUDE_DIR__.'/msr-file.php';

	define('LOG_FILE_READ' , FILE_READONLY  | FILE_HEADER);
	define('LOG_FILE_WRITE', FILE_WRITEONLY | FILE_END   );

	class LogFile extends File {

		public function open($mode) {
			if ($mode == LOG_FILE_READ || $mode == LOG_FILE_WRITE) {
				return parent::open($mode);
			}
			return false;
		}

		public function write($var1, $var2 = NULL, $var4 = STATUS_DEFAULT, $var3 = SECURITY_DEFAULT) {
			// write(Log $var1)
			// write(string $message, string $type, SECURITY $security, STATUS $status)
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
			$array = json_decode(parent::readLine(), true);
			if (!empty($array)) {
				return new Log($array['message'], $array['type'], $array['security'], $array['status'], $array['date'], $array['time']);
			} else {
				return null;
			}
		}

	}

?>