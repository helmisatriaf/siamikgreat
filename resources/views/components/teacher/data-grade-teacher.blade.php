@extends('layouts.admin.master')
@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
   <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb" class="bg-light rounded-3 p-3 mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Grades</li>
                </ol>
            </nav>
        </div>
    </div>
   <!-- START TABEL -->
   @if (sizeof($data['gradeTeacher']) != 0)
      @foreach ($data['gradeTeacher'] as $dgt)
         <div class="card card-dark">
               <div class="card-header">
                  <h3 class="card-title">{{ $dgt->name . ' - ' . $dgt->class }}</h3>
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
                              <th style="width: 10%">
                                 #
                              </th>
                              <th style="width: 25%">
                                 Student
                              </th>
                              <th>
                                 NISN
                              </th>
                              <th>
                                 Gender
                              </th>
                              <th>
                                 Religion
                              </th>
                              <th>
                                 Place Birth
                              </th>
                           </tr>
                     </thead>
                     <tbody>
                           @if (sizeof($dgt->students) != 0)
                              @foreach ($dgt->students as $el)
                                 <tr id="{{ 'index_grade_' . $el->id }}">
                                       <td>
                                          {{ $loop->index + 1 }}
                                       </td>
                                       <td>
                                          <a>
                                             {{ $el->name }}
                                          </a>
                                       </td>
                                       <td>
                                          <a>
                                             {{ $el->unique_id }}
                                          </a>
                                       </td>
                                       <td>
                                          {{ $el->gender }}
                                       </td>
                                       <td>
                                          {{ $el->religion }}
                                       </td>
                                       <td>
                                          {{ $el->place_birth }}
                                       </td>
                                 </tr>
                              @endforeach
                           @else
                              <tr>
                                 <td colspan="7" class="text-center">No student in this grade!!!</td>
                              </tr>
                           @endif
                     </tbody>
                  </table>
               </div>
         </div>
      @endforeach
      <!-- END TABLE -->
   @else
      <p class="text-center">You don't have data grade</p>
   @endif
   <!-- END TABEL -->
</div>

<link rel="stylesheet" href="{{asset('template')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script src="{{asset('template')}}/plugins/sweetalert2/sweetalert2.min.js"></script>

   @if(session('after_create_grade')) 
      <script>
         Swal.fire({
            icon: 'success',
            title: 'Successfully',
            text: 'Successfully created new grade in the database.',
        });
      </script>
  @endif

  @if(session('after_update_grade')) 
      <script>
         Swal.fire({
            icon: 'success',
            title: 'Successfully ',
            text: 'Successfully updated the grade in the database.',
         });
    </script>
   @endif

@endsection
