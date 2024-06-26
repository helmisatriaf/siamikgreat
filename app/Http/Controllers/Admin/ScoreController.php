<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Teacher_subject;
use App\Models\Teacher_grade;
use App\Models\Grade_subject;
use App\Models\Grade_exam;
use App\Models\Subject_exam;
use App\Models\Exam_relation;
use App\Models\Type_exam;
use App\Models\Score;
use App\Models\Student_exam;


use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
   public function score($id)
   {
      try {
         session()->flash('page',  $page = (object)[
            'page' => 'exams',
            'child' => 'database exams score',
         ]);

         $data = Exam::join('grade_exams', 'exams.id', '=', 'grade_exams.exam_id')
               ->join('grades', 'grade_exams.grade_id', '=', 'grades.id')
               ->join('subject_exams', 'exams.id', '=', 'subject_exams.exam_id')
               ->join('subjects', 'subject_exams.subject_id', '=', 'subjects.id')
               ->join('teachers', 'exams.teacher_id', '=', 'teachers.id')
               ->join('type_exams', 'exams.type_exam', '=', 'type_exams.id')
               ->join('student_exams', 'exams.id', '=', 'student_exams.exam_id')
               ->join('students', 'student_exams.student_id', '=', 'students.id')
               ->join('scores', function($join) {
                  $join->on('student_exams.student_id', '=', 'scores.student_id')
                     ->on('exams.id', '=', 'scores.exam_id');
               })
               ->where('exams.id', $id, 'exams.is_active')
               ->select('exams.id as exam_id', 'exams.name_exam as exam_name', 'exams.date_exam as date_exam',
               'grades.id as grade_id','grades.name as grade_name', 'grades.class as grade_class',
               'subjects.name_subject as subject_name', 'subjects.id as subject_id',
               'teachers.name as teacher_name', 'teachers.id as teacher_id', 
               'type_exams.name as type_exam', 'type_exams.id as type_exam_id',
               'students.id as student_id', 'students.name as student_name',
               'scores.score as score')
               ->get();

         return view('components.exam.data-exam-score')->with('data', $data);

      } catch (Exception $err) {
         return dd($err);
      }
   }
  
   public function actionUpdateScore(Request $request)
   {
      // DB::beginTransaction();
      try {
         session()->flash('page',  $page = (object)[
            'page' => 'exams',
            'child' => 'database exams',
         ]);

         $rules = [
            'exam_id' => $request->exam_id,
            'subject_id' => $request->subject_id,
            'grade_id' => $request->grade_id,
            'teacher_id' => $request->teacher_id,
            'type_exam_id' => $request->type_exam_id,
            'student_id' => $request->student_id,
            'score' => $request->score,
            'created_at' => now(),
         ];

         // $validator = Validator::make($rules, [
         //       'type_exam' => 'required|string',
         //       'name_exam' => 'required|string',
         //       'materi' => 'required|string',
         //       'teacher_id' => 'required|string',
         //    ],
         // );

         // if($validator->fails())
         // {
         //    DB::rollBack();
         //    return redirect('/admin/exams/create')->withErrors($validator->messages())->withInput($rules);
         // }

         $student = $request->student_id;
         $score = $request->score;
         
         for ($i=0; $i < sizeof($student); $i++) { 
            $post = [
               'score' => $score[$i],
               'updated_at' => now(),
            ];

            Score::where('student_id', $student[$i])->where('exam_id', $request->exam_id)->update($post);
         }

         Exam::where('id', $request->exam_id)->update(['is_active' => 0]);

         session()->flash('after_update_score');

         if (session('role') == 'superadmin') {
            return redirect('/superadmin/exams');
         }
         elseif (session('role') == 'admin') {
            return redirect('/admin/exams');
         }
         elseif (session('role') == 'teacher') {
            return redirect('/teacher/dashboard/exam/score/' . $request->exam_id);
         }

      } catch (Exception $err) {
         DB::rollBack();
         return dd($err);
      }
   }
}