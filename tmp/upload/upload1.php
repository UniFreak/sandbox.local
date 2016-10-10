<?php
header("Content-Type: text/html;charset=utf-8");
include "upload.class1.php";
 
    $up = new FileUpload;

    if($up->upload("pic")) {
       
        //获取上传后文件名子
        print_r($up->getFileName());
        
 
    } else {
        //获取上传失败以后的错误提示
        print_r($up->getErrorMsg());
       
    }
?>