<!doctype html>
<html lang="en">


<!-- Mirrored from codervent.com/rocker/demo/horizontal/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 30 May 2026 14:47:16 GMT -->
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />
	<!--plugins-->
	<link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
	<link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
	<link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet"/>
	<!-- loader-->
	<link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
	<script src="{{ asset('assets/js/pace.min.js') }}"></script>
	<!-- Bootstrap CSS -->
	<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
	<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />
	<!-- DataTables CSS -->
	<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@stack('styles')
	<title></title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
	 <!--start header wrapper-->	
	  <div class="header-wrapper">
		<!--start header -->
		<header>
			<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand gap-3">
					<div class="topbar-logo-header d-none d-lg-flex">
						<div class="">
							<img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
						</div>
						<div class="">
							<h4 class="logo-text"></h4>
						</div>
					</div>
					<div class="mobile-toggle-menu d-block d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"><i class='bx bx-menu'></i></div>
					@unless(auth()->check() && auth()->user()->student)
					<div class="search-bar d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
						<a href="avascript:;" class="btn d-flex align-items-center"><i class='bx bx-search'></i>Search</a>
					 </div>
					@endunless
					  <div class="top-menu ms-auto">
						<ul class="navbar-nav align-items-center gap-1">
							@unless(auth()->check() && auth()->user()->student)
							<li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
								<a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
								</a>
							</li>
							<li class="nav-item dark-mode d-none d-sm-flex">
								<a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
								</a>
							</li>
							@endunless


							@unless(auth()->check() && auth()->user()->student)
							<li class="nav-item dropdown dropdown-large">
								<a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown" id="notifBellBtn">
									@if ($notifCount > 0)
										<span class="alert-count" id="notifBadge">{{ $notifCount > 99 ? '99+' : $notifCount }}</span>
									@endif
									<i class='bx bx-bell'></i>
								</a>
								<div class="dropdown-menu dropdown-menu-end" style="min-width: 360px; max-height: 450px; overflow-y: auto;">
									<a href="javascript:;">
										<div class="msg-header">
											<p class="msg-header-title">Notifications</p>
											<p class="msg-header-badge" id="notifHeaderBadge">{{ $notifCount > 0 ? $notifCount . ' New' : 'No new' }}</p>
										</div>
									</a>
									<div class="header-notifications-list" id="notifList">
										@if ($notifCount > 0 && $notifRecentSubmissions->count() > 0)
											@foreach ($notifRecentSubmissions as $sub)
												<a class="dropdown-item" href="{{ route('admin.submissions.show', $sub->id) }}">
													<div class="d-flex align-items-center">
														<div class="notify bg-light-primary text-primary">
															<i class="bx bx-code-block"></i>
														</div>
														<div class="flex-grow-1">
															<h6 class="msg-name">
																{{ $sub->student->first_name ?? 'Unknown' }} {{ $sub->student->last_name ?? '' }}
																<span class="msg-time float-end">{{ $sub->submitted_at?->diffForHumans() ?? '' }}</span>
															</h6>
															<p class="msg-info">Submitted to <strong>{{ $sub->quiz->name ?? 'a quiz' }}</strong></p>
														</div>
													</div>
												</a>
											@endforeach
										@else
											<div class="text-center py-4 text-muted">
												<i class="bx bx-bell-off font-35 d-block mb-2"></i>
												<p class="mb-0 small">No new submissions since you last checked.</p>
											</div>
										@endif
									</div>
									@if ($notifCount > 0)
										<div class="text-center msg-footer">
											<button class="btn btn-primary w-100" id="markAllReadBtn">
												<i class="bx bx-check-double"></i> Mark All as Read
											</button>
										</div>
									@else
										<a href="{{ route('admin.submissions.index') }}">
											<div class="text-center msg-footer">
												<button class="btn btn-outline-primary w-100">
													<i class="bx bx-task"></i> View All Submissions
												</button>
											</div>
										</a>
									@endif
								</div>
							</li>
							@endunless
							
						</ul>
					</div>
					<div class="user-box dropdown px-3">
						<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $nameParts = explode(' ', auth()->user()->name ?? 'User');
                                $initials = isset($nameParts[1]) ? substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1) : substr($nameParts[0], 0, 1);
                            @endphp
							<div class="user-img d-flex align-items-center justify-content-center bg-primary text-white fw-bold">
                                {{ strtoupper($initials) }}
                            </div>
							<div class="user-info">
								<p class="user-name mb-0">{{ auth()->user()->name ?? 'Guest' }}</p>
								<p class="designattion mb-0 text-capitalize">{{ auth()->user()->employee->role ?? 'User' }}</p>
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->student ? route('student.profile.edit') : route('profile.edit') }}"><i class="bx bx-user fs-5"></i><span>Profile</span></a>
						</li>
							<li>
								<div class="dropdown-divider mb-0"></div>
							</li>
							<li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item d-flex align-items-center" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="bx bx-log-out-circle fs-5"></i><span>Logout</span>
                                    </a>
                                </form>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		<!--end header -->
		<!--navigation-->
		   <div class="primary-menu">
			   <nav class="navbar navbar-expand-lg align-items-center">
				  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
					<div class="offcanvas-header border-bottom">
						<div class="d-flex align-items-center">
							<div class="">
								<img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
							</div>
							<div class="">
								<h4 class="logo-text"></h4>
							</div>
						</div>
					  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
					</div>
					<div class="offcanvas-body">
					  <ul class="navbar-nav align-items-center flex-grow-1">
						@if(auth()->check() && auth()->user()->student)
						  <!-- Student Navigation Links -->
						  <li class="nav-item">
							<a class="nav-link" href="{{ route('student.dashboard') }}">
								<div class="parent-icon"><i class='bx bx-home-alt'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Dashboard</div>
							</a>
						  </li>
						  <li class="nav-item">
							<a class="nav-link" href="#">
								<div class="parent-icon"><i class='bx bx-book-open'></i>
								</div>
								<div class="menu-title d-flex align-items-center">My Exams</div>
							</a>
						  </li>
						  <li class="nav-item">
							<a class="nav-link" href="#">
								<div class="parent-icon"><i class='bx bx-brain'></i>
								</div>
								<div class="menu-title d-flex align-items-center">My Quizzes</div>
							</a>
						  </li>
						@else
						  <!-- Admin Navigation Links -->
						  <li class="nav-item">
							<a class="nav-link" href="{{ route('dashboard') }}">
								<div class="parent-icon"><i class='bx bx-home-alt'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Dashboard</div>
							</a>
						  </li>
						  <li class="nav-item">
							<a class="nav-link" href="{{ route('employees.index') }}">
								<div class="parent-icon"><i class='bx bx-group'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Users</div>
							</a>
						  </li>
						  <li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
								<div class="parent-icon"><i class='bx bx-brain'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Quizzes</div>
								<div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
							</a>
							<ul class="dropdown-menu">
							  <li><a class="dropdown-item" href="{{ route('quizzes.index') }}"><i class='bx bx-list-ul'></i>All Quizzes</a></li>
							  <li><a class="dropdown-item" href="{{ route('quizzes.create') }}"><i class='bx bx-plus-circle'></i>Create Quiz</a></li>
							</ul>
						  </li>
						  <li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
								<div class="parent-icon"><i class='bx bx-group'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Students</div>
								<div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
							</a>
							<ul class="dropdown-menu">
							  <li><a class="dropdown-item" href="{{ route('admin.students.index') }}"><i class='bx bx-user'></i>All Students</a></li>
							  <li><a class="dropdown-item" href="{{ route('admin.submissions.index') }}"><i class='bx bx-task'></i>Submissions</a></li>
							</ul>
						  </li>
						  <li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
								<div class="parent-icon"><i class='bx bx-cog'></i>
								</div>
								<div class="menu-title d-flex align-items-center">Settings</div>
								<div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
							</a>
							<ul class="dropdown-menu">
							  <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class='bx bx-cog'></i>AI Configuration</a></li>
							  <li><div class="dropdown-divider"></div></li>
							  <li><a class="dropdown-item" href="{{ route('years.index') }}"><i class='bx bx-calendar'></i>Years</a></li>
							  <li><a class="dropdown-item" href="{{ route('sections.index') }}"><i class='bx bx-layer'></i>Sections</a></li>
							</ul>
						  </li>
						@endif
					  </ul>
					</div>
				  </div>
			  </nav>
		</div>
		<!--end navigation-->
	   </div>
	   <!--end header wrapper-->
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				@yield('content')
			</div>
		</div>
		<!--end page wrapper -->



		<!-- search modal -->
		<div class="modal" id="SearchModal" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
			  <div class="modal-content">
				<div class="modal-header gap-2">
				  <div class="position-relative popup-search w-100">
					<input class="form-control form-control-lg ps-5 border border-3 border-primary" type="search" placeholder="Search">
					<span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i class='bx bx-search'></i></span>
				  </div>
				  <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="search-list">
					   <p class="mb-1">Html Templates</p>
					   <div class="list-group">
						  <a href="javascript:;" class="list-group-item list-group-item-action active align-items-center d-flex gap-2 py-1"><i class='bx bxl-angular fs-4'></i>Best Html Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vuejs fs-4'></i>Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-magento fs-4'></i>Responsive Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-shopify fs-4'></i>eCommerce Html Templates</a>
					   </div>
					   <p class="mb-1 mt-3">Web Designe Company</p>
					   <div class="list-group">
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-windows fs-4'></i>Best Html Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-dropbox fs-4' ></i>Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-opera fs-4'></i>Responsive Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-wordpress fs-4'></i>eCommerce Html Templates</a>
					   </div>
					   <p class="mb-1 mt-3">Software Development</p>
					   <div class="list-group">
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-mailchimp fs-4'></i>Best Html Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-zoom fs-4'></i>Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-sass fs-4'></i>Responsive Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vk fs-4'></i>eCommerce Html Templates</a>
					   </div>
					   <p class="mb-1 mt-3">Online Shoping Portals</p>
					   <div class="list-group">
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-slack fs-4'></i>Best Html Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-skype fs-4'></i>Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-twitter fs-4'></i>Responsive Html5 Templates</a>
						  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vimeo fs-4'></i>eCommerce Html Templates</a>
					   </div>
					</div>
				</div>
			  </div>
			</div>
		  </div>
		<!-- end search modal -->



		<!--start overlay-->
		<div class="overlay toggle-icon"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<footer class="page-footer">
			<p class="mb-0">Copyright © 2023. All right reserved.</p>
		</footer>
	</div>
	<!--end wrapper-->
	<!-- Bootstrap JS -->
	<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
	<!--plugins-->
	<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
	<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
	<script src="{{ asset('assets/plugins/chartjs/js/chart.js') }}"></script>
	<script src="{{ asset('assets/js/index.js') }}"></script>
	<!--app JS-->
	<script src="{{ asset('assets/js/app.js') }}"></script>
	<!-- DataTables JS -->
	<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
	@stack('scripts')

	{{-- Notification dismiss handler --}}
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var markBtn = document.getElementById('markAllReadBtn');
		if (markBtn) {
			markBtn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

				fetch('{{ route("admin.notifications.dismiss") }}', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': csrf,
						'Accept': 'application/json',
						'Content-Type': 'application/json',
					},
				}).then(function (res) {
					return res.json();
				}).then(function (data) {
					if (data.success) {
						// Remove the badge
						var badge = document.getElementById('notifBadge');
						if (badge) badge.remove();

						// Update header
						var headerBadge = document.getElementById('notifHeaderBadge');
						if (headerBadge) headerBadge.textContent = 'No new';

						// Replace notification list with empty state
						var list = document.getElementById('notifList');
						if (list) {
							list.innerHTML = '<div class="text-center py-4 text-muted">' +
								'<i class="bx bx-bell-off font-35 d-block mb-2"></i>' +
								'<p class="mb-0 small">No new submissions since you last checked.</p>' +
								'</div>';
						}

						// Replace footer with "View All" button
						var footer = markBtn.closest('.msg-footer');
						if (footer) {
							footer.innerHTML = '<a href="{{ route("admin.submissions.index") }}">' +
								'<button class="btn btn-outline-primary w-100">' +
								'<i class="bx bx-task"></i> View All Submissions</button></a>';
						}
					}
				}).catch(function (err) {
					console.error('Notification dismiss error:', err);
				});
			});
		}
	});
	</script>

</body>

<script>'undefined'=== typeof _trfq || (window._trfq = []);'undefined'=== typeof _trfd && (window._trfd=[]),_trfd.push({'tccl.baseHost':'secureserver.net'},{'ap':'cpsh-oh'},{'server':'p3plzcpnl509132'},{'dcenter':'p3'},{'cp_id':'10399385'},{'cp_cl':'8'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.</script><script src='../../../../img1.wsimg.com/signals/js/clients/scc-c2/scc-c2.min.js'></script>
<!-- Mirrored from codervent.com/rocker/demo/horizontal/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 30 May 2026 14:47:56 GMT -->
</html>