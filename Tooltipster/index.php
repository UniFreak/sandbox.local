<!DOCTYPE html>
<html>
<head>
    <title>Learning Tooltipster</title>

    <link rel="stylesheet" type="text/css" href="css/tooltipster.css">

    <script src="../vendor/js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.tooltipster.min.js"></script>
    <script>
    $('document').ready(function() {
        $('.tooltip').tooltipster({
        });
        $('#span1').tooltipster({
            animation: 'fade',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevice: false,
            trigger: 'click',
            content: $('<span><img style="width:30px;height:30px" src="img/1.jpg" /><strong>new tooltip</strong></span>'),
        })
    })
    </script>
</head>
<body>
    <img src="img/1.jpg" style="width:30px; height:30px" class="tooltip" title="tooltip for img">
    <a href="www.baidu.com" class="tooltip" title="tooltip for hlink">www.baidu.com</a>
    <div class="tooltip" title="tooltip for div">
        this is a div
    </div>

    <span id="span1">span1</span>
</body>
</html>