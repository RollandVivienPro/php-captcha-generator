<?php

namespace App;

class CaptchaGen
{
    private static $instance = null;
    private static $sessionKeyName = 'vr_sess_captcha_keyname';
    private $token;
    private $length;
    private $textColors = [];
    private $assetsDir;
    private $fonts = [];
    private $im;

    public function __construct(array $args = [])
    {
        if (!isset($args['length'])) {
            $this->length = 6;
        } else {
            $length = (int) $args['length'];
            if ($length < 3 || $length > 10) {
                throw new \InvalidArgumentException('Length is not valid. (set a value between 3 and 10)');
            } else {
                $this->length = $length;
            }
        }

        if ($this->token===null) {
            $this->token = $this->createToken();
            $_SESSION[self::$sessionKeyName] = $this->token;
        }

        $this->assetsDir = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $this->imageWidth = 18 * $this->length + 6;
        $this->im = imagecreatetruecolor($this->imageWidth, 35);
        $this->textColors = $this->setTextColors();
        $this->fonts = $this->setFonts();
    }

    public static function getInstance(array $args = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new CaptchaGen($args);
        }
 
        return self::$instance;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function createImg()
    {
        $colors = $this->getTextColors();
        $fonts = $this->getFonts();

        $bg = imagecolorallocate($this->im, 255, 255, 255);
        imagefilledrectangle($this->im, 0, 0, $this->imageWidth, 35, $bg);
     
        $textD = 8;
        $length = strlen($this->token);
        for ($i = 0; $i < $length; $i++) {
            $color = array_rand($colors, 1);
            $font = array_rand($fonts, 1);
            imagettftext($this->im, mt_rand(17, 22), mt_rand(-10, 10), $textD, mt_rand(25, 30), $colors[$color], $fonts[$font], $this->token[$i]);

            $rdColor = imagecolorallocatealpha($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(80, 120));
            imagefilledellipse($this->im, $textD, 15, mt_rand(10, 20), mt_rand(10, 20), $rdColor);
            $textD += 17;
        }

        for ($i=0; $i<10; $i++) {
            $rdColor = imagecolorallocatealpha($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(25, 60));
            imageline($this->im, 0, mt_rand(10, 50), 200, mt_rand(10, 50), $rdColor);
        }

        $carre = imagecreatefrompng($this->assetsDir .  'carre.png');
        imagesettile($this->im, $carre);
        imagefilledrectangle($this->im, 0, 0, $this->imageWidth, 35, IMG_COLOR_TILED);

        ob_start();
        imagejpeg($this->im, null, 100);
        
        return ob_get_clean();
    }

    public function getImg()
    {
        return "data:image/jpeg;base64," .base64_encode($this->createImg());
    }

    public static function getToken()
    {
        return $this->token;
    }

    public static function getSessionKeyName()
    {
        return self::$sessionKeyName;
    }

    public static function destroy()
    {
        imagedestroy($this->im);
        unset($_SESSION[self::$sessionKeyName]);
    }

    /**
     * Create the token, remove some lower letter to prevent confusing characters
     *
     * @return string
     */
    private function createToken() : string
    {
        $chrs = array_merge(range('a', 'z'), ['A','F','E','H','R','T'], range('1', '9'));
        $r = [];
        shuffle($chrs);
        for ($i = 0; $i < $this->length; $i++) {
            if (in_array($chrs[$i], ['w', 'm', 'l', 'i', 'j', 'o', 'q', 'f','5'])) {
                $charas = array_merge(range('a', 'e'), range('x', 'z'));
                $chara = array_rand($charas, 1);
                $chrs[$i] = $charas[$chara];
            }
            $r[] = $chrs[$i];
        }

        return implode("", $r);
    }


    private function setTextColors()
    {
        $black = imagecolorallocate($this->im, 50, 20, 20);
        $red = imagecolorallocate($this->im, 161, 1, 166);
        $pink = imagecolorallocate($this->im, 255, 1, 166);
        $orange = imagecolorallocate($this->im, 50, 200, 0);
        $blue = imagecolorallocate($this->im, 10, 100, 164);
        $green = imagecolorallocate($this->im, 0, 157, 0);

        return [$black, $pink, $red, $orange, $blue, $green];
    }

    private function getTextColors()
    {
        return $this->textColors;
    }

    private function setFonts()
    {
        $armWrestler =  $this->assetsDir . 'armWrestler.ttf';
        $arial =  $this->assetsDir . 'arial.ttf';
        $roboto =  $this->assetsDir . 'roboto.ttf';

        return [$roboto, $armWrestler, $arial];
    }

    private function getFonts()
    {
        return $this->fonts;
    }
}
