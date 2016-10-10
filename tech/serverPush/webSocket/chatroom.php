<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatroom</title>
    <script src="http://sandbox.local/vendor/js/jquery-1.11.1.min.js"></script>
    <script src="./js/chatroom.js"></script>
    <style>
        .chat_wrapper {
            width: 500px;
            margin-right: auto;
            margin-left: auto;
            background: #CCCCCC;
            border: 1px solid #999999;
            padding: 10px;
            font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
        }
        .chat_wrapper .message_box {
            background: #FFFFFF;
            height: 150px;
            overflow: auto;
            padding: 10px;
            border: 1px solid #999999;
        }
        .chat_wrapper .panel input{
            padding: 2px 2px 2px 5px;
        }
        .system_msg{color: #BDBDBD;font-style: italic;}
        .user_name{font-weight:bold;}
        .user_message{color: #88B6E0;}
    </style>
</head>
<body>
    <div class="chat_wrapper">
    <div class="message_box" id="message_box"></div>
    <div class="panel">
    <input type="text" name="name" id="name" placeholder="Your Name" maxlength="10" style="width:20%"  />
    <input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:60%" />
    <button id="send-btn">Send</button>
    </div>
    </div>
</body>
</html>