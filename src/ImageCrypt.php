<?php

namespace mix5003\ImageCrypt;

use mix5003\ImageCrypt\Exception\FileNotFoundException;
use mix5003\ImageCrypt\Exception\FileNotSupportedException;

class ImageCrypt {

    protected function openAsImage($path){
        if(!file_exists($path)){
            throw new FileNotFoundException("File '{$path}' not exists.");
        }
        $bin = file_get_contents($path);
        $im = imagecreatefromstring($bin);
        if(!$im){
            throw new FileNotSupportedException("File '{$path}' not supported image format.");
        }

        return $im;
    }

    protected function getImageSize($im){
        return array(
            'x'=>imagesx($im),
            'y'=>imagesy($im)
        );
    }

    protected function calculateNumberOfPart($sizeSrc,$sizeKey){
        if($sizeSrc['x'] < $sizeKey['x'] || $sizeSrc['y'] < $sizeKey['y']){
            throw new \Exception('Source must not smaller than keys file');
        }
        return array(
            'x' => ceil(($sizeSrc['x'] * 1.0) / $sizeKey['x']),
            'y' => ceil(($sizeSrc['y'] * 1.0) / $sizeKey['y']),
        );
    }

    protected function getReadableColor($rgb){
        return array(
            'r' => ($rgb >> 16) & 0xFF,
            'g' => ($rgb >> 8) & 0xFF,
            'b' => $rgb & 0xFF,
        );
    }

    protected function convertReadableToSystemColor($color){
        return ($color['r'] << 16) + ($color['r'] << 8) + $color['b'];
    }

    protected function encryptPixel($imSrc,$imKey,$imDst,$x,$y){
        $sizeKey = $this->getImageSize($imKey);

        $srcColor = $this->getReadableColor(imagecolorat($imSrc,$x,$y));
        $keyColor = $this->getReadableColor(imagecolorat($imKey,$x % $sizeKey['x'],$y % $sizeKey['y']));

        $dstColor = array(
            'r' => $srcColor['r'] ^ $keyColor['r'],
            'g' => $srcColor['g'] ^ $keyColor['g'],
            'b' => $srcColor['b'] ^ $keyColor['b'],
        );
        $color = $this->convertReadableToSystemColor($dstColor);

        imagesetpixel($imDst,$x,$y,$color);
    }

    public function createRandomKeyImage($pathKey,$width = 64,$height = 64){
        $imKey = imagecreatetruecolor($width,$height);
        for($x = 0;$x < $width;$x++){
            for($y=0;$y<$height;$y++){
                $keyColot=array(
                    'r'=>rand(0,255),
                    'g'=>rand(0,255),
                    'b'=>rand(0,255)
                );

                $color = $this->convertReadableToSystemColor($keyColot);

                imagesetpixel($imKey,$x,$y,$color);
            }
        }

        imagepng($imKey,$pathKey);
        return $imKey;
    }

    protected function encryptSubPart($imSrc,$imKey,$imDst,$noPartX,$noPartY){
        $sizeSrc = $this->getImageSize($imSrc);
        $sizeKey = $this->getImageSize($imKey);

        $startX = $noPartX * $sizeKey['x'];
        $startY = $noPartY * $sizeKey['y'];

        $endX = $startX + $sizeKey['x'];
        if($endX > $sizeSrc['x']){
            $endX = $sizeSrc['x'];
        }

        $endY = $startY + $sizeKey['y'];
        if($endY > $sizeSrc['y']){
            $endY = $sizeSrc['y'];
        }

        for($x = $startX;$x<$endX;$x++){
            for($y = $startY;$y<$endY;$y++){
                $this->encryptPixel($imSrc,$imKey,$imDst,$x,$y);
            }
        }
    }

    public function encrypt($pathSrc,$pathDst,$pathKey){
        $imSrc = $this->openAsImage($pathSrc);
        try {
            $imKey = $this->openAsImage($pathKey);
        }catch(FileNotFoundException $e){
            $imKey = $this->createRandomKeyImage($pathKey);
        }
        $sizeSrc = $this->getImageSize($imSrc);
        $sizeKey = $this->getImageSize($imKey);

        $partDetail = $this->calculateNumberOfPart($sizeSrc,$sizeKey);

        $imDst = imagecreatetruecolor($sizeSrc['x'],$sizeSrc['y']);

        for($currentPartX = 0;$currentPartX < $partDetail['x'];$currentPartX++){
            for($currentPartY = 0;$currentPartY < $partDetail['y'];$currentPartY++){
                $this->encryptSubPart($imSrc,$imKey,$imDst,$currentPartX,$currentPartY);
            }
        }

        return imagepng($imDst,$pathDst);
    }

    public function decrypt($pathSrc,$pathDst,$pathKey){
        return $this->encrypt($pathSrc,$pathDst,$pathKey);
    }
}