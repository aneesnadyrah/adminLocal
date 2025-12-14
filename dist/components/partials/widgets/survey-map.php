<!--begin::List Widget -->
<div class="card mx-auto" id="map" style="box-shadow: none; ">
</div>
<!--end:List Widget -->

<?php
// Add the custom CSS style directly in PHP
echo '<style>
    /* Default styles for the map */
    #map {
        height: 350px; /* Adjust the default height as needed */
        width: 100%;
    }

    /* Media query for smaller screens (e.g., mobile devices) */
    @media (max-width: 767px) {
        #map {
            height: 200px; /* Adjust the height for smaller screens */
            width: 100%;
        }
    }

    /* Media query for medium-sized screens (e.g., tablets) */
    @media (min-width: 768px) and (max-width: 991px) {
        #map {
            height: 250px; /* Adjust the height for medium-sized screens */
            width: 100%;
        }
    }

    /* Media query for larger screens (e.g., desktops) */
    @media (min-width: 992px) {
        #map {
            height: 350px; /* Adjust the height for larger screens */
            width: 100%;
        }
    }
</style>';
?>