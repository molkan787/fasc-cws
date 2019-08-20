<?php

function rnd_str($length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function rnd_digit_str($length = 10) {
    return rnd_str($length, '0123456789');
}

function NumberToFixed($number, $decimals) {
  return number_format($number, $decimals, ".", "");
}
