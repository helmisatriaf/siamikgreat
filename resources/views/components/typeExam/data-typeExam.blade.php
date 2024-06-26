@extends('layouts.admin.master')
@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
    <div class="row">
        <a type="button" href="{{ url('/' . session('role') . '/typeExams/create') }}" class="btn btn-success btn mt-5 mx-2">   <i class="fa-solid fa-user-plus"></i>
        </i>   
        Add type exam
        </a>
    </div>

    <div class="card card-dark mt-2">
        <div class="card-header">
            <h3 class="card-title">Type Exams</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th>
                           #
                        </th>
                        <th style="width: 15%">
                           Type Exams
                        </th>
                        <th style="width: 80%">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $el)
                    <tr id={{'index_grade_' . $el->id}}>
                        <td>
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                           <a>
                                {{$el->name}}
                           </a>
                        </td>
                        
                        <td class="project-actions text-left toastsDefaultSuccess">
                           <a class="btn btn-info btn"
                              href="{{url('/' . session('role') .'/typeExams') . '/edit/' . $el->id}}">
                              {{-- <i class="fa-solid fa-user-graduate"></i> --}}
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                           </a>
                            <a class="btn btn-danger btn" data-toggle="modal" data-target="#exampleModalCenter_{{ $el->id }}">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalCenter_{{ $el->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Delete type exam</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure want to delete type exam?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <a class="btn btn-danger btn" href="{{ url('/' . session('role') . '/typeExams/delete/' . $el->id) }}">Yes delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>

<link rel="stylesheet" href="{{asset('template')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script src="{{asset('template')}}/plugins/sweetalert2/sweetalert2.min.js"></script>

@if(session('after_create_typeExam')) 

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
                title: 'Successfully created new type exam in the database.',
        });
        }, 1500);
    </script>

@endif

@if(session('after_update_typeExam')) 
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
                title: 'Successfully updated the type exam in the database.',
        });
        }, 1500);
    </script>
@endif

@if(session('after_delete_type_exam')) 
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
                title: 'Successfully deleted type exam in the database.',
        });
        }, 1500);
    </script>
@endif

@endsection
