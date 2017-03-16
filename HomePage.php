<?php
session_start();

include_once("include/PageDisplay.php");

/**
 * Function display
 * Purpose  Display the home page
 */
function display()
{
    $obPage = new PageDisplay("CST Auction");
    $obPage->pageHead();
    $obPage->nav(isset($_SESSION["user"]));

    $obPage->mainBody("<div id='homePagePic' "
            . "class='col-lg-offset-3 col-lg-12 "
            ."col-md-offset-3 col-md-12 "
            ."col-xs-offset-1 col-xs-12 "
            . "col-sm-offset-1 col-sm-12' >"
            . "<img src='images/homepg.jpg' alt='home page image'/></div>");
    echo $obPage->displayPage();
}
display();