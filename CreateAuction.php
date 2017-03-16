<?php
session_start();

if(!isset($_SESSION["user"]))
{
    header("location:Login.php?AccessDenied=1");
}

include_once("include/cstDB.php");
include_once("include/utils.php");
include_once("include/PageDisplay.php");

/*
Function	getTypesAForSelect
Purpose		Gets types of items for the auction from ItemTypes table
Params		$obDB - database object
*/
function getTypesAForSelect($obDB)
{
    $aTypes=array();
    $sqlGetTypes = "select type from ItemTypes";
    $obDB->doQuery($sqlGetTypes);
    $dbTypes = $obDB->fetchAssocResult();
    foreach ($dbTypes as $row) {
        array_push($aTypes, array("Value"=>$row["type"],"Name"=>$row["type"]));
    }
    return $aTypes;
}
/*
Function	display
Purpose		Echos out a display
Params		$aTypes - values and display names for select boxes
			$msg - msg to display. Default is none
*/
function display($aTypes, $msg="")
{
    $today = strtotime("now");
    $todayDate = date("Y-m-d", $today);

    $obBootForm = new PageDisplayForm("frm","CreateAuction.php","POST", "enctype='multipart/form-data'");
    $obBootForm->addBasicFormControl("Title", "txtTitle", "text", 
            "required='required'");
    $obBootForm->addTextArea("Description", "txtDesc", 4, 64, 
            "required='required' maxlength='150'");
    $obBootForm->addBasicFormControl("Start Date", "txtDate", "date", 
            "required='required' value='".$todayDate."'");
    $obBootForm->addBasicFormControl("Bid Duration", "txtDuration", "number",
            "required='required' value='1' min='1' max='14'");
    $obBootForm->addBasicFormControl("Minimum Bid Price", "txtMinBid", 
            "number", "required='required' value='1' min='1' max='100'");
    $obBootForm->addSelectBox("Type", "txtType", $aTypes);
	$obBootForm->addBasicFormControl("Image", "auctionImg", "file");
    $form = $obBootForm->finishForm("Submit New Auction", "submitAuction");

    $obPage = new PageDisplay("Create a New Auction");
    $obPage->pageHead();
    $obPage->nav(isset($_SESSION["user"]));
    $obPage->mainBody("$msg".$form);

    echo $obPage->displayPage();
}
/*
Function	saveAuctionItem
Purpose		Saves an auction item in the database
Params		$obDB - database object
			$ownerid - id of owner, who made the item
			$title -  title of auction item
			$desc - description of auction item
			$start - the start date of the auction
			$duration - days of the auction 
			$bidPrice - the minimum bid 
			$type - the type of item
*/
function saveAuctionItem($obDB, $ownerid, $title, $desc, $start, $duration, 
        $bidPrice, $type)
{   
    $startTime = strtotime($start);
    $date = date("F d, o",$startTime);
    
    $sqlInsertItem = "insert into AuctionItems (ownerID, title, description,"
            . " start_date, bidDuration, minBidPrice, type, totalBid) "
        . "values ($ownerid, '$title', '$desc', str_to_date('$date','%M %d,%Y'), "
            . "$duration, $bidPrice, '$type', 0)";
    
    $obDB->doQuery($sqlInsertItem);
    
    return $obDB->getPrimeKey();
    
}

/*
Function	checkImage
Purpose		Checks the image if its suitable for upload
Params		$file - reference to the file
			$fileName - the name to give to the file
Return		$flag - 1 represents good file
					2 represents error of file size larger than allowed
					3 represents error of the file extension/type not being supported
*/
function checkImage($file)
{
    $flag = 1;
	
    $name = $_FILES[$file]['name'];
    $type = $_FILES[$file]['type'];
    $size = $_FILES[$file]['size'];
    $max_size = 2097152;
    $extension = strtolower(substr($name, strpos($name,'.')+1));

	
    if( ($extension=='jpg' ||$extension=='png')&& 
                ($type == 'image/jpeg' || $type == 'image/png')
                && $size <= $max_size)
    {
        $flag = 1;
    }
    else 
    {
        if($size > $max_size)
        {
            $flag = 2;
        }
        else if( !(($extension=='jpg' ||$extension=='png')) && 
                !($type == 'image/jpeg' || $type == 'image/png'))
        {
            $flag = 3;
        }
    }
	
    return $flag;
}


$obDB=new cstDB();

$aTypes = getTypesAForSelect($obDB);

if(!isset($_POST["submitAuction"]))
{
	//on initial load, display normal page
    display($aTypes);
}
else
{
    //who is ever logged in is the owner of the new item
    $ownerID = $_SESSION['user'];
    //sanitize input
    $title=sanitize("txtTitle"); 
    $desc=sanitize("txtDesc"); 
    $start=sanitize("txtDate"); 
    $duration=sanitize("txtDuration"); 
    $bidPrice=sanitize("txtMinBid"); 
    $type=sanitize("txtType"); 

    $msgDisplay = "";
    $pk = saveAuctionItem($obDB, $ownerID, $title, $desc, $start, $duration, $bidPrice, $type);
    $msgDisplay = "<p class='successMsg'>You have created an"
                    . " auction item</p>";
    
    $imgUploaded = 0;

    if(is_uploaded_file($_FILES['auctionImg']['tmp_name'])) 
    {
	//tries to upload image, will return 1 for success, 2 for invalid image size, 3 for invalid extension
	$imgUploaded = checkImage('auctionImg');
        if($imgUploaded == 1)
        {
            $tmp_name = $_FILES['auctionImg']['tmp_name'];
            $name = $_FILES['auctionImg']['name'];
            $extension = strtolower(substr($name, strpos($name,'.')));
            $nameNoExt = substr($name, 0, strpos($name,'.'));
            $fileLocation = "images/".$nameNoExt.$pk.$extension;
            $obDB->doQuery("UPDATE AuctionItems SET fileLocation='$fileLocation' WHERE itemID=$pk");
            move_uploaded_file($tmp_name, $fileLocation);
             

        }
        else if($imgUploaded == 2)
        {
            $msgDisplay = "<p class='errorMsg'>Please upload a smaller image</p>";

        }
        else if($imgUploaded == 3)
        {
            $msgDisplay = "<p class='errorMsg'>Please upload a .jpg or .png file</p>";

        }
        else if($imgUploaded == 0)
        {
            $msgDisplay = "<p class='errorMsg'>Something went wrong with file upload. Please try again</p>";
        }
        
    } 


   

    display($aTypes, $msgDisplay);
}