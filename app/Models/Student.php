<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;


    protected $fillable = [
      'id',
      'name',
      'grade_id',
      'gender',
      'religion',
      'place_birth',
      'date_birth',
      'id_or_passport',
      'nationality',
      'place_of_issue',
      'date_exp',
      'created_at',
      'updated_at',
    ];

    public function relationship()
    {
       return $this->belongsToMany(Relationship::class, 'student_relations', 'student_id', 'relation_id');
    }

    public function grade()
    {
      return $this->belongsTo(Grade::class,'grade_id');
    }

    public function brotherOrSister()
    {
       return $this->hasMany(Brothers_or_sister::class, 'student_id');
    }
}