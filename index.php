<?php
    session_start();
    require_once "config/db.php";

    if(isset($_GET['delete'])){
        $delete_id = $_GET['delete'];
        $imgName = $_GET['imgName'];
        $deletestmt = $conn->query("DELETE FROM users WHERE id=$delete_id");
        $deletestmt->execute();

        if($deletestmt){
            if($imgName){
                $pathImgDelete = "uploads/".$imgName;
                unlink($pathImgDelete);
            }
            echo "<script>alert('Data has been deleted successfully.')</script>";
            $_session['success']= "Data has been deleted successfully.";
            header("refresh:2, url:index.php");
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
        .container{
            max-width:1328px;
        }
    </style>
</head>
<body>
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">เพิ่มผู้ใช้</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="insert.php" method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="fullname" class="col-form-label">ชื่อ - สกุล :</label>
            <input type="text" required class="form-control" name="fullname">
          </div>
          <div class="mb-3">
            <label for="email" class="col-form-label">e-mail:</label>
            <input type="email" required class="form-control" name="email">
          </div>
          <div class="mb-3">
            <label for="position" class="col-form-label">ตำแหน่ง :</label>
            <input type="text" required class="form-control" name="position">
          </div>
          <div class="mb-3">
            <label for="img" class="col-form-label">ภาพ :</label>
            <input type="file" required class="form-control" id="imgInput" name="img" accept="image/png, image/gif, image/jpeg">
            <img width="100%"  id="previewImg" alt="">
          </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button type="submit" name="submit" class="btn btn-success">ตกลง</button>
      </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h1>ยินดีต้อนรับสู่ LNcompany</h1>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">เพิ่มผู้ใช้</button>
        </div>
    </div>
    <?php if(isset($_session['success'])){?>
        <div class="alert alert-success">
            <?php 
                echo $_session['success'];
                unset($_session['success']);
            ?>
        </div>
    <?php } ?>
    <?php if(isset($_session['error'])){?>
        <div class="alert alert-danger">
            <?php 
                echo $_session['error'];
                unset($_session['error']);
            ?>
        </div>
    <?php } ?>

    <!-- ส่วนแสดงรายการข้อมูล -->
    <table class="table table-striped table-bordered mt-5">
        <thead>
            <tr>
                <th scope="col" class="text-center">รหัสสมาชิก</th>
                <th scope="col" class="text-center">ชื่อ-สกุล</th>
                <th scope="col" class="text-center">e-mail</th>
                <th scope="col" class="text-center">ตำแหน่ง</th>
                <th scope="col" class="text-center">รูปภาพ</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $stmt = $conn->query("SELECT * FROM users");
                $stmt->execute();
                $users = $stmt->fetchAll();
                if(!$users){
                    echo "<tr><td colspan='6' class='text-center'> No user found</td></tr>";
                } else {
                    foreach ($users as $user){
            ?>
            <tr>
                <th scope="row" class="text-center"><?= $user['id']; ?></th>
                    <td class="text-center"><?= $user['Fullname']; ?></td>
                    <td class="text-center"><?= $user['email']; ?></td>
                    <td class="text-center"><?= $user['position']; ?></td>
                    <td width="250px" ><img width="100%" src="uploads/<?= $user['img'];?>" class="rounded"></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?= $user['id']; ?>" class="btn btn-warning">แก้ไข</a>
                        <a href="?delete=<?= $user['id']; ?>&imgName=<?= $user['img'] ?>" class="btn btn-danger" onclick="return confirm('Do you want to delete this data?');">ลบ</a>
                    </td>
            </tr>
            <?php 
                    }
                }           
            ?>
        </tbody>
    </table>

</div>

<!-- เรียกใช้งาน script เพื่อสามารถแสดงภาพตัวอย่างออกทางหน้าจอได้ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<!-- คำสั่งแสดงตัวอย่างของภาพ -->
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