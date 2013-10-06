<?php

/*
	Log object for php.
	Created by Darren Liu (MSR.B, msr-b)

	Format of a log in json: {
		"date": "yyyy-mm-dd",
		"time": "hh:mm:ss",
		"type": "string",
		"status": "success|warning|error",
		"security": "safe|warning|danger",
		"message": "string"
	}
*/

	require_once __INCLUDE_DIR__.'/msr-status.php';
	require_once __INCLUDE_DIR__.'/msr-security.php';

	class Log {

		public function __construct($message                    ,									
		                            $type     = NULL            ,
		                            $status   = STATUS_DEFAULT  ,
		                            $security = SECURITY_DEFAULT,
		                            $date     = NULL            ,
									$time     = NULL            ) {
			/*
				string   $message  - essential
				string   $type     - optional
				STATUS   $status   - optional
				SECURITY $security - optional
				string   $date     - optional
				string   $time     - optional
			*/
			if (!$date) {
				$date = date('Y-m-d');
			}
			if (!$time) {
				$time = date('h:i:s');
			}
			// $this -> message  = str_replace(PHP_EOL, '\n', $message);
			// json_encode() will automatically replace PHP_EOL with '\n'
			$this -> message  = $message;
			$this -> type     = $type;
			$this -> status   = $status;
			$this -> security = $security;
			$this -> date     = $date;
			$this -> time     = $time;
		}

		public function __toString() {
			/*
				Automatically convert Log to string in json format.
			*/
			$array = array("date"     => $this -> date    ,
			               "time"     => $this -> time    ,
			               "type"     => $this -> type    ,
			               "status"   => $this -> status  ,
			               "security" => $this -> security,
			               "message"  => $this -> message );
			return json_encode($array);
		}

		public function toArray() {
			/*
				Return array format log.
			*/
			return json_decode($this);
		}

		private $type;
		private $message;
		private $date;
		private $time;
		private $status;
		private $security;

	}

?>
