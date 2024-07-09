<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
date_default_timezone_set('Asia/Jakarta');
class Student extends Model
{
   use HasFactory;
   
   protected $fillable = [
      'id',
      'is_active',
      'user_id',
      'unique_id',
      'name',
      'grade_id',
      'gender',
      'religion',
      'nisn',
      'place_birth',
      'date_birth',
      'id_or_passport',
      'nationality',
      'place_of_issue',
      'date_exp',
      'is_graduate',
      'created_at',
      'updated_at',
   ];

   public function user()
   {
      return $this->belongsTo(User::class, 'user_id');
   }

   public function relationship()
   {
      return $this->belongsToMany(Relationship::class, 'student_relationships');
   }

   public function grade()
   {
      return $this->belongsTo(Grade::class);
   }

   public function brotherOrSister()
   {
      return $this->hasMany(Brother_or_sister::class, 'student_id');
   }

   public function exam()
   {
      return $this->belongsToMany(Exam::class, 'student_exams');
   }

   public function attendances()
   {
       return $this->hasMany(Attendance::class);
   }

}