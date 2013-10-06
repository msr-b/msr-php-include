<?php

/*
	A package of MySQL database functions
	Created by Darren Liu (MSR.B, msr-b)
*/

	class MySQLConnect {

		public function __construct($servername, $username, $password, $autoConnect = false) {
			/*
				string $servername  - essential
				string $username    - essential
				string $password    - essential
				bool   $autoConnect - optional
			*/
			$this -> servername = $servername;
			$this -> username = $username;
			$this -> password = $password;
			if ($autoConnect) {
				$this -> connect();
			}
		}

		public function connect() {
		/*
			Will not create multi-connection in one object.

			When connected   : return true
			When disconnected: return false
		*/
			if (!$this -> isConnected()) {
				$this -> connect = mysql_connect($this -> servername, $this -> username, $this -> password, true); // new_link must be true
				$this -> database = NULL;
			}
			return $this -> isConnected();
		}

		public function disconnect() {
		/*
			When still connected: return false
			When disconnected   : return true
		*/
			if ($this -> isConnected()) {
				if (!mysql_close($this -> connect)) {
					return false;
				}
				$this -> connect = NULL;
				$this -> database = NULL;
			}
			return true;
		}

		public function isConnected() {
			return $this -> connect? true : false;
		}

		public function selectDatabase($database) {
		/*
			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> isConnected()) {
				if (mysql_select_db($database, $this -> connect)) {
					$this -> database = $database;
					return true;
				}
			}
			$this -> database = NULL;
			return false;
		}

		public function databaseIsSelected() {
			return $this -> database? true : false;
		}

		public function insert($table, $data) {
		/*
			string $table
			array  $data = array(
				'key' => 'value',
				...
			)

			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> databaseIsSelected()) {
				$encodedData = self::FieldsAndValuesEncode($data);
				$query = "INSERT INTO $table ".
				         '('.$encodedData['fields'].
				         ') VALUES ('.$encodedData['values'].');';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				return $succeed;
			}
			return false;
		}

		public function delete($table, $condition) {
		/*
			string $table
			string $condition

			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> databaseIsSelected()) {
				$query = "DELETE FROM $table ".
				         'WHERE '.$condition.';';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				return $succeed;
			}
			return false;
		}

		public function update($table, $data, $condition) {
		/*
			string $table
			array  $data = array(
				'key' => 'value',
				...
			)
			string $condition

			When succeeded: return true
			When failed   : return false
		*/
			if ($this -> databaseIsSelected()) {
				$query = "UPDATE $table ".
				         'SET '.self::DataEncode($data).' '.
				         'WHERE '.$condition.';';
				$succeed = mysql_query($query, $this -> connect)? true : false;
				return $succeed;
			}
			return false;
		}

		public function select($table, $position, $condition) {		
		/*
			string       $table
			string/array $position = 'value'/array('value', ...)
			string       $condition
			
			return result array
		*/
			$results = NULL;
			if ($this -> databaseIsSelected()) {
				$query = 'SELECT '.self::PositionEncode($position).' '.
				         "FROM $table ".
				         'WHERE '.$condition.';';
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
