<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
// Check for errors in the connection
if (!$conn) {
    die("Error in connection: " . pg_last_error());
}

// Execute a SELECT query on the database
$query = "SELECT sys_users.created_at, sys_users.id, sys_users.full_name, sys_users.telegram_id, sys_users.profile_pic, ls_user_role.role AS role  
FROM sys_users LEFT JOIN ls_user_role ON ls_user_role.id = sys_users.role_id  WHERE activation = 0";
$result = pg_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Error in query: " . pg_last_error());
}

// fetch the rows from the query result as an associative array
$rows = pg_fetch_all($result);

 if(pg_num_rows($result) > 0)  { 
    foreach ($rows as $data) {
        echo ('<!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="approve-user'.$data['id'].'">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Pengesahan Akaun</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="d-flex justify-content-center symbol symbol-100px symbol-circle mb-4">
                            <img src="assets/media/avatars/blank.png" />
                        </div>
                        <div class="">
                            <!--begin::Section-->
                            <span class="d-flex align-items-center fs-4 fw-bold text-gray-600 mb-2">
                            <i class="fad fa-user me-4 fs-2"></i>'.$data['full_name'].'
                            </span>
                            <!--end::Section-->
                            <!--begin::Section-->
                            <span class="d-flex align-items-center fs-4 fw-bold text-gray-600 mb-2">
                                <i class="fad fa-circle-user me-4 fs-2"></i>'.$data['role'].'
                            </span>
                            <!--end::Section-->
                            <!--begin::Section-->
                            <span class="d-flex align-items-center fs-4 fw-bold text-gray-600 mb-2">
                                <i class="fab fa-telegram me-4 fs-2"></i>'.$data['telegram_id'].'
                            </span>
                            <!--end::Section-->
                            <!--begin::Section-->
                            <span class="d-flex align-items-center fs-4 fw-bold text-gray-600 mb-2">
                                <i class="fad fa-calendar me-4 fs-2"></i>'.$data['created_at'].'
                            </span>
                            <!--end::Section-->
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-danger">Tolak</button>
                        <button type="button" class="btn btn-success">Terima</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Modal - Upgrade plan-->');
    }
} else {

}

?>