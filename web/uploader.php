<?php
// error_reporting(E_ALL);
/**
 * Recursive function to check if filename is already in use, if not append suffix.
 */
function checkFilenameForUniqueness($name, $suffix = "_1"){
    if(file_exists('media/' . $name)){
        // get filename part and extension
        $piecesName = explode('.', $name);
        $extension = array_pop($piecesName);
        $filename = implode('_', $piecesName);
        // append suffix and recheck if unique
        $uniqueName = checkFilenameForUniqueness($filename . $suffix . '.' . $extension);
    }
    else {
        $uniqueName = $name;
    }
    return $uniqueName;
}
/**
 * Create thumbnails
 */
function createThumbnail($filename, $cropWidth = 100, $cropHeight = 100, $suffix = "_thumb.png") {

    $piecesName = explode('.', $filename);
    $extension = array_pop($piecesName);
    $filenameBody = implode('_', $piecesName);

    if (preg_match('/jpg|jpeg/', $extension)){
        $srcImage = imagecreatefromjpeg($filename);
    }
    elseif (preg_match('/png/', $extension)){
        $srcImage = imagecreatefrompng($filename);
    }
    else {
        // todo add dummy image
        $srcImage = imagecreatefrompng("dummy.png");
    }

    $width  = imagesx($srcImage);
    $height = imagesy($srcImage);

    $source_aspect_ratio = $width / $height;
    $desired_aspect_ratio = $cropWidth / $cropHeight;

    if ($source_aspect_ratio > $desired_aspect_ratio) {
        /*
         * Triggered when source image is wider
         */
        $temp_height = $cropHeight;
        $temp_width = ( int ) ($cropHeight * $source_aspect_ratio);
    } else {
        /*
         * Triggered otherwise (i.e. source image is similar or taller)
         */
        $temp_width = $cropWidth;
        $temp_height = ( int ) ($cropWidth / $source_aspect_ratio);
    }

    /*
     * Resize the image into a temporary GD image
     */
    $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
    imagecopyresampled(
        $temp_gdim,
        $srcImage,
        0, 0,
        0, 0,
        $temp_width, $temp_height,
        $width, $height
    );

    /*
     * Copy cropped region from temporary image into the desired GD image
     */

    $x0 = ($temp_width - $cropWidth) / 2;
    $y0 = ($temp_height - $cropHeight) / 2;
    $desired_gdim = imagecreatetruecolor($cropWidth, $cropHeight);
    imagecopy(
        $desired_gdim,
        $temp_gdim,
        0, 0,
        $x0, $y0,
        $cropWidth, $cropHeight
    );

    /*
     * Render the image
     * Alternatively, you can save the image in file-system or database
     */

    imagepng ( $desired_gdim, $filenameBody . $suffix  );

    return $filenameBody . $suffix;
}

// first check if europeana image






// write file and set status
$status = "nok";
//if(file_put_contents('media/'.$filename, $HTTP_RAW_POST_DATA)){

if(isset($_REQUEST['image_source']) && $_REQUEST['image_source']=="uri"){
    // extension is always .jpg (europeana transcodes to jpeg even if original has .png extension)
    $extension = ".jpg";


    $filename = uniqid() . $extension;


    if(file_put_contents('media/'.$filename, file_get_contents($_REQUEST['image_uri']))){
        $status = "ok";
    }


}
else {
    $filename = checkFilenameForUniqueness($_REQUEST['uploadfile']);
    if(file_put_contents('media/'.$filename, file_get_contents('php://input'))){
        $status = "ok";
    }
}


$thumbnail = createThumbnail('media/' . $filename);

// prepare response
$response = array('filename' => $filename, 'status' => $status, 'thumbnail' => $thumbnail);

// echo output
header('Content-Type: application/json');
echo json_encode($response);

