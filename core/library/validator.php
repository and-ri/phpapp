<?php

class Validator {
    protected $registry;
    protected $errors = array();

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    /**
     * Validate data against rules.
     *
     * $this->validator->validate($this->request->post, [
     *     'email'    => 'required|email',
     *     'password' => 'required|min:8',
     *     'confirm'  => 'match:password',
     *     'age'      => 'integer',
     *     'role'     => 'in:admin,editor,viewer'
     * ]);
     *
     * Returns true when everything passed; otherwise errors() holds
     * a message per failed field.
     */
    public function validate($data, $rules) {
        $this->errors = array();

        foreach ($rules as $field => $field_rules) {
            $value = isset($data[$field]) ? $data[$field] : null;

            foreach (explode('|', $field_rules) as $rule) {
                $param = null;

                if (strpos($rule, ':') !== false) {
                    list($rule, $param) = explode(':', $rule, 2);
                }

                // Skip all other rules for optional empty fields
                if ($rule != 'required' && ($value === null || $value === '')) {
                    continue;
                }

                if (!$this->check($rule, $value, $param, $data)) {
                    $this->addError($field, $rule, $param);

                    break; // First failed rule per field is enough
                }
            }
        }

        return !$this->errors;
    }

    public function errors() {
        return $this->errors;
    }

    public function error($field) {
        return isset($this->errors[$field]) ? $this->errors[$field] : '';
    }

    protected function check($rule, $value, $param, $data) {
        switch ($rule) {
            case 'required':
                return $value !== null && $value !== '' && $value !== array();
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'numeric':
                return is_numeric($value);
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT) !== false;
            case 'min':
                return mb_strlen((string)$value) >= (int)$param;
            case 'max':
                return mb_strlen((string)$value) <= (int)$param;
            case 'match':
                return isset($data[$param]) && $value === $data[$param];
            case 'in':
                return in_array((string)$value, explode(',', (string)$param), true);
            case 'regex':
                return (bool)preg_match($param, (string)$value);
            default:
                trigger_error('Validator: unknown rule ' . $rule . '!', E_USER_WARNING);
                return true;
        }
    }

    protected function addError($field, $rule, $param) {
        // A language key like error_validation_min overrides the default text
        $key = 'error_validation_' . $rule;

        $language = $this->registry->get('language');

        if ($language && $language->get($key) != $key) {
            $message = $language->get($key);
        } else {
            $message = $this->defaultMessage($rule);
        }

        $this->errors[$field] = str_replace(array('%field%', '%param%'), array($field, (string)$param), $message);
    }

    protected function defaultMessage($rule) {
        $messages = array(
            'required' => 'The %field% field is required.',
            'email' => 'The %field% field must be a valid email address.',
            'url' => 'The %field% field must be a valid URL.',
            'numeric' => 'The %field% field must be a number.',
            'integer' => 'The %field% field must be an integer.',
            'min' => 'The %field% field must be at least %param% characters.',
            'max' => 'The %field% field must be at most %param% characters.',
            'match' => 'The %field% field must match the %param% field.',
            'in' => 'The %field% field contains an invalid value.',
            'regex' => 'The %field% field has an invalid format.',
        );

        return isset($messages[$rule]) ? $messages[$rule] : 'The %field% field is invalid.';
    }
}
