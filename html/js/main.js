(function($) {
    var decrypter = new window.decrypter();

    var $keyFile = $('#key-file');
    var $picFile = $('#pic-file');
    var $imgPreview = $('#img-preview')
    var divEL = document.getElementById('decrypted');
    var autoDecrypt = function(){

        if($keyFile.val() == '' || $picFile.val() == '') return;

        $imgPreview.attr('src',$picFile.val());

        decrypter.decrypt(divEL,$picFile.val(),$keyFile.val());
    };
    $keyFile.change(autoDecrypt);
    $picFile.change(autoDecrypt);
})(jQuery);