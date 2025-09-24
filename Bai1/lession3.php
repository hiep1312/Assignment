<?php

//---------------------Bài 3:---------------------
$stringOrigin = "IT001=>ITSU0005
IT001=>ITSU0017
IT001=>ITSU0021
IT001=>ITSU0026
IT001=>ITSU0015
IT001=>ITSU0025";

$arraySplit = explode("\n", $stringOrigin);
$arrayResult = [];
for($i = 0; $i < count($arraySplit); $i++){
    $arraySeparation = explode("=>", $arraySplit[$i]);
    if(array_key_exists($arraySeparation[0], $arrayResult)):
        $arrayResult[$arraySeparation[0]] = [...(is_array($arrayResult[$arraySeparation[0]]) ? $arrayResult[$arraySeparation[0]] : [$arrayResult[$arraySeparation[0]]]), $arraySeparation[1]];
    else:
        $arrayResult[$arraySeparation[0]] = $arraySeparation[1];
    endif;
}

print_r($arrayResult);
//---------------------Done Bài 3---------------------