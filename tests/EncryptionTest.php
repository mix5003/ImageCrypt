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
}
