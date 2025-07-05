<?php
function generateCode(){
    $authCode = random_int(100000, 999999); // 6-digit number
    return $authCode;

}
?>