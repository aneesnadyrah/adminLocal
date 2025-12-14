<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// NOTE - Static Class
require_once "config/functions/general.php";
require_once "config/functions/applicationEntry.php";
require_once "config/functions/wayleave.php";
require_once "config/functions/applicationAmendments.php";


// NOTE - Instance Class
require_once "config/functions/dashboard.php";
require_once "config/functions/roles.php";
require_once "config/functions/tasks.php";
require_once "config/functions/listing.php";
require_once "config/functions/priority.php";
require_once "config/functions/tracker.php";
require_once "config/functions/user.php";
require_once "config/functions/projects.php";
require_once "config/functions/projectsActive.php";
require_once "config/functions/teamSurvey.php";
require_once "config/functions/letter.php";
require_once "config/functions/surveyFinance.php";
require_once "config/functions/teamPPKD.php";
require_once "config/functions/sitevisit.php";
require_once "config/functions/surveyReport.php";

// NOTE - Add for video preparation, not thoroughly check
require_once "config/functions/report.php";
require_once "config/functions/projectDetails.php";
require_once "config/functions/permitting.php";
