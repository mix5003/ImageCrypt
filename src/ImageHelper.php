<?php

namespace mix5003\ImageCrypt;


use mix5003\ImageCrypt\Exception\FileNotFoundException;
use mix5003\ImageCrypt\Exception\FileNotSupportedException;

class ImageHelper
{
    public function openAsImage($path)
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException("File '{$path}' not exists.");
        }
        $bin = file_get_contents($path);
        $im = imagecreatefromstring($bin);
        if (!$im) {
            throw new FileNotSupportedException("File '{$path}' not supported image format.");
        }

        return $im;
    }

    public function getImageSize($im)
    {
        return array(
            'x' => imagesx($im),
            'y' => imagesy($im)
        );
    }

    public function getReadableColor($rgb)
    {
        return array(
            'r' => ($rgb >> 16) & 0xFF,
            'g' => ($rgb >> 8) & 0xFF,
            'b' => $rgb & 0xFF,
        );
    }

    public function convertReadableToSystemColor($color)
    {
        return ($color['r'] << 16) + ($color['g'] << 8) + $color['b'];
    }

    public function checkImageMatch($pathSrc, $pathDst)
    {
        $imSrc = $this->openAsImage($pathSrc);
        $imDst = $this->openAsImage($pathDst);

        $sizeSrc = $this->getImageSize($imSrc);
        $sizeDst = $this->getImageSize($imDst);

        if ($sizeSrc['x'] != $sizeDst['x'] || $sizeSrc['y'] != $sizeDst['y']) {
            return false;
        }

        for ($x = 0; $x < $sizeSrc['x']; $x++) {
            for ($y = 0; $y < $sizeSrc['y']; $y++) {
                if (imagecolorat($imSrc, $x, $y) != imagecolorat($imDst, $x, $y)) {
                    return false;
                }
            }
        }

        return true;
    }
}