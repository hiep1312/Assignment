# 📚 Bài Tập PHP - Tổng Hợp 6 Bài
## 📝 Bài 1: Xử lý mảng số ngẫu nhiên và định dạng

**Mô tả ngắn gọn**  
- Tạo mảng `$listNumbers` gồm 100 số ngẫu nhiên từ 1–50.  
- Sắp xếp giảm dần, lấy 50 phần tử lớn nhất.  
- Xóa khỏi mảng gốc các phần tử đã chọn, ghép lại mảng.  
- Thêm số `0` trước các số < 10 (tránh trùng lặp).  
- In kết quả và đếm số phần tử.

### 📜 Code

```php
<?php
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
for($i = 0; $i < $length && !empty($listNumbersSortedTemp); $i++){
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
```

### 🖥️ Kết quả
```
Array
(
    [0] => 50
    [1] => 50
    [2] => 49
    [3] => 48
    ...
    [95] => 05
    [96] => 07
    [97] => 08
    [98] => 09
    [99] => 03
)
100
```

## 📝 Bài 2: Tìm phần tử trùng và không trùng giữa hai mảng

**Mô tả ngắn gọn**  
- Tạo hai mảng:  
  - `$list100Numbers`: gồm 100 phần tử (cắt ra từ 150 số ngẫu nhiên).  
  - `$list150Numbers`: gồm 150 số ngẫu nhiên.  
- Xác định **các phần tử trùng nhau** và **không trùng nhau** giữa hai mảng.  
- Có hai cách làm: dùng hàm tích hợp (`array_intersect`, `array_diff`) hoặc vòng lặp thủ công.

### 📜 Code

```php
<?php
$list100Numbers = [];
$list150Numbers = [];

for($i = 0; $i < 150; $i++){
    if($i < 100) $list100Numbers[] = rand(6, 666);
    $list150Numbers[] = rand(6, 666);
};

//Cách 1:
print_r(array_intersect($list100Numbers, $list150Numbers));
print_r(array_diff($list100Numbers, $list150Numbers));

//Cách 2:
//Các phần tử trùng nhau:
$overlapItems = [];
for($i = 0; $i < count($list100Numbers); $i++){
    if(in_array($list100Numbers[$i], $list150Numbers, true) && !in_array($list100Numbers[$i], $overlapItems, true)){
        $overlapItems[] = $list100Numbers[$i];
    }
}
print_r($overlapItems);

//Các phần tử không trùng nhau:
$nonOverlapItems = [];
for($i = 0; $i < count($list100Numbers); $i++){
    if(!in_array($list100Numbers[$i], $list150Numbers, true) && !in_array($list100Numbers[$i], $nonOverlapItems, true)){
        $nonOverlapItems[] = $list100Numbers[$i];
    }
}
print_r($nonOverlapItems);
```

### 🖥️ Kết quả
```
Array
(
    [0] => 432
    [1] => 210
    [2] => 666
    ...
)
Array
(
    [0] => 111
    [1] => 345
    ...
)
```

## 📝 Bài 3: Chuyển đổi chuỗi ánh xạ thành mảng nhóm

**Mô tả ngắn gọn**  
- Chuỗi ban đầu `$stringOrigin` chứa nhiều cặp `ITxxx => ITSUxxxx` cách nhau bằng xuống dòng.  
- Mục tiêu: Tách chuỗi thành mảng `$arrayResult` sao cho **mỗi key** (`IT001`) chứa **danh sách tất cả giá trị** `ITSUxxxx`.  
- Nếu key chưa tồn tại, thêm mới. Nếu đã tồn tại, gộp thêm giá trị vào mảng của key đó.

### 📜 Code

```php
<?php
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
    if(array_key_exists($arraySeparation[0], $arrayResult)){
        $arrayCurrent = is_array($arrayResult[$arraySeparation[0]]) ? $arrayResult[$arraySeparation[0]] : [$arrayResult[$arraySeparation[0]]];
        $arrayResult[$arraySeparation[0]] = array_merge($arrayCurrent, [$arraySeparation[1]]);
    }else{
        $arrayResult[$arraySeparation[0]] = $arraySeparation[1];
    }
}

print_r($arrayResult);
```

### 🖥️ Kết quả
```
Array
(
    [IT001] => Array
        (
            [0] => ITSU0005
            [1] => ITSU0017
            [2] => ITSU0021
            [3] => ITSU0026
            [4] => ITSU0015
            [5] => ITSU0025
        )
)
```

## 📝 Bài 4: Phân tích mảng số ngẫu nhiên (20 phần tử)

**Mô tả ngắn gọn**  
- Sinh mảng `$list20Numbers` gồm **20 số ngẫu nhiên** từ 1–100.  
- In mảng gốc.  
- Tìm **số lớn nhất**, **số nhỏ nhất**, **giá trị trung bình** bằng hai cách:  
  - **Cách 1:** Dùng hàm tích hợp `max`, `min`, `array_sum`.  
  - **Cách 2:** Duyệt mảng thủ công để tìm giá trị.  
- Đếm số **chẵn** và **lẻ** trong mảng.

### 📜 Code

```php
<?php
$list20Numbers = [];
for($i = 0; $i < 20; $i++){
    $list20Numbers[] = rand(1, 100);
};

//In ra mảng gốc:
print_r($list20Numbers);

//In ra số lớn nhất, nhỏ nhất, giá trị trung bình:
//Cách 1:
echo "Số lớn nhất trong mảng: " . max($list20Numbers) . PHP_EOL;
echo "Số nhỏ nhất trong mảng: " . min($list20Numbers) . PHP_EOL;
echo "Giá trị trung bình trong mảng: " . (array_sum($list20Numbers) / count($list20Numbers)) . PHP_EOL;

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
```

### 🖥️ Kết quả
```
Array
(
    [0] => 87
    [1] => 12
    [2] => 54
    [3] => 99
    [4] => 6
    ...
)
Số lớn nhất trong mảng: 99
Số nhỏ nhất trong mảng: 6
Giá trị trung bình trong mảng: 52.4
Số phần tử chẵn trong mảng: 8
Số phần tử lẻ trong mảng: 12
```

## 📝 Bài 5: Xử lý mảng (xóa trùng lặp, tính tổng, đảo ngược)

**Mô tả ngắn gọn**  
- Mảng ban đầu `$arr = [1, 2, 2, 3, 4, 4, 5, 6]`.  
- Thực hiện:  
  1. **Xóa phần tử trùng lặp**.  
  2. **Tính tổng** các phần tử còn lại.  
  3. **Đảo ngược** thứ tự mảng.  
- Thực hiện bằng hai cách:  
  - **Cách 1:** Dùng các hàm tích hợp (`array_unique`, `array_sum`, `array_reverse`).  
  - **Cách 2:** Duyệt thủ công, vừa loại bỏ trùng vừa cộng dồn và đảo mảng.

### 📜 Code

```php
<?php
$arr = [1, 2, 2, 3, 4, 4, 5, 6];
//Xóa trùng lặp, tính tổng, đảo ngược mảng:
//Cách 1:
$arrUnique = array_unique($arr);
$arrSum = array_sum($arrUnique);
$arrReverse = array_reverse($arrUnique);

echo "Mảng sau khi xóa trùng lặp: [" . implode(", ", $arrUnique) . "]" . PHP_EOL;
echo "Tính tổng mảng: " . $arrSum . PHP_EOL;
echo "Mảng sau khi đảo ngược: [" . implode(", ", $arrReverse). "]" . PHP_EOL;

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
```

### 🖥️ Kết quả
```
Mảng sau khi xóa trùng lặp: [1, 2, 3, 4, 5, 6]
Tính tổng mảng: 21
Mảng sau khi đảo ngược: [6, 5, 4, 3, 2, 1]
```

## 📝 Bài 6: Quản lý danh sách sinh viên

**Mô tả ngắn gọn**  
- Định nghĩa lớp `Student` với các thuộc tính: `id`, `name`, `age`, `score`.  
- Tạo danh sách 5 sinh viên với thông tin khác nhau.  
- Thực hiện các yêu cầu:  
  1. **Tìm sinh viên có điểm cao nhất**.  
  2. **Sắp xếp danh sách sinh viên theo tuổi tăng dần**.  
- Có hai cách sắp xếp:  
  - **Cách 1:** Dùng `usort` với toán tử so sánh.  
  - **Cách 2:** Dùng thuật toán **bubble sort** thủ công.

### 📜 Code

```php
<?php
class Student{
    public function __construct(
        public readonly int|string $id, 
        public readonly string $name,
        public readonly int $age,
        public readonly float $score
    ){}
}

$students = [
    new Student(1001, "Nguyễn Văn An", 20, 8.5),
    new Student(1002, "Trần Thị Bích", 17, 7.8),
    new Student(1003, "Lê Minh Quang", 22, 9.1),
    new Student(1004, "Phạm Hoàng Duy", 19, 6.9),
    new Student(1005, "Đỗ Thùy Trang", 18, 8.2),
];

// Tìm sinh viên có điểm cao nhất
$bestStudent = null;
foreach($students as $student){
    if($bestStudent === null || $bestStudent?->score < $student->score){
        $bestStudent = $student;
    }
}

echo "Sinh viên có điểm cao nhất: " . $bestStudent->name . PHP_EOL;
echo "Id: " . $bestStudent->id . PHP_EOL;
echo "Tuổi: " . $bestStudent->age . PHP_EOL;
echo "Điểm: " . $bestStudent->score . PHP_EOL;

//Sắp xếp sinh viên theo tuổi tăng dần:
//Cách 1:
$studentsSorted = $students;
usort($studentsSorted, fn(Student $studentA, Student $studentB) => $studentA->age <=> $studentB->age);
print_r($studentsSorted);

//Cách 2: (Thuật toán bubble sort)
$studentsSorted = $students;
for($i = 0, $length = count($studentsSorted); $i < $length; $i++){
    for($j = 0; $j < $length - $i - 1; $j++){
        if($studentsSorted[$j]->age > $studentsSorted[$j + 1]->age){
            $tempData = $studentsSorted[$j + 1];
            $studentsSorted[$j + 1] = $studentsSorted[$j];
            $studentsSorted[$j] = $tempData;
        }
    }
}

print_r($studentsSorted);
```

### 🖥️ Kết quả
```
Sinh viên có điểm cao nhất: Lê Minh Quang
Id: 1003
Tuổi: 22
Điểm: 9.1
Array
(
    [0] => Student Object ( [id] => 1002 [name] => Trần Thị Bích [age] => 17 [score] => 7.8 )
    [1] => Student Object ( [id] => 1005 [name] => Đỗ Thùy Trang [age] => 18 [score] => 8.2 )
    [2] => Student Object ( [id] => 1004 [name] => Phạm Hoàng Duy [age] => 19 [score] => 6.9 )
    [3] => Student Object ( [id] => 1001 [name] => Nguyễn Văn An [age] => 20 [score] => 8.5 )
    [4] => Student Object ( [id] => 1003 [name] => Lê Minh Quang [age] => 22 [score] => 9.1 )
)
```