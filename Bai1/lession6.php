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
    new Student(id: 1001, name: "Nguyễn Văn An", age: 20, score: 8.5),
    new Student(id: 1002, name: "Trần Thị Bích", age: 17, score: 7.8),
    new Student(id: 1003, name: "Lê Minh Quang", age: 22, score: 9.1),
    new Student(id: 1004, name: "Phạm Hoàng Duy", age: 19, score: 6.9),
    new Student(id: 1005, name: "Đỗ Thùy Trang", age: 18, score: 8.2),
];

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
/* $studentsSorted = $students;
for($i = 0, $length = count($studentsSorted); $i < $length; $i++){
    for($j = 0; $j < $length - $i - 1; $j++){
        if($studentsSorted[$j]->age > $studentsSorted[$j + 1]->age){
            $tempData = $studentsSorted[$j + 1];
            $studentsSorted[$j + 1] = $studentsSorted[$j];
            $studentsSorted[$j] = $tempData;
        }
    }
} */

print_r($studentsSorted);
//---------------------Done Bài 6---------------------