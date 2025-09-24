<?php

//---------------------Bài 5:---------------------
$arr = [1, 2, 2, 3, 4, 4, 5, 6];
//Xóa trùng lặp, tính tổng, đảo ngược mảng:
//Cách 1:
/* $arrUnique = array_unique($arr);
$arrSum = array_sum($arrUnique);
$arrReverse = array_reverse($arrUnique);

echo "Mảng sau khi xóa trùng lặp: [" . implode(", ", $arrUnique) . "]" . PHP_EOL;
echo "Tính tổng mảng: " . $arrSum . PHP_EOL;
echo "Mảng sau khi đảo ngược: [" . implode(", ", $arrReverse). "]" . PHP_EOL; */

//Cách 2:
$arrUniqueNew = [];
$arrSumNew = 0;
$arrReverseNew = [];
for($i = 0; $i < count($arr); $i++){
    if(!in_array($arr[$i], $arrUniqueNew, true)){
        $arrUniqueNew[] = $arr[$i];
        $arrSumNew += $arr[$i];
        array_unshift($arrReverseNew, $arr[$i]);
    }
}

echo "Mảng sau khi xóa trùng lặp: [" . implode(", ", $arrUniqueNew) . "]" . PHP_EOL;
echo "Tính tổng mảng: " . $arrSumNew . PHP_EOL;
echo "Mảng sau khi đảo ngược: [" . implode(", ", $arrReverseNew). "]" . PHP_EOL;
//---------------------Done Bài 5---------------------