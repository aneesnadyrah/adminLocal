<?php
session_start();

// Check if session exists and if the username session variable is set
if (isset($_SESSION['username'])) {
   // Check if the URL is valid (e.g. it exists in your routing system)
   if (!isValidUrl($_SERVER['REQUEST_URI'])) {
      // Route to 404 page
      header("Location: /error/404");
      exit;
   }

   // Redirect to dashboard
   header("Location: /dashboard");
   exit;
}

// If no session and valid URL, route to login page
if (!isValidUrl($_SERVER['REQUEST_URI'])) {
   // Route to 404 page
   header("Location: /error/404");
   exit;
}

// Route to login page
header("Location: /auth/signin");
exit;

// Function to check if URL is valid
function isValidUrl($url)
{
   // Add your logic here to validate if the URL is valid
   // You can use a routing system or other validation methods

   // Example implementation:
   // List of valid URLs
   $validUrls = array(

      //Main Routes
      '/',
      '/index',
      '/dashboard',

      //Project Routes
      '/projects/create/entry',
      '/projects/create/update',
      '/projects/create/remove',
      '/projects/status',
      '/projects/tasks/approval',
      '/projects/tasks/updates',
      '/projects/tasks/payments',
      '/projects/tasks/pendings',
      '/projects/detail',

      //Map Routes
      '/maps/projects',
      '/maps/utilities',

      //Payment Routes
      '/payments/receipt',

      //Authentication Routes
      '/auth/signin',
      '/auth/signup',
      '/auth/signout',
      '/auth/verify'
   );

   // Check if the URL is in the list of valid URLs
   return in_array($url, $validUrls);
}


?>
