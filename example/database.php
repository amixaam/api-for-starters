<?php

class Database
{
    private $hostname = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'task_management';
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public function GetConnection()
    {
        return $this->conn;
    }

    public function GetTasks()
    {
        $sql = "SELECT * FROM tasks";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $rows = array(); // Initialize an empty array to store the rows

            while ($row = $result->fetch_assoc()) {
                $rows[] = $row; // Add each row to the array
            }

            echo json_encode($rows); // Convert the array to a JSON string
        } else {
            echo json_encode(array("message" => "No results found")); // JSON response for no results
        }
    }
    public function GetTaskId($id)
    {
        $sql = "SELECT * FROM tasks WHERE id = $id";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $rows = array(); // Initialize an empty array to store the rows

            while ($row = $result->fetch_assoc()) {
                $rows[] = $row; // Add each row to the array
            }

            echo json_encode($rows); // Convert the array to a JSON string
        } else {
            echo json_encode(array("message" => "No results found")); // JSON response for no results
        }
    }
    public function PostTask($post)
    {
        $title = $post['title'];
        $description = $post['description'];
        $due_date = $post['due_date'];

        if (strlen($title) <= 0 || strlen($title) > 50) {
            echo json_encode(array("message" => "Title must be between 1 and 50 characters"));
            return;
        }
        if (strlen($description) <= 0 || strlen($description) > 250) {
            echo json_encode(array("message" => "Description must be between 1 and 250 characters"));
            return;
        }
        if ($due_date <= date("Y-m-d")) {
            echo json_encode(array("message" => "Due date must be in the future"));
            return;
        }

        $sql = "INSERT INTO tasks (title, description, due_date, status) VALUES ('$title', '$description', '$due_date', 0)";
        $result = $this->conn->query($sql);
        echo json_encode(array("message" => "Task created successfully", "id" => $this->conn->insert_id));
        return;
    }
    public function PutTaskId($id, $post)
    {
        // Check if task with given ID exists
        $sql = "SELECT * FROM tasks WHERE id = $id";
        $result = $this->conn->query($sql);
        if ($result->num_rows == 0) {
            echo json_encode(array("message" => "Task with given ID does not exist"));
            return;
        }

        // Define fields and their validation rules
        $fields = array(
            'title' => array('length' => 50),
            'description' => array('length' => 250),
            'due_date' => array('date' => true),
            'status' => array('values' => array(0, 1))
        );

        // Validate and prepare fields for update
        $fieldsToUpdate = array();
        foreach ($fields as $field => $rules) {
            if (isset($post[$field])) {
                $value = $post[$field];
                if ($value == null) {
                    continue;
                }
                if (isset($rules['length']) && (strlen($value) <= 0 || strlen($value) > $rules['length'])) {
                    echo json_encode(array("message" => ucfirst($field) . " must be between 1 and {$rules['length']} characters"));
                    return;
                }
                if (isset($rules['date']) && $value <= date("Y-m-d")) {
                    echo json_encode(array("message" => ucfirst($field) . " must be in the future"));
                    return;
                }
                if (isset($rules['values']) && !in_array($value, $rules['values'])) {
                    echo json_encode(array("message" => "Status must be either 0 or 1"));
                    return;
                }
                $fieldsToUpdate[] = "$field = '$value'";
            }
        }

        // Check if any fields need to be updated
        if (empty($fieldsToUpdate)) {
            echo json_encode(array("message" => "No fields to update"));
            return;
        }

        // Update task
        $sql = "UPDATE tasks SET " . implode(', ', $fieldsToUpdate) . " WHERE id = $id";
        $result = $this->conn->query($sql);

        echo json_encode(array("message" => "Task updated successfully"));
    }


    public function DeleteTaskId($id)
    {
        $checkSql = "SELECT id FROM tasks WHERE id = $id";
        $checkResult = $this->conn->query($checkSql);

        if ($checkResult->num_rows === 0) {
            echo json_encode(array("message" => "Task not found"));
            return;
        }

        $deleteSql = "DELETE FROM tasks WHERE id = $id";
        $deleteResult = $this->conn->query($deleteSql);

        if ($deleteResult) {
            echo json_encode(array("message" => "Task deleted successfully"));
            return;
        } else {
            echo json_encode(array("message" => "Error deleting task"));
            return;
        }
    }
}
