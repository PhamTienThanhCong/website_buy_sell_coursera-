<?php
session_start();

$id_admin = $_SESSION['id'];
$id_course = $_POST['id_course'];
$name_course = addslashes($_POST['name_course']);
$price = addslashes($_POST['price']);
$image_course = $_FILES['image_course'];
$description_course =addslashes( $_POST['description_course']);

$sql = "UPDATE `course` SET `name_course`='$name_course',`description_course`='$description_course',`price`='$price' WHERE `id_course` = '$id_course' and `id_admin` = '$id_admin'";

if (basename($image_course["name"]) != ""){
    // thư mục lưu file
    $target_dir = "../../../public/images/upload/";
    // lấy đặt tên file 
    $target_file = $target_dir . basename($image_course["name"]);
    // Lấy đuôi mở rộng
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // đặt lại tên file 
    $ramdomValue = time();
    $fileImageName = "$name_course" . "$ramdomValue" . "." . "$imageFileType";
    
    $target_file = $target_dir . $fileImageName;

    move_uploaded_file($image_course["tmp_name"], $target_file);

    $sql = "UPDATE `course` SET `name_course`='$name_course',`description_course`='$description_course',`image_course`='$fileImageName',`price`='$price' 
    WHERE `id_course` = '$id_course' and `id_admin` = '$id_admin'";
}

require "../../../public/connect_sql.php";

mysqli_query($connection, $sql);

// mail_send_by_cong($email_admin,$name_admin,$title,$content);

mysqli_close($connection);

header("Location: ../course_add_detail.php?id=$id_course");