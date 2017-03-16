<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * We are creating exception classes here specifically for dealing with connection
 * problems and bad query results
 *
 * @author cst228
 */
class DB_Exception extends Exception
{
    public function __toString()
    {
        //This is the place where we can actually define a special error handling message
        //This could potentially include something like writing to an error log file
        $sReturn = "<table border='2'><tr><th>Code</th><th>Message</th></tr>";
        $sReturn .= "<tr><td>". $this->getCode()."</td><td>". $this->getMessage()."</td></tr></table>";
        return $sReturn;
    }       
}
class DB_Connect_Exception extends Exception
{
    /**
     * We are going to overwrite the constructor and pass in a file name that
     * we could use to actually write info out to
     * This might be useful for debugging purposes
     */
    
    private $sFileName;
    
    public function __construct($message = "", $code = 0, $sFile="../wide_open/error.txt") {
        $this->sFileName=$sFile;
        parent::__construct($message, $code);
    }
    
    public function saveInfoToFile()
    {
        try{
            $fp = fopen($this->sFileName, "w");
            fwrite($fp, $this->__toString());
            fwrite($fp, $this->getTraceAsString());
            fclose($fp);
        } catch (Exception $ex1) {
            echo $ex1;
        }
        
    }
    
    public function __toString()
    {
        $sReturn = "<table border='2'><tr><th>File</th><th>Line Number</th></tr>";
        $sReturn .= "<tr><td>". $this->getFile()."</td><td>". $this->getLine()."</td></tr></table>";
        return $sReturn;
    } 
}