<?php

use \mix5003\ImageCrypt\ImageCrypt;

class EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_encrypt_png_correctly()
    {
        $imCrypt = new ImageCrypt;
        $res = $imCrypt->encrypt('tests/src/1.png', 'tests/tmp/1.png', 'tests/key/1.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/1.png');
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_correctly()
    {
        $imCrypt = new ImageCrypt;
        $res = $imCrypt->encrypt('tests/src/2.jpg', 'tests/tmp/2.png', 'tests/key/2.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/2.png');
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_bw_correctly()
    {
        $imCrypt = new ImageCrypt;
        $res = $imCrypt->encrypt('tests/src/3.jpg', 'tests/tmp/3.png', 'tests/key/3.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/3.png');
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_bg_white_correctly()
    {
        $imCrypt = new ImageCrypt;
        $res = $imCrypt->encrypt('tests/src/4.jpg', 'tests/tmp/4.png', 'tests/key/4.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/4.png');
    }


    /**
     * @test
     */
    public function it_can_encrypt_jpg_bg_black_correctly()
    {
        $imCrypt = new ImageCrypt;
        $res = $imCrypt->encrypt('tests/src/5.jpg', 'tests/tmp/5.png', 'tests/key/5.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/5.png');
    }
}
