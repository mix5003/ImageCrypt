<?php

use \mix5003\ImageCrypt\ImageCrypt;
use mix5003\ImageCrypt\ImageHelper;

class EncryptionTest extends PHPUnit_Framework_TestCase
{
    /* @var \mix5003\ImageCrypt\ImageHelper */
    protected $helper = null;
    /* @var \mix5003\ImageCrypt\ImageCrypt */
    protected $imCrypt = null;

    public function __construct()
    {
        parent::__construct();
        $this->helper = new ImageHelper();
        $this->imCrypt = new ImageCrypt();

        $this->clearTmp();
    }

    private function clearTmp()
    {
        $arr = scandir('tests/tmp');
        foreach ($arr as $file) {
            if ($file{0} == '.') {
                continue;
            }
            unlink('tests/tmp/' . $file);
        }
    }

    /**
     * @test
     */
    public function it_can_encrypt_png_correctly()
    {
        $res = $this->imCrypt->encrypt('tests/src/1.png', 'tests/tmp/1.png', 'tests/key/1.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/1.png');

        $this->imCrypt->decrypt('tests/tmp/1.png', 'tests/tmp/1_d.png', 'tests/key/1.png');
        $this->assertTrue($this->helper->checkImageMatch('tests/src/1.png', 'tests/tmp/1_d.png'));
    }

    /**
     * @depends it_can_encrypt_png_correctly
     * @test
     */
    public function it_can_not_decrypt_png_if_not_match_key()
    {
        $this->imCrypt->createRandomKeyImage('tests/tmp/1_k.png');
        $this->imCrypt->decrypt('tests/tmp/1.png', 'tests/tmp/1_d_2.png', 'tests/tmp/1_k.png');
        $this->assertFalse($this->helper->checkImageMatch('tests/src/1.png', 'tests/tmp/1_d_2.png'));
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_correctly()
    {
        $res = $this->imCrypt->encrypt('tests/src/2.jpg', 'tests/tmp/2.png', 'tests/key/2.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/2.png');

        $this->imCrypt->decrypt('tests/tmp/2.png', 'tests/tmp/2_d.png', 'tests/key/2.png');
        $this->assertTrue($this->helper->checkImageMatch('tests/src/2.jpg', 'tests/tmp/2_d.png'));
    }

    /**
     * @depends it_can_encrypt_jpg_correctly
     * @test
     */
    public function it_can_not_decrypt_jpg_if_key_not_match()
    {
        $this->imCrypt->createRandomKeyImage('tests/tmp/2_k.png');
        $this->imCrypt->decrypt('tests/tmp/2.png', 'tests/tmp/2_d_2.png', 'tests/tmp/2_k.png');
        $this->assertFalse($this->helper->checkImageMatch('tests/src/2.jpg', 'tests/tmp/2_d_2.png'));
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_bw_correctly()
    {
        $res = $this->imCrypt->encrypt('tests/src/3.jpg', 'tests/tmp/3.png', 'tests/key/3.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/3.png');

        $this->imCrypt->decrypt('tests/tmp/3.png', 'tests/tmp/3_d.png', 'tests/key/3.png');
        $this->assertTrue($this->helper->checkImageMatch('tests/src/3.jpg', 'tests/tmp/3_d.png'));
    }

    /**
     * @depends it_can_encrypt_jpg_bw_correctly
     * @test
     */
    public function it_can_not_decrypt_jpg_bw_if_key_not_match()
    {
        $this->imCrypt->createRandomKeyImage('tests/tmp/3_k.png');
        $this->imCrypt->decrypt('tests/tmp/3.png', 'tests/tmp/3_d_2.png', 'tests/tmp/3_k.png');
        $this->assertFalse($this->helper->checkImageMatch('tests/src/3.jpg', 'tests/tmp/3_d_2.png'));
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_bg_white_correctly()
    {
        $res = $this->imCrypt->encrypt('tests/src/4.jpg', 'tests/tmp/4.png', 'tests/key/4.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/4.png');

        $this->imCrypt->decrypt('tests/tmp/4.png', 'tests/tmp/4_d.png', 'tests/key/4.png');
        $this->assertTrue($this->helper->checkImageMatch('tests/src/4.jpg', 'tests/tmp/4_d.png'));
    }

    /**
     * @depends it_can_encrypt_jpg_bg_white_correctly
     * @test
     */
    public function it_can_not_decrypt_jpg_bg_white_if_key_not_match()
    {
        $this->imCrypt->createRandomKeyImage('tests/tmp/4_k.png');
        $this->imCrypt->decrypt('tests/tmp/4.png', 'tests/tmp/4_d_2.png', 'tests/tmp/4_k.png');
        $this->assertFalse($this->helper->checkImageMatch('tests/src/4.jpg', 'tests/tmp/4_d_2.png'));
    }

    /**
     * @test
     */
    public function it_can_encrypt_jpg_bg_black_correctly()
    {
        $res = $this->imCrypt->encrypt('tests/src/5.jpg', 'tests/tmp/5.png', 'tests/key/5.png');

        $this->assertTrue($res);
        $this->assertFileExists('tests/tmp/5.png');

        $this->imCrypt->decrypt('tests/tmp/5.png', 'tests/tmp/5_d.png', 'tests/key/5.png');
        $this->assertTrue($this->helper->checkImageMatch('tests/src/5.jpg', 'tests/tmp/5_d.png'));
    }

    /**
     * @depends it_can_encrypt_jpg_bg_black_correctly
     * @test
     */
    public function it_can_not_decrypt_jpg_bg_black_if_key_not_match()
    {
        $this->imCrypt->createRandomKeyImage('tests/tmp/5_k.png');
        $this->imCrypt->decrypt('tests/tmp/5.png', 'tests/tmp/5_d_2.png', 'tests/tmp/5_k.png');
        $this->assertFalse($this->helper->checkImageMatch('tests/src/5.jpg', 'tests/tmp/5_d_2.png'));
    }
}
