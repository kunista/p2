<?php

namespace Schools;

class School
{

    # Properties
    private $schools;

    # Methods
    public function __construct($jsonPath)
    {

        # Get our data and convert it to a JSON object
        $schoolsJson = file_get_contents($jsonPath);
        $this->schools= json_decode($schoolsJson, true);
    }


    public function getAll()
    {
        return $this->schools;
    }


    public function getBySearch($grade, $schoolType, $neighborhood)
    {
        $filteredSchools = [];
        #school filtering
        foreach ($this->schools as $name => $school) {
            if (($schoolType == 'All') AND ($neighborhood == 'All')) {
                if ((($school['gradeFloor']<=$grade) AND ($school['gradeCeiling']>=$grade)) ) {
                    $filteredSchools[$name] = $school;
                }
            }
            elseif ($schoolType == 'All') {
                if ((($school['gradeFloor']<=$grade) AND ($school['gradeCeiling']>=$grade)) AND ($school['neighborhood'] == $neighborhood)) {
                    $filteredSchools[$name] = $school;
                }

            }
            elseif ($neighborhood == 'All') {
                if ((($school['gradeFloor']<=$grade) AND ($school['gradeCeiling']>=$grade)) AND (in_array($school['type'],$schoolType))) {
                    $filteredSchools[$name] = $school;
                }

            }
            else {
                if ((($school['gradeFloor']<=$grade) AND ($school['gradeCeiling']>=$grade)) AND (in_array($school['type'],$schoolType)) AND ($school['neighborhood'] == $neighborhood)) {
                    $filteredSchools[$name] = $school;
                }
            }
       }
        return $filteredSchools;
    }
}
