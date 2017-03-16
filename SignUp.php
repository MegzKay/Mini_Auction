<?php
session_start();



include_once("include/cstDB.php");
include_once("include/utils.php");
include_once("include/PageDisplay.php");


/**
 * Function makeForm
 * Purpose - To create a PageDisplayForm object and return it, 
 *          with possible error messages from aErrors and possible field values 
 *          from aValues. aValues and aErrors are to handle errors after submission
 * @param type $aInputs - Error Message Array
 * @param type $aValues - Array of the values of the form fields
 * @return - PageDisplayForm 
 */
function makeForm($aErrors, $aValues)
{
    $obBootForm = new PageDisplayForm("frmNewUser", "", "POST");
    $obBootForm->addBasicFormControl("First Name", "txtFirst", "text", 
            "required='required' maxlength='20'", 
            $aErrors["txtFirst"],$aValues["txtFirst"]);
    $obBootForm->addBasicFormControl("Last Name", "txtLast","text",
            "required='required' maxlength='20'", 
            $aErrors["txtLast"],$aValues["txtLast"]);
    $obBootForm->addBasicFormControl("Phone Number", "txtPhone","text",
            "required='required' placeholder='NNN-NNN-NNNN' "
            , $aErrors["txtPhone"],$aValues["txtPhone"]);
    $obBootForm->addBasicFormControl("Email","txtEmail","email",
            "required='required' placeholder='something@something.com'", 
            $aErrors["txtEmail"],$aValues["txtEmail"]);
    $obBootForm->addBasicFormControl("Enter a Username", "txtUser","text",
            "required='required'", 
            $aErrors["txtUser"],$aValues["txtUser"]);
    $obBootForm->addBasicFormControl("Enter a Password", "txtPass1",
            "password","required='required'", 
            $aErrors["txtPass1"],$aValues["txtPass1"]);
    $obBootForm->addBasicFormControl("Enter a Password", "txtPass2",
            "password","required='required'", 
            $aErrors["txtPass2"],$aValues["txtPass2"]);
    $form = $obBootForm->finishForm("Enter New User", "submitUser");
    
    return $form;
}

/**
 * Function display
 * Purpose  Uses a PageDisplay object to create a display on the web page 
 * @param type $form - a PageDisplayForm 
 */
function display($form, $msg="")
{ 
    $obPage = new PageDisplay("User Sign Up");
    $obPage->pageHead("<script src='scripts/newUserValidation.js'></script>"
            ."<script src='http://localhost/Files/jquery/jquery.validate.min.js'></script>");
    $obPage->nav(isset($_SESSION["user"]));
    if($msg != "")
    {
         $obPage->mainBody($msg.$form);
    }else{
         $obPage->mainBody($form);
    }
   
    echo $obPage->displayPage();
}

/**
 * Function enterNewUser
 * Purpose  Enters a new User into the AuctionMember and AuctionUser databases
 * @param type $obDB - database objecy
 * @param type $fname - first name of user
 * @param type $lname - last name of user
 * @param type $phone - phone number of user
 * @param type $email - email of user
 * @param type $username - username of user
 * @param type $password - password of user
 */
function enterNewUser($obDB, $fname, $lname, $phone, $email, 
        $username, $password)
{
    //Insert the new member
    $aAuctionMember = array(
        "firstName"=>$fname,
        "lastName"=>$lname,
        "email"=>$email,
        "phoneNum"=>$phone
    );
    $obDB->doInsert($aAuctionMember, "AuctionMember");
    
    //get the id from AuctionMember table, which will be used as a foreign 
    //key in the AuctionUser table so they can be linked together
    $id=$obDB->getPrimeKey();
    //Generate the key
    $genKey = genKey($fname, $lname, $phone);
    
    //Insert the new user
    $sqlUserInsert = "INSERT INTO AuctionUser(username, password, aucMemID) "
            . "values('$username', AES_ENCRYPT('$password','$genKey'), $id)";
    $obDB->doQuery($sqlUserInsert);
    
    //sign user in
    //$_SESSION["user"] = $id;
}


//holds error messages
$aErrors=array(
        "txtFirst"=>"",
        "txtLast"=>"",
        "txtPhone"=>"",
        "txtEmail"=>"",
        "txtUser"=>"",
        "txtPass1"=>"",
        "txtPass2"=>""
    );
//holds values of fields
$aValues=array(
        "txtFirst"=>"",
        "txtLast"=>"",
        "txtPhone"=>"",
        "txtEmail"=>"",
        "txtUser"=>"",
        "txtPass1"=>"",
        "txtPass2"=>""
    );

if(!isset($_POST["submitUser"]))
{
    //make the form and display it
    $form = makeForm($aErrors, $aValues);
    display($form);
    
    
}
else
{
    //Uses utils.inc methods to check validity of entered data
    $aErrors["txtFirst"]= fNameLength($_POST["txtFirst"]);
    $aErrors["txtLast"]=lNameLength($_POST["txtLast"]);
    $aErrors["txtPhone"]=correctPhoneNum($_POST["txtPhone"]);
    $aErrors["txtEmail"]=correctEmail($_POST["txtEmail"]);
    $aErrors["txtUser"]= userDoesNotExist("txtUser");
    $aErrors["txtPass1"]=passwordCorrectLength($_POST["txtPass1"]);
    $aErrors["txtPass2"]=passwordsMatch($_POST["txtPass1"], 
                                        $_POST["txtPass2"]);

    
    //if all fields in $aErrors are blank that means all data is correct
    //so call enterNewUser to enter information into database, 
    //after sanitizing inputs
    if($aErrors["txtFirst"]=="" && $aErrors["txtLast"]=="" && 
            $aErrors["txtPhone"]=="" && $aErrors["txtEmail"]==""&&
            $aErrors["txtUser"]=="" && $aErrors["txtPass1"]==""&&
            $aErrors["txtPass2"]=="")
    {
        $fname = sanitize("txtFirst");
        $lname = sanitize("txtLast");
        $phone = sanitize("txtPhone");
        $email = sanitize("txtEmail");
        $username = sanitize("txtUser");
        $password = sanitize("txtPass1");
        
        $obDB = new cstDB();

        enterNewUser($obDB, $fname, $lname, $phone, $email, $username, $password);
        $msg = "<p class='successMsg'>User Successfully Created!</p>";
	$msg .= "<p class='successMsg'><a href='Login.php'>Click here to sign in</a></p>";
    }
    //Otherwise put the values of fields into aValues so when page refreshes,
    //it will show what the user has entered previously
    else
    {
        $aValues["txtFirst"]=$_POST["txtFirst"];
        $aValues["txtLast"]=$_POST["txtLast"];
        $aValues["txtPhone"]=$_POST["txtPhone"];
        $aValues["txtEmail"]=$_POST["txtEmail"];
        $aValues["txtUser"]=$_POST["txtUser"];
        $aValues["txtPass1"]=$_POST["txtPass1"];
        $aValues["txtPass2"]=$_POST["txtPass2"];
        
        $msg = "<p class='errorMsg'>Please fix the errors and resubmit.</p>";
    }
    
    //make the form and display it
    $form = makeForm($aErrors, $aValues);
    display($form, $msg);
}

