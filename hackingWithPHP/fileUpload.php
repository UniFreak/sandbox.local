<?php
function eVar($var) {
    echo $var . '<br />';
}
function dVar($var) {
    var_dump($var);
}

/**
 * ==================== related settings ====================
 */
// upload_max_filesize


dVar($_FILES);
if (!empty($_FILES['upload'])
    // && move_uploaded_file(
    //         $_FILES['upload']['tmp_name'],
    //         'upload/' . $_FILES['upload']['name'])
    ) {
    $splited = explode('.', $_FILES['upload']['name']);
    $ext = array_pop($splited);

    // move_uploaded_file will auto-check this for you
    $isIndeedUploadedFile = is_uploaded_file($_FILES['upload']['tmp_name']);

    eVar('extension is ' . $ext);
    eVar('is indeed uploaded file?' . (int) $isIndeedUploadedFile);
    eVar('moved to upload dir');
}