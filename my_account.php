<?php
    session_start();
    if (isset($_SESSION['id']) == false){
        header('Location: ./login_and_register.php');
    }
    $id_user = $_SESSION['id'];
    require "./public/connect_sql.php";

    $sql = "SELECT * FROM `user` WHERE `id_user` = '$id_user'";

    $user = mysqli_query($connection, $sql);
    $user = mysqli_fetch_array($user);

    if (!function_exists('currency_format')) {
        function currency_format($number, $suffix = ' VND')
        {
            if (!empty($number)) {
                return number_format($number, 0, ',', '.') . "{$suffix}";
            }
        }
    }

    $sql = "SELECT
                course.id_course,
                course.name_course,
                course.author,
                course.image_course,
                course.price,
                oder.creat_at,
                oder.rate,
                oder.history_lesson
            FROM
                course
            INNER JOIN oder ON oder.id_course = course.id_course
            WHERE
                oder.id_user = '$id_user'";

    $all_courses = mysqli_query($connection, $sql);

    $sql = "SELECT
                COUNT(*) as total_buy,
                SUM(course.price) as total_price
            FROM
                oder
            INNER JOIN course ON course.id_course = oder.id_course
            WHERE
                id_user = '$id_user'";
    $total = mysqli_query($connection, $sql);
    $total = mysqli_fetch_array($total);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi</title>
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/my_account.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <?php require "./default/header.php"; ?>
    <div class=content>
        <h2 >
            Tài khoản của tôi 
            <i class='bx bx-user'></i>
        </h2>
        <p class="money-style">
            <i class='bx bxs-right-arrow-alt'></i>
            Ngân hàng Quân đội (MBBANK)
        </p>
        <p class="money-style" id = "stk">
            <i class='bx bxs-right-arrow-alt'></i>
            Số tài khoản: 
        </p>
        <p class="money-style" id="my-money">
            <i class='bx bxs-right-arrow-alt'></i>
            Số tiền đang có: 
            <?php echo currency_format($_SESSION['money'])?>
        </p>
        <div class="image-avatar">
            <?php if( $_SESSION['image'] == 'null') { ?>
                <img id="avatar-preview" src="./public/images/default/avata.png" alt="">
            <?php } else { ?>
                <img id="avatar-preview" src="./public/images/upload/<?php echo $_SESSION['image']?>" alt="">
            <?php } ?>
        </div>
        <form id="my-in4" method="post" action="./processing/my_account_update.php" enctype="multipart/form-data">
            <label for="">Tên tài khoản: </label>
            <input name="name_user" class="input-in4" type="text" value="<?php echo $user['name_user']?>" readonly>
            <br>
            <label for="">Email đăng nhập: </label> 
            <input name="email_user" class="input-in4" type="text" value="<?php echo $user['email_user']?>" readonly>
            <br>
            <label for="">Số điện thoại: </label>
            <input name="phone_number_user" class="input-in4" type="text" value="<?php echo $user['phone_number_user']?>" readonly>
            <br>
            <input type="hidden">
            <br>
            <button class="btn btn-primary" type="button">Sửa đổi và bổ sung</button>    
            <button id="change-danger" class="btn btn-danger" type="button">Chỉnh sửa nâng cao</button>    
        </form>
        
        <form id="my-password" method="post" action="./processing/my_account_change_password.php">
            <h3 style="display: inline">Đổi mật khẩu và tài khoản ngân hàng</h3>
            <br><br>
            <label for="">Mật khẩu cũ:</label>
            <input class="input-in4 input-replace" type="password" name="password">
            <br>
            <label for="">Mật khẩu mới</label>
            <input class="input-in4 input-replace" type="password" name="new_password">
            <br>
            <label for="">Số tiền hiện tại</label>
            <input class="input-in4 input-replace" type="number" name="money" value="<?php echo $_SESSION['money']?>">
            <button class="btn btn-danger">Lưu Thông tin lại</button>
        </form>

        <div class="your-cart">
            <h2>
                Tổng quan về tài khoản của bạn
                <i class='bx bx-file-find'></i>
            </h2>
            <p>
                <i class='bx bxs-right-arrow-alt'></i>
                Số khóa học đã mua: <?php echo $total['total_buy']?> khóa
            </p>
            <p>
                <i class='bx bxs-right-arrow-alt'></i>
                Số tiền đã chi trả: <?php echo currency_format($total['total_price'])?>
            </p>
            <p>
                <i class='bx bxs-right-arrow-alt'></i>
                Số tiền còn lại: <?php echo currency_format($_SESSION['money'])?>
            </p>
        </div>
        <div class = "your-cart">
            <h2>
                Giỏ hàng đã mua
                <i class='bx bx-cart'></i>
            </h2>
            <?php foreach ($all_courses as $course) { ?>
                <div class = "course">  
                    <div class="img-course">
                        <img src="./public/images/upload/<?php echo $course['image_course']?>" alt="">
                    </div>
                    <div class = "in4-course">
                        <a href="./my_course_view_lesson.php?idcourse=<?php echo $course['id_course'] ?>">
                            <h2>Tên: <?php echo $course['name_course']?></h2>
                        </a>
                        <p>
                            <i class='bx bx-user-check'></i>
                            Tác Giả: <?php echo $course['author']?>
                        </p>
                        <p>
                            <i class='bx bx-credit-card-front'></i>
                            Giá Tiền: <?php echo currency_format($course['price'])?>
                        </p>
                        <p>
                            <i class='bx bx-time-five'></i>
                            Thời gian mua: 
                            <?php echo date("H:i:s - d/m/Y", strtotime($course['creat_at']))?>
                        </p>
                        <p>
                            <i class='bx bx-book-content'></i>
                            Đã học: <?php echo $course['history_lesson']?> 
                            Bài 
                        </p>
                        <p>
                            <i class='bx bx-star'></i>
                            Xếp hạng: <?php echo $course['rate']?>
                            <i class='bx bxs-star' style='color:#FFD700'></i>
                        </p>
                        <br>
                        <p class="rate-vote">
                            <a class="btn-rate" href="./view_course.php?id=<?php echo $course['id_course'] ?>">
                                Đánh giá hoặc đánh giá lại
                            </a>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
        
    </div>
    <div class="tab-right"></div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="./script/my_account.js"></script>
</body>
</html>