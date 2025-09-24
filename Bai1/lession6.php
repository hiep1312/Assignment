<?php

//---------------------Bài 6:---------------------
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

$bestStudent = null;

foreach($students as $student){
    if($bestStudent === null || $bestStudent?->score < $student->score):
        $bestStudent = $student;
    endif;
}

echo "Sinh viên có điểm cao nhất: " . $bestStudent->name . PHP_EOL;
echo "Id: " . $bestStudent->id . PHP_EOL;
echo "Tuổi: " . $bestStudent->age . PHP_EOL;
echo "Điểm: " . $bestStudent->score . PHP_EOL;

//Sắp xếp sinh viên theo tuổi tăng dần:
//Cách 1:
/* $studentsSorted = $students;
usort($studentsSorted, fn(Student $studentA, Student $studentB) => $studentA->age <=> $studentB->age);
print_r($studentsSorted); */

//Cách 2: (Thuật toán bubble sort)
$studentsSorted = $students;
for($i = 0, $length = count($studentsSorted); $i < $length; $i++){
    for($j = 0; $j < $length - $i - 1; $j++){
        if($studentsSorted[$j]->age > $studentsSorted[$j + 1]->age):
            list($studentsSorted[$j + 1], $studentsSorted[$j]) = array($studentsSorted[$j], $studentsSorted[$j + 1]);
        endif;
    }
}

print_r($studentsSorted);
//---------------------Done Bài 6---------------------