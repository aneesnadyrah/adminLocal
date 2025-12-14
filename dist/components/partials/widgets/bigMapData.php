<?php
    echo <<<TEMPLATE
        <div class="card card-flush h-md-100" id="map" width="100%" >
        </div>
    TEMPLATE;
    
    // Add the custom CSS style directly in PHP
    echo <<<STYLE
        <style>
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
        </style>
    STYLE;
?>