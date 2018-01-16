<?php

// make sure we got two arguments
if (isset($argv[1]) && isset($argv[2])){
    $logFile = $argv[1];
    $iteration = $argv[2];
} else {
    echo "Missing argument! Faux-Logs takes file and iteration:\n\n";
    echo "\tphp -f Faux-Logs.php filename_log 100\n\n";
    exit;
}

// make sure we can write to the log file
if (!is_file($logFile)){
    if (!touch($logFile)){
        echo "Sorry, \"$logFile\" is not writable.\n\n";
        exit;
    }
}
elseif(!is_writable($logFile)){
    echo "Sorry, \"$logFile\" is not writable.\n\n";
    exit;
}

// make sure we got an int
// thanks https://stackoverflow.com/a/29018655
if ( strval($iteration) != strval(intval($iteration)) ) {
    echo "Sorry, \"$iteration\" is not an integer.\n\n";
    exit;
}

// we have good in inputs, run Faux-Logs
require_once ("Faux-Logs-Class.php");
new Faux_Logs($logFile, $iteration);