<?php
require_once ("Faux-Logs-constants.php");
class Faux_Logs
{
    public $logFile;
    public $iterations;
    public $config;
    public $preCooked = array();
    public $error = false;
    public $error_msg = null;
    function __construct($logFile, $iterations, $configFile = "config.php"){
        $this->read_config($configFile);
        $logEntries = $this->generate_lines($iterations);
        $this->write_log($logFile);
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
        // generate pre cooked values
        if(is_array($this->config['pre']) && sizeof($this->config['pre'] > 0)){
            foreach ($this->config['pre'] as $field){
                $this->preCooked[] = $this->generate_field($field);
            }
        }
     }

    function write_log($logFile){

    }

    function generate_field($fieldConfig){

    }

}