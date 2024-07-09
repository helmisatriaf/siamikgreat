@extends('layouts.admin.master')
@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
   <div class="row">
      <a type="button" href="{{ url('/' . session('role') . '/chineseLower/add') }}" class="btn btn-success btn mt-5 mx-2">   
         <i class="fa-solid fa-user-plus"></i>
         Add Student
      </a>
   </div>

   <div class="card card-dark mt-2">
      <div class="card-header">
         <h3 class="card-title">Student Chinese Higher</h3>

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
                     No
                  </th>
                  <th style="width: 20%">
                     Student
                  </th>
                  <th style="width: 10%">
                     Class
                  </th>
                  <th style="width: 70%">
                     Action
                  </th>
               </tr>
            </thead>
            @if (!empty($data['data']))
            <tbody>
               @foreach ($data['data'] as $el)
               <tr id={{'index_grade_' . $el->id}}>
                  <td>
                        {{ $loop->index + 1 }}
                  </td>
                  <td>
                     <a>
                        {{$el->student_name}}
                     </a>
                  </td>
                  <td>
                     <a>
                        {{$el->grade_name}} - {{ $el->grade_class }}
                     </a>
                  </td>
                  
                  <td class="project-actions text-left toastsDefaultSuccess">
                     @if (session('role') == 'superadmin' || session('role') == 'admin')
                        <a class="btn btn-danger btn" href="{{url('/' . session('role') .'/chineseLower/student') . '/delete/' . $el->student_id}}">
                        <i class="fas fa-trash"></i>
                        Delete
                        </a>
                     @endif
                  </td>
               </tr>
               @endforeach
            @else
               <tr colspan="3">Student Empty</tr>
            @endif
            </tbody>
         </table>

         
      </div>
      <!-- /.card-body -->
   </div>
  
</div>

<link rel="stylesheet" href="{{asset('template')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script src="{{asset('template')}}/plugins/sweetalert2/sweetalert2.min.js"></script>


  @if(session('after_add_student_chinese_lower')) 

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
              title: 'Successfully add student chinese lower in the database.',
        });
        }, 1500);


      </script>

  @endif

   @if(session('after_delete_student_chinese_lower')) 

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
              title: 'Successfully deleted student chinese lower in the database.',
        });
        }, 1500);

      </script>

  @endif

@endsection
