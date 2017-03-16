<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include("connectionDetails.php");
include("DB_Exception.php");
define("INT_TYPE",3);
define("FLOAT_TYPE",4);
define("DOUBLE_TYPE", 5);
define("DATE_TYPE",10);
define("VAR_CHAR_TYPE",253);
/**
 * Description of cstDB
 *This class will allow us to easily connect to a backend database and 
 * display informmation or place values into the database
 * 
 * 
 * @author cst228
 */
class cstDB {
     //Attribute
    protected $obDB;    //Database object we will work with
    protected $obResult;    //Any result that might be returned
    protected $aFieldObjects; //array of field objects
    protected $bDebug = 0;  //Debug flag
    /**
     * Function Construct
     * Purpose  The constructor will automatically connect to the database
     *          if it cannot for some reason we will exit
     */ 
    public function __construct() {
        //@ symbol just makes regular error message not show up
        @ $this->obDB = new mysqli(HOST,USER, PASSWORD, DB);
        if(mysqli_connect_errno())
        {
            //Could not connect
            //echo "<p style='color:red'><b>The error message is ". mysqli_connect_errno() . "</b></p>";
            throw new DB_Connect_Exception(mysqli_connect_error(),mysqli_connect_errno());
            
            exit(2);
        }
    }
    
    public function __destruct() {
        @ $this->obDB->close();
    }
    
    
    public function setDebug($bValue)
    {
        $this->bDebug = $bValue;
    }
    
    /*
     * This function will return our database connection object
     * This is mainly done when working with prepared statements
     */
    public function getHandle()
    {
        return $this->obDB;
    }
    
    public function showTypes()
    {
        $aFields = mysqli_fetch_fields($this->obResult);
        foreach ($aFields as $obREF) {
            echo "Field type is ".$obREF->type . "<br>";
        }
    }
    
    /**
     * Function doQuery
     * Purpose  This routine will just run the query against the backend database
     * The obResult object will contain the results of running the query
     * Params   sSQL - This is the query to run
     */
    public function doQuery($sSQL)
    {
        if($this->bDebug)
        {
            echo "<br><br>DO QUERY<br>";
            echo "In Query: Running statement $sSQL";
        }
        $this->obResult = $this->obDB->query($sSQL);
        if($this->obDB->errno)
        {
            //echo "<p style='color:red'><b>Query Error: ". $this->obDB->error . "</b></p>";
            throw new DB_Exception($this->obDB->error, $this->obDB->errno);
            
        }
    }
    
    
    
    /************************************************
     * Funciton doInsert
     * Purose This routine will take in a series of key/value pairs in an
     * associative array and use this to build up a SQL Statement. It is assumed that 
     * the keys correspond to the field names and the values corresponding to that information
     * which is to be inserted.
     * Params   aInfo - This is the key/value array for fields and values
     *          sTableName - Name of the table we are inserting into
     *************************************************/
    public function doInsert($aInfo, $sTableName) 
    {
        $sInsert = "insert into $sTableName (";
        
        foreach($aInfo as $sKey=>$sValue)
        {
            $sInsert .= $sKey . ",";
        }
        
        $sInsert = rtrim($sInsert, ",");
        $sInsert.= ") values (";
        
        //Add the actual field values
        
        //Call getTableFldInfo to determine the nature of our fields
        
         $this->getTableFieldInfo($sTableName);
        
        if ($this->bDebug)
        {
           
            $this->showTypes();
        }
        
        
        foreach ($aInfo as $sFldName => $sValue)
        {
            $nFieldType = $this->getFieldType($sFldName);
            
            if (!$this->validFieldMatch($nFieldType,$sValue))
            {
                throw new DB_Exception("Incorrect Field match for " .  $sFldName, 17);
            }
            
            if ($nFieldType == VAR_CHAR_TYPE  ||
                    $nFieldType == MYSQLI_TYPE_DATE)
            {
                $sInsert .= "'$sValue',";
            }
            else 
            {
                $sInsert .= $sValue . ",";
            }
            
            
        }
        $sInsert = rtrim("$sInsert" , ",");
        
        $sInsert .= ")";
        
       
        
        return $this->doQuery($sInsert);
        
        
    }
    
    
    /*******************************
     * Method DoUpdate
     * Purpose  This routine will be responsible for doing an update on the 
     * given table.  It is expected that (i) the Where clause will not be empty and that
     * (2) Will include a reference to the Primary Key (as that is the way MySQL is currently 
     * set up.
     * Params:
     *          aInfo = Associative list of fields and values
     *          sTableName = Name of the table being worked with
     *          sWhere - Clause  for where Condition
     ****************************************************/
    
     public function doUpdate($aInfo, $sTableName, $sWhere)
     {
           $sUpdate = "update $sTableName set " ;
           $this->getTableFieldInfo($sTableName);
           
           //Where check 
           if ($sWhere =="")
           {
               throw new DB_Exception("Where clause cannot be empty", 17);
           }
           
           foreach ($aInfo as $sFld=>$sValue)
           {
                $nFieldType = $this->getFieldType($sFld);
            
                if (!$this->validFieldMatch($nFieldType,$sValue))
                {
                    throw new DB_Exception("Incorrect Field match for " .  $sFld, 17);
                }
                
                if ($nFieldType == VAR_CHAR_TYPE || $nFieldType==DATE_TYPE)
                {
                    $sUpdate .= "$sFld='$sValue',";
                }
                else 
                {
                    $sUpdate .= "$sFld=$sValue,";
                }
                
              
               
           }
           
           //Get rid of the Extra ','
          $sUpdate =  rtrim($sUpdate,",");
           
           $sUpdate .= " where $sWhere";
           
            return $this->doQuery($sUpdate);
     }
             
    
    
    
    
    /*************************
     * Function validFieldMatch 
     * Purpose  Specific for Assignment 2 - This Could be expanded  for 
     * other  types as necessary.  This method will determine if the approprite 
     * type being passed in is correct or not for the associated database field.
     * 
     */
    
    private function validFieldMatch($sField,$sValue)
    {
        
        
        switch ($sField)
        {
            case INT_TYPE:
                //Following deals with the Special Problem of 0 For Filter Var
                //Cannot have a Floating point value !
               
                if (filter_var($sValue, FILTER_VALIDATE_INT) === 0 || !filter_var($sValue, FILTER_VALIDATE_INT) === false)
                {
                    return true;
                }
                break;
                
            case FLOAT_TYPE:
            case DOUBLE_TYPE:
                if (filter_var($sValue,FILTER_VALIDATE_FLOAT))
                {
                    return true;
                }
                break;
                
            case DATE_TYPE:
                //Remember this must be coming in form of YYYY-MM-DD
                $aDateTest = explode('-', $sValue);
                                
                if (checkdate($aDateTest[1], $aDateTest[2], $aDateTest[0]))
                {
                    return true;
                }
                break;
            case VAR_CHAR_TYPE:
                //Really anything could be a string
                return true;
        }
        
        return false; 
        
        
    }
    
    
    /**
     * Function fetchAssocResult
     * Purpose  This will fetch the complete result as an associated array
     *          if associated arrays - Remeber that we are only getting one row 
     *          back each time we call fetch_assoc
     */
    public function fetchAssocResult()
    {
        $obReturn = array();
        
        for ($i=0; $i<$this->obDB->affected_rows;$i++)
        {
            array_push($obReturn,$this->obResult->fetch_assoc());
        }
        
        //reset cursor to first position in result set
        $this->obResult->data_seek(0);
        
        return $obReturn;
    }
    
    /**
     * Function 
     * Purpose  This routine exists soley to figure out the various types that 
     *          we are working with when trying to do an insert. This routine
     *          requires that we run an empty Select call against the backend
     *          database to determine the field types
     * Parameter    sTableName
     */
    protected function getTableFieldInfo($sName)
    {
        $this->doQuery("SELECT * from $sName");
        
        //Procedural way
        //$this->aFieldObjects = mysqli_fetch_field($this->obResult);
        
        //object oriented way
        $this->aFieldObjects = $this->obResult->fetch_fields();
        
    }
    
    /**
     * Function getFieldType
     * Purpose  This will return the type that is associated with
     *          a particular field name or 0
     * It is assumed that the attribute aFieldObjects has the field objects 
     * for the appropriate table loaded
     * Parameters  sName - name if the field
     */
    protected function getFieldType($sName)
    {
        
        foreach ($this->aFieldObjects as $obElem) {
            if($obElem->name == $sName)
            {
                if($this->bDebug)
                {
                    echo "Looking at " . $sName . " and " . $obElem->name . "<br>";
                }
                
                return $obElem->type;
            }
        }
        
        //this should not happen - but you never know
        return 0;
    }

    /**
     * Function tableRep
     * Purpose  This routine will go through and return a string that can
     *          be output as a table on our page
     * Params   sOptions - Any additional display options
     * It is assumed that doQuery has been called before we get to this point
     */
    public function tableRep($sOptions="")
    {
        $sReturn = "<table $sOptions>\n";
        //quick debug statement 
        $aFields = $this->obResult->fetch_fields();
        $sReturn .= "<tr>\n";
        foreach($aFields as $obField)
        {
            $sReturn .= "<th>". $obField->name ."</th>\n"; 
        }
         $sReturn .= "</tr>\n";

         //get the complete result set
         $aResults = $this->fetchAssocResult();
         foreach ($aResults as $obRow) {
             $sReturn .= "<tr>\n";
             foreach($aFields as $obField)
             {
                 $sReturn.= "<td>".$obRow[$obField->name]."</td>\n";
             }
             $sReturn .= "</tr>\n";
         }
         $sReturn .= "</table>\n";
         echo $sReturn;
    }
    
    
    
    
    
    /**
     * Function getPrimeKey
     * Purpose  This will just return the primary key of the last successful
     * insert statement. This is a critical function for working with foreign
     * keys when an insert table will neccessitate another insert in a diff
     * table linked by foreign key
     */
    public function getPrimeKey()
    {
        if($this->bDebug)
        {
            echo "<br><br>Prime Key<br>";
            echo $this->obDB->insert_id;
        }
        return $this->obDB->insert_id;
    }
    

    
    
}
