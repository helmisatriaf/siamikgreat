<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Teacher_subject;
use App\Models\Grade_subject;
use App\Models\Subject_exam;
use App\Models\Exam;
use App\Models\Grade_exam;
use App\Models\Score;
use App\Models\Acar;
use App\Models\Comment;
use App\Models\Acar_comment;
use App\Models\Sooa_primary;
use App\Models\Sooa_secondary;
use App\Models\Report_card;
use App\Models\Report_card_status;
use App\Models\Scoring_status;
use App\Models\Acar_status;
use App\Models\Sooa_status;
use App\Models\Score_attendance;
use App\Models\Score_attendance_status;
use App\Models\Nursery_toddler;
use App\Models\Kindergarten;

use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScoringController extends Controller
{
    public function actionPostMajorPrimary(Request $request){
        try {
            $type = "major_subject_assessment";

            // dd($request);
            for($i=0; $i < count($request->student_id); $i++){
                $final_score = round($request->final_score[$i]);
    
                $grade = $this->determineGrade($final_score);

                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $request->subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'comment' => $request->comment[$i],
                    'grades' => $grade,
                    'final_score' => $request->final_score[$i],
                ];

                $comment = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $request->subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'type' => $type,
                    'comment' => $request->comment[$i],
                ];
                
                Acar::create($scoring);
                Comment::create($comment);
            }

            $status = [
                'grade_id' => $request->grade_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->subject_teacher,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Scoring_status::create($status);

            session()->flash('after_post_final_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostMinorPrimary(Request $request){
        try {
            $type = "minor_subject_assessment";
            
            $subject = Subject::where('id', $request->subject_id)->value('name_subject');
            $getReligionId = Subject::where('name_subject', '=', 'religion')->value('id');

            if (strtolower($subject) == "religion islamic" || 
                strtolower($subject) == "religion catholic" || 
                strtolower($subject) == "religion christian" || 
                strtolower($subject) == "religion buddhism" || 
                strtolower($subject) == "religion hinduism" || 
                strtolower($subject) == "religion confucianism") {
                $subject_id = $getReligionId;
            }

            for($i=0; $i < count($request->student_id); $i++){
                $final_score = round($request->final_score[$i]);

                $grade = $this->determineGrade($final_score);

                // dd($grade);

                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'comment' => $request->comment[$i],
                    'grades' => $grade,
                    'final_score' => $request->final_score[$i],
                ];

                $comment = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $request->subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'type' => $type,
                    'comment' => $request->comment[$i],
                ];

                Acar::create($scoring);
                Comment::create($comment);
            }

            $status = [
                'grade_id' => $request->grade_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->subject_teacher,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Scoring_status::create($status);

            session()->flash('after_post_final_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostSecondary(Request $request){
        try {
            $type = "subject_assessment_secondary";

            $checkSubject = Subject::where('id', $request->subject_id)->value('name_subject');

            if (strtolower($checkSubject) == "chinese higher" || strtolower($checkSubject) == "chinese lower") {
                $subject = Subject::where('id', $request->subject_id)->value('name_subject');
                $getChineseId = Subject::where('name_subject', '=', 'chinese')->value('id');
    
                if (strtolower($subject) == "chinese lower" || 
                    strtolower($subject) == "chinese higher") {
                    $subject_id = $getChineseId;
                }
            }
            elseif (strtolower($checkSubject) == "religion islamic" || 
                    strtolower($checkSubject) == "religion catholic" || 
                    strtolower($checkSubject) == "religion christian" || 
                    strtolower($checkSubject) == "religion buddhism" || 
                    strtolower($checkSubject) == "religion hinduism" || 
                    strtolower($checkSubject) == "religion confucianism") {
                $subject = Subject::where('id', $request->subject_id)->value('name_subject');
                $getReligionId = Subject::where('name_subject', '=', 'religion')->value('id');
    
                if (strtolower($subject) == "religion islamic" || 
                    strtolower($subject) == "religion catholic" || 
                    strtolower($subject) == "religion christian" || 
                    strtolower($subject) == "religion buddhism" || 
                    strtolower($subject) == "religion hinduism" || 
                    strtolower($subject) == "religion confucianism") {
                    $subject_id = $getReligionId;
                }
            }


            // dd($subject_id);
            
            for($i=0; $i < count($request->student_id); $i++){
                $final_score = round($request->final_score[$i]);
    
                $grade = $this->determineGrade($final_score);

                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'comment' => $request->comment[$i],
                    'grades' => $grade,
                    'final_score' => $request->final_score[$i],
                ];

                $comment = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'subject_id' => $request->subject_id,
                    'subject_teacher_id' => $request->subject_teacher,
                    'semester' => $request->semester,
                    'type' => $type,
                    'comment' => $request->comment[$i],
                ];
                
                Acar::create($scoring);
                Comment::create($comment);
            }

            $status = [
                'grade_id' => $request->grade_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->subject_teacher,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Scoring_status::create($status);

            session()->flash('after_post_final_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostAcarPrimary(Request $request){
        try {
            // dd($request);
            for($i=0; $i < count($request->student_id); $i++){
                $final_score = $request->final_score[$i];
    
                $grade = $this->determineGrade($final_score);
                $type = "academic_assessment_report";
    
                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->class_teacher,
                    'semester' => $request->semester,
                    'academic' => $request->final_score[$i],
                    'grades_academic' => $grade,
                ];
    
                $comment = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->class_teacher,
                    'semester' => $request->semester,
                    'type' => $type,
                    'comment' => $request->comment[$i],
                ];
    
                Sooa_primary::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $scoring
                );

                Acar_comment::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $comment
                );
            }

            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->class_teacher,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Acar_status::create($status);

            session()->flash('after_post_final_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
       
    }

    public function actionPostAcarSecondary(Request $request){
        try {
            for($i=0; $i < count($request->student_id); $i++){
                $final_score = $request->final_score[$i];
    
                $grade = $this->determineGrade($final_score);
                $type = "academic_assessment_report";
    
                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->class_teacher,
                    'semester' => $request->semester,
                    'academic' => $request->final_score[$i],
                    'grades_academic' => $grade,
                ];
    
                $comment = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->class_teacher,
                    'semester' => $request->semester,
                    'type' => $type,
                    'comment' => $request->comment[$i],
                ];
    
                Sooa_secondary::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $scoring
                );

                Acar_comment::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $comment
                );
            }

            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->class_teacher,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Acar_status::create($status);

            session()->flash('after_post_final_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostSooaPrimary(Request $request){
        try {
            for($i=0; $i < count($request->student_id); $i++){
                $academic = Sooa_primary::where('sooa_primaries.grade_id', $request->grade_id)
                    ->where('sooa_primaries.class_teacher_id', $request->class_teacher)
                    ->where('student_id', $request->student_id[$i])
                    ->value('academic');

                $attendance = Sooa_primary::where('sooa_primaries.grade_id', $request->grade_id)
                    ->where('sooa_primaries.class_teacher_id', $request->class_teacher)
                    ->where('student_id', $request->student_id[$i])
                    ->value('attendance');

                if ($request->choice[$i] == 0) {
                    $final_score = ($academic  
                    + $request->language_and_art[$i] 
                    + $request->self_development[$i] 
                    + $request->eca_aver[$i] 
                    + $request->behavior[$i] 
                    + $attendance 
                    + $request->participation[$i]) / 7;

                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->class_teacher,
                        'semester' => $request->semester,
                        'choice' => 0,
                        'grades_choice' => "-",
                        'language_and_art' => $request->language_and_art[$i],
                        'grades_language_and_art' => $this->determineGrade($request->language_and_art[$i]),
                        'self_development' => $request->self_development[$i],
                        'grades_self_development' => $this->determineGrade($request->self_development[$i]),
                        'eca_aver' => $request->eca_aver[$i],
                        'grades_eca_aver' => $this->determineGrade($request->eca_aver[$i]),
                        'behavior' => $request->behavior[$i],
                        'grades_behavior' => $this->determineGrade($request->behavior[$i]),
                        'participation' => $request->participation[$i],
                        'grades_participation' => $this->determineGrade($request->participation[$i]),
                        'final_score' => round($final_score),
                        'grades_final_score' => $this->determineGrade($final_score),
                        'created_at' => now()
                    ];
                } 
                elseif (!empty($request->choice[$i])) {
                    $final_score = ($academic 
                    + $request->choice[$i] 
                    + $request->language_and_art[$i] 
                    + $request->self_development[$i] 
                    + $request->eca_aver[$i] 
                    + $request->behavior[$i] 
                    + $attendance 
                    + $request->participation[$i]) / 8;

                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->class_teacher,
                        'semester' => $request->semester,
                        'choice' => $request->choice[$i],
                        'grades_choice' => $this->determineGrade($request->choice[$i]),
                        'language_and_art' => $request->language_and_art[$i],
                        'grades_language_and_art' => $this->determineGrade($request->language_and_art[$i]),
                        'self_development' => $request->self_development[$i],
                        'grades_self_development' => $this->determineGrade($request->self_development[$i]),
                        'eca_aver' => $request->eca_aver[$i],
                        'grades_eca_aver' => $this->determineGrade($request->eca_aver[$i]),
                        'behavior' => $request->behavior[$i],
                        'grades_behavior' => $this->determineGrade($request->behavior[$i]),
                        'participation' => $request->participation[$i],
                        'grades_participation' => $this->determineGrade($request->participation[$i]),
                        'final_score' => round($final_score),
                        'grades_final_score' => $this->determineGrade($final_score),
                        'created_at' => now()
                    ];
                }
                
                Sooa_primary::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $scoring
                );
            }
    
            $allScores = Sooa_primary::where('grade_id', $request->grade_id)
                ->where('semester', $request->semester)
                ->orderBy('final_score', 'desc')
                ->get();
    
            foreach ($allScores as $index => $student) {
                $student->ranking = $index + 1;
                $student->save();
            }
            
            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->class_teacher,
                'semester' => $request->semester,
                'status' => 1,
                'created_at' => now()
            ];
    
            Sooa_status::updateOrCreate(
                ['grade_id' => $request->grade_id, 'class_teacher_id' => $request->class_teacher, 'semester' => $request->semester],
                $status
            );
    
            session()->flash('after_post_sooa');
    
            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);   
        }
    }
    

    public function actionPostSooaSecondary(Request $request){
        // dd($request);
        try {
            for($i=0; $i < count($request->student_id); $i++){
                $academic = Sooa_secondary::where('sooa_secondaries.grade_id', $request->grade_id)
                    ->where('sooa_secondaries.class_teacher_id', $request->class_teacher)
                    ->where('student_id', $request->student_id[$i])
                    ->value('academic');

                $attendance = Sooa_secondary::where('sooa_secondaries.grade_id', $request->grade_id)
                    ->where('sooa_secondaries.class_teacher_id', $request->class_teacher)
                    ->where('student_id', $request->student_id[$i])
                    ->value('attendance');


                if ($request->eca_1[$i] == 0 || $request->eca_1[$i] == 0 && $request->eca_2[$i] ==  0) {
                    $final_score = ($academic 
                    + $request->self_development[$i]  
                    + $request->eca_aver[$i] 
                    + $request->behavior[$i] 
                    + $attendance 
                    + $request->participation[$i]) / 6;
                
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->class_teacher,
                        'semester' => $request->semester,
                        'eca_1' => 0,
                        'grades_eca_1' => "-",
                        'eca_2' => 0,
                        'grades_eca_2' => "-",
                        'self_development' => $request->self_development[$i],
                        'grades_self_development' => $this->determineGrade($request->self_development[$i]),
                        'eca_aver' => $request->eca_aver[$i],
                        'grades_eca_aver' => $this->determineGrade($request->eca_aver[$i]),
                        'behavior' => $request->behavior[$i],
                        'grades_behavior' => $this->determineGrade($request->behavior[$i]),
                        'participation' => $request->participation[$i],
                        'grades_participation' => $this->determineGrade($request->participation[$i]),
                        'final_score' => round($final_score),
                        'grades_final_score' => $this->determineGrade($final_score),
                        'created_at' => now(),
                    ];    
                }
                elseif ($request->eca_1[$i] != 0 && $request->eca_2[$i] == 0) {
                    $final_score = ($academic 
                    + $request->eca_1[$i]
                    + $request->self_development[$i]  
                    + $request->eca_aver[$i] 
                    + $request->behavior[$i] 
                    + $attendance 
                    + $request->participation[$i]) / 7;
                
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->class_teacher,
                        'semester' => $request->semester,
                        'eca_1' => $request->eca_1[$i],
                        'grades_eca_1' => $this->determineGrade($request->eca_1[$i]),
                        'eca_2' => 0,
                        'grades_eca_2' => "-",
                        'self_development' => $request->self_development[$i],
                        'grades_self_development' => $this->determineGrade($request->self_development[$i]),
                        'eca_aver' => $request->eca_aver[$i],
                        'grades_eca_aver' => $this->determineGrade($request->eca_aver[$i]),
                        'behavior' => $request->behavior[$i],
                        'grades_behavior' => $this->determineGrade($request->behavior[$i]),
                        'participation' => $request->participation[$i],
                        'grades_participation' => $this->determineGrade($request->participation[$i]),
                        'final_score' => round($final_score),
                        'grades_final_score' => $this->determineGrade($final_score),
                        'created_at' => now()
                    ];
                }
                else {
                    $final_score = ($academic 
                    + $request->eca_1[$i]
                    + $request->eca_2[$i]
                    + $request->self_development[$i]  
                    + $request->eca_aver[$i] 
                    + $request->behavior[$i] 
                    + $attendance 
                    + $request->participation[$i]) / 8;
                
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->class_teacher,
                        'semester' => $request->semester,
                        'eca_1' => $request->eca_1[$i],
                        'grades_eca_1' => $this->determineGrade($request->eca_1[$i]),
                        'eca_2' => $request->eca_2[$i],
                        'grades_eca_2' => $this->determineGrade($request->eca_2[$i]),
                        'self_development' => $request->self_development[$i],
                        'grades_self_development' => $this->determineGrade($request->self_development[$i]),
                        'eca_aver' => $request->eca_aver[$i],
                        'grades_eca_aver' => $this->determineGrade($request->eca_aver[$i]),
                        'behavior' => $request->behavior[$i],
                        'grades_behavior' => $this->determineGrade($request->behavior[$i]),
                        'participation' => $request->participation[$i],
                        'grades_participation' => $this->determineGrade($request->participation[$i]),
                        'final_score' => round($final_score),
                        'grades_final_score' => $this->determineGrade($final_score),
                        'created_at' => now()
                    ];
                }
                
                Sooa_secondary::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester],
                    $scoring
                );
            }
    
            $allScores = Sooa_secondary::where('grade_id', $request->grade_id)
                ->orderBy('final_score', 'desc')
                ->get();
    
            foreach ($allScores as $index => $student) {
                $student->ranking = $index + 1;
                $student->save();
            }
            
            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->class_teacher,
                'semester' => $request->semester,
                'status' => 1,
                'created_at' => now()
            ];
    
            Sooa_status::updateOrCreate(
                ['grade_id' => $request->grade_id, 'class_teacher_id' => $request->class_teacher, 'semester' => $request->semester],
                $status
            );
    
            session()->flash('after_post_sooa');
    
            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostScoreAttendance(Request $request){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'attendance class teacher',
            ]);

            // dd($request);

            $getGrade = Grade::where('id', $request->grade_id)->value('name');

            for($i=0; $i < count($request->student_id); $i++){
                $score = [
                    'grade_id' => $request->grade_id,
                    'teacher_id' => $request->class_teacher,
                    'student_id' => $request->student_id[$i],
                    'score' => $request->final_score[$i],
                    'semester' => $request->semester,
                ];
                
                if (strtolower($getGrade) == "primary") {
                    $scoring = [
                        'attendance' => $request->final_score[$i],
                        'grades_attendance' => $this->determineGrade($request->final_score[$i]),
                    ];

                    Sooa_primary::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id,
                        'semester' => $request->semester, 'class_teacher_id' => $request->class_teacher], 
                        $scoring
                    );
                }
                elseif (strtolower($getGrade) == "secondary") {
                    $scoring = [
                        'attendance' => $request->final_score[$i],
                        'grades_attendance' => $this->determineGrade($request->final_score[$i]),
                    ];

                    Sooa_secondary::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id,
                        'semester' => $request->semester, 'class_teacher_id' => $request->class_teacher], 
                        $scoring
                    );
                }
                
                Score_attendance::create($score);
            }

            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->class_teacher,
                'semester' => $request->semester,
                'status' => 1,
            ];

            Score_attendance_status::create($status);
            session()->flash('after_post_attendance_score');

            return redirect()->back()->with('role', session('role'));

        } catch (Exception $err) {
            dd($err);
        }
    }


    public function actionPostReportCard1(Request $request)
    {
        try {

            for($i=0; $i < count($request->student_id); $i++){

                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->teacher_id,
                    'semester' => $request->semester,
                    'independent_work' => $request->independent_work[$i],
                    'initiative' => $request->initiative[$i],
                    'homework_completion' => $request->homework_completion[$i],
                    'use_of_information' => $request->use_of_information[$i],
                    'cooperation_with_other' => $request->cooperation_with_other[$i],
                    'conflict_resolution' => $request->conflict_resolution[$i],
                    'class_participation' => $request->class_participation[$i],
                    'problem_solving' => $request->problem_solving[$i],
                    'goal_setting_to_improve_work' => $request->goal_setting_to_improve_work[$i],
                    'strength_weakness_nextstep' => $request->strength_weakness_nextstep[$i],
                    'remarks' => $request->remarks[$i],
                    'created_at' => now()
                ];
                
                
                Report_card::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                    'class_teacher_id' => $request->teacher_id],
                    $scoring
                );
            }
            
            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->teacher_id,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Report_card_status::create($status);

            // dd($request);
            session()->flash('after_post_report_card1');

            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostReportCard2(Request $request)
    {
        try {

            for($i=0; $i < count($request->student_id); $i++){

                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->teacher_id,
                    'semester' => $request->semester,
                    'independent_work' => $request->independent_work[$i],
                    'initiative' => $request->initiative[$i],
                    'homework_completion' => $request->homework_completion[$i],
                    'use_of_information' => $request->use_of_information[$i],
                    'cooperation_with_other' => $request->cooperation_with_other[$i],
                    'conflict_resolution' => $request->conflict_resolution[$i],
                    'class_participation' => $request->class_participation[$i],
                    'problem_solving' => $request->problem_solving[$i],
                    'goal_setting_to_improve_work' => $request->goal_setting_to_improve_work[$i],
                    'strength_weakness_nextstep' => $request->strength_weakness_nextstep[$i],
                    'promotion_status' => $request->status[$i],
                    'created_at' => now()
                ];
                
                
                Report_card::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                    'class_teacher_id' => $request->teacher_id],
                    $scoring
                );
            }
            
            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->teacher_id,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Report_card_status::create($status);

            // dd($request);
            session()->flash('after_post_report_card2');

            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostReportCardToddler(Request $request)
    {
        try {
            
            for($i=0; $i < count($request->student_id); $i++){
                
                // dd($request->student_id[$i]);

                $student_id = $request->student_id[$i];

                // dd($student_id);
                $scoring = [
                    'student_id' => $request->student_id[$i],
                    'grade_id' => $request->grade_id,
                    'class_teacher_id' => $request->teacher_id,
                    'semester' => $request->semester,
                    'songs' => $request->songs[$student_id],
                    'prayer' => $request->prayer[$student_id],
                    'colour' => $request->colour[$student_id],
                    'number' => $request->number[$student_id],
                    'object' => $request->object[$student_id],
                    'body_movement' => $request->body_movement[$student_id],
                    'colouring' => $request->colouring[$student_id],
                    'painting' => $request->painting[$student_id],
                    'chinese_songs' => $request->chinese_songs[$student_id],
                    'ability_to_recognize_the_objects' => $request->ability_to_recognize_the_objects[$student_id],
                    'able_to_own_up_to_mistakes' => $request->able_to_own_up_to_mistakes[$student_id],
                    'takes_care_of_personal_belongings_and_property' => $request->takes_care_of_personal_belongings_and_property[$student_id],
                    'demonstrates_importance_of_self_control' => $request->demonstrates_importance_of_self_control[$student_id],
                    'management_emotional_problem_solving' => $request->management_emotional_problem_solving[$student_id],
                    'remarks' => $request->remarks[$student_id],
                    'created_at' => now()
                ];
                
                Nursery_toddler::updateOrCreate(
                    ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                    'class_teacher_id' => $request->teacher_id],
                    $scoring
                );
            }
            
            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->teacher_id,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Report_card_status::create($status);

            // dd($request);
            session()->flash('after_post_report_card_toddler');

            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostReportCardNursery(Request $request)
    {
        try {
            
            if ($request->semester == 1) {
                for($i=0; $i < count($request->student_id); $i++){
                
                    $student_id = $request->student_id[$i];
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->teacher_id,
                        'semester' => $request->semester,
                        'songs' => $request->songs[$student_id],
                        'prayer' => $request->prayer[$student_id],
                        'colour' => $request->colour[$student_id],
                        'number' => $request->number[$student_id],
                        'object' => $request->object[$student_id],
                        'body_movement' => $request->body_movement[$student_id],
                        'colouring' => $request->colouring[$student_id],
                        'painting' => $request->painting[$student_id],
                        'chinese_songs' => $request->chinese_songs[$student_id],
                        'ability_to_recognize_the_objects' => $request->ability_to_recognize_the_objects[$student_id],
                        'able_to_own_up_to_mistakes' => $request->able_to_own_up_to_mistakes[$student_id],
                        'takes_care_of_personal_belongings_and_property' => $request->takes_care_of_personal_belongings_and_property[$student_id],
                        'demonstrates_importance_of_self_control' => $request->demonstrates_importance_of_self_control[$student_id],
                        'management_emotional_problem_solving' => $request->management_emotional_problem_solving[$student_id],
                        'remarks' => $request->remarks[$student_id],
                        'created_at' => now()
                    ];
                    
                    Nursery_toddler::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                        'class_teacher_id' => $request->teacher_id],
                        $scoring
                    );
                }
            }
            elseif ($request->semester == 2) {
                for($i=0; $i < count($request->student_id); $i++){
                
                    $student_id = $request->student_id[$i];
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->teacher_id,
                        'semester' => $request->semester,
                        'songs' => $request->songs[$student_id],
                        'prayer' => $request->prayer[$student_id],
                        'colour' => $request->colour[$student_id],
                        'number' => $request->number[$student_id],
                        'object' => $request->object[$student_id],
                        'body_movement' => $request->body_movement[$student_id],
                        'colouring' => $request->colouring[$student_id],
                        'painting' => $request->painting[$student_id],
                        'chinese_songs' => $request->chinese_songs[$student_id],
                        'ability_to_recognize_the_objects' => $request->ability_to_recognize_the_objects[$student_id],
                        'able_to_own_up_to_mistakes' => $request->able_to_own_up_to_mistakes[$student_id],
                        'takes_care_of_personal_belongings_and_property' => $request->takes_care_of_personal_belongings_and_property[$student_id],
                        'demonstrates_importance_of_self_control' => $request->demonstrates_importance_of_self_control[$student_id],
                        'management_emotional_problem_solving' => $request->management_emotional_problem_solving[$student_id],
                        'remarks' => $request->remarks[$student_id],
                        'promote' => 1,
                        'created_at' => now()
                    ];
                    
                    Nursery_toddler::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                        'class_teacher_id' => $request->teacher_id],
                        $scoring
                    );
                }
            }

            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->teacher_id,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Report_card_status::create($status);

            // dd($request);
            session()->flash('after_post_report_card_nursery');

            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function actionPostReportCardKindergarten(Request $request)
    {
        // dd($request);
        try {
            
            if ($request->semester == 1) {
                for($i=0; $i < count($request->student_id); $i++){
                
                    $student_id = $request->student_id[$i];
                    $scoring = [
                        'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->teacher_id,
                        'semester' => $request->semester,
                        'english' => $request->english[$student_id],
                        'mathematics' => $request->mathematics[$student_id],
                        'chinese' => $request->chinese[$student_id],
                        'science' => $request->science[$student_id],
                        'character_building' => $request->character_building[$student_id],
                        'art_&_craft' => $request->art_and_craft[$student_id],
                        'it' => $request->it[$student_id],
                        'conduct' => $request->conduct[$student_id],
                        'remarks' => $request->remarks[$student_id],
                        'created_at' => now()
                    ];
                    
                    Kindergarten::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                        'class_teacher_id' => $request->teacher_id],
                        $scoring
                    );
                }
            }
            elseif ($request->semester == 2) {
                for($i=0; $i < count($request->student_id); $i++){
                
                    $student_id = $request->student_id[$i];
                    $scoring = [
                       'student_id' => $request->student_id[$i],
                        'grade_id' => $request->grade_id,
                        'class_teacher_id' => $request->teacher_id,
                        'semester' => $request->semester,
                        'english' => $request->english[$student_id],
                        'mathematics' => $request->mathematics[$student_id],
                        'chinese' => $request->chinese[$student_id],
                        'science' => $request->science[$student_id],
                        'character_building' => $request->character_building[$student_id],
                        'art_and_craft' => $request->art_and_craft[$student_id],
                        'it' => $request->it[$student_id],
                        'conduct' => $request->conduct[$student_id],
                        'remarks' => $request->remarks[$student_id],
                        'promote' => 1,
                        'created_at' => now()
                    ];
                    
                    Kindergarten::updateOrCreate(
                        ['student_id' => $request->student_id[$i], 'grade_id' => $request->grade_id, 'semester' => $request->semester,
                        'class_teacher_id' => $request->teacher_id],
                        $scoring
                    );
                }
            }

            $status = [
                'grade_id' => $request->grade_id,
                'class_teacher_id' => $request->teacher_id,
                'status' => 1,
                'semester' => $request->semester,
                'created_at' => now()
            ];

            Report_card_status::create($status);

            // dd($request);
            session()->flash('after_post_report_card_kindergarten');

            return redirect()->back()->with('role', session('role'));
        } catch (Exception $err) {
            dd($err);
        }
    }

    private function determineGrade($finalScore)
    {
        if ($finalScore >= 95 && $finalScore <= 100) {
            return 'A+';
        } elseif ($finalScore >= 85 && $finalScore <= 94) {
            return 'A';
        } elseif ($finalScore >= 75 && $finalScore <= 84) {
            return 'B';
        } elseif ($finalScore >= 65 && $finalScore <= 74) {
            return 'C';
        } elseif ($finalScore >= 45 && $finalScore <= 64) {
            return 'D';
        } else {
            return 'R';
        }
    }
}
