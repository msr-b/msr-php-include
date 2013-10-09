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
	define('FILE_READWRITE', FILE_READ | FILE_WRITE);

	class File {

		public function __construct($fileURL) {
			$this -> fileR   = NULL;
			$this -> fileW   = NULL;
			$this -> fileURL = $fileURL;
		}

		public function open($mode) {
		/*
			5 modes:
				FILE_READONLY | FILE_HEADER
					Read form header.
				FILE_WRITEONLY | FILE_HEADER
					Create/Recreate the file.
					Write from header.
				FILE_WRITEONLY | FILE_END
					Continue to write from the end.
				FILE_READWRITE | FILE_HEADER
					Create/Recreate the file.
					Read/Write from header.
				FILE_READWRITE | FILE_END
					Read form header.
					Continue to write from the end.

			When succeeded: return true
			When failed   : return false
		*/
			if (!$this -> close()) {
				return false;
			}
			if (is_integer($mode) && 0 < $mode && $mode < 16) {
				switch ($mode) {
					case FILE_READONLY | FILE_HEADER:
						if ($this -> fileR = fopen($this -> fileURL, 'rb')) {
							return true;
						}
						break;
					case FILE_WRITEONLY | FILE_HEADER:
						if ($this -> fileW = fopen($this -> fileURL, 'wb')) {
							return true;
						}
						break;
					case FILE_WRITEONLY | FILE_END:
						if ($this -> fileW = fopen($this -> fileURL, 'ab')) {
							return true;
						}
						break;
					case FILE_READWRITE | FILE_HEADER:
						if (($this -> fileW = fopen($this -> fileURL, 'wb')) &&
							($this -> fileR = fopen($this -> fileURL, 'rb'))   ) {
							return true;
						}
						break;
					case FILE_READWRITE | FILE_END:
						if (($this -> fileW = fopen($this -> fileURL, 'ab')) && 
							($this -> fileR = fopen($this -> fileURL, 'rb'))   ) {
							return true;
						}
						break;
					default:
						break;
				}
			}
			$this -> fileR  = NULL;
			$this -> fileW  = NULL;
			return false;
		}

		public function close() {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> fileR && !fclose($this -> fileR)) {
				return false;
			}
			if ($this -> fileW && !fclose($this -> fileW)) {
				return false;
			}
			$this -> fileR  = NULL;
			$this -> fileW  = NULL;
			return true;
		}

		public function write($str) {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> fileW && flock($this -> fileW, LOCK_EX | LOCK_NB)) {
				fwrite($this -> fileW, $str);
				flock($this -> fileW, LOCK_UN);
				return true;
			}
			return false;
		}

		public function writeLine($str) {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			return self::write($str."\n");
		}

		public function readLine() {
		/*
			You can just read by lines now.
			I'll implemented other functions in the future.

			When succeeded: return $str
			When failed   : return NULL
		*/
			if ($this -> fileR && flock($this -> fileR, LOCK_SH | LOCK_NB)) {
				if (!feof($this -> fileR)) {
					$str = fgets($this -> fileR);
					flock($this -> fileR, LOCK_UN);
					return $str;
				}
				flock($this -> fileR, LOCK_UN);
			}
			return NULL;
		}

		public function read() {
		/*
			Read a charactor.

			When succeeded: return $c
			When failed   : return NULL
		*/
			if ($this -> fileR && flock($this -> fileR, LOCK_SH | LOCK_NB)) {
				if (!feof($this -> fileR)) {
					$c = fgetc($this -> fileR);
					flock($this -> fileR, LOCK_UN);
					return $c;
				}
				flock($this -> fileR, LOCK_UN);
			}
			return NULL;
		}
		
		public function __destruct() {
		/*
			Simply unset($file) will close the file.
		*/
			$this -> close();
		}

		protected $fileR;
		protected $fileW;
		protected $fileURL;

	}

?>
