/**
 * Created by pieter on 12/08/15.
 */

var ImageObject = function(){
    this.hh = new handlebar_helper();
};

/**
 * Draw an image using Handlebar template section-image-template
 * @param image object with properties "id", "label" and "thumbnail"
 * @returns {string}
 */
ImageObject.prototype.draw = function(image) {
    return this.hh.draw_template_from_object(image, 'section-image-template')
};

/* Legacy functions */

function loadImages(sectionId){
    console.log (sectionId);
    $.ajax({
        type: 'post',
        url: Routing.generate('load_images_section'),
        data: {
            'section_id' : sectionId
        },
        success: function(images){
            console.log (images);
            drawImages(images);
            //$('#parameter-images').html(html);
        }
    })
}


function loadImage(sectionId, imageId){
    /* So this should load the images
     * In the main form that is*/
    console.log ('log_images_section');
    $.ajax({
        type: 'post',
        url: Routing.generate('load_image_section'),
        data: {
            'section_id' : sectionId,
            'image_id'   : imageId
        },
        success: function(image){
            console.log (image);
            var html = drawImageEdit(image);
            console.log (html);
            var f = document.getElementById ('image-edit');
            console.log (f);
            $('#image-edit').html(html);

        }
    })

}


function drawImages(images){
    console.log ('images');
    $('#parameter-images').html('');
    var shtml = '';
    for (var img in images){
        var html = drawImage(images[img]);
        /*$(document).ready(function () {
         var f = document.getElementById ('parameter-images');
         console.log (f);
         $('#parameter-images').append(html);
         });*/
        shtml = shtml + html;

    }
    /* html does not get appended */
    /* We use a jquery function I found somewhere until the container id exists, and then append it all */
    $('#parameter-images').waitUntilExists (function () {
        $('#parameter-images').append(shtml);
    });

}


function drawImage(image){
    var io = new ImageObject();
    return io.draw(image);
}


function drawImageEdit(image){
    var source   = $("#edit-media-template-open").html();
    var template = Handlebars.compile(source);
    return template(image);
}


