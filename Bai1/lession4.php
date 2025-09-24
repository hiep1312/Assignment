<?php

//---------------------Bài 4:---------------------
$list20Numbers = [];
for($i = 0; $i < 20; $i++, $list20Numbers[] = rand(1, 100));

//In ra mảng gốc:
print_r($list20Numbers);

//In ra số lớn nhất, nhỏ nhất, giá trị trung bình:
//Cách 1:
/* echo "Số lớn nhất trong mảng: " . max($list20Numbers) . PHP_EOL;
echo "Số nhỏ nhất trong mảng: " . min($list20Numbers) . PHP_EOL;
echo "Giá trị trung bình trong mảng: " . (array_sum($list20Numbers) / count($list20Numbers)) . PHP_EOL; */

//Cách 2:
$valueBiggest = $valueSmallest = $averageValue = ($list20Numbers[0] ?? 0);

for($i = 1; $i < count($list20Numbers); $i++){
    //Kiểm tra và lấy giá trị lớn nhất và nhỏ nhất:
    if($valueBiggest < $list20Numbers[$i]) $valueBiggest = $list20Numbers[$i];
    if($valueSmallest > $list20Numbers[$i]) $valueSmallest = $list20Numbers[$i];

    //Tính giá trị trung bình:
    $averageValue += $list20Numbers[$i];
}
$averageValue = $averageValue / (count($list20Numbers) ?: 1);

echo "Số lớn nhất trong mảng: " . $valueBiggest . PHP_EOL;
echo "Số nhỏ nhất trong mảng: " . $valueSmallest . PHP_EOL;
echo "Giá trị trung bình trong mảng: " . $averageValue . PHP_EOL;

//Đếm số chẵn và số lẻ trong mảng:
$numberEven = $numberOdd = 0;
for($i = 0; $i < count($list20Numbers); $i++){
    if($list20Numbers[$i] % 2 === 0) $numberEven++;
    else $numberOdd++;
}

echo "Số phần tử chẵn trong mảng: " . $numberEven . PHP_EOL;
echo "Số phần tử lẻ trong mảng: " . $numberOdd . PHP_EOL;
//---------------------Done Bài 4---------------------