<?php


/**
 * PageDisplay
 * Uses bootstap to make a page, which it then returns so it can be displayed
 */
class PageDisplay {
    protected $sBody;
    protected $sTitle;
    
    /**
     * Construc for PageDisplay class. Begins the body and takes in a title
     * @param type $sPageTitle - The title of the page
     */
    public function __construct($sPageTitle) {
        $this->sBody="<!DOCTYPE html>\n<html>\n";
        $this->sTitle=$sPageTitle;
    }
    
    /**
     * Function nav
     * Purpose  Makes the navigation for the page. For simplicity of this 
     *          assignment,since every page will have the same navigation, 
     *          pages are hardcoded in
     * @param type $loggedIn - whether the user is logged in or not, 
     *              this is so the correct text and link can be used
     *              for the logout/login link
     */
    public function nav($loggedIn=false)
    {
          $nav="<body>\n<nav class='navbar navbar-inverse'>\n".
            "<div class='container-fluid'>\n".
              "<div class='navbar-header'>\n".
                "<button type='button' class='navbar-toggle' data-toggle="
                        . "'collapse' data-target='#myNavbar'>\n".
                  "<span class='icon-bar'></span>\n".
                  "<span class='icon-bar'></span>\n".
                  "<span class='icon-bar'></span>\n".
                "</button>\n".
                "<a class='navbar-brand' href='HomePage.php'>CST Auction"
                  . "</a>\n</div>\n".
              "<div class='collapse navbar-collapse' id='myNavbar'>\n".
                "<ul class='nav navbar-nav'>\n".
                    "<li><a href='HomePage.php'>Home</a></li>".
                    "<li><a href='CreateAuction.php'>Create Auction</a></li>".
                    "<li class='dropdown'><a class='dropdown-toggle' "
                            . "data-toggle='dropdown' href='#'>Auction Items"
                            . "<span class='caret'></span></a>\n".
                        "<ul class='dropdown-menu'>\n".
                          "<li><a href='DisplayItems.php?itemType=Computers"
                                . "/Game Consoles'>Computers/Game "
                                . "Consoles</a></li>\n".
                          "<li><a href='DisplayItems.php?itemType=Furniture'>"
                                . "Furniture</a></li>\n".
                          "<li><a href='DisplayItems.php?itemType="
                                . "Used Books'>Used Books</a></li>\n".
                        "</ul>\n".
                      "</li>\n".
                "</ul>\n".
                "<ul class='nav navbar-nav navbar-right'>\n".
                    "<li><a href='SignUp.php'><span class='glyphicon "
                  . "glyphicon-user'></span> Sign Up</a></li>\n";
                    if($loggedIn)
                    {
                        $nav.="<li><a href='Logout.php'><span "
                                . "class='glyphicon glyphicon-log-in'>"
                                . "</span> Logout</a></li>\n";
                    }
                    else 
                    {
                        $nav.="<li><a href='Login.php'><span "
                                . "class='glyphicon glyphicon-log-in'>"
                                . "</span> Login</a></li>\n";
                    }
                    
                    
                $nav.="</ul>\n".
              "</div>\n".
            "</div>\n".
          "</nav>\n";
                    
        $this->sBody.=$nav;
    }
    /**
     * Function pageHead
     * Purpos   Makes the head section for the html page
     * @param type $extraLinks - can allow extra links for the html page
     *                          to use
     */
    public function pageHead($extraLinks="")
    {
         $head =  "<head>\n";
         $head .= "<meta name='viewport' content='width=device-width, "
                 . "initial-scale=1.0'>\n"
            . "<title>$this->sTitle</title>\n"
            . "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/"
                 . "bootstrap/3.3.7/css/bootstrap.min.css'>\n"
            . "<script src='https://ajax.googleapis.com/ajax/libs/jquery/"
                 . "3.1.1/jquery.min.js'></script>\n"
            . "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/"
                 . "js/bootstrap.min.js'></script>\n"
            . "<link rel='stylesheet' href='css/main.css' "
                 . "type='text/css'/>\n";
        if($extraLinks != "")
        {
            $head .= $extraLinks."\n";
        }
        $head .= "</head>\n";
         $this->sBody .= $head;

    }
    
    /**
     * Function mainBody
     * Purpose  sets up the body of the html page
     * @param type $sMainBody - what user wants in the main body. This will
     *                          be inserted after the navigation
     */
    public function mainBody($sMainBody="")
    {
        $this->sBody .= "<div class='container'>\n"
                        . "<h1>".$this->sTitle."</h1>\n"
                        .$sMainBody
                    . "</div>\n"
                . "</body>\n</html>\n";
    }
    
    /**
     * Function displayPage
     * Purpose  returns the page to be displayed. Need to use echo to display
     * @return PageDisplay
     */
    public function displayPage()
    {
        return $this->sBody;
    }
}
class PageDisplayForm
{
    protected $formBody;
    
    public function __construct($sName,$sAction,$sMethod="POST",$sOptions="") 
    {
        $this->formBody = "\n<form name='$sName' id='$sName' "
                . "action='$sAction' Method='$sMethod' "
                . "class='form-horizontal' $sOptions>\n";
    }
    /**
     * Function addBasicFormControl
     * Purpose  Adds a bootstap form label and field to the form
     * @param type $sLabel - the label you want displayed
     * @param type $sName - used for both name and id of form field
     * @param type $type - can be almost all input types except for
     *                      submit/reset, and select boxes
     * @param type $sOptions - extra options for an input
     * @param type $errorMsg -  error message for the field
     * @param type $value - the value for the field
     * 
     * NOTE: errorMsg and value are used for if there are errors in the field,
     * the value is there so that the value can be displayed, so user can see
     * what they entered 
     */
    public function addBasicFormControl($sLabel, $sName, $type, 
            $sOptions="", $errorMsg="", $value="")
    {
        $formControl= "<div class='form-group'>"
            ."<label class='col-sm-offset-1 control-label col-sm-2'>"
                . "$sLabel</label>\n"
            ."<div class='col-sm-6'>\n"
            ."    <input type='$type' class='form-control' id='$sName' "
                . "name='$sName' $sOptions value='$value'>\n";
        if($errorMsg!="")
        {
            $formControl.="<span class='error'>$errorMsg</span>\n";
        }
        
        $formControl.= "</div>\n"
        ."</div>\n";
        
        $this->formBody .= $formControl;
    }
    /**
     * Function addSelectBox
     * Purpose  Adds select box to form
     * @param type $sLabel - the label of the select box
     * @param type $sName - used for name and id of select box
     * @param type $aOptions - associative array of options, Name is
     *                         what is displayed, Value is the actual
     *                          value of the option
     */
    public function addSelectBox($sLabel, $sName, $aOptions)
    {
        $formControl= "<div class='form-group'>"
            ."<label class='col-sm-offset-1 control-label col-sm-2'>"
                . "$sLabel</label>\n"
            ."<div class='col-sm-6'>\n"
                . "<select class='form-control' id='$sName' name='$sName'>";
        foreach ($aOptions as $option) {
            $formControl.="<option value='".$option['Value']."'>".
                    $option['Name']."</option>";
        }
        
        $formControl.= "</select>"
            . "</div>\n"
        ."</div>\n";
        
        $this->formBody .= $formControl;
    }
    /**
     * Function addTextArea
     * Purpose  Adds a text area to the form
     * @param type $sLabel - the label of the text area
     * @param type $sName - used for name and if of text area
     * @param type $rows - number of rows of the textarea
     * @param type $cols - number of columns of the text area
     * @param type $sOptions - extra options for the text area
     */
    public function addTextArea($sLabel, $sName, $rows=5, $cols=50, 
            $sOptions="")
    {
        $formControl= "<div class='form-group'>"
                . "<label class='col-sm-offset-1 control-label col-sm-2'>"
                . "$sLabel</label>"
                . "<textarea class='col-sm-offset-1' cols='$cols' rows=$rows "
                . "id='$sName' name='$sName' $sOptions></textarea>"
                . "</div>";
        
        $this->formBody .= $formControl;
    }
    
    /**
     * Function finishForm  
     * Purpose  Returns a completed form which for example can be a 
     *          parameter for the mainBody function in PageDisplay or
     *          be displayed by iteself
     * @param type $sSubmitText - the text for submit button
     * @param type $name - name and id of submit button
     * @param type $sResetText -  the text for the reset button
     * @return a PageDisplayForm
     */
    public function finishForm($sSubmitText = "Submit Form", $name="submit", 
            $sResetText = "Clear Form")
    {
        $this->formBody .= "<div class='form-group'>\n"
            ."<div class='col-sm-offset-3 col-sm-10'>\n"
                  ."<input type='submit' class='btn btn-default' "
                . "name='$name' id='$name' value='$sSubmitText'>\n"
                ."<input type='reset' class='btn btn-default' "
                . "value='$sResetText'>\n"
            ."</div>\n"
        ."</div>\n</form>\n";
        
        return $this->formBody;
    }
}