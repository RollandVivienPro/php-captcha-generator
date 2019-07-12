<?php 
session_start();
require('CaptchaGen.php');
?>

<?php

if($_POST){
    $sess_capt = $_SESSION[CaptchaGen::getSessionKeyName()];
    if(!isset($sess_capt)){
        echo 'not possible';
        die();
    }
    if($_POST['captcha'] != $sess_capt){
        echo 'wrong captcha';
    }else{
        echo 'good captcha<br>';
        CaptchaGen::destroy();
        echo '<a href="test.php">back</a>';
        die();
    }
}

$captcha = CaptchaGen::getInstance(['length'=>7]);

?>

<form method="post">
<input name="captcha" type="text" value=""/>
<img src="<?= $captcha->getImg() ?>" />
<br>
<button type="submit">Submit</button>
