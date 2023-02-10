<?php

require_once "Database.php";


class User extends Database
{
    // store
    public function store($request)
    {
        // $request will catch the value of $_POST
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $password   = $request['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, password) VALUES ('$first_name', '$last_name', '$username', '$password')";

        if($this->conn->query($sql)){
            header('location: ../views'); // go to index.php(login)
            exit;
        } 
        else {
            die('Error creating new user: ' . $this->conn->error);
        }  
    }
    // end store


    // login
    public function login($request)
    {
        $username = $request['username'];
        $password = $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($sql);

        # Check username
        if($result->num_rows == 1){
            # Check if the password is correct
            $user = $result->fetch_assoc();
            // $user = ['id' => 1, 'username' => 'james', 'password' => 'sdgh423$%!'];

            if(password_verify($password, $user['password'])){
                # Create session variables for future use.

                session_start();
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['first_name']. " " .$user['last_name'];

                header('location: ../views/dashboard.php');
                exit;
            }else {
                die('Password is incorrect');
            }
        }else {
            die('Username is not found.');
        }
    }
    // end login


    // logout
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
    }
    // end logout


    // getAllUsers
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        if($result = $this->conn->query($sql)){
            return $result;
        }
        else {
            die('Error retrieving all users: ' . $this->conn->error);
        }
    }
    // end getAllUsers


    // getUser
    public function getUser()
    {
        $id = $_SESSION['id'];

        $sql = "SELECT first_name, last_name, username, photo FROM users WHERE id = $id";

        if($result = $this->conn->query($sql)){
            return $result->fetch_assoc();
        }
        else {
            die('Error retrieving the user: ' . $this->conn->error);
        }
    }
    // end getUser


    // update
    public function update($request, $files)
    {
        session_start();
        $id         = $_SESSION['id'];
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $photo      = $files['photo']['name'];
        $tmp_photo  = $files['photo']['tmp_name'];

        $sql = "UPDATE users SET first_name =  '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

        if ($this->conn->query($sql)){
            $_SESSION['username']   = $username;
            $_SESSION['full_name']  = $first_name . " " . $last_name;

            # if there is an upload photo, save(name) it to db and save(image) to images folder.
            if ($photo){
                $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                $destination = "../assets/images/$photo";

                // Save the image name to db
                if ($this->conn->query($sql)){
                    // Save the actual image to images folder
                    if (move_uploaded_file($tmp_photo, $destination)){
                        header('location: ../views/dashboard.php');
                        exit;
                    } else {
                        die('Error moving the photo.');
                    }
                } else {
                    die('Error uploading photo: ' . $this->conn->error);
                }
            }
            header('location: ../views/dashboard.php');
            exit;
        } else {
            die('Error updating the user: ' . $this->conn->error);
        }
    }
    // end update


    // delete
    public function delete()
    {
        session_start();
        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if ($this->conn->query($sql)){
            $this->logout();
        }
        else {
            die('Error deleting your own account: ' . $this->conn->error);
        }
    }
    // end delete
}