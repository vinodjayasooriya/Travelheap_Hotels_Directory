<?php include "../../includes/db.php"; ?>
<?php ob_start(); ?>

<?php
function make_query($conn)
{
  if(isset($_GET['hotel_id']))
  {
    $get_hotel_id = $_GET['hotel_id'];
  }
 $query = "SELECT * FROM tbl_hotel_images WHERE hotel_id = '{$get_hotel_id}' ORDER BY id ASC";
 $result = mysqli_query($conn, $query);
 return $result;
}

function make_slide_indicators($conn)
{
 $output = ''; 
 $count = 0;
 $result = make_query($conn);
 while($row = mysqli_fetch_array($result))
 {
  if($count == 0)
  {
   $output .= '
   <li data-target="#dynamic_slide_show" data-slide-to="'.$count.'" class="active"></li>
   ';
  }
  else
  {
   $output .= '
   <li data-target="#dynamic_slide_show" data-slide-to="'.$count.'"></li>
   ';
  }
  $count = $count + 1;
 }
 return $output;
}

function make_slides($conn)
{
 $output = '';
 $count = 0;
 $result = make_query($conn);
 while($row = mysqli_fetch_array($result))
 {
  if($count == 0)
  {
   $output .= '<div class="item active">';
  }
  else
  {
   $output .= '<div class="item">';
  }
  $output .= '
   <img width="80%" style="padding-left: 100px;"  height="80%" src="../images/hotels/'.$row["image"].'"/>

  </div>
  ';
  $count = $count + 1;
 }
 return $output;
}
?>

<?php
  if(isset($_GET['hotel_id']))
  {
    $get_hotel_id = $_GET['hotel_id'];
  
  
  $message = "";
  if(isset($_POST['add_images']))
  {

    // Images
    $img = $_FILES['image']['name'];
    $img_temp = $_FILES['image']['tmp_name'];

    move_uploaded_file($img_temp, "../images/hotels/$img");

    $query_add_image = "INSERT INTO tbl_hotel_images (hotel_id,image) VALUES ({$get_hotel_id},'{$img}')";
    $result_add_image = mysqli_query($conn, $query_add_image);
    if (!$result_add_image)
    {
        die("Create Query Failed! " . mysqli_error($conn));    
    }else
    {
      $message = "<p class='text-center alert alert-success' role='alert'>Image Uploaded Successfully</p>";          
    }
  }
?>





<!DOCTYPE html>
<html>
 <head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <div class="container">
<?php
  $query_hotel = "SELECT * FROM tbl_hotels WHERE id = {$get_hotel_id} ";
  $result_hotel = mysqli_query($conn, $query_hotel);
  while ($row = mysqli_fetch_assoc($result_hotel)) 
  {
    $hotel_id   = $row['id'];
    $hotel_name = $row['name'];
  }
}
?>    
  <h3 align="center">Images of <?php echo $hotel_name; ?><a href="../hotels.php?source=edit_hotel&hotel_id=<?php echo $get_hotel_id; ?>"> View Hotel Details</a> or <a href="../hotels.php"> View All Hotels</a></h3>
  
  <!--  Carousol  -->
  <div class="row justify-content-center mb-1">
    <div class="col-lg-2"></div>
    <div class="col-lg-7">
      <div id="dynamic_slide_show" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
        <?php echo make_slide_indicators($conn); ?>
      </ol>

    <div class="carousel-inner">
      <?php echo make_slides($conn); ?>
    </div>

    <a class="left carousel-control" href="#dynamic_slide_show" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
    </a>

    <a class="right carousel-control" href="#dynamic_slide_show" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
    </a>

    </div>
    </div>
    <div class="col-lg-3"></div>
  </div>

  <!--  Form Add and Delete  -->
  <div class="row justify-content-center">
    <div class="col-lg-4"></div>
    <div class="col-lg-4 bg-dark rounded px-4">
      <h4 class="text-center text-light p-1">Select Image To Upload</h4>
      <h5 class="text-success text-center"><?php echo $message; ?></h5>
      <!-- enctype="multipart/form-data" for the IMAGE -->
      <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" name="image" class="form-control text-primary">
        </div>

        <div class="form-group">
          <input class="btn btn-warning btn-block" type="submit" name="add_images" value="Add Image" >
        </div>

        <div class="form-group">
          <a class="btn btn-danger btn-block" onClick="javascript: return confirm('Are you sure you want to delete all the images?'); " href="hotel_images_add.php?delete=<?php echo $get_hotel_id; ?>">Delete Images</a>
        </div>
      </form>
    </div>
    <div class="col-lg-4"></div>
  </div>
</div>

<?php
  if(isset($_GET['delete']))
  {
   $get_hotel_if_delete = $_GET['delete']; 

    $query_del = "DELETE FROM tbl_hotel_images WHERE hotel_id = {$get_hotel_if_delete}";
    $result_del = mysqli_query($conn,$query_del);
    if(!$result_del)
    {
      die("Delete Query Failed! " . mysqli_error($conn));    
    }
    else
    {
      header("Location: hotel_images_add.php?hotel_id={$get_hotel_if_delete}");        
    }
  }
?>

 </body>
</html>


