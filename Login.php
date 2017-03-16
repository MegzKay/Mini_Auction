<?php
session_start();


include_once("include/cstDB.php");
include_once("include/utils.php");
include_once("include/PageDisplay.php");
/**
 * Function: checkUserName
 * Purpose: Checks to see if the username exists and is equal to username passed in
 *          If a valid user exist, it will return the auction member's id,
 *          otherwise will return a 0
 * @param type $obDB - a reference to the database object
 * @param type $username -  the username entered into the login form
 */
function checkUserName($obDB, $username)
{
    $validUser = 0;
    $sSQL="select aucMemID, username, password from AuctionUser where username='".$username."'";
    $obDB->doQuery($sSQL);
    $sqlResult = $obDB->fetchAssocResult();
    if(count($sqlResult)!=0)
    {
        if($sqlResult[0]["username"]==$username)
        {
            $validUser =  $sqlResult[0]["aucMemID"];
        }
    }
    
    return $validUser;
}
/**
 * Function: checkPassword
 * Purpose: If the username exists, this method will be called. It will check
 *          to see if the password entered into the form is what exists for
 *          the password for that username in the database
 * @param type $obDB - a reference to the database object
 * @param type $password -  the password entered into the login form
 * @param type $id - the member id
 */
function checkPassword($obDB,$password, $id)
{
    $validPassword = false;
    $sSQLCustomer = "select firstName, lastName, phoneNum from AuctionMember where aucMemID=" . $id;
    $obDB->doQuery($sSQLCustomer);
    $sqlResultCustomer = $obDB->fetchAssocResult();
    
    $fname = $sqlResultCustomer[0]["firstName"];
    $lname = $sqlResultCustomer[0]["lastName"];
    $phone = $sqlResultCustomer[0]["phoneNum"];
    $genKey = genKey($fname, $lname, $phone);
    

    $sSQLPassword="SELECT cast(aes_decrypt(password,'$genKey')as Char(50)) as "
            . "dePass FROM AuctionUser where aucMemID=" . $id;
    $obDB->doQuery($sSQLPassword);
    $sqlResultPassword = $obDB->fetchAssocResult();
    if(count($sqlResultPassword)!=0)
    {
        if(trim($sqlResultPassword[0]["dePass"])==trim($password))
        {
            $validPassword =  true;
        }
    }
    
    return $validPassword;
}
/**
 * Function: display
 * Purpose: To display the form. If the flag is false there was a login error 
 *          so display a message,otherwise just display the username 
 *          and password fields
 * @param type $flag - if false that means there was a login error 
 *                      so display an error message
 */
function display($error="")
{
    $obBootForm = new PageDisplayForm("frmLogin", "Login.php", "POST");
    $obBootForm->addBasicFormControl("Username", "txtUsername", "text", 
            "required='required'");
    $obBootForm->addBasicFormControl("Password", "txtPassword", "password", 
            "required='required'");
    $form = $obBootForm->finishForm("Login");
    
    
    $obPage = new PageDisplay("Login Page");
    $obPage->pageHead();
    $obPage->nav(isset($_SESSION["user"]));
    
    $body = "";
    if($error!="")
    {
        $body .= $error."\n";
    }
    $body .= $form;
    $obPage->mainBody($body);
    echo $obPage->displayPage();

}

if(!isset($_POST["txtUsername"]) && !isset($_POST["txtPassword"]))
{
    $error="";
    if(isset($_GET["AccessDenied"]))
    {
        $error = "<p class='errorMsg'>You need to Login to "
                . "create an auction</p>";
    }
    display($error);
}
else if(isset($_POST["txtUsername"]) && isset($_POST["txtPassword"]))
{
    $obDB =  new cstDB();
    //$obDB->setDebug(true);
    $username = sanitize("txtUsername");
    $password = sanitize("txtPassword");

    $id = checkUserName($obDB, $username);
    $validPassword = false;
    if($id != 0)
    {
        $validPassword = checkPassword($obDB,$password, $id);
        if($validPassword)
        {
            $_SESSION["user"] = $id;
            header("location:HomePage.php");
        }
    }
    if($id==0 || !$validPassword)
    {
        display("<p class='errorMsg'>Incorrect Username or Password</p>");
    }
}
?>
