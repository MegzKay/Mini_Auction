<?php
session_start();
include_once("include/cstDB.php");
include_once("include/PageDisplay.php");
include_once("include/utils.php");



if(isset($_GET["itemID"]))
{
    $obDB=new cstDB();
    $aItemInfo = getItemInfo($_GET["itemID"], $obDB);
    $itemInfo = displayItemForBid($aItemInfo);

    $obPageDisplay=new PageDisplay("Item Bid");
    $obPageDisplay->pageHead("<script src='scripts/UpdateBidAjax.js'></script>");
    $obPageDisplay->nav(isset($_SESSION["user"]));
    $obPageDisplay->mainBody($itemInfo);
    echo $obPageDisplay->displayPage();
}
else
{
    header("location:DisplayItems.php");
}

/**
 * Function displayItemForBid
 * Purpose  Display bidding information for the current item (based on $_GET["itemID"])
 * @param $aItemInfo - an associative array containing information about an item
 */
function displayItemForBid($aItemInfo)
{
    $itemID = $aItemInfo["itemID"];
    $title = $aItemInfo["title"];
    $ownerID = $aItemInfo["ownerID"];
    $totalBid = $aItemInfo["totalBid"];
    $desc = $aItemInfo["description"];
    $minBid = $aItemInfo["minBidPrice"];
    
    $display = "<div id='bidItemContainer' class='col-sm-offset-3 col-sm-12'>
                <table id='bidItem'>
                    <tr id='bidError'>
                        <td colspan='4'>Please place a bid at a minimum of $".$minBid."<td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            ".getPhoto($itemID)."
                        </td>
                        <td id='itemName' colspan='2'>
                            <h2>$title</h2>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4' id='itemDesc'>$desc</td> 
                    </tr>
                    <tr>
                        <td colspan='2'><span style='font-weight:bold'>Total Bid is: $<input type='text' size='5' id='currentBid' value='$totalBid' readonly style='border:none'></span></td>
                        <td>$<input id='bid' type='number' value='".$minBid."' min='".$minBid."' step='.01'/>
                            <input id='minBid' type='hidden' value='".$minBid."'/></td>
                            <input id='itemID' type='hidden' value='".$itemID."'/></td>
                        <td><input type='button' id='updateBid' ".isDisabled()." value='BID AMOUNT'/></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>
        </div>";
    return $display;

}
/**
 * Function getItemInfo
 * Purpose  Get information about an auction item from the database 
 * @param 	$id - id of the auction item
			$obDB - database object
 */
function getItemInfo($id, $obDB)
{
    $obDB->doQuery("select * from auctionitems where itemID=".$id." LIMIT 1");
    $aResult = $obDB->fetchAssocResult()[0];
    return $aResult;
    
}
/**
 * Function isDisabled
 * Purpose  Diisable updating the bid, if the current user owns the item, 
			or the person viewing this page is not logged in
 */
function isDisabled()
{
    $obDB = new cstDB();
    $obDB->doQuery('SELECT ownerID from AuctionItems where itemID='.$_GET["itemID"].' LIMIT 1');
    $result=$obDB->fetchAssocResult();
    
    
    if(!isset($_SESSION["user"]) )
    {
        return "disabled";
    }
    else if(count($result)!=0)
    {
        if($result[0]["ownerID"]==$_SESSION["user"])
        {
            return "disabled";
        }
    }
        
    return "";
}

