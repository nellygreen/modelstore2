 </div><br/><br/> 
    
    <div class=" col-md-12 text-center">&copy; Copyright 20012-20016 Ingreen Decor</div>
    
    
    <script>
      jQuery(window).scroll(function(){
          var vscroll  =jQuery(this).scrollTop();
          jQuery('#logo').css({
              "transform" : "translate(0px, "+vscroll/2+"px)"
          });
      });
        
        
    function detailsmodal(id){
        var data = {"id" : id};
        jQuery.ajax({
            url : '/modelstore/includes/details_modal.php',
            method : "post",
            data : data,
            success: function(data){
                jQuery('body').append(data);
                jQuery('#details-modal').modal('toggle');
            },
            error: function(){
                alert("something went wrong!");
            }
        });
    }
    </script>
  </body>
</html>