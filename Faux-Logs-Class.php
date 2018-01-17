<?php
require_once ("Faux-Logs-constants.php");
class Faux_Logs
{
    public $logFile;
    public $config;
    public $preCooked = array();
    public $lines = '';
    public $error = false;
    public $error_msg = null;
    function __construct($logFile, $iterations, $configFile = "config.php"){
        $this->setTimezone('America/Los_Angeles');
        $this->read_config($configFile);
        $this->generate_lines($iterations);
        $this->write_log($logFile);

        // todo - handle errors better
        if($this->error){
            print $this->error_msg;
        }
    }

    /**
     * read in the config file for Faux-Logs
     * @param $configFile path to config file
     */
    function read_config($configFile){
        if(is_file($configFile)){
            require_once ($configFile);
            if (isset($fl_config) && is_array($fl_config)){
                $this->config = $fl_config;
            } else {
                $this->error = true;
                $this->error_msg = "Could not read config file '$configFile'";
            }
        } else {
            $this->error = true;
            $this->error_msg = "Could not read config file '$configFile'";
        }
    }


    /**
     * Generate $iterations log lines based on config
     * @param $iterations of log lines
     */
    function generate_lines($iterations){

        // generate log entries
        $count = 1;
        while ($count <= $iterations) {
            if (is_array($this->config['log']) && sizeof($this->config['log'] > 0)) {
                // generate pre cooked values
                if(isset($this->config['pre']) && is_array($this->config['pre']) && sizeof($this->config['pre'] > 0)){
                    $this->preCooked = array();
                    foreach ($this->config['pre'] as $fieldConfig){
                        $this->preCooked[] = $this->generate_field($fieldConfig);
                    }
                }

                foreach ($this->config['log'] as $fieldConfig) {
                    $this->lines .= $this->generate_field($fieldConfig);
                }
                $this->lines .= "\n";
            }
            $count++;
        }
    }

    /**
     * output results to file
     * @param $logFile
     */
    function write_log($logFile){
        // todo check for/handle output errors
        file_put_contents($logFile, $this->lines);
    }

    function generate_field($fieldConfig){
        switch ($fieldConfig){
            case FAUX_DATE1:
                return date('d/M/Y:G:i:s O');
                break;

            case FAUX_IPv4:
                return $this->faux_ipv4();
                break;

            case FAUX_IPv6:
                return $this->faux_ipv6();
                break;

            case FAUX_NL:
                return "\n";
                break;

            // thanks https://secure.php.net/manual/en/function.array-rand.php#112227
            case (is_array($fieldConfig)):
                return $fieldConfig[mt_rand(0,count($fieldConfig) -1)];
                break;

            case (substr($fieldConfig,0,FAUX_ID_OFFSET) == 'str::'):
                return substr($fieldConfig,FAUX_ID_OFFSET);
                break;

            case (substr($fieldConfig,0,FAUX_ID_OFFSET) == 'int::'):
                // todo - error check we get a valid array and valid int in the end
                $rangeStr = substr($fieldConfig,FAUX_ID_OFFSET);
                $rangeAry = explode("-", $rangeStr);
                $low = $rangeAry[0];
                $high = $rangeAry[1];
                return mt_rand($low, $high);

            case (substr($fieldConfig,0,FAUX_ID_OFFSET) == 'pre::'):
                $offset = substr($fieldConfig,FAUX_ID_OFFSET);
                if(isset($this->preCooked[$offset])){
                    return $this->preCooked[$offset];
                }
                break;

            case (substr($fieldConfig,0,FAUX_ID_OFFSET) == 'fle::'):
                $file = substr($fieldConfig,FAUX_ID_OFFSET);
                if(is_file($file) && is_readable($file)){
                    // thanks https://stackoverflow.com/a/12119002
                    $lines = file($file);
                    return trim($lines[mt_rand(0,count($lines) -1)]);
                }
                break;
        }
    }

    /**
     * return a random IPv4 address
     * @return string
     */
    function faux_ipv4(){
        return mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
    }

    /**
     * return random IPv6
     * Thanks https://stackoverflow.com/a/17199526
     * @return string
     */
    function faux_ipv6(){
        return implode(':', str_split(md5(rand()), 4));
    }

    /**
     * Try to derive the local time, if not fall back to $default
     * thanks https://secure.php.net/manual/en/function.date-default-timezone-set.php#113864
     * @param $default
     */
    function setTimezone($default) {
        $timezone = "";

        // On many systems (Mac, for instance) "/etc/localtime" is a symlink
        // to the file with the timezone info
        if (is_link("/etc/localtime")) {

            // If it is, that file's name is actually the "Olsen" format timezone
            $filename = readlink("/etc/localtime");

            $pos = strpos($filename, "zoneinfo");
            if ($pos) {
                // When it is, it's in the "/usr/share/zoneinfo/" folder
                $timezone = substr($filename, $pos + strlen("zoneinfo/"));
            } else {
                // If not, bail
                $timezone = $default;
            }
        }
        else {
            // On other systems, like Ubuntu, there's file with the Olsen time
            // right inside it.
            $timezone = file_get_contents("/etc/timezone");
            if (!strlen($timezone)) {
                $timezone = $default;
            }
        }
        date_default_timezone_set($timezone);
    }

}