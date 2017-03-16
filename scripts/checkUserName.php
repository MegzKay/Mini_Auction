<?php

include_once("../include/cstDB.php");

if (!empty($_POST['txtUser']))
{
    $usernameDoesNotExist = true;
    
    $username = filter_input(INPUT_POST, 'txtUser', FILTER_SANITIZE_MAGIC_QUOTES);
    $obDB = new cstDB();
    $obDB->doQuery("select username from auctionuser where username='$username'");
    $result = $obDB->fetchAssocResult();
    if(count($result) > 0)
    {
        if($result[0]["username"] == $username)
        {
            echo 'false';
        }
        else
        {
            echo 'true';
        }
    }
    else
    {
        echo 'true';
    }
}
else
{
    echo 'true';
}

