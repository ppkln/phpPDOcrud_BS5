<?php
    session_start();
    require_once "config/db.php";

    if (isset($_POST['submit'])){
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $img = $_FILES['img'];

        $allow = array('jpg','jpeg','png');//เช็คว่าต้องเป็นไฟล์ที่นามสกุสต่อไปนี้เท่านั้น
        $extension = explode('.', $img['name']);
        $fileActExt = strtolower(end($extension));
        $fileNew = mktime(date('H'), date('i'), date('s'),date('m'), date('d'), date('Y')) . "PPK." . $fileActExt; // ตั้งชื่อใหม่ให้ไฟล์ภาพที่เราอัพโหลด ด้วยการประยุกต์ใช้รูปแบบของวันเวลาปัจจุบันมากำหนดเป็นชื่อ
        $filePath = "uploads/".$fileNew;

        if(in_array($fileActExt,$allow)){
            if($img['size'] > 0 && $img['error']==0 ){
                if (move_uploaded_file($img['tmp_name'],$filePath)){
                    $sql = $conn->prepare("INSERT INTO users(Fullname, email, position, img) VALUES(:fullname, :email, :position, :img)");
                    $sql->bindParam(":fullname",$fullname);
                    $sql->bindParam(":email",$email);
                    $sql->bindParam(":position",$position);
                    $sql->bindParam(":img",$fileNew);
                    $sql->execute();

                    if($sql){
                        $_session['success'] = "Data has been insert to database successfully";
                        header("location: index.php");
                    } else {
                        $_session['error'] = "Data has not been insert to database.";
                        header("location: index.php");
                    }
                }

            }

        }
    }
?>