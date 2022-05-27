<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

function hex_to_rgb($hex) {
    preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
    if(!isset($match[1])) {
        return false;
    }

    if(mb_strlen($match[1]) == 6) {
        list($r, $g, $b) = [$match[1][0].$match[1][1],$match[1][2].$match[1][3],$match[1][4].$match[1][5]];
    }
    elseif(mb_strlen($match[1]) == 3) {
        list($r, $g, $b) = [$match[1][0].$match[1][0],$match[1][1].$match[1][1],$match[1][2].$match[1][2]];
    }
    else if(mb_strlen($match[1]) == 2) {
        list($r, $g, $b) = [$match[1][0].$match[1][1],$match[1][0].$match[1][1],$match[1][0].$match[1][1]];
    }
    else if(mb_strlen($match[1]) == 1) {
        list($r, $g, $b) = [$match[1].$match[1],$match[1].$match[1],$match[1].$match[1]];
    }
    else {
        return false;
    }

    $color = [];
    $color['r'] = hexdec($r);
    $color['g'] = hexdec($g);
    $color['b'] = hexdec($b);

    return $color;
}
