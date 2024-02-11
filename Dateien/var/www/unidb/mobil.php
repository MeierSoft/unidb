<?php
$agent = $_SERVER['HTTP_USER_AGENT']; // Put browser name into local variable
header("X-XSS-Protection: 1");
$mobil=0;
if(preg_match('/(alcatel|android|blackberry|benq|cell|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mobi|motorola|nokia|palm|panasonic|philips|phone|sagem|sharp|smartphone|sony|symbian|t-mobile|up\.browser|up\.link|vodafone|wap|wireless|xda|zte)/i', $_SERVER['HTTP_USER_AGENT'])){
    $mobil=1;
}
?>