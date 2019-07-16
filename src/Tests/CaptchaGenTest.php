<?php
namespace App\Tests;

use App\CaptchaGen;
use PHPUnit\Framework\TestCase;

class CaptchaGenTest extends TestCase
{

    protected function setUp() : void
    {

        $singleton = CaptchaGen::getInstance();
        $reflection = new \ReflectionClass($singleton);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null); //reinit singleton
        $instance->setAccessible(false);
    }

    public function testSingleton()
    {
        $first = CaptchaGen::getInstance();
        $second = CaptchaGen::getInstance(['length'=>3]);
        $this->assertInstanceOf(CaptchaGen::class, $first);
        $this->assertSame($first, $second);
    }

    public function testSessionKeyName()
    {
        $instance = CaptchaGen::getInstance();
        $this->assertEquals('vr_sess_captcha_keyname', CaptchaGen::getSessionKeyName());
    }

    public function testLength()
    {
        $instance = CaptchaGen::getInstance();
        $this->assertGreaterThan(2, $instance->getLength());
        $this->assertLessThan(10, $instance->getLength());
        $this->assertIsInt($instance->getLength());
    }

    /**
     * @dataProvider badLength
     */
    public function testBadLength($bd)
    {
        $this->expectException(\InvalidArgumentException::class);
        $instance = CaptchaGen::getInstance(['length'=>$bd]);
    }

    public function badLength()
    {
        return [
            [1],
            [0.3],
            ['coucou'],
            [11]
        ];
    }
}
