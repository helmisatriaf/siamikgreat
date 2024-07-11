@extends('layouts.admin.master')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb" class="bg-white rounded-3 p-3 mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item"><a href="{{url('/teacher/dashboard/exam/teacher')}}">Exam</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assessment Exam</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">Exams</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <form method="POST" action="{{ route('actionUpdateScoreExamTeacher') }}">
                @csrf
                @method('PUT')
                <table class="table table-striped projects">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name Exam</th>
                            <th>Date Exam</th>
                            <th>Grade</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Type Exam</th>
                            <th>Teacher</th>
                            <th style="width: 25%">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $el)
                        <tr id={{'index_grade_' . $el->id}}>
                            <td class="text-sm">{{ $loop->index + 1 }}</td>
                            <td class="text-sm"><a>{{ $el->exam_name }}</a></td>
                            <td class="text-sm"><a>{{ $el->date_exam }}</a></td>
                            <td class="text-sm">{{ $el->grade_name }} - {{ $el->grade_class }}</td>
                            <td class="text-sm">{{ $el->student_name }}</td>
                            <td class="text-sm">{{ $el->subject_name }}</td>
                            <td class="text-sm">{{ $el->type_exam }}</td>
                            <td class="text-sm">{{ $el->teacher_name }}</td>
                            <td class="project-actions text-right">
                                <div class="input-group">
                                    <input name="exam_id" type="text" class="form-control d-none" id="exam_id" value="{{ $el->exam_id }}">
                                    <input name="subject_id" type="text" class="form-control d-none" id="subject_id" value="{{ $el->subject_id }}">
                                    <input name="grade_id" type="text" class="form-control d-none" id="grade_id" value="{{ $el->grade_id }}">
                                    <input name="teacher_id" type="text" class="form-control d-none" id="teacher_id" value="{{ $el->teacher_id }}">
                                    <input name="type_exam_id" type="text" class="form-control d-none" id="type_exam_id" value="{{ $el->type_exam_id }}">
                                    <input name="student_id[]" type="text" class="form-control d-none" id="student_id" value="{{ $el->student_id }}">
                                    <input name="score[]" type="number" class="form-control" id="score" placeholder="Score" value="{{ old('score', $el->score) }}" autocomplete="off" min="0" max="100" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success" name="update_single" value="{{ $el->id }}">Update</button>
                                    </div>
                                </div>
                                @if($errors->has('score'))
                                <p style="color: red">{{ $errors->first('score') }}</p>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right" name="update_all">Update Scores</button>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
</div>

<link rel="stylesheet" href="{{ asset('template')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script src="{{ asset('template')}}/plugins/sweetalert2/sweetalert2.min.js"></script>

@if(session('after_create_score'))
<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    setTimeout(() => {
        Toast.fire({
            icon: 'success',
            title: 'Successfully created new score in the database.'
        });
    }, 1500);
</script>
@endif

@if(session('after_update_score'))
<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    setTimeout(() => {
        Toast.fire({
            icon: 'success',
            title: 'Successfully updated the scores in the database.'
        });
    }, 1500);
</script>
@endif

@endsection
