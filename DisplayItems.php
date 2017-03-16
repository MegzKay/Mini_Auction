<?php
session_start();

include_once("include/cstDB.php");
include_once("include/utils.php");
include_once("include/PageDisplay.php");
//ITEM TYPES: Computers/Game Consoles,Furniture,Used Books


/**
 * Function: getItems(
 * Purpose: Checks to see if the username exists and is equal to username passed in
 *          If a valid user exist, it will return the auction member's id,
 *          otherwise will return a 0
 * @param 	$obDB - a reference to the database object
 * @param 	$type -  the type of item to get
 */
function getItems($obDB, $type)
{
    $obDB->doQuery("select * from auctionitems where type='$type'");
    return $obDB->fetchAssocResult();
}

/**
 * Function: getItems
 * Purpose: Display all items based on type
 * @param 	$aItems - an array of items to display
 */
function displayItems($aItems)
{
    $body = "";
    $itemCount = 1;
    for($i = 0; $i < count($aItems); $i++)
    {
        
        $item = $aItems[$i];
        
        if(isNowBetweenStartDateAndEndOfAuction($item["start_date"], $item["bidDuration"]))
        {

            if($i % 2 != 0)
            {
                $body .= "<div class='itemRow'>";
            }
            $body .= "<div class='item'>
                <table id='item' border='1'>
                    <tr>
                        <td colspan='2'>
                            ".getPhoto($item["itemID"])."
                        </td>
                        <td id='itemName' colspan='2'>".$item["title"]."</td>
                    </tr>
                    <tr>
                        <td colspan='4' id='itemDesc'>".$item["description"]."</td> 
                    </tr>
                    <tr>
                        <td colspan='2'><span id='currentBid'>Total Bid: $".$item["totalBid"]."</span></td>
                        <td colspan='2' id='btnBid'>
                            <button><a href='ItemBid.php?itemID=".$item["itemID"]."'>Go To Bid</a></button>
                        </td> 
                    </tr>
                </table>
            </div>";

            if($i == (count($aItems)-1) || $i % 2 == 0)
            {
                $body .= "</div>";
                $itemCount = 1;
            }
            else
            {
                $itemCount++;
            }
        }
        
    }
    
    if($body=="")
    {
        $body = "<p class='errorMsg'>No Current Auction Items to Display</p>";
    }
    return $body;
}
/**
 * Function: isNowBetweenStartDateAndEndOfAuction
 * Purpose:  For the auction don't list items that are past expiry
 * @param DATE - the start date of the auction
 * @param INT - Number of days
 * @return   Will return true if there is time left to bid on auction, 
 *          otherwise will return false
 */
function isNowBetweenStartDateAndEndOfAuction($start_date, $days)
{
    $now = strtotime("now");
    $daysInSeconds = strtotime("$days day 30 second", 0);
    $startDateInSecs = strtotime($start_date);
    
    $totalTime = $startDateInSecs + $daysInSeconds;
    $diffBetweenTotalAndNow = $totalTime - $now;
    $diffBetweenStartAndNow = $now -$startDateInSecs;


    //Check that there is time between start date + days
    //Check that the auction has started
    if($diffBetweenTotalAndNow >= 0 && $diffBetweenStartAndNow >= 0)
    {
        return true;
    }
    else 
    {
        return false;
    }
}
/**
 * Function: getItems
 * Purpose: If user goes to this page without selecting a type, 
			display error message and proper links
			This gets passed below into display()
 */
function errorWrongLink()
{
    $error = "<p class='errorMsg'>Please select an Auction Item type from "
            . "the menu above or from below links</p>";
    $links = "<ul class='itemLinks'>\n".
                  "<li><a href='DisplayItems.php?itemType=Computers/Game Consoles'>Computers/Game Consoles</a></li>\n".
                  "<li><a href='DisplayItems.php?itemType=Furniture'>Furniture</a></li>\n".
                  "<li><a href='DisplayItems.php?itemType=Used Books'>Used Books</a></li>\n".
                "</ul>";
    
    return $error . "<br>" . $links ;
}
/**
 * Function: display
 * Purpose: Display the page using display items
 * @param 	$body - the body of the page
 * @param 	$errorMsg - default is nothing, but if there is something passed in display error message
 */
function display($body,$errorMsg="")
{
    $obPage = new PageDisplay("Auction Items");
    $obPage->pageHead();
    $obPage->nav(isset($_SESSION["user"]));
    if($errorMsg == "")
    {
        $obPage->mainBody($body);
    }
    else
    {
        $obPage->mainBody($errorMsg);
    }
    echo $obPage->displayPage();
}

//display items if there was a selected type from GET
if(isset($_GET["itemType"]))
{
    $type = $_GET["itemType"];

    if($type == "Computers/Game Consoles" || $type == "Furniture" ||$type == "Used Books")
    {
        $obDB = new cstDB();
        $aItems = getItems($obDB, $type);
        $body = displayItems($aItems);
        display($body);
    }
    else
    {
        display("",errorWrongLink());
    }
    
    
}

else
{
    
    display("",errorWrongLink());
}