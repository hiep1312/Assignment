<?php

//---------------------Bài 1:---------------------
$listNumbers = [];
for($i = 0; $i < 100; $i++, $listNumbers[] = rand(1, 50));
// print_r($listNumbers);

$listNumbersSorted = $listNumbers;
rsort($listNumbersSorted);
array_splice($listNumbersSorted, 50, 100);

for($i = 0, $length = count($listNumbers), $listNumbersSortedTemp = $listNumbersSorted; $i < $length && !empty($listNumbersSortedTemp); $i++){
    if(in_array($listNumbers[$i], $listNumbersSortedTemp, true)){
        unset($listNumbersSortedTemp[array_search($listNumbers[$i], $listNumbersSortedTemp, true)]);
        unset($listNumbers[$i]);
    }
}

$listNumbers = array_merge($listNumbersSorted, $listNumbers);

for($i = 0, $list0Numbers = []; $i < count($listNumbers); $i++){
    if($listNumbers[$i] < 10 && !in_array($listNumbers[$i], $list0Numbers, true)){
        $list0Numbers[] = $listNumbers[$i];
        $listNumbers[$i] = "0" . $listNumbers[$i];
    }
}

print_r($listNumbers);
print_r(count($listNumbers));
//---------------------Done Bài 1---------------------