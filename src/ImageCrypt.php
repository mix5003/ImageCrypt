<?php

namespace mix5003\ImageCrypt;

use mix5003\ImageCrypt\Exception\FileNotFoundException;

class ImageCrypt
{
    /* @var \mix5003\ImageCrypt\ImageHelper */
    protected $helper;

    public function __construct()
    {
        $this->helper = new ImageHelper();
    }

    public function createRandomKeyImage($pathKey, $width = 64, $height = 64)
    {
        $imKey = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $keyColot = array(
                    'r' => rand(0, 255),
                    'g' => rand(0, 255),
                    'b' => rand(0, 255)
                );

                $color = $this->helper->convertReadableToSystemColor($keyColot);

                imagesetpixel($imKey, $x, $y, $color);
            }
        }

        imagepng($imKey, $pathKey);

        return $imKey;
    }


    public function encrypt($pathSrc, $pathDst, $pathKey)
    {
        $imSrc = $this->helper->openAsImage($pathSrc);
        try {
            $imKey = $this->helper->openAsImage($pathKey);
        } catch (FileNotFoundException $e) {
            $imKey = $this->createRandomKeyImage($pathKey);
        }
        $sizeSrc = $this->helper->getImageSize($imSrc);
        $sizeKey = $this->helper->getImageSize($imKey);

        $partDetail = $this->calculateNumberOfPart($sizeSrc, $sizeKey);

        $imDst = imagecreatetruecolor($sizeSrc['x'], $sizeSrc['y']);

        for ($currentPartX = 0; $currentPartX < $partDetail['x']; $currentPartX++) {
            for ($currentPartY = 0; $currentPartY < $partDetail['y']; $currentPartY++) {
                $this->encryptSubPart($imSrc, $imKey, $imDst, $currentPartX, $currentPartY, $sizeSrc, $sizeKey);
            }
        }

        return imagepng($imDst, $pathDst);
    }

    public function decrypt($pathSrc, $pathDst, $pathKey)
    {
        return $this->encrypt($pathSrc, $pathDst, $pathKey);
    }

    protected function calculateNumberOfPart($sizeSrc, $sizeKey)
    {
        if ($sizeSrc['x'] < $sizeKey['x'] || $sizeSrc['y'] < $sizeKey['y']) {
            throw new \Exception('Source must not smaller than keys file');
        }

        return array(
            'x' => ceil(($sizeSrc['x'] * 1.0) / $sizeKey['x']),
            'y' => ceil(($sizeSrc['y'] * 1.0) / $sizeKey['y']),
        );
    }

    protected function encryptPixel($imSrc, $imKey, $imDst, $x, $y, $sizeKey = null)
    {
        if ($sizeKey == null) {
            $sizeKey = $this->helper->getImageSize($imKey);
        }

        $srcColor = $this->helper->getReadableColor(imagecolorat($imSrc, $x, $y));
        $keyColor = $this->helper->getReadableColor(imagecolorat($imKey, $x % $sizeKey['x'], $y % $sizeKey['y']));

        $dstColor = array(
            'r' => $srcColor['r'] ^ $keyColor['r'],
            'g' => $srcColor['g'] ^ $keyColor['g'],
            'b' => $srcColor['b'] ^ $keyColor['b'],
        );
        $color = $this->helper->convertReadableToSystemColor($dstColor);

        imagesetpixel($imDst, $x, $y, $color);
    }

    protected function encryptSubPart($imSrc, $imKey, $imDst, $noPartX, $noPartY, $sizeSrc = null, $sizeKey = null)
    {
        if ($sizeSrc == null) {
            $sizeSrc = $this->helper->getImageSize($imSrc);
        }
        if ($sizeKey == null) {
            $sizeKey = $this->helper->getImageSize($imKey);
        }

        $startX = $noPartX * $sizeKey['x'];
        $startY = $noPartY * $sizeKey['y'];

        $endX = $startX + $sizeKey['x'];
        if ($endX > $sizeSrc['x']) {
            $endX = $sizeSrc['x'];
        }

        $endY = $startY + $sizeKey['y'];
        if ($endY > $sizeSrc['y']) {
            $endY = $sizeSrc['y'];
        }

        for ($x = $startX; $x < $endX; $x++) {
            for ($y = $startY; $y < $endY; $y++) {
                $this->encryptPixel($imSrc, $imKey, $imDst, $x, $y, $sizeKey);
            }
        }
    }

}