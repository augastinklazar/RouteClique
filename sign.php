<?php

    require 'connect/DB.php';
    require 'core/load.php';

    if( isset($_POST['first-name']) && !empty($_POST['first-name'])) {
        $upfirst = $_POST['first-name'];
        $uplast = $_POST['last-name'];
        $upemailmobile = $_POST['email-mobile'];
        $uppassword = $_POST['up-password'];
        $birthDay = $_POST['birthday'];
        $birthMonth = $_POST['birthmonth'];
        $birthYear = $_POST['birthyear'];
        if(!empty($_POST['gen'])) {
            $upgen = $_POST['gen'];
        }
        $birth = ''.$birthYear.'-'.$birthMonth.'-'.$birthDay.'';  //dots are used for concatenation

        if(empty($upfirst) or empty($uplast) or empty($upemailmobile) or empty($upgen)) {
            $error = 'All fields are required';
        }
        else {
            $first_name = $loadfromuser->checkInput($upfirst);
            $last_name = $loadfromuser->checkInput($uplast);
            $email_mobile = $loadfromuser->checkInput($upemailmobile);
            $password = $loadfromuser->checkInput($uppassword);
            $screen_name = ''.$first_name.'_'.$last_name.'';
            if(DB::query('SELECT screen_name FROM users WHERE screen_name = :screen_name', array(':screen_name'=> $screen_name))) {
                $screenrand = rand();         // users is the table in the sql database.
                $userlink = ''.$screen_name.''.$screenrand.'';    
            } //if username is found in database... it gives a unique other name.
            else {
                $userlink = $screen_name;
            } // else it is stored as it is

            //validation of email or mobile. Using regular expressions
            if(!preg_match("^[a-z0-9]+(\.[a-z0-9]+)*@[a-z0-9]+(\.[a-z0-9]+)*(\.[a-z]{2,3})$^" , $email_mobile)) {
                if(!preg_match("^[0-9]{10}^", $email_mobile)) {
                    $error = 'Email id or Mobile number is not valid, please try again.';
                }
                else {
                    $mob = strlen((string)$email_mobile);
                    if($mob > 10 || $mob < 10) {
                        $error = 'Mobile number is not valid';
                    }
                    else if(strlen($password) < 5 || strlen($password) >= 60) {
                        $error = 'password too short or too long';
                    }
                    else {
                        if(DB::query('SELECT mobile FROM users WHERE mobile=:mobile', array(':mobile'=>$email_mobile))) {
                            $error = 'Mobile number already in use.';
                        }
                        else {
                            $user_id = $loadfromuser->create('users',array('first_name'=>$first_name,'last_name'=>$last_name, 'mobile'=>$email_mobile, 'password'=>password_hash($password, PASSWORD_BCRYPT), 'screen_name'=>$screen_name, 'user_link'=>$userlink, 'birthday'=>$birth, 'gender'=>$upgen));
                            $tstrong = true;
                            $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                            $loadfromuser->create('token', array('token'=>$token, 'user_id'=>$user_id));    // this is insertion into table (line 57 too)

                            setcookie('FBID', $token, time()+60*60*24*7, '/', NULL, NULL, true); // FBID is the cookie name, set for 7 days, path is'/' ie, available globally.

                            header('Location: index.php');  // the content of the cookie is the token (not literally same but can identify)
                        }
                    }
                }
            }
            else {
                if(!filter_var($email_mobile)) {
                    $error = 'Invalid email format';
                }
                else if(strlen($first_name) > 20) {
                    $error = "Name must be less than 2-20 characters.";
                }
                else if(strlen($password) < 5 && strlen($password) >= 60) {
                    $error = 'The password is either too short or too long';
                }
                else {
                    if((filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) && $loadfromuser->checkEmail($email_mobile) === true) {
                        $error = 'Email is already in use.';
                    }
                    else {
                        $user_id = $loadfromuser->create('users', array('first_name'=>$first_name,'last_name'=>$last_name, 'email'=>$email_mobile, 'password'=>password_hash($password, PASSWORD_BCRYPT), 'screen_name'=>$screen_name, 'user_link'=>$userlink, 'birthday'=>$birth, 'gender'=>$upgen));
                        $tstrong = true;
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                        $loadfromuser->create('token', array('token'=>$token, 'user_id'=>$user_id));    // this is insertion into table (line 57 too)

                        setcookie('FBID', $token, time()+60*60*24*7, '/', NULL, NULL, true); // FBID is the cookie name, set for 7 days, path is'/' ie, available globally.

                        header('Location: index.php');  // the content of the cookie is the token (not literally same but can identify)
                    }
                }
            }

        }
    }
    else {
        echo 'User not found';
    }

   

?> 
<!--all input values are passed to sign.php through post method.... then before submit.... checks if data is filled.... then it is sent to the database-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RouteClique</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header"></div>
    <div class="main">
        <div class="left-side">
            <img src="assets/image/sign-up-img.png" class="RouteClique-img" alt="">
        </div>
        <div class="right-side">
            <div class="error">
                <?php if(!empty($error)) {
                    echo $error;
                } ?>
            </div>
            <h1 style="color: #212121;">Create an account</h1>
            <div style="color: #212121; font-size: 20px;">Where people communicate...</div>
            <form action="sign.php" method="post" name="user-sign-up">
                <div class="sign-up-form">
                    <div class="sign-up-name">
                        <input type="text" name="first-name" id="first-name" class="text-field" placeholder="First Name">
                        <input type="text" name="last-name" id="last-name" class="text-field" placeholder="Last Name">
                    </div>
                    <div class="sign-wrap-mobile">
                        <input type="text" name="email-mobile" id="up-email" placeholder="Mobile Number or email address" class="text-input">
                    </div>
                    <div class="sign-up-password">
                        <input type="password" name="up-password" id="up-password" class="text-input" placeholder="password">
                    </div>
                    <div class="sign-up-birthday">
                        <div class="bday">Birthday</div>
                        <div class="form-birthday">
                            <select name="birthday" id="days" class="select-body"></select>
                            <select name="birthmonth" id="months" class="select-body"></select>
                            <select name="birthyear" id="years" class="select-body"></select>
                        </div>
                        
                    </div>
                    <div class="gender-wrap">
                            <input type="radio" name="gen" id="fem" value="female" class="m0">
                            <label for="fem" class="gender">Female</label>
                            <input type="radio" name="gen" id="male" value="male" class="m0">
                            <label for="male" class="gender">Male</label>
                    </div>
                    <div class="term">
                        By clicking sign-up, you agree to our Terms, Data-Policy and Cookie-Policy. You may receive SMS notifications from us and can opt out any time
                    </div>
                    <input type="submit" value="sign-up" class="sign-up">
                </div>
            </form>
        </div>
    </div>
<script src="assets/js/jquery.js"></script>
<script>
    for (i = new Date().getFullYear(); i > 1900; i--) {
            // 2019,2018, 2017,2016.....1901
            $("#years").append($('<option/>').val(i).html(i));

    }
    for (i = 1; i < 13; i++) {
        $('#months').append($('<option/>').val(i).html(i));
    }
    
    updateNumberOfDays();

    function updateNumberOfDays() {
        $('#days').html('');
        month = $('#months').val();
        year = $('#years').val();
        days = daysInMonth(month, year);
        for (i = 1; i < days + 1; i++) {
            $('#days').append($('<option/>').val(i).html(i));
        }

    }
    $('#years, #months').on('change', function() {
        updateNumberOfDays();
    })

    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate();
    }
</script>
</body>
</html>