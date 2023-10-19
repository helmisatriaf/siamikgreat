@extends('layouts.admin.master')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">

@if(sizeof($data) <= 0) <div class="row h-100">
        <div class="col-sm-12 my-auto text-center">
            <h3>Payment has never been created. Click the
                button below to get started !!!</h3>
            <a role="button" href="/admin/bills/create" class="btn btn-success mt-4">
                <i class="fa-solid fa-plus"></i>
                Create bill for student
            </a>
        </div>
</div>

@else
<h2 class="text-center display-4">SPP Student</h2>
<form class="mt-5" action="/admin/spp-students">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Grade:</label>
                        @php

                        $selected = $form && $form->grade? $form->grade : 'all';

                        @endphp
                        <select name="grade" class="form-control text-center" required>
                            <option {{$selected === 'all' ? 'selected' : ''}} value="all">-- All Grades --</option>
                            @foreach ($grade as $el)
                            
                                <option {{$selected == $el->id ? 'selected' : ''}} value="{{$el->id}}">{{$el->name. ' - ' .$el->class}}</option>
                                
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">

                        @php

                        $selected = $form && $form->sort? $form->sort : 'desc';

                        @endphp

                        <label>Sort order: <span style="color: red"></span></label>
                        <select name="sort" class="form-control">
                            <option value="desc" {{$selected === 'desc' ? 'selected' : ''}}>Descending</option>
                            <option value="asc" {{$selected === 'asc' ? 'selected' : ''}}>Ascending</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">

                        @php

                        $selected = $form && $form->order? $form->order : 'id';

                        @endphp

                        <label>Sort by:</label>
                        <select name="order" class="form-control">
                            <option {{$selected === 'id'? 'selected' : ''}} value="id">Register</option>
                            <option {{$selected === 'name'? 'selected' : ''}} value="name">Name</option>
                            <option {{$selected === 'grade_id'? 'selected' : ''}} value="grade_id">Grade</option>
                        </select>

                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Set Spp: <span style="color: red"></span></label>

                        @php

                        $selected = $form && $form->status ? $form->status : 'all';

                        @endphp

                        <select name="status" class="form-control text-center">
                            <option {{$selected == 'all'? 'selected' : ''}} value="all">
                                -- All --
                            </option>
                            <option  {{$selected == 'true'? 'selected' : ''}} value="true">
                                Already
                            </option>
                            <option  {{$selected == 'false'? 'selected' : ''}} value="false">
                                Not yet
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group input-group-lg">
                    <input name="search" value="{{$form? $form->search : ''}}" type="search" class="form-control form-control-lg"
                        placeholder="Type your keywords here">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-lg btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<div class="card mt-5">
    <div class="card-header">
        <h3 class="card-title">Student</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped projects" style="margin-left:auto;margin-right:auto;">
            <thead>
                <tr>
                    <th style="width: 12%">
                        #
                    </th>
                    <th  style="width: 20%">
                        Student
                    </th>
                    <th >
                        Grade
                    </th>
                    <th  style="width: 8%">
                        Class
                    </th>
                    <th  style="width: 15%">
                        Spp
                    </th>
                    
                    <th class="text-center" style="width: 30%">
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $el)
                <tr id={{'index_student_' . $el->id}}>
                    <td>
                        {{ $loop->index + 1 }}
                    </td>
                    <td >
                        {{$el->name}}
                    </td>
                    <td >
                       {{$el->grade->name}}
                     </td>
                     <td >
                        {{$el->grade->class}}
                     <td >
                           <a>
                               @if($el->spp_student)
                               {{-- <h1 class="badge badge-success">already set</h1> --}}
                               IDR {{number_format($el->spp_student->amount - $el->spp_student->amount*$el->spp_student->discount/100, 0, ',', '.')}} <br>
                               @if ($el->spp_student->discount && $el->spp_student->discount>0)
                                    <small>Discount: {{$el->spp_student->discount}}%</small>
                               @endif
                               @else
                               <h1 class="badge badge-danger">not set yet</h1>
                               @endif
                           </a>
                        </td>
                        <td class="project-actions text-center toastsDefaultSuccess">

                            @if ($el->spp_student)
                                
                            <a class="btn btn-primary btn-lg"
                                href="/admin/spp-students/detail/{{$el->unique_id}}">
                                <i class="fas fa-folder">
                                </i>
                                View
                            </a>
                            @else
                            <a class="btn btn-success btn-lg"
                                href="/admin/spp-students/create/{{$el->unique_id}}">
                                <i class="fa-solid fa-plus"></i>
                                </i>
                                  Create
                            </a>
                            @endif
                        </td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>


@endif
</div>

@endsection
