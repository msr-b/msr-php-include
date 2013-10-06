<?php

/*
	A package of php file functions.
	Created by Darren Liu (MSR.B, msr-b)
*/

	define('FILE_READ'  , 1);
	define('FILE_WRITE' , 2);
	define('FILE_HEADER', 0);
	define('FILE_END'   , 4);

	define('FILE_READONLY' , FILE_READ );
	define('FILE_WRITEONLY', FILE_WRITE);
	// define('FILE_READWRITE', FILE_READ | FILE_WRITE);

	class File {

		public function __construct($fileURL) {
			$this -> status  = NULL;
			$this -> file    = NULL;
			$this -> fileURL = $fileURL;
		}

		public function open($mode) {
		/*
			3 modes implemented:
				FILE_READONLY | FILE_HEADER
					(rb)  Read form header.
				FILE_WRITEONLY | FILE_HEADER
					(wb)  Create/Recreate the file.
					      Write from header.
				FILE_WRITEONLY | FILE_END
					(ab)  Continue to write from the end.
			2 modes unimplemented:
				FILE_READWRITE | FILE_HEADER
				FILE_READWRITE | FILE_END

			When succeeded: return true
			When failed   : return false
		*/
			if (!$this -> status && is_integer($mode) && 0 < $mode && $mode < 16) {
				switch ($mode) {
					case FILE_READONLY | FILE_HEADER:
						if ($this -> file = fopen($this -> fileURL, 'rb')) {
							$this -> status = $mode;
							return true;
						}
						break;
					case FILE_WRITEONLY | FILE_HEADER:
						if ($this -> file = fopen($this -> fileURL, 'wb')) {
							$this -> status = $mode;
							return true;
						}
						break;
					case FILE_WRITEONLY | FILE_END:
						if ($this -> file = fopen($this -> fileURL, 'ab')) {
							$this -> status = $mode;
							return true;
						}
						break;
					case FILE_READWRITE | FILE_HEADER:
						break;
					case FILE_READWRITE | FILE_END:
						break;
					default:
						break;
				}
			}
			return false;
		}

		public function close() {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> status && fclose($this -> file)) {
				$this -> file   = NULL;
				$this -> status = NULL;
				return true;
			}
			return false;
		}

		public function write($str) {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			if (($this -> status & FILE_WRITE) && flock($this -> file, LOCK_EX | LOCK_NB)) {
				fwrite($this -> file, $str);
				flock($this -> file, LOCK_UN);
				return true;	
			}
			return false;
		}

		public function readLine() {
		/*
			You can just read by lines now.
			I'll implemented other functions in the future.

			When succeeded: return $str
			When failed   : return false
		*/
			if (flock($this -> file, LOCK_SH | LOCK_NB)) {
				if (!feof($this -> file)) {
					$str = fgets($this -> file);
					flock($this -> file, LOCK_UN);
					return $str;
				}
				flock($this -> file, LOCK_UN);
			}
			return false;
		}
		
		public function __destruct() {
		/*
			Simply unset($file) will close the file.
		*/
			$this -> close();
		}

		protected $file;
		protected $fileURL;
		protected $status;

	}

?>
