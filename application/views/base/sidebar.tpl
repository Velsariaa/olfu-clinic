<!-- Main sidebar -->
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

	<!-- Sidebar content -->
	<div class="sidebar-content">

		<!-- Sidebar header -->
		<div class="sidebar-section">
			<div class="sidebar-section-body d-flex justify-content-center">
				<h5 class="sidebar-resize-hide flex-grow-1 my-auto">Navigation</h5>

				<div>
					<button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
						<i class="ph-arrows-left-right"></i>
					</button>

					<button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
						<i class="ph-x"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- /sidebar header -->


		<!-- Main navigation -->
		<div class="sidebar-section">
			<ul class="nav nav-sidebar" data-nav-type="accordion">

				<!-- Main -->
				<li class="nav-item-header pt-0">
					<div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Main</div>
					<i class="ph-dots-three sidebar-resize-show"></i>
				</li>
				<li class="nav-item">
					<a href="{'home'|route}" class="nav-link {'active'|iif:($current_url_name == 'home')}">
						<i class="ph-house"></i>
						<span>Home</span>
					</a>
				</li>
				{if $user.user_type|in_array:[USER_TYPE_USER]}
				<li class="nav-item">
					<a href="{'users-appointments'|route}" class="nav-link {'active'|iif:($current_url_name == 'users-appointments')}">
						<i class="ph-list"></i>
						<span>My Appointments</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="{'users-transactions'|route}" class="nav-link {'active'|iif:($current_url_name == 'users-transactions')}">
						<i class="ph-list"></i>
						<span>My Transactions</span>
					</a>
				</li>
				{/if}
				{if $user.user_type|in_array:[USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN, USER_TYPE_NURSE, USER_TYPE_DOCTOR]}
				{if $user.user_type|in_array:[USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN, USER_TYPE_NURSE]}
				<li class="nav-item">
					<a href="{'announcements'|route}" class="nav-link {'active'|iif:($current_url_name == 'announcements')}">
						<i class="ph-list"></i>
						<span>Announcements</span>
					</a>
				</li>
				{/if}
				{* <li class="nav-item">
					<a href="{'specialties'|route}" class="nav-link {'active'|iif:($current_url_name == 'specialties')}">
						<i class="ph-list"></i>
						<span>Specialties</span>
					</a>
				</li> *}
				{if $user.user_type|in_array:[USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN, USER_TYPE_NURSE]}
				<li class="nav-item">
					<a href="{'transaction-types'|route}" class="nav-link {'active'|iif:($current_url_name == 'transaction-types')}">
						<i class="ph-list"></i>
						<span>Transaction Types</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="{'doctors'|route}" class="nav-link {'active'|iif:($current_url_name == 'doctors')}">
						<i class="ph-list"></i>
						<span>Doctors</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="{'nurses'|route}" class="nav-link {'active'|iif:($current_url_name == 'nurses')}">
						<i class="ph-list"></i>
						<span>Nurses</span>
					</a>
				</li>
				{/if}
				<li class="nav-item">
					<a href="{'patients'|route}" class="nav-link {'active'|iif:($current_url_name == 'patients')}">
						<i class="ph-list"></i>
						<span>Patients</span>
					</a>
				</li>
				{if $user.user_type|in_array:[USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN, USER_TYPE_NURSE]}
				<li class="nav-item">
					<a href="{'appointment-limits'|route}" class="nav-link {'active'|iif:($current_url_name == 'appointment-limits')}">
						<i class="ph-list"></i>
						<span>Appointment Limits</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="{'appointments'|route}" class="nav-link {'active'|iif:($current_url_name == 'appointments')}">
						<i class="ph-list"></i>
						<span>Appointments</span>
					</a>
				</li>
				{/if}
				<li class="nav-item">
					<a href="{'transactions'|route}" class="nav-link {'active'|iif:($current_url_name == 'transactions')}">
						<i class="ph-list"></i>
						<span>Transactions</span>
					</a>
				</li>
				{/if}
				{access_block access=($user.user_type|default:false)|in_array:[USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN]}
				<li class="nav-item">
					<a href="{'users'|route}" class="nav-link {'active'|iif:($current_url_name == 'users')}">
						<i class="ph-list"></i>
						<span>Students</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="{'admins'|route}" class="nav-link {'active'|iif:($current_url_name == 'admins')}">
						<i class="ph-users-three"></i>
						<span>Admins</span>
					</a>
				</li>
				{/access_block}
				{if $user.user_type|in_array:[USER_TYPE_USER]}
				<li class="nav-item">
					<a href="{'messages.user'|route}" class="nav-link {'active'|iif:($current_url_name == 'messages.user')}">
						<i class="ph-list"></i>
						<span>Message</span>
					</a>
				</li>
				{else}
				<li class="nav-item">
					<a href="{'messages.staff'|route}" class="nav-link {'active'|iif:($current_url_name|in_array:['messages.staff', 'messages.staff-user'])}">
						<i class="ph-list"></i>
						<span>Message</span>
					</a>
				</li>
				{/if}
				<li class="nav-item">
					<a href="{'home.about-us'|route}" class="nav-link {'active'|iif:($current_url_name == 'home.about-us')}">
						<i class="ph-list"></i>
						<span>About Us</span>
					</a>
				</li>
			</ul>
		</div>
		<!-- /main navigation -->

	</div>
	<!-- /sidebar content -->
	
</div>
		<!-- /main sidebar -->