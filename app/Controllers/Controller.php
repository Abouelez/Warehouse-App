<?php

namespace App\Controllers;

use Config\Database;

class Controller
{
    public function response($data, $status_code = 200, $headers = [])
    {
        http_response_code($status_code);

        header('Content-Type: application/json');

        foreach ($headers as $header) {
            header($header);
        }
        echo json_encode($data);
        exit;
    }
    public function is_unique($table_with_field, $value)
    {

        $data = explode(',', $table_with_field); // form users,email to ['users', 'email']
        $table = $data[0];
        $field = $data[1];

        $connection = Database::get_instance()->get_connection();
        $query = "SELECT COUNT(*) FROM $table WHERE $field = :value";
        $stmt = $connection->prepare($query);
        $stmt->execute(['value' => $value]);

        return $stmt->fetchColumn() == 0;
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
                elseif (str_starts_with($rule, "min:") && strlen($value) < explode(":", $rule)[1])
                    $errors[$field][] = "$field must be at least " . explode(":", $rule)[1] . " characters.";
                elseif (str_starts_with($rule, "max:") && strlen($value) > explode(":", $rule)[1])
                    $errors[$field][] = "$field must be less than " . explode(":", $rule)[1] + 1 . " characters.";
                elseif (str_starts_with($rule, 'unique:') && !$this->is_unique(explode(':', $rule)[1], $value))
                    $errors[$field][] = "$field must be unique";
                elseif ($rule == "confirmed" && $value != $data['password_confirmation'])
                    $errors[$field][] = "Password confirmation does not match. Please ensure both passwords are identical.";
            }
        }
        if (!empty($errors)) {
            $this->response(['errors' => $errors], 422);
            exit;
        }
    }
    function test()
    {

        $var1 = 'key';
        $var2 = 'value';

        $arr = [$var1 => $var2];
        print_r($arr);
        // var_dump($this->is_unique('UOM,name', 'new test'));
        // die();

        // $this->validate($data, ['name' => 'unique:UOM,name']);
        // $s = Uom::insert($data);

        // return $this->response(['message' => 'inserted successfully', 'data' => $s]);
    }
}
