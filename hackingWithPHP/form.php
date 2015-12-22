<?php
var_dump($_POST);
?>

<!-- if you need to contain a file upload input, `enctype` must set as this -->
<form enctype="multipart/form-data" action="fileUpload.php" method="POST">
    <!-- you can design a form with checkbox like this, using [] -->
    one:<input name="selected[]" type="checkbox" value="one">
    two:<input name="selected[]" type="checkbox" value="two">
    three:<input name="selected[]" type="checkbox" value="three"><br>

    choose a file: <input type="file" name="upload" id="">

    <input type="submit" value="submit">
</form>