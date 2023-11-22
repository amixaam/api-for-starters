<?php

class Database
{
    private $hostname = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'database';
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public function GetFunction($getVariable)
    {
        echo json_encode(array("message" => "Get function called!"));
    }
    public function PostFunction($postVariable)
    {
        echo json_encode(array("message" => "Post function called!"));
    }
    public function PutFunction($putVariable)
    {
        echo json_encode(array("message" => "Put function called!"));
    }
    public function DeleteFunction($deleteVariable)
    {
        echo json_encode(array("message" => "Delete function called!"));
    }
}
