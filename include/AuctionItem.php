<?php

/**
 * Description of AuctionItem
 *
 * @author cst228
 */
class AuctionItem {
    protected $itemID;
    protected $ownerID;
    protected $title;
    protected $desc;
    protected $start;
    protected $duration;
    protected $minBidPrice;
    protected $type;
    protected $totalBid;
    
    public function __construct($itemID, $ownerID,$title,$desc,$start,
            $duration,$minBidPrice,$type,$totalBid) 
    {
        $this->itemID=$itemID;
        $this->ownerID=$ownerID;
        $this->title=$title;
        $this->desc=$desc;
        $this->start=$start;
        $this->duration=$duration;
        $this->minBidPrice=$minBidPrice;
        $this->type=$type;
        $this->totalBid=$totalBid;
    }
    public function getOwner()
    {
        return $this->ownerID;
    }
    private function retrievePhoto()
    {
        $image = "";
        if(file_exists("images/".$this->title.$this->ownerID.".jpg"))
        {
            $image= "<img src='images/".$this->title.$this->ownerID.".jpg' "
                    . "alt='$this->title'>";
        }
        else if(file_exists("images/".$this->title.$this->ownerID.".png"))
        {
            $image= "<img src='images/".$this->title.$this->ownerID.".png' "
                    . "alt='$this->title'>";
        }
        else
        {
            $image= "<img src='images/noImage.jpg' alt='$this->title'>";
        }
        return $image;
    }
    public function updateBid($bid)
    {
        $bidReturned = -1;
        if($bid>$this->minBidPrice)
        {
            $this->minBidPrice=$bid;
            $bidReturned = $this->minBidPrice;
        }
        return $bidReturned;
    }
    public function displayItemForShow()
    {
        $display = "<div class='item' >
            <table id='item' border='2'>
                <tr>
                    <td colspan='2'>
                        ".$this->retrievePhoto()."
                    </td>
                    <td id='itemName' colspan='2'>$this->title</td>
                </tr>
                <tr>
                    <td colspan='4' id='itemDesc'>$this->desc</td> 
                </tr>
                <tr>
                    <td colspan='2'><span id='currentBid'>
                        $".$this->totalBid."</span>
                    </td>
                    <td colspan='2' id='btnBid'>
                        <button><a href='AuctionItem.php'>
                            Go To Bid</a></button>
                    </td> 
                </tr>
            </table>
        </div>";
        
        
        return $display;
    }
    public function displayItemForBid()
    {
        $display = "<div id='bidItemContainer' class='col-sm-offset-4 col-sm-10'>
                <table id='bidItem' border='2'>
                    <tr>
                        <td colspan='2'>
                            ".$this->retrievePhoto()."
                        </td>
                        <td id='itemName' colspan='2'>$this->title</td>
                    </tr>
                    <tr>
                        <td colspan='4' id='itemDesc'>$this->desc</td> 
                    </tr>
                    <tr>
                        <td colspan='2'><span id='currentBid'>$".$this->totalBid."</span></td>
                    </tr>
                </table>
            </div>";
        return display();
    }
}
