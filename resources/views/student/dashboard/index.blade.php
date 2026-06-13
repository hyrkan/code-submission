@extends('main_layout.master')

@section('content')
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">My Courses</p>
                        <h4 class="my-1 text-info">3</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-book'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Upcoming Exams</p>
                        <h4 class="my-1 text-danger">2</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-edit'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Completed Quizzes</p>
                        <h4 class="my-1 text-success">15</h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-check-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card radius-10">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div>
                <h6 class="mb-0">Recent Activity</h6>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Activity</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Score/Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Midterm Exam</td>
                        <td>Computer Science 101</td>
                        <td>Oct 15, 2026</td>
                        <td><span class="badge bg-success text-white">95%</span></td>
                    </tr>
                    <tr>
                        <td>Quiz 4</td>
                        <td>Web Development</td>
                        <td>Oct 10, 2026</td>
                        <td><span class="badge bg-success text-white">100%</span></td>
                    </tr>
                    <tr>
                        <td>Assignment 2</td>
                        <td>Data Structures</td>
                        <td>Oct 05, 2026</td>
                        <td><span class="badge bg-warning text-dark">Pending Grading</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
