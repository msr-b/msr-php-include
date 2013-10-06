<?php

/*
	Security level definitions.
	Created by Darren Liu (MSR.B, msr-b)
*/

	define('SECURITY_SAFE'   , 0            );
	define('SECURITY_WARNING', 1            );
	define('SECURITY_DANGER' , 2            );
	define('SECURITY_DEFAULT', SECURITY_SAFE);

	function SecurityString($security) {
		switch ($security) {
			case SECURITY_SAFE:
				return 'SECURITY_SAFE';
			case SECURITY_WARNING:
				return 'SECURITY_WARNING';
			case SECURITY_DANGER:
				return 'SECURITY_DANGER';			
			default:
				return 'SECURITY_UNKNOWN';
		}
	}

?>
