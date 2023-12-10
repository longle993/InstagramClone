<?php
session_start();

class Connection{
    public $host = "localhost";
    public $user = "root";
    public $password = "";
    public $db_name = "chat";
    public $conn;
    public function __construct(){
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->db_name);
        
    }
}
class Register extends Connection{
    public function register_user($username, $fullName, $email, $password){
        if (empty($username) || empty($fullName) || empty($email) || empty($password)) {
            return 0; 
        }
        $duplicate = mysqli_query($this->conn, "SELECT * FROM users WHERE user_name = '$username' OR email = '$email'");
        if(mysqli_num_rows($duplicate) > 0){
            return 10;
        }
        else{
            $query = "INSERT INTO users VALUES('', '$username', '$fullName', '$email', '$password')";
            mysqli_query($this->conn, $query);
            return 1;
        }
    }
}
class Login extends Connection{
    public $id;
    public function login($usernameemail, $password){
        $result = mysqli_query($this->conn, "SELECT * FROM users WHERE user_name = '$usernameemail' OR email = '$usernameemail'");
        $row = mysqli_fetch_assoc($result);

        if(mysqli_num_rows($result) > 0){
            if($password == $row["password"]){
                $this->id = $row["user_id"];
                return 1;
            }
            else{
                return 10;
            }
        }
        else{
            return 100;
        }
    }  
    public function idUser(){
        return $this->id;
    }
}