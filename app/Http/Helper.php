<?php
/**
 * Created by PhpStorm.
 * User: mahdi
 * Date: 3/4/19
 * Time: 1:59 PM
 */


function getRandomIdentIcons()
{
    return "jdenticon/jdenticon-" . rand(1, 25) . ".png";
}

function createImageFromBase64($file_data, $scope = "storage")
{
    $file_name = $scope . '/' . \Illuminate\Support\Str::random(32) . '.png';
    if ($file_data != "") {
        \Illuminate\Support\Facades\Storage::disk('public')->put($file_name, base64_decode($file_data));
    }
    return $file_name;
}