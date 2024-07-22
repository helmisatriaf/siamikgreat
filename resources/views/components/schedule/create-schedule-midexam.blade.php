@extends('layouts.admin.master')
@section('content')

<div class="container-fluid" style="overflow-x:auto;">
    <div class="row">
        <div class="col">
            <nav aria-label="breadcrumb" class="bg-light rounded-3 p-3 mb-3">
                <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item"><a href="{{url('' .session('role'). '/schedules/midexams')}}">Mid Exam Schedule</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Schedule Mid Exam {{ $data['grade'][0]['name'] }} - {{ $data['grade'][0]['class'] }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- left column -->
        <div class="col-md-8">
        @if (session('role') == 'superadmin')
            <form method="POST" action={{route('actionSuperCreateMidExam')}}>
        @elseif (session('role') == 'admin')
            <form method="POST" action={{route('actionAdminCreateMidExam')}}>
        @endif
        @csrf
            <!-- general form elements -->
            <div>
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">Create schedule mid exam</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-2 d-none">
                                <label for="semester">Semester<span style="color: red"> *</span></label>
                                <select required name="semester" class="form-control">
                                    <option value="">-- Select Semester -- </option>
                                    <option value="1" {{ session('semester') == '1' ? "selected" : "" }}>Semester 1</option>
                                    <option value="2" {{ session('semester') == '2' ? "selected" : "" }}>Semester 2</option>
                                </select>

                                @if($errors->has('semester'))
                                <p style="color: red">{{ $errors->first('semester') }}</p>
                                @endif
                            </div>

                            <div class="col-md-2 d-none">
                                <label for="type_schedule">Type Schedule<span style="color: red"> *</span></label>
                                <select required name="type_schedule" class="form-control" id="type_schedule">
                                    @foreach($data['typeSchedule'] as $el)
                                    <option value="{{ $el->id }}" selected>{{ $el->name }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('type_schedule'))
                                <p style="color: red">{{ $errors->first('type_schedule') }}</p>
                                @endif
                            </div>

                            <div class="col-md-2 d-none">
                                <label for="grade_id">Grade<span style="color: red"> *</span></label>
                                <select required name="grade_id" class="form-control" id="grade_id">
                                    @foreach($data['grade'] as $el)
                                    <option value="{{ $el->id }}" selected>{{ $el->name }} - {{ $el->class}}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('grade_id'))
                                <p style="color: red">{{ $errors->first('grade_id') }}</p>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <label for="date">Start Date Mid Exam<span style="color: red"> *</span></label>
                                <input name="date" type="date" class="form-control" id="date" required>
                                @if($errors->has('date'))
                                    <p style="color: red">{{ $errors->first('date') }}</p>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="end_date">End Date Mid Exam<span style="color: red"> *</span></label>
                                <input name="end_date" type="date" class="form-control" id="_end_date">
                                @if($errors->has('end_date'))
                                    <p style="color: red">{{ $errors->first('end_date') }}</p>
                                @endif
                            </div>
                        </div>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <th style="font-size:11px;">Subject</th>
                                <th style="font-size:11px;">Invigilater</th>
                                <th style="font-size:11px;">Days</th>
                                <th style="font-size:11px;">Start Time</th>
                                <th style="font-size:11px;">End Time</th>
                                <th style="font-size:11px;">Notes</th>
                                <th style="font-size:11px;">Action</th>
                            </thead>
                            <tbody id="scheduleTableBody">
                                <tr>
                                    <td>
                                        <select name="subject_id[]" class="form-control" id="subject_id"></select>
                                        @if($errors->has('subject_id'))
                                        <p style="color: red">{{ $errors->first('subject_id') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="teacher_id[]" class="form-control" id="teacher_id"> 
                                        <option value="" selected >-- Invigilater --</option>
                                        @foreach ($data['teacher'] as $te)
                                            <option value="{{ $te->id }}">{{ $te->name }}</option>
                                        @endforeach
                                        </select>
                                        @if($errors->has('teacher_id'))
                                        <p style="color: red">{{ $errors->first('teacher_id') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <select required name="day[]" class="form-control">
                                            <option value="" class="text-xs">Day</option>
                                            <option value="1">Monday</option>
                                            <option value="2">Tuesday</option>
                                            <option value="3">Wednesday</option>
                                            <option value="4">Thursday</option>
                                            <option value="5">Friday</option>
                                        </select>

                                        @if($errors->has('day'))
                                        <p style="color: red">{{ $errors->first('day') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="time" class="form-control" id="start_time" name="start_time[]">
                                        @if($errors->has('start_time'))
                                        <p style="color: red">{{ $errors->first('start_time') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="time" class="form-control" id="end_time" name="end_time[]">
                                        @if($errors->has('end_time'))
                                        <p style="color: red">{{ $errors->first('end_time') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <textarea name="notes[]" class="form-control" id="notes" cols="10" rows="1"></textarea>
                                        @if($errors->has('notes'))
                                        <p style="color: red">{{ $errors->first('notes') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm btn-tambah mt-1" title="Tambah Data" id="tambah"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus mt-1 d-none" title="Hapus Baris" id="hapus"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row d-flex justify-content-center">
                        <input role="button" type="submit" class="btn btn-success center col-11 m-3">
                    </div>
                </div>
            </div>
            </form>
        </div>

        <!-- right column -->
        <div class="col-md-4">
            <!-- general form elements -->
            <div>
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">See Schedule Mid Exam</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body" style="height:340px;overflow-y:auto;">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Teacher: <span style="color: red"></span></label>
                                    <select id="teacher-select" name="teacher_select" class="form-control">
                                        <option value="" selected>-- Select Teacher --</option>
                                        @foreach ($data['teacher'] as $tc)
                                            <option value="{{ $tc->id }}">{{ $tc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Grade: <span style="color: red"></span></label>
                                    <select id="grade-select" name="grade_select" class="form-control">
                                        <option value="" selected>-- Select Grade --</option>
                                        @foreach ($data['grades'] as $gr)
                                            <option value="{{ $gr->id }}">{{ $gr->name }} - {{ $gr->class }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="scheduleTeacher"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Function to add a new row
    function addRow() {
        var newRow = `<tr>
            <td>
                <select name="subject_id[]" class="form-control subject_id">
                    <option value="" selected > Select Subject</option>
                </select>
            </td>
            <td>
                <select name="teacher_id[]" class="form-control" id="teacher_id"> 
                    <option value="" selected >-- Select Invigilater --</option>
                    @foreach ($data['teacher'] as $te)
                        <option value="{{ $te->id }}">{{ $te->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select required name="day[]" class="form-control">
                    <option value="" class="text-xs">Day</option>
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                </select>
            </td>
            <td>
                <input type="time" class="form-control" name="start_time[]">
            </td>
            <td>
                <input type="time" class="form-control" name="end_time[]">
            </td>
            <td>
                <textarea name="notes[]" class="form-control" cols="10" rows="1"></textarea>
            </td>
            <td>
                <button type="button" class="btn btn-success btn-sm btn-tambah mt-1" title="Tambah Data"><i class="fa fa-plus"></i></button>
                <button type="button" class="btn btn-danger btn-sm btn-hapus mt-1" title="Hapus Baris"><i class="fa fa-times"></i></button>
            </td>
        </tr>`;
        $('#scheduleTableBody').append(newRow);

        // Call the function to populate subject and teacher options for the new row
        const newSubjectSelect = $('#scheduleTableBody tr:last .subject_id');
        const newTeacherSelect = $('#scheduleTableBody tr:last .teacher_id');

        loadSubjectOptionExam($('#grade_id').val(), newSubjectSelect);
        newSubjectSelect.change(function() {
            loadTeacherOption($('#grade_id').val(), $(this).val(), newTeacherSelect);
        });

        updateHapusButtons();
    }

    // Function to update the visibility of the "Hapus" buttons
    function updateHapusButtons() {
        $('#scheduleTableBody tr').each(function(index, row) {
            var hapusButton = $(row).find('.btn-hapus');
            if (index === $('#scheduleTableBody tr').length - 1) {
                hapusButton.removeClass('d-none');
            } else {
                hapusButton.addClass('d-none');
            }
        });
    }

    // Event listener for the "Tambah" button
    $('#scheduleTableBody').on('click', '.btn-tambah', function() {
        addRow();
    });

    // Event listener for the "Hapus" button
    $('#scheduleTableBody').on('click', '.btn-hapus', function() {
        $(this).closest('tr').remove();
        updateHapusButtons();
    });

    // Initial call to update the visibility of the "Hapus" buttons
    updateHapusButtons();
});

function loadSubjectOptionExam(gradeId, subjectSelect) {
    // Clear existing options and add the default option
    subjectSelect.html('<option value="" selected >Subject</option>');

    fetch(`/get-subjects/${gradeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                // If no subjects, add "Subject Empty" option
                const option = document.createElement('option');
                option.value = '';
                option.text = 'Subject Empty';
                subjectSelect.append(option);
            } else {
                data.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.text = subject.name_subject;
                    subjectSelect.append(option);
                });
            }
        })
        .catch(error => console.error(error));
}

// Call loadSubjectOptionExam if grade_id is already selected
window.onload = function() {
    const gradeSelect = document.getElementById('grade_id');
    const subjectSelect = document.getElementById('subject_id');

    if (gradeSelect.value) {
        loadSubjectOptionExam(gradeSelect.value, $(subjectSelect));
    }

    $(subjectSelect).change(function() {
        const teacherSelect = document.getElementById('teacher_id');
        loadTeacherOption(gradeSelect.value, $(this).val(), $(teacherSelect));
    });
};
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const teacherSelect = document.getElementById('teacher-select');
        const gradeSelect = document.getElementById('grade-select');
        const scheduleTeacherDiv = document.getElementById('scheduleTeacher');

        teacherSelect.addEventListener('change', validateAndFetchSchedule);
        gradeSelect.addEventListener('change', validateAndFetchSchedule);

        function validateAndFetchSchedule() {
            const teacher = teacherSelect.value || 'null';
            const grade = gradeSelect.value || 'null';

            fetchTeacherSchedule(teacher, grade);
        }

        function fetchTeacherSchedule(teacher, grade) {
            fetch(`/get-schedulemidexam-edit/${teacher}/${grade}`)
                .then(response => response.json())
                .then(data => {
                    renderScheduleTable(data, scheduleTeacherDiv);
                })
                .catch(error => console.error('Error fetching schedule:', error));
        }

        function renderScheduleTable(data, container) {
            let table = '<table class="table table-bordered">';
            table += `
                <thead>
                    <tr>
                        <th style="font-size:11px;">Grade</th>
                        <th style="font-size:11px;">Subject</th>
                        <th style="font-size:11px;">Invigilator</th>
                        <th style="font-size:11px;">Day</th>
                        <th style="font-size:11px;">Start Time</th>
                        <th style="font-size:11px;">End Time</th>
                    </tr>
                </thead>
                <tbody>
            `;

            const getDayName = (day) => {
                switch(day) {
                    case 1:
                        return "Monday";
                    case 2:
                        return "Tuesday";
                    case 3:
                        return "Wednesday";
                    case 4:
                        return "Thursday";
                    case 5:
                        return "Friday";
                    default:
                        return "";
                }
            }

            data.forEach((item, index) => {
                table += `
                    <tr>
                        <td style="font-size:11px;">${item.grade_name || ''}</td>
                        <td style="font-size:11px;">${item.subject_name}</td>
                        <td style="font-size:11px;">${item.teacher_name}</td>
                        <td style="font-size:11px;">${getDayName(item.day)}</td>
                        <td style="font-size:11px;">${item.start_time}</td>
                        <td style="font-size:11px;">${item.end_time}</td>
                    </tr>
                `;
            });

            table += '</tbody></table>';
            container.innerHTML = table;
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('schedule_same'))
            var teacherName = "{{ session('schedule_same') }}";
            Swal.fire({
                icon: 'error',
                title: 'Jadwal Sama',
                text: 'Terdapat jadwal yang sama dengan guru ' + teacherName,
                showConfirmButton: true
            });
        @endif
    });
</script>

@if(session('after_create_midexam_schedule')) 
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Successfully created mid exam schedule in the database.',
        });
    </script>
@endif

@endsection
