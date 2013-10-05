<?php

	require_once __INCLUDE_DIR__.'/msr-log-file-instance.php';

	class MySQLConnect {

		public function __construct($servername, $username, $password) {
			$this -> servername = $servername;
			$this -> username = $username;
			$this -> password = $password;
			$this -> connect();
		}

		public function connect() {
			if (!$this -> isConnected()) {
				$this -> connect = mysql_connect($this -> servername, $this -> username, $this -> password, true); // new_link must be true
				if (!$this -> connect) {
					MSRLog('Cannot connected to '.$this -> servername.': '.mysql_error(), 'DATABASE', STATUS_ERROR);
				}
				$this -> database = NULL;
			}
			return $this -> isConnected();
		}

		public function disconnect() {
			if ($this -> isConnected()) {
				if (!mysql_close($this -> connect)) {
					MSRLog('Cannot disconnected from '.$this -> servername.' normally, discarded the connection.'.mysql_error(), 'DATABASE', STATUS_WARNING);
				}
				$this -> connect = NULL;
			}
			$this -> database = NULL;
			return true;
		}

		public function isConnected() {
			return $this -> connect? true : false;
		}

		public function selectDatabase($database) {
			if ($this -> isConnected()) {
				if (mysql_select_db($database, $this -> connect)) {
					$this -> database = $database;
					return true;
				}
				$this -> database = NULL;
				MSRLog('Cannot select database: '.$database.': '.mysql_error(), 'DATABASE', STATUS_ERROR);
				return false;
			}
			$this -> database = NULL;
			return false;
		}

		public function databaseIsSelected() {
			return $this -> database? true : false;
		}

		public function insert($table, $data) {
			if ($this -> databaseIsSelected()) {
				$encodedData = self::FieldsAndValuesEncode($data);
				$query = "INSERT INTO $table ".
				         '('.$encodedData['fields'].
				         ') VALUES ('.$encodedData['values'].');';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				MSRLog("> $query // database = ".$this -> database, 'DATABASE', $succeed? STATUS_SUCCESS : STATUS_ERROR);
				return $succeed;
			}
			return false;
		}

		public function delete($table, $condition) {
			if ($this -> databaseIsSelected()) {
				$query = "DELETE FROM $table ".
				         'WHERE '.$condition.';';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				MSRLog("> $query // database = ".$this -> database, 'DATABASE', $succeed? STATUS_SUCCESS : STATUS_ERROR);
				return $succeed;
			}
			return false;
		}

		public function update($table, $data, $condition) {
			if ($this -> databaseIsSelected()) {
				$query = "UPDATE $table ".
				         'SET '.self::DataEncode($data).' '.
				         'WHERE '.$condition.';';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				MSRLog("> $query // database = ".$this -> database, 'DATABASE', $succeed? STATUS_SUCCESS : STATUS_ERROR);
				return $succeed;
			}
			return false;
		}

		public function select($table, $position, $condition) {
			$results = NULL;	
			if ($this -> databaseIsSelected()) {
				$query = 'SELECT '.self::PositionEncode($position).' '.
				         "FROM $table ".
				         'WHERE '.$condition.';';
				MSRLog("> $query // database = ".$this -> database, 'DATABASE', STATUS_DEFAULT);
				$results = mysql_query($query, $this -> connect);
			}
			$array = array();
			if ($results) {
				while ($result = mysql_fetch_assoc($results)) {
					array_push($array, $result);
				}
			}
			return $array;
		}

		public function query($query) {
			MSRLog("> $query", 'DATABASE', STATUS_WARNING, SECURITY_WARNING);
			if ($this -> isConnected()) {
				return mysql_query($query, $this -> connect);
			}
			return false;
		}

		public function error() {
			return mysql_error();
		}

		public function errno() {
			return mysql_errno();
		}

		static private function DataEncode($data) {
			$result = '';
			$len = count($data);
			reset($data);			
			for ($i = 0; $i < $len; $i++) { 
				list($field, $value) = each($data);
				$result .= $field.'='.self::ValueString($value);
				if ($i != $len - 1) {
					$result .= ',';
				}
			}
			return $result;
		}

		static private function PositionEncode($position) {
			if (is_string($position)) {
				return $position;
			}
			$result = '';
			$len = count($position);
			reset($position);
			for ($i = 0; $i < $len; $i++) { 
				$field = $position[$i];
				$result .= $field;
				if ($i != $len - 1) {
					$result .= ',';
				}
			}
			return $result;
		}

		static private function FieldsAndValuesEncode($data) {
			$fields = '';
			$values = '';
			$len = count($data);
			reset($data);
			for ($i = 0; $i < $len; $i++) { 
				list($field, $value) = each($data);
				$fields .= $field;
				$values .= self::ValueString($value);
				if ($i != $len - 1) {
					$fields .= ',';
					$values .= ',';
				}
			}
			return array('fields' => $fields,
			             'values' => $values);
		}

		static public function ValueSting($value) {
			if (is_null($value)) {
				return 'NULL';
			} else if (is_bool($value)) {
				return $value? 'TRUE' : 'FALSE';
			} else if (is_numeric($value) && !is_string($value)) {
				return (string)$value;
			} else {
				return "'".$value."'";
			}
		}

		public function __destruct() {
			if ($this -> isConnected()) {
				$this -> disconnect();
			}
		}

		private $connect;    // mysql connect
		private $database;   // string
		private $servername; // string
		private $username;   // string
		private $password;   // string
		
	}

?>