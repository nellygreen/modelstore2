<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/modelstore/core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';

if (isset($_GET['add'])){
 $brandQuery = $db->query("SELECT * FROM brand");  
 $parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
 $sizesArray = array();
 if ($_POST) {
     $title = sanitize($_POST['title']);
     $brand = sanitize($_POST['brand']);
     $category = sanitize($_POST['parent']);
     $price = sanitize($_POST['price']);
     $list_price = sanitize($_POST['list_price']);
     $sizes = sanitize($_POST['sizes']);
     $description = sanitize($_POST['description']);
     $dbpath = '';
     $errors = array();
     
     if (!empty($_POST['sizes'])) {
         $sizeString = sanitize($_POST['sizes']);
         $sizeString = rtrim($sizeString,',');
         $sizesArray = explode(',',$sizeString);
         $sArray = array();
         foreach($sizesArray as $ss){
            $s = explode(':', $ss);
            $sArray[] = $s[0];
         }
     }else{
     $sizesArray = array();}
     $required = array('title', 'brand', 'parent', 'child');
     foreach($required as $field){
         if($_POST[$field] == ''){
             $errors[] = 'All Fields With * Are Required.';
             break;  
         }
     }
     if (!empty($_FILES)) {
         var_dump($_FILES);
         $photo = $_FILES['photo'];
         $name = $photo['name'];
         $nameArray = explode('.',$name);
         $fileName = $nameArray[0];
         $fileExt = $nameArray[0];
         $mime = explode('/',$photo['type']);
         $mimeType = $mime[0];
         $mimeExt = $mime[0];
         $tmpLoc = $photo['tmp_name'];
         $fileSize = $photo['size'];
         $allowed = array('png','jpg','jpeg','gif');
         $uploadName = md5(microtime()).'.'.$fileExt;
         $uploadPath = BASEURL.'images/products/'.$uploadName;
         $dbpath = '/modelstore/images/products/'.$uploadName;
         if ($mimeType != 'image') {
             $errors[] = 'The file must be an image.';
         }
         if (!in_array($fileExt, $allowed)) {
             $errors[] = 'The photo file extension must be a png, jpg, jpeg, or gif.';
         }
         if ($fileSize > 25000000) {
             $errors[] = 'The files size must be under 25MB.';
         }
         if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')) {
             $errors[] = 'File extension does not match the file.';
         }
     }
     if(!empty($errors)){
         echo display_errors($errors);
     }else{
         //upload file and insert into database
        move_uploaded_file($tmpLoc, $uploadPath);
         $updatesql = "INSERT INTO products (`title`, price, list_price, brand, category, description, sizes) VALUES ('$title', $price', '$list_price', '$brand', '$category', '$description', $sizes')";
        $db->query($updatesql);
         header('Location: products.php');
     }
   }
 ?> 

 <h2 class="text-center">Add A New Product</h2><hr>
 <form action="products.php?add=1" method="POST" enctype="multipart/form-data">
  <div class="form-group col-md-3">
    <label for="title">Title*:</label>
    <input type="text" name="title" class="form-control" id="title" value="<?=((isset($_POST['title']))?sanitize($_POST['title']):'');?>">
  </div>
  <div class="form-group col-md-3">
   <label for="brand">Brand*:</label>
   <select class="form-control" id="brand" name="brand">
       <option value=""<?=((isset($_POST['brand']) && $_POST['brand'] == '')?' selected':'');?>></option>
    <?php while($brand = mysqli_fetch_assoc($brandQuery)): ?>
       <option value="<?=$brand['id'];?>"<?=((isset($_POST['brand']) && $_POST['brand'] == $brand['id'])?' selected':'');?>><?=$brand['brand'];?></option>
    <?php endwhile; ?>
    </select>
  </div>
  <div class="form-group col-md-3">
     <label for="parent">Parent Category*:</label>
      <select class="form-control" id="parent" name="parent">
       <option value=""<?=((isset($_POST['parent']) && $_POST['parent'] == '')?' selected':'');?>></option>
        <?php while($parent = mysqli_fetch_assoc($parentQuery)): ?>
        <option value="<?=$parent['id'];?>"<?=((isset($_POST['parent']) && $_POST['parent'] == $parent['id'])?' select':'');?>><?=$parent['category'];?></option>
        <?php endwhile; ?>
      </select>
  </div>
 <div class="form-group col-md-3">
  <label for="child">Child Category*:</label>
  <select id="child" name="child" class="form-control"></select>
 </div>
 <div class="form-group col-md-3">
  <label for="price">Price*:</label>  
  <input type="text" id="price" name="price" class="form-control" value="<?=((isset($_POST['price']))?sanitize($_POST['price']):'');?>">
 </div>
 <div class="form-group col-md-3">
  <label for="price">List Price:</label>  
  <input type="text" id="list_price" name="list_price" class="form-control" value="<?=((isset($_POST['list_price']))?sanitize($_POST['list_price']):'');?>">
 </div>
 <div class="form-group col-md-3">
    <label>Sizes:</label>
   <button class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Sizes</button>    
 </div>
 <div class="form-group col-md-3">
     <label for="sizes">Sizes Preview</label>
     <input type="text" class="form-control" name="sizes" id="sizes" value="<?=((isset($_POST['sizes']))?$_POST['sizes']:'');?>" readonly>
 </div>
 <div class="form-group col-md-6">
     <label for="photo">Product Image</label>
     <input type="file" name="photo" id="photo" class="form-control">
 </div>
 <div class="form-group col-md-6">
     <label for="description">Description:</label>
     <textarea id="description" name="description" class="form-control" rows="6"><?=((isset($_POST['description']))?sanitize($_POST['description']):'');?></textarea>
 </div>
 <div class="form-group col-md-2 pull-right">
    <input type="submit" value="Add Product" class="form-control btn btn-success"> 
</div><div class="clearfix"></div>
</form>

<!-- Modal -->
<div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="sizesModalLabel">Sizes</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
           <?php for($i=1;$i <= 12;$i++): ?>
            <div class="form-group col-md-4">
             <label for="size<?=$i;?>">Size:</label>
             <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=(!empty($sArray[$i-1])?$sArray[$i-1]:'');?>" class="form-control">
            </div>
         <?php endfor;?>
        </div>
      </div>
       
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php }else{ }   
$sql = "SELECT * FROM products WHERE deleted = 0";
$presults = $db->query($sql);

if (isset($_GET['featured'])) {
    $id = (int)$_GET['id'];
    $featured = (int)$_GET['featured'];
    $featuredSql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
    $db->query($featuredSql);
    header('Location: products.php');
}

?>


<h2 class="text-center">PRODUCTS</h2>
<hr>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a><div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
 <thead>
 <th></th> <th>Product</th> <th>Price</th> <th>Category</th> <th>Featured</th> <th>Sold</th>     
</thead>
 <tbody>
     
   <?php while($product = mysqli_fetch_assoc($presults)):              $childID = $product['category'];
      $catSql = "SELECT * FROM categories WHERE id = $childID";
      $result = $db->query($catSql);
      $cat = mysqli_fetch_assoc($result);
      $parentID = $cat['parent'];
      $pSql = "SELECT * FROM categories WHERE id = '$parentID'";
      $presult = $db->query($pSql);
      $parent = mysqli_fetch_assoc($presult);
      $category = $parent['category'].'~'.$cat['category']; 
     ?>
     <tr>
      <td>
        <a href="products.php?edit=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
        <a href="products.php?delete=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
      </td>
      <td><?=$product['title'];?></td>
      <td><?=money($product['price']);?></td>
      <td><?=$category;?></td>
         <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-xs btn-default glyphicon glyphicon-<?=(($product['featured'] == 1)?'minus':'plus');?>">    
         </a>&nbsp <?=(($product['featured'] == 1)?'Featured Product':'');?></td>
      <td>0</td>
     </tr>
   <?php endwhile; ?>
 </tbody>
</table>
<?php include 'includes/footer.php';