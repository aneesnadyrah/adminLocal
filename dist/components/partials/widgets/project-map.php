<!--begin::List Widget 3-->
<div class="card card-xl-stretch mb-5" id="mappkd" height="400px" width="100%" >
</div>
<!--end:List Widget 3-->
<?php
// Add the custom CSS style directly in PHP
echo '<style>
    /* For mobile devices */
    @media (max-width: 767px) {
        #map {
            height: 200px; /* Adjust the height as per your requirement */
        }
    }

    /* For laptops and small desktops */
    @media (min-width: 768px) and (max-width: 991px) {
        #map {
            height: 250px; /* Adjust the height as per your requirement */
        }
    }

    /* For larger desktop devices */
    @media (min-width: 992px) {
        #map {
            height: 350px; /* Adjust the height as per your requirement */
        }
    }
</style>';
?>