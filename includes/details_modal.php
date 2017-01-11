<?php

require_once '../core/init.php';
$id = $_POST['id'];
$id = (int)$id;
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);
$brand_id = $product['brand'];
$sql = "SELECT brand FROM brand WHERE id = '$brand_id'";
$brand_query = $db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);

?>

<?php ob_start(); ?>
    <div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
           <div class="modal-header">
          <button class="close" type="button" onclick="closeModal()" aria-label="close">
             <span aria-hidden="true">&times;</span>
          </button>
            <h4 class="modal-title text-center"><?= $product['title']; ?></h4>
        </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                 <div class="col-sm-6">
                   <div class="center-block">
                     <img src="<?= $product['image']; ?>" alt="<?= $product['title'] ?>" class="details img-responsive" />
                       
                   </div> 
                 </div>
                 <div class="col-sm-6">
                 <h4>Details</h4>
                 <p><?= $product['description']; ?></p>
                 <hr>
                 <p>Price: $<?= $product['price']; ?></p>
                 <p>Brand: <?=  $brand['brand']; ?></p>
                 <form action="add_cart.php" method="post">
                    <div class="form-group">
                      <div class="col-xs-3">
                        <label for="quantity"></label>
                      </div>
                    </div>    
                 </form>
                 </div>
              </div>  
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" onclick="closeModal()">Close</button>
            <button class="btn btn-warning" type="submit"><span class="glyphicon glyphicon-shopping-cart"></span>Add to cart</button>
          </div>
        </div>
      </div>
    </div>
<script>
  function closeModal() {
      jQuery('#details-modal').modal('hide');
      setTimeout(function(){
          jQuery('#details-modal').remove();
          jQuery('modal-backdrop').remove();
      },500);
  }
</script>
<?= ob_get_clean(); ?>