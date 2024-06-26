<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Grade_subject;
use App\Models\Grade_exam;
use App\Models\Teacher_grade;
use App\Models\Teacher_subject;
use App\Models\Attendance;
use App\Models\Score_attendance_status;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendance',
            ]);
            
            $data  = Grade::with(['student', 'subject'])
                ->join('teacher_grades', 'teacher_grades.grade_id', '=', 'grades.id')
                ->leftJoin('teachers', function ($join) {
                    $join->on('teachers.id', '=', 'teacher_grades.teacher_id');
                })
                ->select('grades.id as id', 'grades.name as grade_name', 'grades.class as grade_class',
                'teachers.name as teacher_class')
                ->withCount(['student as active_student_count', 'subject as active_subject_count'])
                ->orderBy('grades.id', 'asc')
                ->get();


            // dd($data);
            return view('components.attendance.all-attendance')->with('data', $data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function detailAttendance(Request $request){
        try {
            $id = $request->id;
            $gradeId = $request->gradeId;

            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendance',
            ]);

            $subjectTeacher = Teacher_subject::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_subjects.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $classTeacher = Teacher_grade::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_grades.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();
            
            $grade = Grade::where('id', $gradeId)
                ->select('grades.name as grade_name','grades.class as grade_class', 'grades.id as grade_id')
                ->first();

            $semester = session('semester');

            $results = Grade::join('students', 'students.grade_id', '=', 'grades.id')
                ->leftJoin('attendances', function ($join) {
                    $join->on('attendances.student_id', '=', 'students.id');
                })
                ->select(
                    'students.id as student_id',
                    'students.name as student_name',
                    'attendances.*',
                )
                ->where('grades.id', $gradeId)
                ->where('attendances.semester', $semester)
                ->get();

                $totalAttendances = [
                    'total' => $results->count(),
                    'dates' => $results->pluck('date')->unique()->values()->all(),
                    'datesByMonth' => $results->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->date)->format('F');
                    })->map(function($group) {
                        return $group->pluck('date')->unique()->values()->all();
                    })
                ];
    
                // dd($totalAttendances);
    
                $attendancesByStudent = $results->groupBy('student_id')->map(function($attendances) {
                    $student = $attendances->first();
                    $totalPresent = $attendances->where('present', 1)->count();
                    $totalSick = $attendances->where('sick', 1)->count();
                    $totalAlpha = $attendances->where('alpha', 1)->count();
                    $totalLate = $attendances->where('late', 1)->count();
                    $totalPermission = $attendances->where('permission', 1)->count();
                    $totalNonPresent = $attendances->where('present', 0)->count();
                    $effectiveDays = ($totalPresent + $totalAlpha + $totalSick + $totalLate + $totalPermission);
                
                    // Calculate the score ensuring effectiveDays is not zero to avoid division by zero
                    if ($effectiveDays > 0) {
                        $score = round((($effectiveDays - $totalAlpha - $totalPermission - ($totalLate * 0.5)) / $effectiveDays) * 100);
                    } else {
                        $score = 0;
                    }
                
                    return [
                        'student_id' => $student->student_id,
                        'student_name' => $student->student_name,
                        'total_present' => $totalPresent,
                        'total_alpha' => $totalAlpha,
                        'total_sick' => $totalSick,
                        'total_late' => $totalLate,
                        'total_pe' => $totalPermission,
                        'total_non_present' => $totalNonPresent,
                        'score' => $score,
                
                        'attendances' => $attendances->map(function ($attend) {
                            return [
                                'attendances_id' => $attend->id,
                                'attendances_date' => $attend->date,
                                'attendances_present' => $attend->present,
                                'attendances_alpha' => $attend->alpha,
                                'attendances_sick' => $attend->sick,
                                'attendances_late' => $attend->late,
                                'attendances_latest' => $attend->latest,
                                'attendances_permission' => $attend->permission,
                                'attendances_information' => $attend->information,
                            ];
                        })
                    ];
                })->values()->all();

                $status = Score_attendance_status::where('grade_id', $grade->grade_id)
                    ->where('semester', $semester)
                    ->where('class_teacher_id', $classTeacher->teacher_id)
                    ->first();
                
                $data = [
                    'classTeacher' => $classTeacher,
                    'semester' => $semester,
                    'students' => $attendancesByStudent,
                    'attendancesByMonth' => $attendancesByStudent,
                    'grade' => $grade,
                    'totalAttendances' => $totalAttendances,
                    'status' => $status,
                ];

            // dd($data);
            return view('components.attendance.detail')->with('data', $data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function subject($gradeId){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendance',
            ]);

            $subject = Grade_subject::join('subjects', 'subjects.id', '=', 'grade_subjects.subject_id')
                ->leftJoin('teacher_subjects', function ($join) {
                    $join->on('teacher_subjects.subject_id', '=', 'grade_subjects.subject_id')
                        ->on('teacher_subjects.grade_id', '=', 'grade_subjects.grade_id');
                })
                ->leftJoin('teachers', 'teachers.id', '=', 'teacher_subjects.teacher_id')
                ->where('grade_subjects.grade_id', $gradeId)
                ->select(
                    'subjects.id as subject_id',
                    'teachers.id as teacher_id',
                    'subjects.name_subject as subject_name',
                    'teachers.name as teacher_name',
                )
                ->get();

            $grade = grade::where('id', $gradeId)->get();


            $data = [
                'grade' => $grade,
                'subject' => $subject,
            ];

            return view('components.attendance.subject')->with('data', $data);
            // dd($data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function detailAttend($gradeId, $subjectId){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendances',
            ]);

            $subjectTeacher = Teacher_subject::where('grade_id', $gradeId)
                ->where('subject_id', $subjectId)
                ->join('teachers', 'teachers.id', '=', 'teacher_subjects.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $classTeacher = Teacher_grade::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_grades.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $subject = Subject::where('id', $subjectId)
                ->select('subjects.name_subject as subject_name', 'subjects.id as subject_id')
                ->first();
            
            $grade = Grade::where('id', $gradeId)
                ->select('grades.name as grade_name','grades.class as grade_class', 'grades.id as grade_id')
                ->first();


            $semester = session('semester');

            $results = Grade::join('students', 'students.grade_id', '=', 'grades.id')
                ->leftJoin('attendances', function ($join) {
                    $join->on('attendances.student_id', '=', 'students.id');
                })
                ->select(
                    'students.id as student_id',
                    'students.name as student_name',
                    'attendances.*',
                )
                ->where('grades.id', $gradeId)
                ->where('attendances.subject_id', $subjectId)
                ->where('attendances.semester', $semester)
                ->get();

            $totalAttendances = [
                'total' => $results->count(),
                'dates' => $results->pluck('date')->unique()->values()->all()
            ];

            $attendancesByStudent = $results->groupBy('student_id')->map(function($attendances){
                $student = $attendances->first();
                $totalPresent = $attendances->where('present', 1)->count();
                $totalNonPresent = $attendances->where('present', 0)->count();

                return [
                    'student_id' => $student->student_id,
                    'student_name' => $student->student_name,
                    'total_present' => $totalPresent,
                    'total_non_present' => $totalNonPresent,
                    'attendances' => $attendances->map(function ($attend) {
                        return [
                            'attendances_id' => $attend->id,
                            'attendances_date' => $attend->date,
                            'attendances_present' => $attend->present,
                            'attendances_alpha' => $attend->alpha,
                            'attendances_sick' => $attend->sick,
                            'attendances_permission' => $attend->permission,
                            'attendances_information' => $attend->information,
                        ];
                    })
                ];
            })->values()->all();

            $data = [
                'subjectTeacher' => $subjectTeacher,
                'classTeacher' => $classTeacher,
                'subject' => $subject,
                'semester' => $semester,
                'students' => $attendancesByStudent,
                'grade' => $grade,
                'totalAttendances' => $totalAttendances,
            ];

            dd($data);
            return view('components.attendance.detail')->with('data', $data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function pageDetail($gradeId){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendance',
            ]);

            $gradeStudent = Student::where('grade_id', $id)->where('is_active', true)->get();


        } catch (Exception $err) {
            dd($err);
        }
    }

    // FOR TEACHER 
    public function attendTeacher($id)
    {
        try {
            //code...
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendance',
            ]);

            $getIdTeacher = Teacher::where('user_id', $id)->value('id');

            $gradeTeacher = Teacher_grade::where('teacher_id', $getIdTeacher)
                ->join('grades', 'grades.id', '=', 'teacher_grades.grade_id')
                ->select('grades.*',)
                ->get();

                foreach ($gradeTeacher as $gt) {
                    $gt->students = Student::where('grade_id', $gt->id)->get();
                    $gt->countStudent = count($gt->students);
                }

            $subjectTeacher = Teacher_subject::where('teacher_id', $getIdTeacher)
                ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                ->get();
            
            $data = [
                'gradeTeacher' => $gradeTeacher,
                'subjectTeacher' => $subjectTeacher,
            ];

            //  dd($data);

            return view('components.attendance.data-attendance')->with('data', $data);

        } catch (Exception $err) {
            return dd($err);
        }
    }

    public function gradeTeacher($id)
    {
        try {
            //code...
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'attendance class teacher',
            ]);

            $getIdTeacher = Teacher::where('user_id', $id)->value('id');

            $gradeTeacher = Teacher_grade::where('teacher_id', $getIdTeacher)
                ->join('grades', 'grades.id', '=', 'teacher_grades.grade_id')
                ->select('grades.*',)
                ->get();

            $gradeId = Teacher_grade::where('teacher_id', $getIdTeacher)->value('grade_id');

            $subjectTeacher = Grade_subject::where('grade_id', $gradeId)
                ->join('subjects', 'subjects.id', '=', 'grade_subjects.subject_id')
                ->get();
            
            $data = [
                'gradeTeacher' => $gradeTeacher,
                'subjectTeacher' => $subjectTeacher,
            ];

            //  dd($data);

            return view('components.attendance.grade-attendance')->with('data', $data);

        } catch (Exception $err) {
            return dd($err);
        }
    }

    public function subjectTeacher($id)
    {
        try {
            //code...
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'attendance subject teacher',
            ]);

            $getIdTeacher = Teacher::where('user_id', $id)->value('id');

            $subjectTeacher = Teacher_subject::where('teacher_id', $getIdTeacher)
                ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                ->join('grades', 'grades.id', '=', 'teacher_subjects.grade_id')
                ->get();
            
            $data = $subjectTeacher;

            // dd($data);

            return view('components.attendance.subject-attendance')->with('data', $data);

        } catch (Exception $err) {
            return dd($err);
        }
    }

    public function detailAttendTeacher(Request $request){
        try {
            $id = $request->id;
            $gradeId = $request->gradeId;

            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'attendance class teacher',
            ]);

            $subjectTeacher = Teacher_subject::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_subjects.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $classTeacher = Teacher_grade::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_grades.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();
            
            $grade = Grade::where('id', $gradeId)
                ->select('grades.name as grade_name','grades.class as grade_class', 'grades.id as grade_id')
                ->first();

            $semester = session('semester');

            $results = Grade::join('students', 'students.grade_id', '=', 'grades.id')
                ->leftJoin('attendances', function ($join) {
                    $join->on('attendances.student_id', '=', 'students.id');
                })
                ->select(
                    'students.id as student_id',
                    'students.name as student_name',
                    'attendances.*',
                )
                ->where('grades.id', $gradeId)
                ->where('attendances.semester', $semester)
                ->get();

                $totalAttendances = [
                    'total' => $results->count(),
                    'dates' => $results->pluck('date')->unique()->values()->all(),
                    'datesByMonth' => $results->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->date)->format('F');
                    })->map(function($group) {
                        return $group->pluck('date')->unique()->values()->all();
                    })
                ];
    
                // dd($totalAttendances);
    
                $attendancesByStudent = $results->groupBy('student_id')->map(function($attendances) {
                    $student = $attendances->first();
                    $totalPresent = $attendances->where('present', 1)->count();
                    $totalSick = $attendances->where('sick', 1)->count();
                    $totalAlpha = $attendances->where('alpha', 1)->count();
                    $totalLate = $attendances->where('late', 1)->count();
                    $totalPermission = $attendances->where('permission', 1)->count();
                    $totalNonPresent = $attendances->where('present', 0)->count();
                    $effectiveDays = ($totalPresent + $totalAlpha + $totalSick + $totalLate + $totalPermission);
                
                    // Calculate the score ensuring effectiveDays is not zero to avoid division by zero
                    if ($effectiveDays > 0) {
                        $score = round((($effectiveDays - $totalAlpha - $totalPermission - ($totalLate * 0.5)) / $effectiveDays) * 100);
                    } else {
                        $score = 0;
                    }
                
                    return [
                        'student_id' => $student->student_id,
                        'student_name' => $student->student_name,
                        'total_present' => $totalPresent,
                        'total_alpha' => $totalAlpha,
                        'total_sick' => $totalSick,
                        'total_late' => $totalLate,
                        'total_pe' => $totalPermission,
                        'total_non_present' => $totalNonPresent,
                        'score' => $score,
                
                        'attendances' => $attendances->map(function ($attend) {
                            return [
                                'attendances_id' => $attend->id,
                                'attendances_date' => $attend->date,
                                'attendances_present' => $attend->present,
                                'attendances_alpha' => $attend->alpha,
                                'attendances_sick' => $attend->sick,
                                'attendances_late' => $attend->late,
                                'attendances_latest' => $attend->latest,
                                'attendances_permission' => $attend->permission,
                                'attendances_information' => $attend->information,
                            ];
                        })
                    ];
                })->values()->all();

                $status = Score_attendance_status::where('grade_id', $grade->grade_id)
                    ->where('semester', $semester)
                    ->where('class_teacher_id', $classTeacher->teacher_id)
                    ->first();
                
                $data = [
                    'classTeacher' => $classTeacher,
                    'semester' => $semester,
                    'students' => $attendancesByStudent,
                    'attendancesByMonth' => $attendancesByStudent,
                    'grade' => $grade,
                    'totalAttendances' => $totalAttendances,
                    'status' => $status,
                ];

            // dd($data);
            return view('components.attendance.detail')->with('data', $data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function detailViewAttendTeacher($id, $gradeId, $subjectId){
        try {
            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'database attendances',
            ]);

            $subjectTeacher = Teacher_subject::where('grade_id', $gradeId)
                ->where('subject_id', $subjectId)
                ->join('teachers', 'teachers.id', '=', 'teacher_subjects.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $classTeacher = Teacher_grade::where('grade_id', $gradeId)
                ->join('teachers', 'teachers.id', '=', 'teacher_grades.teacher_id')
                ->select('teachers.id as teacher_id', 'teachers.name as teacher_name')
                ->first();

            $subject = Subject::where('id', $subjectId)
                ->select('subjects.name_subject as subject_name', 'subjects.id as subject_id')
                ->first();
            
            $grade = Grade::where('id', $gradeId)
                ->select('grades.name as grade_name','grades.class as grade_class', 'grades.id as grade_id')
                ->first();


            $semester = session('semester');

            $results = Grade::join('students', 'students.grade_id', '=', 'grades.id')
                ->leftJoin('attendances', function ($join) {
                    $join->on('attendances.student_id', '=', 'students.id');
                })
                ->select(
                    'students.id as student_id',
                    'students.name as student_name',
                    'attendances.*',
                )
                ->where('grades.id', $gradeId)
                ->where('attendances.subject_id', $subjectId)
                ->where('attendances.semester', $semester)
                ->get();

            $totalAttendances = [
                'total' => $results->count(),
                'dates' => $results->pluck('date')->unique()->values()->all()
            ];

            $attendancesByStudent = $results->groupBy('student_id')->map(function($attendances){
                $student = $attendances->first();
                $totalPresent = $attendances->where('present', 1)->count();
                $totalNonPresent = $attendances->where('present', 0)->count();

                return [
                    'student_id' => $student->student_id,
                    'student_name' => $student->student_name,
                    'total_present' => $totalPresent,
                    'total_non_present' => $totalNonPresent,
                    'attendances' => $attendances->map(function ($attend) {
                        return [
                            'attendances_id' => $attend->id,
                            'attendances_date' => $attend->date,
                            'attendances_present' => $attend->present,
                            'attendances_alpha' => $attend->alpha,
                            'attendances_sick' => $attend->sick,
                            'attendances_late' => $attend->late,
                            'attendances_permission' => $attend->permission,
                            'attendances_information' => $attend->information,
                        ];
                    })
                ];
            })->values()->all();

            $data = [
                'subjectTeacher' => $subjectTeacher,
                'classTeacher' => $classTeacher,
                'subject' => $subject,
                'semester' => $semester,
                'students' => $attendancesByStudent,
                'grade' => $grade,
                'totalAttendances' => $totalAttendances,
            ];

            dd($data);
            return view('components.attendance.detail')->with('data', $data);
        } catch (Exception $err) {
            dd($err);
        }
    }

    public function detail($userId, $gradeId)
    {
       try {
          //code...
        session()->flash('page',  $page = (object)[
            'page' => 'attendance',
            'child' => 'attendance class teacher',
        ]);
 
        $getIdTeacher = Teacher::where('user_id', $userId)->value('id');
        $student      = Student::where('grade_id', $gradeId)->get();
        $grade        = Grade::where('id', $gradeId)->first();
        $teacher      = Teacher::where('id', $getIdTeacher)->value('name');


        $nameGrade    = "$grade->name - $grade->class";
        $nameTeacher  = $teacher;

        $data = [
            'student'     => $student,
            'teacherId'   => $getIdTeacher,
            'gradeId'     => $gradeId,
            'nameGrade'   => $nameGrade,
            'nameTeacher' => $nameTeacher,
        ];
 
         return view('components.attendance.detail-attendance')->with('data', $data);
 
       } catch (Exception $err) {
         return dd($err);
       }
    }

    public function postAttendance(Request $request){
        try {

            session()->flash('page',  $page = (object)[
                'page' => 'attendance',
                'child' => 'attendance subject teacher',
            ]);

            // dd($request);
            $userId = session('id_user');
            $getIdTeacher = Teacher::where('user_id', $userId)->value('id');

            foreach($request->status as $studentId => $status) {

                if(Attendance::where('date', $request->date)
               ->where('grade_id', $request->grade_id)
               ->where('teacher_id', $request->teacher_id)
               ->where('student_id', $studentId)
               ->where('semester', $request->semester)
               ->exists()) {
                    return redirect('/teacher/dashboard/attendance/'. $userId . '/' . $request->grade_id)
                    ->with('failed_attend', 'Attendance already recorded for this student.');
                }

                // Skip if no status is selected
                if(empty($status)) continue;
    
                // Initialize attendance array
                $attend = [
                    'student_id'  => $studentId,
                    'grade_id'    => $request->grade_id,
                    'teacher_id'  => $request->teacher_id,
                    'date'        => $request->date,
                    'present'     => $status === 'present' ? 1 : 0,
                    'alpha'       => $status === 'alpha' ? 1 : 0,
                    'sick'        => $status === 'sick' ? 1 : 0,
                    'late'        => $status === 'late' ? 1 : 0,
                    'latest'      => $request->latest[$studentId] ?? 0,
                    'permission'  => $status === 'permission' ? 1 : 0,
                    'information' => $request->comment[$studentId] ?? '',
                    'semester'    => $request->semester,
                    'created_at'  => now(),
                ];
    
                // Save the attendance to the database
                Attendance::create($attend);
            }

            session()->flash('after_create_attendance');

            if(session('role') == 'superadmin') {
                return redirect('/superadmin/attendances');
            }
            elseif (session('role') == 'admin') {
                return redirect('/admin/attendances');
            }
            elseif (session('role') == 'teacher') {
                return redirect('/teacher/dashboard/attendance/gradeTeacher/' . session('id_user'));
            }

        } catch(Exception $err){
            dd($err);
        }
    }
}
