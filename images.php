<?php

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    die("Access denied");
}

$images_path = "./stored/";

$image_data = $_POST['data'];

$image_src = str_replace("data:image/jpeg;base64,", "", $image_data);
$image_src = base64_decode($image_src);

if( @file_put_contents ( $images_path . $_POST['filename'], $image_src ) ) {
    echo json_encode( array("true") );
} else {
    echo json_encode( array("false") );
}
