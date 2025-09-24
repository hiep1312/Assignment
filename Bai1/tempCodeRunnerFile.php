<?php
$arrUnique = array_unique($arr);
$arrSum = array_sum($arr);
$arrReverse = array_reverse($arr);

echo "Mảng sau khi xóa trùng lặp: [" . implode(", ", $arrUnique) . "]" . PHP_EOL;
echo "Tính tổng mảng: " . $arrSum . PHP_EOL;
echo "Mảng sau khi đảo ngược: [" . implode(", ", $arrReverse). "]" . PHP_EOL;