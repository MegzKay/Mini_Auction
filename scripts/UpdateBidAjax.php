<?php
include_once("../include/cstDB.php");
//include_once("../include/utils.inc");
$itemID = $_POST["itemID"];
$bid= $_POST["bid"];
$obDB = new cstDB();
$sSQL = "select * from auctionitems where itemID=".$itemID." LIMIT 1";
$obDB->doQuery($sSQL);
$result = $obDB->fetchAssocResult()[0];


if(count($result))
{
    $sSQLUpdate = "update auctionitems set totalBid=$bid+totalBid where itemID=".$itemID;
    $obDB->doQuery($sSQLUpdate);
    echo json_encode(array("error"=>"false"));
}
else
{
    echo json_encode(array("error"=>"true"));
}



