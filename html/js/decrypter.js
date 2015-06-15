(function($) {
    //TODO: Bug Render in table & Smartphone
    'use strict';
    window.decrypter = function () {

        function getColor(pixels,width,height,x,y){
            var i = (y * width) + x;
            return {
                r: pixels[i*4],
                g: pixels[i*4+1],
                b: pixels[i*4+2]
            }
        }

        function setColor(pixels,width,height,x,y,color){
            var i = (y * width) + x;
            pixels[i*4] = color.r;
            pixels[i*4+1] = color.g;
            pixels[i*4+2] = color.b;
        }

        function encryptPixel(pxSrc, pxKey, x, y,srcWidth,srcHeight,keyWidth,keyHeight){
            var srcColor = getColor(pxSrc,srcWidth,srcHeight,x,y);
            var keyColor = getColor(pxKey,keyWidth,srcHeight,x % keyWidth,y % keyHeight);

            var dstColor = {
                r: srcColor.r ^ keyColor.r,
                g: srcColor.g ^ keyColor.g,
                b: srcColor.b ^ keyColor.b
            };

            setColor(pxSrc,srcWidth,srcHeight,x,y,dstColor);
        }

        function decryptBlock(pxSrc,pxKey,blockX,blockY,srcWidth,srcHeight,keyWidth,keyHeight){
            var startX = blockX * keyWidth;
            var startY = blockY * keyHeight;

            var endX = startX + keyWidth;
            if (endX > srcWidth) {
                endX = srcWidth;
            }

            var endY = startY + keyHeight;
            if (endY > srcHeight) {
                endY = srcHeight;
            }

            for (var x = startX; x < endX; x++) {
                for (var y = startY; y < endY; y++) {
                    encryptPixel(pxSrc, pxKey, x, y,srcWidth,srcHeight,keyWidth,keyHeight);
                }
            }
        }

        this.decrypt = function (divEL, source, key) {
            var canvasEl= document.createElement('canvas');
            var ctx = canvasEl.getContext("2d");

            var imSrc = new Image();
            var imKey = new Image();

            var laodCount = 0;
            var decryptCallBack = function(){
                laodCount++;
                if( laodCount == 2 ){
                    canvasEl.width = imSrc.width;
                    canvasEl.height = imSrc.height;

                    ctx.drawImage(imSrc, 0, 0);
                    var dataSrc = ctx.getImageData(0, 0, imSrc.width, imSrc.height);
                    var pxSrc = dataSrc.data;

                    canvasEl.width = imKey.width;
                    canvasEl.height = imKey.height;
                    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);

                    ctx.drawImage(imKey, 0, 0);
                    var dataKey = ctx.getImageData(0, 0, imKey.width, imKey.height);
                    var pxKey = dataKey.data;

                    var srcWidth = imSrc.width;
                    var srcHeight = imSrc.height;

                    var keyWidth = imKey.width;
                    var keyHeight = imKey.height;

                    var numBlockWidth = Math.ceil(srcWidth/keyWidth);
                    var numBlockHeight = Math.ceil(srcHeight/keyHeight);

                    for(var x = 0;x<numBlockWidth;x++){
                        for(var y = 0;y<numBlockHeight;y++){
                            decryptBlock(pxSrc,pxKey,x,y,srcWidth,srcHeight,keyWidth,keyHeight);
                        }
                    }

                    canvasEl.width = imSrc.width;
                    canvasEl.height = imSrc.height;
                    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
                    ctx.putImageData(dataSrc, 0, 0);

                    $(divEL).append(canvasEl);

                    //$(divEL).css('background-image','url('+canvasEl.toDataURL('image/png')+')').width(imSrc.width).height(imSrc.height);


                    //clear data
                    pxKey = null;
                    dataKey = null;
                    pxSrc = null;
                    dataSrc = null;
                    ctx = null;
                    canvasEl = null;
                }
            };


            imSrc.addEventListener('load',decryptCallBack);
            imKey.addEventListener('load',decryptCallBack);


            imSrc.src = source;
            imKey.src = key;
        }
    };
})(jQuery);