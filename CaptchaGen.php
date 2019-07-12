<?php

class CaptchaGen
{

    private static $_instance = null;

    private $length;
    private static $token;
    private static $sessionKeyName = 'vr_sess_captcha_keyname';
    private $textColors = [];
    private $assetsDir;
    private $fonts = [];
    private $im;

    public function __construct(array $args)
    {
        if (!isset($args['length'])) {
            $this->length = 6;
        }
        $length = (int) $args['length'];
        if ($length < 3 || $length > 10) {
            throw new \Exception('Length is not valid. (set a value between 3 and 10)');
        } else {
            $this->length = $length;
        }

        if(self::$token===null){
            self::$token = $this->_createToken();
            $_SESSION[self::$sessionKeyName] = self::$token;
        }     

        $this->assetsDir = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR;

        $this->imageWidth = 19 * $this->length;
        $this->im = imagecreatetruecolor($this->imageWidth, 35);

        $this->textColors = $this->_setTextColors();
        $this->fonts = $this->_setFonts();

    }

   public static function getInstance(array $args) {
 
     if(is_null(self::$_instance)) {
       self::$_instance = new CaptchaGen($args);  
     }
 
     return self::$_instance;
   }

    public function getImg()
    {


        $colors = $this->_getTextColors();
        $fonts = $this->_getFonts();

        $bg = imagecolorallocate($this->im, 255, 255, 255);
        imagefilledrectangle($this->im, 0, 0, $this->imageWidth, 35, $bg);

        $textD = 8;
        for ($i = 0; $i < strlen(self::$token); $i++) {
            $color = array_rand($colors, 1);
            $font = array_rand($fonts, 1);
            imagettftext($this->im, mt_rand(17, 22), mt_rand(-20,30), $textD, mt_rand(25, 30), $colors[$color],$fonts[$font], self::$token[$i]);
            $textD += 17;
        }

        $carre = imagecreatefrompng($this->assetsDir .  'carre.png');
        imagesettile($this->im, $carre);
        imagefilledrectangle($this->im, 0, 0, $this->imageWidth, 35, IMG_COLOR_TILED);

        imagejpeg($this->im, self::$sessionKeyName . '.jpg');
        imagedestroy($this->im);

        return self::$sessionKeyName . '.jpg';
    }

    public static function getToken()
    {
        return self::$token;
    }

    public static function getSessionKeyName(){
        return self::$sessionKeyName;
    }

    public static function destroy(){
        unset($_SESSION[self::$sessionKeyName]);
        @unlink(self::$sessionKeyName . '.jpg');
    }

    private function _createToken()
    {
        $chrs = array_merge(range('a', 'z'), ['A','F','E','R'], range('1', '9'));
        $r = [];
        shuffle($chrs);
        for ($i = 0; $i < $this->length; $i++) {
            if (in_array($chrs[$i], ['w', 'm', 'l', 'i', 'j', 'o', 'q', 'f'])) {
                $charas = range('a', 'e');
                $chara = array_rand($charas, 1);
                $chrs[$i] = $charas[$chara];
            }
            $r[] = $chrs[$i];
        }

        return implode("", $r);
    }


    private function _setTextColors(){

        $black = imagecolorallocate($this->im, 0, 20, 20);
        $red = imagecolorallocate($this->im, 161, 1, 166);
        $orange = imagecolorallocate($this->im, 255, 144, 0);
        $blue = imagecolorallocate($this->im, 10, 100, 164);
        $green = imagecolorallocate($this->im, 76, 157, 0);

        return [$black, $red, $orange, $blue, $green];
    }

    private function _getTextColors(){

        return $this->textColors;
    }

    private function _setFonts(){

        $armWrestler =  $this->assetsDir . 'armWrestler.ttf';
        $arial =  $this->assetsDir . 'arial.ttf';
        $roboto =  $this->assetsDir . 'roboto.ttf';

        return [$roboto, $armWrestler, $arial];
    }

    private function _getFonts(){

        return $this->fonts;
    }

}
