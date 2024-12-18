{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Home"}

{block name='content'}
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0">Announcements</h5>
				</div>
				<div class="card-body">
					<div class="accordion">
						{foreach from=$announcements key=announcement_key item=announcement}
							<div class="accordion-item">
								<div class="accordion-header">
									<button type="button" class="accordion-button {''|iif:($announcement_key eq 0):'collapsed'}"" data-bs-toggle="collapse"
										data-bs-target="#question{$announcement.id}">
										{$announcement.title}
									</button>
								</div>

								<div id="question{$announcement.id}" class="accordion-collapse collapse {'show'|iif:($announcement_key eq 0):''}">
									<div class="accordion-body">
										<p class="mb-3">{$announcement.details}</p>

										<div class="d-sm-flex align-items-sm-center">
											<span class="text-muted">Latest Update: {$announcement.date_updated}</span>
										</div>
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0">Calendar</h5>
				</div>
				<div class="card-body">
					<div class="cal-schedules"></div>
				</div>
			</div>
		</div>
	</div>

{/block}

{block name='styles'}
	<style type="text/css">

	</style>
{/block}

{block name='scripts'}
	{#JS_JQUERY#|assets}
	{#JS_FULLCALENDAR#|assets}

	<script src="{$assets_url}/js/vendor/visualization/echarts/echarts.min.js"></script>
	<script type="text/javascript">
		$(() => {
			// Define element
			const calendarBasicViewElement = document.querySelector('.cal-schedules');

			// Initialize
			if (calendarBasicViewElement) {
				const calendarBasicViewInit = new FullCalendar.Calendar(calendarBasicViewElement, {
					headerToolbar: {
						left: 'prev,next today',
						center: 'title',
						right: 'dayGridMonth,timeGridWeek,timeGridDay'
					},
					initialDate: new Date(),
					navLinks: true, // can click day/week names to navigate views
					nowIndicator: true,
					weekNumberCalculation: 'ISO',
					editable: false,
					selectable: true,
					direction: document.dir == 'rtl' ? 'rtl' : 'ltr',
					dayMaxEvents: true, // allow "more" link when too many events
					events: {$events|default:'[]'}
				});

				// Init
				calendarBasicViewInit.render();

				// Resize calendar when sidebar toggler is clicked
				document.querySelectorAll('.sidebar-control').forEach(function(sidebarToggle) {
					sidebarToggle.addEventListener('click', function() {
						calendarBasicViewInit.updateSize();
					});
				});
			}
		});
	</script>
{/block}