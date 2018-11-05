<?php

class Controller
{
    protected $config;
    protected $db;

    public $createErrors = [];

    public function __construct()
    {
        $this->config = include('config.php');
        $this->db = new DB();
    }

    public function getConfig($key)
    {
        return $this->config[$key];
    }

    public function isFilter()
    {
        return (
            (isset($_GET['action']) && $_GET['action'] == 'filter') &&
            (isset($_GET['email']) && !empty($_GET['email']))
        );
    }

    public function isCreate()
    {

        return (

        (isset($_POST['action']) && $_POST['action'] == 'create')

        );
    }

    public function getAllUsers()
    {
        // query the users table and return all rows

        $allUsers = "SELECT * FROM users";
        return $this->db->query($allUsers);

    }


    public function filterUsersByEmail($mail)
    {
        // query the users table and return only rows
        // where email is equal $mail

        $email = "SELECT * FROM users WHERE email='$mail'";

        return $this->db->query($email);
    }

    public function createUser()
    {
        $email = ($_POST['email']);
        $name = $_POST['username'];
        $password = $_POST['password'];

        if (empty($name)) {
            $this->createErrors[] = "The name field is required";
        }

        //validate the email field
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->createErrors[] = "The email field is required and must be a valid email address";
        }

        //if the email field is valid check if the email already exist
        elseif(mysqli_num_rows($this->filterUsersByEmail($email)) > 0) {
            $this->createErrors[] = "User with this email already exist";
        }

        //check if the password field not empty
        if (empty($password)) {
            // if the password is empty push message to the
            // "createErrors" array message
            $this->createErrors[] = "The password field is required";
        }

        // if the "createErrors" array not empty
        if (!empty($this->createErrors)) {
            return $this->createErrors;
        }

        //if everything is ok and there are no errors
        //insert the new row to the users table
        if (empty($this->createErrors)) {
          $insert = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', MD5('$password'))";
          $this->db->query($insert);
        }

        //refresh the page and exit from the script
        header("Location: " . $_SERVER['REQUEST_URI']);

        exit;
    }

}