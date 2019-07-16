<?php

namespace App;

require '../vendor/autoload.php';
use App\CaptchaGen;

session_start();

if ($_POST) {
    $sess_capt = $_SESSION[CaptchaGen::getSessionKeyName()];
    if (!isset($sess_capt)) {
        echo 'not possible';
        die();
    }
    if ($_POST['captcha'] != $sess_capt) {
        echo 'wrong captcha';
    } else {
        echo 'good captcha<br>';
        CaptchaGen::destroy();
        echo '<a href="index.php">back</a>';
        die();
    }
}

$captcha = CaptchaGen::getInstance(['length'=>10]);

?>

<form method="post">
<input name="captcha" type="text" value=""/>
<img src="<?php echo $captcha->getImg() ?>" />
<img src="<?php echo $captcha->getImg() ?>" />
<img src="<?php echo $captcha->getImg() ?>" />
<img src="<?php echo $captcha->getImg() ?>" />
<img src="<?php echo $captcha->getImg() ?>" />
<img src="<?php echo $captcha->getImg() ?>" />
<br>
<button type="submit">Submit</button>
