function checkifImage(images){
    var image = "images/default.jpg";
    $.each(images,function(i, img){
        $.ajax({
            url:img,
            type:'HEAD',
            success: function(){
                image = img;
            }
        });
    });
    return image;
}