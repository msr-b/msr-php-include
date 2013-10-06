<?php

/*
	Status definitions.
	Created by Darren Liu (MSR.B, msr-b)
*/

	define('STATUS_SUCCESS', 0             );
	define('STATUS_WARNING', 1             );
	define('STATUS_ERROR'  , 2             );
	define('STATUS_DEFAULT', STATUS_SUCCESS);

	function StatusString($status) {
		switch ($status) {
			case STATUS_SUCCESS:
				return 'STATUS_SUCCESS';
			case STATUS_WARNING:
				return 'STATUS_WARNING';
			case STATUS_ERROR:
				return 'STATUS_ERROR';			
			default:
				return 'STATUS_UNKNOWN';
		}
	}

?>
