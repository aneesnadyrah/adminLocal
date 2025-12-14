<div class="col-xl-12 mb-5 mb-xl-10">
	<!--begin::List widget 6-->
	<div class="card card-flush h-md-100">
		<!--begin::Header-->
		<div class="card-header pt-7">
			<!--begin::Title-->
			<h3 class="card-title align-items-start flex-column">
				<span class="card-label fw-bold text-gray-800">Rujukan Lawatan Tapak</span>
				<span class="text-gray-400 mt-1 fw-semibold fs-6">10 Lawatan Tapak Minggu ini</span>
			</h3>
			<!--end::Title-->
			<!--begin::Toolbar-->
			<!-- <div class="card-toolbar">
				<a href="../../demo7/dist/apps/ecommerce/catalog/categories.html" class="btn btn-sm btn-light">View All</a>
			</div> -->
			<!--end::Toolbar-->
		</div>
		<!--end::Header-->
		<!--begin::Body-->
		<div class="card-body pt-5">
			<!--begin::Table container-->
			<div class="table-responsive hover-scroll-overlay-y pe-6 me-n6" style="height: 380px">
				<!--begin::Table-->
				<table class="table table-row-dashed align-middle gs-0 gy-4 my-0 " >
					<!--begin::Table head-->
					<thead>
						<tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
							<th class="p-0 min-w-175px
							<?php
							if($_SESSION['roleId'] == 32 || $_SESSION['roleId'] == 33){
								echo 'min-w-150px';
							} else if($_SESSION['roleId'] == 52 || $_SESSION['roleId'] == 51){
								echo 'min-w-250px';
							}
							?>
							pb-1">No Rujukan</th>
							<!-- <th class="ps-0 min-w-140px"></th> -->
							<th class="min-w-150px p-0 pb-1">Tarikh Lawatan Tapak</th>
						</tr>
					</thead>
					<!--end::Table head-->
					<!--begin::Table body-->
					<tbody >
						<tr>
							<td class="min-w-175px">
								<div class="position-relative ps-6 pe-3 py-4">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-info"></div>
									<a href="#" class="mb-1 text-dark text-hover-primary fw-bold">KUDR/LL/12/22/67/A2</a>
									<!-- <div class="fs-7 text-muted fw-bold">Created on 24 Dec 21</div> -->
								</div>
							</td>
							<td class="min-w-150px">
								<div class="mb-2 fw-bold">10:00 am - 06 Jan 23</div>
								<!-- <div class="fs-7 fw-bold text-muted"></div> -->
							</td>
						</tr>
						<tr>
							<td class="min-w-175px">
								<div class="position-relative ps-6 pe-3 py-4">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-warning"></div>
									<a href="#" class="mb-1 text-dark text-hover-primary fw-bold">KUDR/LL/12/22/67/A2</a>
									<!-- <div class="fs-7 text-muted fw-bold">Created on 24 Dec 21</div> -->
								</div>
							<td class="min-w-150px">
								<div class="mb-2 fw-bold">2:00 pm - 14 Feb 23</div>
								<!-- <div class="fs-7 fw-bold text-muted">Date range</div> -->
							</td>
						</tr>
						<tr>
							<td class="min-w-175px">
								<div class="position-relative ps-6 pe-3 py-4">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-success"></div>
									<a href="#" class="mb-1 text-dark text-hover-primary fw-bold">KUDR/LL/12/22/67/A2</a>
									<!-- <div class="fs-7 text-muted fw-bold">Created on 24 Dec 21</div> -->
								</div>
							</td>
							<td class="min-w-150px">
								<div class="mb-2 fw-bold">3:00 pm - 04 Apr 23</div>
								<!-- <div class="fs-7 fw-bold text-muted">Date range</div> -->
							</td>
						</tr>
						<tr>
							<td class="min-w-175px">
								<div class="position-relative ps-6 pe-3 py-4">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-danger"></div>
									<a href="#" class="mb-1 text-dark text-hover-primary fw-bold">KUDR/LL/12/22/67/A2</a>
									<!-- <div class="fs-7 text-muted fw-bold">Created on 24 Dec 21</div> -->
								</div>
							</td>
							<td class="min-w-150px">
								<div class="mb-2 fw-bold">9:00 am - 30 Jun 23</div>
								<!-- <div class="fs-7 fw-bold text-muted">Date range</div> -->
							</td>
						</tr>
						<tr>
							<td class="min-w-175px">
								<div class="position-relative ps-6 pe-3 py-4">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-primary"></div>
									<a href="#" class="mb-1 text-dark text-hover-primary fw-bold">KUDR/LL/12/22/67/A2</a>
									<!-- <div class="fs-7 text-muted fw-bold">Created on 24 Dec 21</div> -->
								</div>
							</td>
							<td class="min-w-150px">
								<div class="mb-2 fw-bold">12:00 pm - 01 Sep 23</div>
								<!-- <div class="fs-7 fw-bold text-muted">Date range</div> -->
							</td>
						</tr>
					</tbody>
					<!--end::Table body-->
				</table>
			</div>
			<!--end::Table-->
		</div>
		<!--end::Body-->
	</div>
	<!--end::List widget 6-->
</div>