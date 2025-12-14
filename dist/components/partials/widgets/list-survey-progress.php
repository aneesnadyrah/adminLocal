<div class="card card-xxl-stretch mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Senarai Kerja Pengukuran Terkini</span>
			<span class="text-muted mt-1 fw-semibold fs-7">
				<?php
				// Call the totalProjectProgress() function and store the result in a variable
				$total = Survey::totalProjectProgress();
				echo $total; ?> Jumlah Kerja Ukur Minggu Ini</span>
		</h3>
		<div class="card-toolbar">
			<a href="/tasks/new" class="btn btn-sm btn-light btn-light-primary">
				Lihat semua</a>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body py-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table head-->
				<thead>
					<?php
					if(!empty($total)) {
					echo '<tr class="fw-bold text-muted">
						<th class="min-w-180px">Nama</th>
						<th class="min-w-100px">Jarak</th>
						<th class="min-w-150px">Progress</th>
						<th class="min-w-100px">Tarikh Mula</th>
						<th class="min-w-100px">Tarikh Jangkaan Tamat</th>
					</tr>';
					} else {
                        echo'
						<tr>';
                    }
					?>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>
					<?php
					if(!empty($total)) {
					// Call the totalProjectProgress() function and store the result in a variable

						foreach (Survey::tableWidgetSelection(1, 0) as $data) {
							$c = Survey::currentProgressUdm($data['sysID']);
							$p = Survey::listProvider($data['sysID']);
							$progress =  round(($c / $p[0]['application_length']) * 100);
							echo '
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<div class="symbol symbol-45px me-5">
												<img src="assets/media/provider/' . $data['providerID'] . '.webp" class="" alt="" />
											</div>
											<div class="d-flex justify-content-start flex-column">
												<a href="/projects/details.php?sid=' . $data['sysID'] . '" class="text-dark fw-bold text-hover-primary fs-6">' . $data['refNo'] . '</a>
												
											</div>
										</div>
									</td>
									<td>
										<span class="fw-semibold text-muted d-block fs-7">' . $data['length'] . '</span>
									</td>
									<td class="text-end">
										<div class="d-flex flex-column w-100 me-2">
											<div class="d-flex flex-stack mb-2">
												<span class="text-muted me-2 fs-7 fw-bold">' . $progress . '%</span>
											</div>
											<div class="progress h-6px w-100">' .
								'<div class="progress-bar ' . ($progress <= 25 ? 'bg-warning' : ($progress <= 50 ? 'bg-primary' : ($progress <= 75 ? 'bg-warning' : 'bg-success'))) . ' progress-bar-striped" role="progressbar" style="width: ' . $progress . '%" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</div>
									</td>
									<td>
										<span class="fw-semibold text-muted d-block fs-7 text-center">' . $data['startDate'] . '</span>
									</td>
									<td>
										<span class="fw-semibold text-muted d-block fs-7 text-center">' . $data['endDate'] . '</span>
									</td>
								</tr>';
						}
					} else {
                        echo'
                        <!--begin::Empty-->
						<div class="fw-semibold py-10 text-center">
							<div class="text-gray-600 fs-3 mb-2">Tiada Kerja Ukur Terkini</div>
							<div class="text-muted fs-6">Sila mula kerja ukur untuk paparkan data...</div>
						</div>
						<img src="assets/media/illustrations/empty/01.svg" alt="" class="w-100 h-200px" />
						<!--end::Empty-->';
                    }
					?>
				</tbody>
				<!--end::Table body-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>