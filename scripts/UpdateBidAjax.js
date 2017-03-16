$(document).ready(function(){
    
    
    
   $('#updateBid').click(function(){
       
       
    var bid = $("#bid").val();
    var minBid = $("#minBid").val();
    var itemID = $("#itemID").val();
    var currentBid = $("#currentBid").val(); 

    if(parseFloat(bid) >= parseFloat(minBid))
    {
        $.ajax({
        url: "scripts/UpdateBidAjax.php",
        method: "POST",
        data:{
            itemID:itemID,
            bid:bid
        },
        success:function(JSONData){
            var data = JSON.parse(JSONData);

            if(data.error==="false")
            {
                var newBid = parseFloat(bid) + parseFloat(currentBid);
                $("#currentBid").val(newBid.toFixed(2));
                $('#bidError').hide();
            }
            else
            {
                $('#bidError').show();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            alert("Status: " + textStatus + " Error: " + errorThrown);
        }   
        });

    }
    else
    {
        $('#bidError').show();
    }
    
      

   });
});
