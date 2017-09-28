<?php
require('Form.php');
require('helpers.php');
require('School.php');

use DWA\Form;
use Schools\School;
$form = new Form($_GET);
$school = new School('schools.json');

$schoolTypeResults = $form->getCheckboxArray('schoolTypes','All') ;

if ($form->isSubmitted()) {
    # Retrieve data from form
    $grade = $form->get('grade',0);
    $schoolTypesArray = $form->get('schoolTypes','All');
    $schoolTypeResults = $form->getCheckboxArray('schoolTypes','All') ;
    $neighborhood = $form->get('neighborhood','All');
    # Validate
    $errors = $form->validate([
        'grade' => 'required',
        'grade' => 'numeric',
        'grade' => 'min:0',
        'grade' => 'max:13'
    ]);

    # If there were no validation errors, proceed...
    if (empty($errors)) {

        $schools = $school->getBySearch($grade,$schoolTypesArray,$neighborhood);

        if ((count($schools) == 0)) {
            $haveResults = false;
        } else {
            $haveResults = true;
        }
    }
}


