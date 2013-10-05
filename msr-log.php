<?php

	// {
	// 	    "date": "yyyy-mm-dd",
	// 	    "time": "hh:mm:ss",
	// 	    "type": "string",
	// 	  "status": "success|warning|error",
	// 	"security": "safe|warning|danger",
	// 	 "message": "string"
	// }

	require_once __INCLUDE_DIR__.'/msr-status.php';
	require_once __INCLUDE_DIR__.'/msr-security.php';

	class Log {

		public function __construct($message                    ,									
		                            $type     = NULL            ,
		                            $status   = STATUS_DEFAULT  ,
		                            $security = SECURITY_DEFAULT,
		                            $date     = NULL            ,
									$time     = NULL            ) {
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
			$array = array("date"     => $this -> date    ,
			               "time"     => $this -> time    ,
			               "type"     => $this -> type    ,
			               "status"   => $this -> status  ,
			               "security" => $this -> security,
			               "message"  => $this -> message );
			return json_encode($array);
		}

		public function toArray() {
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