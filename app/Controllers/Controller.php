<?php

namespace App\Controllers;

class Controller
{
    public function response($data, $status_code = 200, $headers = [])
    {
        http_response_code($status_code);

        header('Content-Type: Application/json');
        foreach ($headers as $header) {
            header($header);
        }
        echo json_encode($data);
        exit;
    }

    public function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $_rules) {
            $value = $data[$field] ?? null;

            foreach (explode("|", $_rules) as $rule) {
                if ($rule == "required" && !$value)
                    $errors[$field][] = "$field is required.";
                elseif ($rule == "number" && !is_numeric($value))
                    $errors[$field][] = "$field must be a numeric value.";
                elseif ($rule == "int" && !is_int($value))
                    $errors[$field][] = "$field must be an integer";
                elseif ($rule == "string" && !is_string($value))
                    $errors[$field][] = "$field must be a string";
                elseif (str_starts_with($rule, "min:") && strlen($field) < explode(":", $rule)[1])
                    $errors[$field][] = "$field must be at least " . explode(":", $rule)[1] . " characters.";
                elseif (str_starts_with($rule, "max:") && strlen($field) > explode(":", $rule)[1])
                    $errors[$field][] = "$field must be less than " . explode(":", $rule)[1] + 1 . " characters.";
            }
        }
        if (!empty($errors)) {
            $this->response(['errors' => $errors], 422);
            exit;
        }
    }
    function test($data)
    {
        $this->validate($data, ['name' => "required"]);
        echo $this->response(['data' => $data, 'message' => 'Work Successfully']);
    }
}
