<?php

//---------------------Bài 2:---------------------
$list100Numbers = [];
$list150Numbers = [];

for($i = 0; $i < 150; $i++, $list100Numbers[] = rand(6, 666), $list150Numbers[] = rand(6, 666));
array_splice($list100Numbers, 100, 150);

//Cách 1:
/* print_r(array_intersect($list100Numbers, $list150Numbers));
print_r(array_diff($list100Numbers, $list150Numbers)); */

//Cách 2:
//Các phần tử trùng nhau:
$overlapItems = [];
for($i = 0; $i < count($list100Numbers); $i++){
    if(in_array($list100Numbers[$i], $list150Numbers, true) && !in_array($list100Numbers[$i], $overlapItems, true)):
        $overlapItems[] = $list100Numbers[$i];
    endif;
}

print_r($overlapItems);

//Các phần tử không trùng nhau:
$nonOverlapItems = [];
for($i = 0; $i < count($list100Numbers); $i++){
    if(!in_array($list100Numbers[$i], $list150Numbers, true) && !in_array($list100Numbers[$i], $nonOverlapItems, true)):
        $nonOverlapItems[] = $list100Numbers[$i];
    endif;
}

print_r($nonOverlapItems);
//---------------------Done Bài 2---------------------