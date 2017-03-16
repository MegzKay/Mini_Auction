<?php
/*
 * This file will contain utility functions that are one-ffs (stand-alone) that
 * assist us in various projects
 */

include_once("include/cstDB.php");

define("EMAIL_PATTERN",'/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/');
define("PHONE_NUMBER",'/^(\d{3}?)[\s|-]\d{3}[\s|-]\d{4}$/');

//In PHP since boolean only returns 1 or 0, use those numbers to index
//into this assocative to get True or False
$booleanString = array(
    0=>"False",
    1 => "True"
);

/*
 * Function sanitize
 * Purpose  This routine will be used to sanitize a text string that is being 
 *          passed in - essentiall if quotes have not been escaped in the string 
 *          thay is being passed in - they will be escaped by this function
 * Params   sTarget
 */
function sanitize($sTarget)
{
    $sResult = filter_input(INPUT_POST, $sTarget, FILTER_SANITIZE_MAGIC_QUOTES);
    
    return trim($sResult);
}


 /**
 * Function getKey
 * Purpose  This routine will be responsible for generating the key
 * we will be using for encrypting our password fields using the AES function in mysql
  * Our generated key will be combo of first and last letters of first and last name
  * and 2nd digit of phone number
  * 
  * params  sFirst,sLast, sPhone
  * 
  * for Rob Miller 919-1212, the key would be RbMr1
 */
    
function genKey($sFirst, $sLast, $sContact)
{
    $sF = substr($sFirst, 0, 1) . substr($sFirst,-1,1);
    $sL = substr($sLast, 0, 1) . substr($sLast, -1,1);
    $sC = substr($sContact, 1,1);
        
    return $sF.$sL.$sC;
}
function userDoesNotExist($sUser)
{
    $userDoesNotExist = "";
    $username = sanitize($sUser);
    $obDB = new cstDB();
    $obDB->doQuery("select username from AuctionUser where username='$username' limit 1");
    $result = $obDB->fetchAssocResult();
    if(count($result) > 0)
    {
        if($result[0]["username"] == $username)
        {
            $userDoesNotExist = "Username already exists";
        }
    }
    
    return $userDoesNotExist;
}
//NEW - updated to save location in database
function getPhoto($pk)
{
	$class = "class='itemPic'";
	$obDB = new cstDB();
	$obDB->doQuery("select fileLocation from AuctionItems where itemID=".$pk." limit 1");
	$location = $obDB->fetchAssocResult()[0]["fileLocation"];
	$image = "<img src='images/noImage.jpg' alt='no image' $class>";
	
	if(file_exists($location))
    {
        $image = "<img src='$location' alt='$location' $class>";
    }

	return $image;
}
//OLD
function retrievePhoto($ownerID,$title)
{
    $class = "class='itemPic'";
    $image = "";
    if(file_exists("images/".$ownerID.$title.".jpg"))
    {
        $image= "<img src='images/".$ownerID.$title.".jpg' "
                . "alt='$title' $class>";
    }
    else if(file_exists("images/".$ownerID.$title.$itemID.".png"))
    {
        $image= "<img src='images/".$ownerID.$title.".png' "
                . "alt='$title' $class>";
    }
    else
    {
        $image= "<img src='images/noImage.jpg' alt='no image' $class>";
    }
    return $image;
}
function fNameLength($sFName)
{
    if(strlen($sFName) <= 20)
    {
        return "";
    }
    else
    {
        return "First Name has to be less than 20 characters";
    }
}
function lNameLength($sLName)
{
    if(strlen($sLName) <= 20)
    {
        return "";
    }
    else
    {
        return "Last Name has to be less than 20 characters";
    }
}
function correctPhoneNum($sNumber)
{
    if( preg_match(PHONE_NUMBER, $sNumber))
    {
        return "";
    }
    else
    {
        return "Phone number must be in format 111-111-1111";
    }
}
function correctEmail($sEmail)
{
    if(preg_match(EMAIL_PATTERN, $sEmail))
    {
        return "";
    }
    else
    {
        return "Email must be in format something@something.com";
    }
}
function passwordsMatch($sPass1, $sPass2)
{
    if($sPass1 == $sPass2)
    {
        return "";
    }
    else
    {
        return "Passwords do not match";
    }
}
function passwordCorrectLength($sPass1)
{
    if(strlen($sPass1)>=6)
    {
        return "";
    }
    else
    {
        return "Password must be 6 characters or more";
    }
}

