@extends('layouts.admin.master')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">

    @if (session('role') == 'superadmin' || session('role') == 'admin')
    <a type="button" data-toggle="modal" data-target="#modalAddOtherSchedule" class="btn btn-success btn mt-5 ">   
        <i class="fa-solid fa-calendar-plus"></i>
        </i>   
        Add Other Schedule
    </a>
    <a href="{{url('/' . session('role') .'/schedules/schools/manage/otherSchedule') }}" class="btn btn-warning btn mt-5">   
        <i class="fa-solid fa-file"></i>
        </i>   
        Manage
    </a>
    @endif

    <div class="card card-dark mt-2">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">School Events & Exam Calendar</h5>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal Detail-->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="eventTitle"></p>
                <p id="eventDescription"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>

 <!-- Modal Add Other Schedule -->
<div class="modal fade" id="modalAddOtherSchedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create Other Schedule</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
        <div>
            @if (session('role') == 'superadmin')
                <form method="POST" action={{route('actionSuperCreateOtherSchedule')}}>
            @elseif (session('role') == 'admin')
                <form method="POST" action={{route('actionAdminCreateOtherSchedule')}}>
            @endif
                @csrf
                <div class="card card-dark">
                    <div class="card-body">

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="type_schedule">Type Schedule<span style="color: red"> *</span></label>
                            <select required name="type_schedule" class="form-control" id="type_schedule">
                                <option value="">-- TYPE SCHEDULE --</option>
                                @foreach($data as $el)
                                    <option value="{{ $el->id }}">{{ $el->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('type_schedule'))
                                <p style="color: red">{{ $errors->first('type_schedule') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="date">Date<span style="color: red"> *</span></label>
                            <input name="date" type="date" class="form-control" id="date" required>
                            
                            @if($errors->has('date'))
                                <p style="color: red">{{ $errors->first('date') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="end_date">Until<span style="color: red"></span></label>
                            <input name="end_date" type="date" class="form-control" id="_end_date">
                            
                            @if($errors->has('end_date'))
                                <p style="color: red">{{ $errors->first('end_date') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="notes">Notes<span style="color: red"> *</span></label>
                            <textarea required name="notes" class="form-control" id="notes" cols="10" rows="1"></textarea>
                            
                            @if($errors->has('notes'))
                                <p style="color: red">{{ $errors->first('notes') }}</p>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="row d-flex justify-content-center">
                    <input role="button" type="submit" class="btn btn-success center col-11 m-3">
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>



<link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<script src="{{ asset('template/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        var exams = @json($exams);
        var schedules = @json($schedules);

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
            },
            events: [
                ...exams.map(exam => ({
                    title: `${exam.type_exam} (${exam.name_exam} - ${exam.grade_name})`,
                    start: exam.date_exam,
                    description: `<br>Teacher : ${exam.teacher_name} <br>Grade : ${exam.grade_name} - ${exam.grade_class}`,
                    color: 'blue'
                })),
                ...schedules.map(schedule => ({
                    title: schedule.note,
                    start: schedule.date,
                    end: schedule.end_date,
                    description: schedule.note,
                    color: schedule.color,
                    jadwal: new Date(schedule.date).toLocaleDateString('id-ID', { month: 'long', day: 'numeric', year: 'numeric' }),
                    sampai: schedule.end_date ? new Date(schedule.end_date).toLocaleDateString('id-ID', { month: 'long', day: 'numeric', year: 'numeric' }) : null,
                })),
            ],
            eventClick: function(info) {
                document.getElementById('eventTitle').innerText = 'Event: ' + info.event.title;
                if (info.event.extendedProps.sampai === null) {
                    document.getElementById('eventDescription').innerHTML = 'Description: ' + info.event.extendedProps.description + '(' + info.event.extendedProps.jadwal + ')';
                }
                else {
                    document.getElementById('eventDescription').innerHTML = 'Description: ' + info.event.extendedProps.description + '(' + info.event.extendedProps.jadwal + ' until ' + info.event.extendedProps.sampai + ')';
                }
                var eventModal = new bootstrap.Modal(document.getElementById('eventModal'), {
                    keyboard: false
                });
                eventModal.show();
            }
        });
        calendar.render();
    });
</script>

@if(session('after_create_otherSchedule')) 
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
                title: 'Successfully created new other schedule in the database.'
            });
        }, 1500);
    </script>
@endif

@endsection