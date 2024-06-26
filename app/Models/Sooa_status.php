<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sooa_status extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'grade_id',
        'class_teacher_id',
        'status',
        'semester',
        'created_at',
        'updated_at',
    ];
}
