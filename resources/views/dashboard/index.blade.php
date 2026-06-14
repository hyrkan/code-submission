@extends('main_layout.master')

@section('content')
				<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
				   <div class="col">
					<div class="card radius-10 border-start border-0 border-4 border-danger">
					   <div class="card-body">
						   <div class="d-flex align-items-center">
							   <div>
								   <p class="mb-0 text-secondary">Active Quizzes</p>
								   <h4 class="my-1 text-danger">{{ $activeQuizzes }}</h4>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bx-brain'></i>
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
								   <p class="mb-0 text-secondary">Total Students</p>
								   <h4 class="my-1 text-success">{{ $totalStudents }}</h4>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bx-group'></i>
							   </div>
						   </div>
					   </div>
					</div>
				  </div>
				  <div class="col">
					<div class="card radius-10 border-start border-0 border-4 border-warning">
					   <div class="card-body">
						   <div class="d-flex align-items-center">
							   <div>
								   <p class="mb-0 text-secondary">Total Submissions</p>
								   <h4 class="my-1 text-warning">{{ $totalSubmissions }}</h4>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bx-task'></i>
							   </div>
						   </div>
					   </div>
					</div>
				  </div>
				</div><!--end row-->


				 <div class="card radius-10">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Recent Submissions</h6>
							</div>
						</div>
					</div>
                         <div class="card-body">
						 <div class="table-responsive">
						   <table class="table align-middle mb-0">
							<thead class="table-light">
							 <tr>
							   <th>Student</th>
							   <th>Quiz</th>
							   <th>Language</th>
							   <th>Status</th>
							   <th>Score</th>
							   <th>Submitted At</th>
							 </tr>
							 </thead>
							 <tbody>
							 @forelse($recentSubmissions as $submission)
							 <tr>
							  <td>{{ $submission->student->name ?? 'N/A' }}</td>
							  <td>{{ $submission->quiz->name ?? 'N/A' }}</td>
							  <td>{{ ucfirst($submission->language ?? '-') }}</td>
							  <td>
								@if($submission->status === 'graded')
									<span class="badge bg-gradient-quepal text-white shadow-sm w-100">Graded</span>
								@elseif($submission->status === 'pending')
									<span class="badge bg-gradient-blooker text-white shadow-sm w-100">Pending</span>
								@else
									<span class="badge bg-gradient-bloody text-white shadow-sm w-100">{{ ucfirst($submission->status) }}</span>
								@endif
							  </td>
							  <td>{{ $submission->score ?? '-' }}</td>
							  <td>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : '-' }}</td>
							 </tr>
							 @empty
							 <tr>
							  <td colspan="6" class="text-center text-muted py-4">No submissions yet.</td>
							 </tr>
							 @endforelse
						    </tbody>
						  </table>
						  </div>
						 </div>
					</div>



@endsection