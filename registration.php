<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])){
            $fullName = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordConfirm = $_POST["confirm_password"];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $errors = array();

            if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordConfirm)){
                array_push($errors,"All fields are required");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Email is not valid");
            }
            if (strlen($password)<8){
                array_push($errors,"Password must be at least 8 characters long");
            }
            if ($password !== $passwordConfirm){
                array_push($errors,"Password does not match");
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCouunt = mysqli_num_rows($result);

            if($rowCouunt>0){
                array_push($errors,"Email already exists!");
            }

            if (count($errors) > 0){
                foreach ($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
            else{
                
                $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

                if($prepareStmt){
                    mysqli_stmt_bind_param($stmt,"sss", $fullName, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You have registered successfully</div>";
                }
                else{
                    die("Something went wrong.");
                }
            } 
        }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email Address">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>  
        <div><p>Already have an account? <a href="login.php">Login here</a></p></div>  
    </div>
</body>
</html>