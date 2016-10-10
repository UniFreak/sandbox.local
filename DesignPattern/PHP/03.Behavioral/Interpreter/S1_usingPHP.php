<?php
$form_input = $_REQUEST['form_input'];
// 用户可能输入: `print file_get_contents('/etc/passwd');`
eval($form_input);

/**
 * P1: 安全性(注释中的用户输入例子已说明这一点)
 * P2: 复杂性
 *     无论你的代码多么清晰, 普通用户不太容易扩展它, 特别是无法通过浏览器窗口来扩展它
 */