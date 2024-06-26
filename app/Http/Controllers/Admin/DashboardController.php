<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher_grade;
use App\Models\Teacher_subject;
use App\Models\Grade_exam;
use App\Models\Grade_subject;
use App\Models\Subject_exam;
use App\Models\Exam;
use App\Models\Student_relationship;
use App\Models\Relationship;
use App\Models\Attendance;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   
   public function index()
   {
      try {
         //code...
         session()->flash('page',  $page = (object)[
            'page' => 'dashboard',
            'child' => 'dashboard',
         ]);
      
         $checkRole = session('role');
         // dd(session('id_user'));

         if($checkRole == 'admin' ||$checkRole == 'superadmin')
         {
            $totalStudent  = Student::where('is_active', true)->orderBy('created_at', 'desc')->get()->count('id');
            $totalTeacher  = Teacher::where('is_active', true)->get()->count('id');
            $totalGrade    = Grade::all()->count('id');
            $totalExam     = Exam::where('is_active', true)->get()->count('id');
            
            $studentData   = Student::where('is_active', true)
            ->join('grades', 'grades.id', '=', 'students.grade_id')
            ->select('students.*', 'grades.name as grade_name', 'grades.class as grade_class')
            ->take(6)
            ->get();
            
            $teacherData   = Teacher::where('is_active', true)->take(6)->get();
            
            $examData  = Grade_exam::join('grades', 'grades.id', '=', 'grade_exams.grade_id')
               ->join('exams', 'exams.id', '=', 'grade_exams.exam_id')
               ->join('type_exams', 'type_exams.id', '=', 'exams.type_exam')
               ->select('exams.*', 'type_exams.name as type_exam_name', 'grades.name as grade_name', 'grades.class as grade_class')
               ->get();

            foreach ($examData as $ed ) {
               $ed->subject = Subject_exam::join('subjects', 'subjects.id', '=', 'subject_exams.subject_id')
               ->where('exam_id', $ed->id)
               ->value('name_subject');
            };

            $gradeData     = Grade::all();
            $subjectData   = Subject::all();

            $data = [
               'totalStudent' => (int)$totalStudent,
               'totalTeacher' => (int)$totalTeacher,
               'totalGrade'   => (int)$totalGrade,
               'totalExam'    => (int)$totalExam,
               'grade' => $gradeData,
               'subject' => $subjectData,
               'exam' => $examData,
               'dataTeacher' => $teacherData,
               'dataStudent' => $studentData,
            ];

            // dd($data);
            return view('components.dashboardtes')->with('data', $data);
         }
         if($checkRole == 'teacher')
         {
            $id = Teacher::where('user_id', session('id_user'))->value('id');
            
            $dataTeacher  = Teacher::where('id', $id)->get();
            $gradeTeacher = Teacher_grade::join('grades', 'teacher_grades.grade_id', '=', 'grades.id')
               ->where('teacher_id', $id)
               ->select('grades.*')
               ->get();
            
            $teacherSubject = Teacher_subject::join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
               ->where('teacher_id', $id)
               ->select('subjects.*')
               ->get();

            $dataExam  = Grade_exam::join('grades', 'grades.id', '=', 'grade_exams.grade_id')
               ->join('exams', 'exams.id', '=', 'grade_exams.exam_id')
               ->join('type_exams', 'type_exams.id', '=', 'exams.type_exam')
               ->select('exams.*', 'type_exams.name as type_exam_name', 'grades.name as grade_name', 'grades.class as grade_class')
               ->where('exams.teacher_id', $id)
               ->get();

            foreach ($dataExam as $ed ) {
               $ed->subject = Subject_exam::join('subjects', 'subjects.id', '=', 'subject_exams.subject_id')
               ->where('exam_id', $ed->id)
               ->value('name_subject');
            };

            $student = Teacher_grade::join('students','students.grade_id', '=', 'teacher_grades.grade_id')
               ->join('grades', 'grades.id', '=', 'students.grade_id')
               ->where('teacher_id', $id)
               ->select('students.*', 'grades.name as grade_name', 'grades.class as grade_class')
               ->get();

            $totalStudent = Teacher_grade::join('students','students.grade_id', '=', 'teacher_grades.grade_id')
               ->where('teacher_id', $id)
               ->get()
               ->count('id');

            $totalExam      = Exam::where('teacher_id', $id)->get()->count('id');
            $totalGrade     = Teacher_grade::where('teacher_id', $id)->get()->count('id');
            $totalSubject   = Teacher_subject::where('teacher_id', $id)->get()->count('id');
            
            $data = [
               'dataTeacher'    => $dataTeacher,
               'gradeTeacher'   => $gradeTeacher,
               'teacherSubject' => $teacherSubject,
               'student'        => $student,
               'exam'           => $dataExam,
               'totalStudent'   => (int)$totalStudent,
               'totalExam'      => (int)$totalExam,
               'totalGrade'     => (int)$totalGrade,
               'totalSubject'   => (int)$totalSubject,
            ];

            // dd($data);

            return view('components.dashboard-teacher')->with('data',$data);
         }
         if($checkRole == 'student')
         {
            $id = Student::where('user_id', session('id_user'))->value('id');
            $gradeIdStudent = Student::where('user_id', session('id_user'))->value('grade_id');


            $dataStudent       = Grade::with(['subject', 'exam', 'teacher', 'student'])->where('id', $gradeIdStudent)->first();
            $totalExam         = Grade_exam::join('exams', 'exams.id', '=', 'grade_exams.exam_id')
            ->where('grade_id', $gradeIdStudent)
            ->where('exams.is_active', 1)
            ->get()
            ->count('id');
            $totalSubject      = Grade_subject::where('grade_id', $gradeIdStudent)->get()->count('id');
            $totalStudentGrade = Student::where('grade_id', $gradeIdStudent)->get()->count('id');
            $totalAbsent       = Attendance::where('student_id', $id)
            ->where('present', 0)
            ->get()
            ->count();

            
            $dataExam  = Grade_exam::join('grades', 'grades.id', '=', 'grade_exams.grade_id')
               ->join('exams', 'exams.id', '=', 'grade_exams.exam_id')
               ->join('type_exams', 'type_exams.id', '=', 'exams.type_exam')
               ->select('exams.*', 'type_exams.name as type_exam_name', 'grades.name as grade_name', 'grades.class as grade_class')
               ->where('grades.id', $gradeIdStudent)
               ->get();

            foreach ($dataExam as $ed ) {
               $ed->subject = Subject_exam::join('subjects', 'subjects.id', '=', 'subject_exams.subject_id')
               ->where('exam_id', $ed->id)
               ->value('name_subject');
            };

            $data = [
               'dataStudent'  => $dataStudent,
               'exam'         => $dataExam,
               'totalExam'    => (int)$totalExam,
               'totalSubject' => (int)$totalSubject,
               'totalStudent' => (int)$totalStudentGrade, 
               'totalAbsent'  => (int)$totalAbsent,  
            ];

            // dd($data);

            return view('components.dashboard-student')->with('data',$data);
         }
         if($checkRole == 'parent')
         {
            $id                = Relationship::where('user_id', session('id_user'))->value('id');
            $getIdStudent      = Student_relationship::where('relationship_id', $id)->value('student_id');
            $gradeIdStudent    = Student::where('id', $getIdStudent)->value('grade_id');

            $dataStudent       = Grade::with(['subject', 'exam', 'teacher', 'student'])->where('id', $gradeIdStudent)->first();
            $totalExam         = Grade_exam::where('grade_id', $gradeIdStudent)->get()->count('id');
            $totalSubject      = Grade_subject::where('grade_id', $gradeIdStudent)->get()->count('id');
            $totalStudentGrade = Student::where('grade_id', $gradeIdStudent)->get()->count('id');
            
            $dataExam  = Grade_exam::join('grades', 'grades.id', '=', 'grade_exams.grade_id')
               ->join('exams', 'exams.id', '=', 'grade_exams.exam_id')
               ->join('type_exams', 'type_exams.id', '=', 'exams.type_exam')
               ->select('exams.*', 'type_exams.name as type_exam_name', 'grades.name as grade_name', 'grades.class as grade_class')
               ->where('grades.id', $gradeIdStudent)
               ->get();

            foreach ($dataExam as $ed ) {
               $ed->subject = Subject_exam::join('subjects', 'subjects.id', '=', 'subject_exams.subject_id')
               ->where('exam_id', $ed->id)
               ->value('name_subject');
            };

            $data = [
               'dataStudent'    => $dataStudent,
               'exam'           => $dataExam,
               'totalExam'      => (int)$totalExam,
               'totalSubject'   => (int)$totalSubject,
               'totalStudent'   => (int)$totalStudentGrade, 
            ];

            // dd($data);

            return view('components.dashboard-parent')->with('data',$data);
         }

      } catch (Exception $err) {
         return dd($err);
      }
   }
}