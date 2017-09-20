<?php
require('helpers.php');

# Get our data and convert it to a JSON object
$schoolsJson = file_get_contents('schools.json');
$schools = json_decode($schoolsJson, true);

#define variables
$schoolTypesArray = [];
$neighborhood = '';
$grade = 0;

# If no school types were checked...
if (!isset($_GET['schoolTypes'])) {
    $schoolTypeResults = 'No school types were selected';
    $schoolTypeAlertType = 'alert-danger';
} else {
    $schoolTypeResults = 'School types chosen: ';
    $schoolTypeAlertType = 'alert-info';
    $schoolTypesArray = $_GET['schoolTypes'];
    foreach ($_GET['schoolTypes'] as $schoolType) {
        $schoolTypeResults .= $schoolType.', ';
    }
    # Remove trailing comma
    $schoolTypeResults = rtrim($schoolTypeResults, ', ');
}

# If no neighborhood was selected...
if (isset($_GET['neighborhood'])) {
    $neighborhood = $_GET['neighborhood'];
    if ($neighborhood == 'choose') {
        $neighborhoodAlertType = 'alert-danger';
        $neighborhoodResults = 'Please choose a neighborhood.';
    } else {
        $neighborhoodAlertType = 'alert-info';
        $neighborhoodResults = 'Neighborhood chosen: '.$neighborhood;
    }
}

# If no grade was specified...
if (isset($_GET['grade'])) {
    $grade = $_GET['grade'];
    if ($grade == '') {
        $gradeAlertType = 'alert-danger';
        $gradeResults = 'Please choose a grade.';
    } else {
        $gradeAlertType = 'alert-info';
        $gradeResults = 'Grade chosen: '.$grade;
    }
}

#No filtering is necessary if no search criteria has been specified
if (!isset($_GET['schoolTypes']) AND !isset($_GET['neighborhood']) AND !isset($_GET['grade']) ) {
    return $schools;
}

#school filtering
foreach ($schools as $name => $school) {
    if ( !(in_array($school['type'], $schoolTypesArray)) OR !(($grade<=$school['gradeCeiling'] && $grade>=$school['gradeFloor'])) OR !($neighborhood == $school['neighborhood']))  {
        unset($schools[$name]);
    }
}




