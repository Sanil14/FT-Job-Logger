<?php 
    $invalid_err = "";
    $valid = 0;
    session_start();
    $id = $_SESSION['userid'];
    if (is_uploaded_file($_FILES['fileToUpload']['tmp_name']) && $_FILES['fileToUpload']['error']==0) {
      $target_dir = 'avatars/';
      $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      if($imageFileType != "jpg" || $imageFileType != "png" || $imageFileType != "jpeg" || $imageFileType != "webp") {
        $invalid_err = '<div id="errorDiv" class="alert alert-danger">
        <strong>Oh snap!</strong> Incorrect file type!
        </div>';
      }
      $path = $target_dir.$id.".png";
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $path)) {
          $valid = $path."?id=".date("s");
        } else {
          $invalid_err = '<div id="errorDiv" class="alert alert-danger">
          <strong>Oh snap!</strong> Something went wrong!
          </div>';
        }
    } else {
      //$invalid_err = '<div class="alert alert-danger">
      //<strong>Oh snap!</strong> File was not uploaded successfully!
    //</div>'; 
    $invalid_err = '<div id="errorDiv" class="alert alert-danger">
    <strong>Oh snap!</strong> Invalid credentials!
    </div>';
    }
    echo $valid;
?>