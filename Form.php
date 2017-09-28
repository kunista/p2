<?php

namespace DWA;

# For examples, see:
# https://github.com/susanBuck/dwa15-php-practice/blob/master/formDemo.php
# https://github.com/susanBuck/dwa15-php-practice/blob/master/formDemoLogic.php
#
# In action:
# http://php-practice.dwa15.com/formDemo.php

class Form
{

    /**
    * Properties
    */
    private $request;
    public $hasErrors = false;


    /**
    *
    */
    public function __construct($postOrGet)
    {
        # Store form data (POST or GET) in a class property called $request
        $this->request = $postOrGet;
    }


    /**
    * Get a value from the request, with the option of including a default
    * if the value is not set.
    * Example usage:
    *   $email = $this->get('email', 'example@gmail.com');
    */
    public function get($name, $default = null)
    {
        $value = isset($this->request[$name]) ? $this->request[$name] : $default;

        return $value;
    }

    /**
     * Get a value of the array from the request, with the option of including a default
     * if the value is not set.
     * Example usage:
     *   $schoolTypeResults = $this->getCheckboxArray('schoolTypes','All') ;
     */
    public function getCheckboxArray($name, $default = null)
    {
        if (isset($this->request[$name])) {
            $value = '';
            $array = $this->request[$name];
            foreach ($array as $arrayValue) {
                $value .= $arrayValue.', ';
            }
            # Remove trailing comma
            $value = rtrim($value, ', ');
        } else {
          $value = $default;
        }

        return $value;
    }


    /**
    * Determines if a single checkbox is checked
    * Example usage:
    *   <input type='checkbox' name='caseSensitive' <?php if($form->isChosen('caseSensitive')) echo 'CHECKED' ?>>
    */
    public function isChosen($name)
    {
        $value = isset($this->request[$name]) ? true : false;

        return $value;
    }




    /**
    * Use in display files to prefill the values of fields if those values are in the request
    * Second optional parameter lets you set a default value if value does not exist
    *
    * Example usage:
    *   <input type='text' name='email' value='<?=$form->prefill('email', "example@gmail.com")?>'>
    */
    public function prefill($field, $default = '', $sanitize = true)
    {

        if (isset($this->request[$field])) {
            if ($sanitize) {
                return $this->sanitize($this->request[$field]);
            } else {
                return $this->request[$field];
            }
        } else {
            return $default;
        }
    }


    /**
    * Returns True if *either* GET or POST have been submitted
    */
    public function isSubmitted()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' || !empty($_GET);
    }


    /**
    * Strips HTML characters; works with arrays or scalar values
    */
    public function sanitize($mixed = null)
    {
        if (!is_array($mixed)) {
            return $this->convertHtmlEntities($mixed);
        }

        function arrayMapRecursive($callback, $array)
        {
            $func = function ($item) use (&$func, &$callback) {
                return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
            };

            return array_map($func, $array);
        }

        return arrayMapRecursive('convertHtmlEntities', $mixed);
    }


    /**
    *
    */
    private function convertHtmlEntities($mixed)
    {
        return htmlentities($mixed, ENT_QUOTES, "UTF-8");
    }


    /**
     * Given an array of fields => validation rules
     * Will loop through each field's rules
     * Returns an array of error messages
     *
     * Stops after the first error for a given field
     *
     * Available rules: alphaNumeric, alpha, numeric, required, email, min:x, max:x
     */
    public function validate($fieldsToValidate)
    {

        $errors = [];

        foreach ($fieldsToValidate as $fieldName => $rules) {
            # Each rule is separated by a |
            $rules = explode('|', $rules);

            foreach ($rules as $rule) {
                # Get the value for this field from the request
                $value = $this->get($fieldName);

                # Handle any parameters with the rule, e.g. max:99
                $parameter = null;
                if (strstr($rule, ':')) {
                    list($rule, $parameter) = explode(':', $rule);
                }

                # Run the validation test with the given rule
                $test = $this->$rule($value, $parameter);

                # Test failed
                if (!$test) {
                    $errors[] = 'The field '.$fieldName.$this->getErrorMessage($rule, $parameter);

                    # Only indicate one error per field
                    break;
                }
            }
        }

        # Set public property hasErrors as Boolean
        $this->hasErrors = !empty($errors);

        return $errors;
    }


    /**
    * Given a String rule like 'alphaNumeric' or 'required'
    * It'll return a String message appropriate for that rule
    * Default message is used if no message is set for a given rule
    */
    private function getErrorMessage($rule, $parameter = null)
    {
        $language = [
            'alphaNumeric' => ' can only contain letters or numbers.',
            'alpha' => ' can only contain letters',
            'numeric' => ' can only contain numbers',
            'required' => ' is required.',
            'email' => ' is not a valid email address.',
            'min' => ' has to be greater than '.$parameter,
            'max' => ' has to be less than '.$parameter,
        ];

        # If a message for the rule was found, use that, otherwise default to " has an error"
        $message = isset($language[$rule]) ? $language[$rule] : ' has an error.';

        return $message;
    }


    ### VALIDATION METHODS FOUND BELOW HERE ###

    /**
    * Returns boolean if given value contains only letters/numbers/spaces
    */
    private function alphaNumeric($value)
    {
        return ctype_alnum(str_replace(' ', '', $value));
    }


    /**
    * Returns boolean if given value contains only letters/spaces
    */
    private function alpha($value)
    {
        return ctype_alpha(str_replace(' ', '', $value));
    }


    /**
    * Returns boolean if given value contains only numbers
    */
    private function numeric($value)
    {
        return ctype_digit(str_replace(' ', '', $value));
    }


    /**
    * Returns boolean if the given value is not blank
    */
    private function required($value)
    {
        $value = trim($value);
        return $value != '' && isset($value) && !is_null($value);
    }


    /**
    * Returns boolean if the given value is a valid email address
    */
    private function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }


    /**
    * Returns value if the given value is GREATER THAN (non-inclusive) the given parameter
    */
    private function min($value, $parameter)
    {
        return floatval($value) > floatval($parameter);
    }


    /**
    * Returns value if the given value is LESS THAN (non-inclusive) the given parameter
    */
    private function max($value, $parameter)
    {
        return floatval($value) < floatval($parameter);
    }
} # end of class
