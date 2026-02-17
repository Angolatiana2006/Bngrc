<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use flight\debug\tracy\TracyExtensionLoader;
use Tracy\Debugger;


Debugger::enable(); 

Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log'; 
Debugger::$strictMode = true; 
if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
	(new TracyExtensionLoader($app)); 
}

