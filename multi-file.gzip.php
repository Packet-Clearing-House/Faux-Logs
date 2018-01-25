<?php

/**
 * This is a wrapper script that will write $iterations lines to a file based on
 * the $files array(). After appending a time stamp and '.dmp'
 *  It will then gzip them and move them to $savePath. Final file name will look like
 * foo.pch.net_2017-10-17.17-07.dmp.gz. You can optionally pass in a $sleep
 * value to have the script sleep for $sleep microseconds (millionth of a second). Finally,
 * you can pass in a $startDate (as epoch) to have the script start at the date. It will work
 * as fast as possible, in 3 minute increments, to get to the current epoch skipping the $sleep variable.
 * After reaching the current time, it continue to to run, honoring $sleep
 *
 * This script will run endlessly until you stop it (ctl + c)
 */

require_once("config2.php");

// init
$sleep = 0;
$startDate = time();
$threeMin = 180;
$filesWritten = 0;
$linesWritten = 0;

// make sure we have valid args
if (isset($argv[1]) && isset($argv[2])){
    $savePath = $argv[1];
    $iterations = $argv[2];
} else {
    echo "Missing argument! multi-file.gzip takes iterations and save path:\n\n";
    echo "\tphp -f multi-file.gzip.php /tmp/pcaps/ 100 2000000\n\n";
    exit;
}

if (isset($argv[3])){
    if (strval($argv[3]) != strval(intval($argv[3])) ){
        echo "Sorry, \"$argv[3]\" is not an integer to sleep for.\n\n";
        exit;
    } else {
        $sleep = $argv[3];
    }
}

if (isset($argv[4])){
    if (strval($argv[4]) != strval(intval($argv[4])) ){
        echo "Sorry, \"$argv[4]\" is not an integer for a start date (use epoch eg 1516907326).\n\n";
        exit;
    } else {
        $startDate = $argv[4];
    }
}

// make sure we can write to the file
if (!is_file($savePath)){
    if (!touch($savePath)){
        echo "Sorry, \"$savePath\" is not writable.\n\n";
        exit;
    }
}
elseif(!is_writable($savePath)){
    echo "Sorry, \"$savePath\" is not writable.\n\n";
    exit;
}

// make sure have a valid files array
if (!isset($files) || !is_array($files) || sizeof($files) < 1){
    echo 'Sorry, $files is either not an array or is an empty array.' . "\n\n";
    exit;
}


// make sure we got an int
// thanks https://stackoverflow.com/a/29018655
if ( strval($iterations) != strval(intval($iterations)) ) {
    echo "Sorry, \"$iterations\" is not an integer for iterations.\n\n";
    exit;
}

$fileCount = sizeof($files);
print "Starting to call Faux-Logs with $fileCount files for $iterations iterations. Please wait while we run the first round.\n";

// run endlessly - wheeeee!
while (true){
    $backLogTime = '';
    // check for older startdate or default to now
    if ($startDate < time(strtotime('-30 seconds'))){
        $_startDateFL =  date('Y-m-d.H-i', $startDate);
        $startDate += $threeMin;
        $backLogTime = '. Working from: ' . $_startDateFL;
    } else {
        $_startDateFL =  date('Y-m-d.H-i');
        if ($sleep > 0) {
            if ($filesWritten > 0) {
                print "Sleeping...\n";
                $backLogTime = '';
                usleep($sleep);
            }
        }
    }

    // build up name and loop over files array.  write log files and gzip
    $fileSuffix = '_' . $_startDateFL . '.dmp';
    foreach ($files as $file){
        $_file = $savePath . $file . $fileSuffix;
        echo `php -f Faux-Logs.php {$_file} {$iterations}`;
        gzCompressFile($_file, 1);
        unlink($_file);
        $linesWritten +=$iterations;
        $filesWritten++;
    }

    // output status
    print "\tFiles Written: $filesWritten, Lines Written: " . number_format($linesWritten). "$backLogTime\n";
}


/**
 * GZIPs a file on disk (appending .gz to the name)
 *
 * From http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
 * Based on function by Kioob at:
 * http://www.php.net/manual/en/function.gzwrite.php#34955
 *
 * @param string $source Path to file that should be compressed
 * @param integer $level GZIP compression level (default: 9)
 * @return string New filename (with .gz appended) if success, or false if operation fails
 */
function gzCompressFile($source, $level = 9){
    $dest = $source . '.gz';
    $mode = 'wb' . $level;
    $error = false;
    if ($fp_out = gzopen($dest, $mode)) {
        if ($fp_in = fopen($source,'rb')) {
            while (!feof($fp_in))
                gzwrite($fp_out, fread($fp_in, 1024 * 512));
            fclose($fp_in);
        } else {
            $error = true;
        }
        gzclose($fp_out);
    } else {
        $error = true;
    }
    if ($error)
        return false;
    else
        return $dest;
}