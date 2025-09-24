<?php

//---------------------Bài 1:---------------------
$listNumbers = [];
for($i = 0; $i < 100; $i++){
    $listNumbers[] = rand(1, 50);
};
// print_r($listNumbers);

$listNumbersSorted = $listNumbers;
rsort($listNumbersSorted);
array_splice($listNumbersSorted, 50, 100);

$length = count($listNumbers);
$listNumbersSortedTemp = $listNumbersSorted;
for($i = 0; $i < $length; $i++){
    if(in_array($listNumbers[$i], $listNumbersSortedTemp, true)){
        $keyCurrent = array_search($listNumbers[$i], $listNumbersSortedTemp, true);
        unset($listNumbersSortedTemp[$keyCurrent]);
        unset($listNumbers[$i]);
    }
}

$listNumbers = array_merge($listNumbersSorted, $listNumbers);

$list0Numbers = [];
for($i = 0; $i < count($listNumbers); $i++){
    if($listNumbers[$i] < 10 && !in_array($listNumbers[$i], $list0Numbers, true)){
        $list0Numbers[] = $listNumbers[$i];
        $listNumbers[$i] = "0" . $listNumbers[$i];
    }
}

print_r($listNumbers);
print_r(count($listNumbers));
//---------------------Done Bài 1---------------------