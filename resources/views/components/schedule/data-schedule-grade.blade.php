@extends('layouts.admin.master')
@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">

   <div class="card card-dark mt-5">
      <div class="card-header">
         <h3 class="card-title">Schedule Grade</h3>

         <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
               </button>
         </div>
      </div>
      <div class="card-body">
         <table class="table table-striped projects">
               <thead>
                  <tr>
                     <th >
                        #
                     </th>
                     <th>
                        Subject
                     </th>
                     <th>
                        Teacher
                     </th>
                     <th>
                        Companion Teacher
                     </th>
                     <th>
                        Note
                     </th>
                     <th>
                        Day
                     </th>
                     <th>
                        Start_time
                     </th>
                     <th>
                        End_time
                     </th>
                     <th class="text-center" style="width: 25%;">
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
                              {{$el->subject_name}}
                        </a>
                     </td>
                     <td>
                        <a>
                              {{$el->teacher_name}}
                        </a>
                     </td>
                     <td>
                        <a>
                              {{$el->teacher_companion_name}}
                        </a>
                     </td>
                     <td>
                        <a>
                              {{$el->note}}
                        </a>
                     </td>
                     <td>
                        <a>
                              {{$el->day}}
                        </a>
                     </td>
                     <td>
                        {{$el->start_time}}
                     </td>
                     <td>
                        {{$el->end_time}}
                     </td>
                     
                     <td class="project-actions text-right toastsDefaultSuccess">
                        <a class="btn btn-primary btn"
                           href="{{url('/' . session('role') .'/schedules/edit') . '/'. $el->grade_id .'/' . $el->id}}">
                           <i class="fas fa-pen">
                           </i>
                           Edit
                        </a>
                        <a class="btn btn-danger btn"
                           href="{{url('/' . session('role') .'/schedules/delete') . '/' . $el->id}}">
                           <i class="fas fa-trash">
                           </i>
                           Delete
                        </a>
                     </td>
                  </tr>

                  @endforeach
               </tbody>
         </table>

      </div>
      <!-- /.card-body -->
   </div>

   <div class="card card-dark mt-5">
      <div class="card-header">
         <h3 class="card-title">Schedule Substitute</h3>

         <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
               </button>
         </div>
      </div>
      <div class="card-body">
         <table class="table table-striped projects">
               <thead>
                  <tr>
                     <th >
                        #
                     </th>
                     <th>
                        Subject
                     </th>
                     <th>
                        Teacher
                     </th>
                     <th>
                        Companion Teacher
                     </th>
                     <th>
                        Date
                     </th>
                     <th>
                        Day
                     </th>
                     <th>
                        Start_time
                     </th>
                     <th>
                        End_time
                     </th>
                     <th class="text-center">
                        Action
                     </th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($subtituteTeacher as $el)
                  <tr id={{'index_grade_' . $el->id}}>
                     <td>
                        {{ $loop->index + 1 }}
                     </td>
                     <td>
                        <a>
                           {{$el->subject_name}}
                        </a>
                     </td>
                     <td>
                        <a>
                           {{$el->teacher_name}}
                        </a>
                     </td>
                     <td>
                        <a>
                           {{$el->teacher_companion_name}}
                        </a>
                     </td>
                     <td>
                        {{ \Carbon\Carbon::parse($el->date)->format('F d, Y') }}
                     </td>
                     <td>
                        <a>
                           {{$el->day}}
                        </a>
                     </td>
                     <td>
                        {{$el->start_time}}
                     </td>
                     <td>
                        {{$el->end_time}}
                     </td>
                     
                     <td class="project-actions text-right toastsDefaultSuccess">
                        <a class="btn btn-primary btn"
                           href="{{url('/' . session('role') .'/schedules/editSubtitute') . '/'. $el->grade_id .'/' . $el->id}}">
                           <i class="fas fa-pen">
                           </i>
                           Edit
                        </a>
                        <a class="btn btn-danger btn"
                           href="{{url('/' . session('role') . '/schedules/deleteSubtitute') . '/' . $el->id}}">
                           <i class="fas fa-trash">
                           </i>
                           Delete
                        </a>
                     </td>
                  </tr>

                  @endforeach
               </tbody>
         </table>

      </div>
      <!-- /.card-body -->
   </div>

</div>

<link rel="stylesheet" href="{{asset('template')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script src="{{asset('template')}}/plugins/sweetalert2/sweetalert2.min.js"></script>

   @if(session('after_update_schedule')) 
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
            title: 'Successfully updated the schedule in the database.',
      });
      }, 1500);

    
      </script>
   @endif

   @if(session('after_edit_grade_schedule')) 

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
            title: 'Successfully edit data schedule in the database.',
      });
      }, 1500);


   </script>

   @endif

   @if(session('after_delete_schedule')) 
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
            title: 'Successfully deleted the schedule in the database.',
      });
      }, 1500);

    
      </script>
   @endif

   @if(session('after_delete_schedule_subtitute')) 
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
            title: 'Successfully deleted the subtitute schedule in the database.',
      });
      }, 1500);

    
      </script>
   @endif

@endsection