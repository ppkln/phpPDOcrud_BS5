<?php
    session_start();
    require_once "config/db.php";

    if(isset($_POST['update'])){
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $img = $_FILES['img'];

        $img2 = $_POST['img2'];
        $upload = $_FILES['img']['name'];

        if($upload != ''){
            $allow = array('jpg','jpeg','png');
            $extension = explode('.', $img['name']);
            $fileActExt = strtolower(end($extension));
            $fileNew = rand() . "." . $fileActExt;  // ฟังก์ชันสุ่มค่าเป็นตัวเลขเพื่อเอามาใช้เป็นชื่อไฟล์ภาพที่เราอัพโหลด
            $filePath = "uploads/".$fileNew;

            if(in_array($fileActExt,$allow)){
                if($img['size'] > 0 && $img['error']==0 ){
                    move_uploaded_file($img['tmp_name'],$filePath);
                }
            }
        } else {
            $fileNew = $img2;
        }

        $sql = $conn->prepare("UPDATE users SET Fullname=:fullname, email=:email, position=:position, img=:img WHERE id=:id");
        $sql->bindParam(":id", $id);
        $sql->bindParam(":fullname",$fullname);
        $sql->bindParam(":email",$email);
        $sql->bindParam(":position",$position);
        $sql->bindParam(":img",$fileNew);
        $sql->execute();

        if($sql){
            $_session['success'] = "Data has been update to database successfully";
            header("location: index.php");
        } else {
            $_session['error'] = "Data has not been update to database.";
            header("location: index.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title>Home </title>

<style>
    .container {
        max-width:550px;
    }
</style>
</head>
<body>
<div class="container mt-5">
    <h1>Edit Data</h1>
    <hr>
    <form action="edit.php" method="post" enctype="multipart/form-data">
        <?php 
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $stmt = $conn->query("SELECT * FROM users WHERE id=$id");
                $stmt->execute();
                $data = $stmt->fetch();
            }
        ?>
            <div class="mb-3">
                <label for="fullname" class="col-form-label">Fullname:</label>
                <input type="text" value="<?= $data['Fullname']; ?>" required class="form-control" name="fullname">
                <input type="hidden" value="<?= $data['id']; ?>" required class="form-control" name="id">
                <input type="hidden" value="<?= $data['img']; ?>" required class="form-control" name="img2">
            </div>
            <div class="mb-3">
                <label for="email" class="col-form-label">email:</label>
                <input type="email" readonly value="<?= $data['email']; ?>"  class="form-control" name="email">
            </div>
            <div class="mb-3">
                <label for="position" class="col-form-label">Position:</label>
                <input type="text" value="<?= $data['position']; ?>" required class="form-control" name="position">
            </div>
            <div class="mb-3">
                <label for="img" class="col-form-label">image:</label>
                <input type="file"  class="form-control" id="imgInput" name="img">
                <img width="100%" src="uploads/<?= $data['img']; ?>" id="previewImg" alt="">
            </div>
            <div class="modal-footer">
                <a class="btn btn-secondary me-2" href="index.php">Go Back</a>
                <button type="submit" name="update" class="btn btn-success me-2">Update</button>
            </div>
        </form>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<script>
    let imgInput = document.getElementById('imgInput');
    let previewImg = document.getElementById('previewImg');

    imgInput.onchange = evt =>{
        const [file] =imgInput.files;
        if(file){
            previewImg.src = URL.createObjectURL(file);
        }
    }
</script>
</body>
</html>