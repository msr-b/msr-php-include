<?php

	// chown www-data:www-data /your/site/

	define('FILE_READ'  , 1);
	define('FILE_WRITE' , 2);
	define('FILE_HEADER', 0);
	define('FILE_END'   , 4);

	define('FILE_READONLY' , FILE_READ             );
	define('FILE_WRITEONLY', FILE_WRITE            );
	define('FILE_READWRITE', FILE_READ | FILE_WRITE);

	class File {

		public function __construct($fileURL) {
			$this -> status  = NULL;
			$this -> file    = NULL;
			$this -> fileURL = $fileURL;
		}

		public function open($mode) {
			if (!$this -> status && is_integer($mode) && 0 < $mode && $mode < 16) {
				switch ($mode) {
					case FILE_READONLY | FILE_HEADER:						
						if ($this -> file = fopen($this -> fileURL, 'rb')) {
							$this -> status = $mode;
							return true;
						}
						return false;
					case FILE_WRITEONLY | FILE_HEADER:
						if ($this -> file = fopen($this -> fileURL, 'wb')) {
							$this -> status = $mode;
							return true;
						}
						return false;
					case FILE_READWRITE | FILE_HEADER:
						if ($this -> file = fopen($this -> fileURL, 'w+b')) {
							$this -> status = $mode;
							return true;
						}
						return false;
					case FILE_WRITEONLY | FILE_END:
						if ($this -> file = fopen($this -> fileURL, 'ab')) {
							$this -> status = $mode;
							return true;
						}
						return false;
					case FILE_READWRITE | FILE_END:
						if ($this -> file = fopen($this -> fileURL, 'a+b')) {
							$this -> status = $mode;
							return true;
						}
						return false;
					default:
						return false;
				}
			}
			return false;
		}

		public function close() {
			if ($this -> status) {
				if (fclose($this -> file)) {
					$this -> file   = NULL;
					$this -> status = NULL;
					return true;
				}
			}
			return false;
		}

		public function write($str) {
			if ($this -> status & FILE_WRITE) {
				if (flock($this -> file, LOCK_EX | LOCK_NB)) {
					fwrite($this -> file, $str);
					flock($this -> file, LOCK_UN);
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

		public function readLine() {
			if (flock($this -> file, LOCK_SH | LOCK_NB)) {
				if (!feof($this -> file)) {
					flock($this -> file, LOCK_UN);
					return fgets($this -> file);				
				}
				return false;
			}
			return false;
		}
		
		public function __destruct() {
			if ($this -> status) {
				$this -> close();
			}
		}

		protected $file;
		protected $fileURL;
		protected $status;

	}

?>