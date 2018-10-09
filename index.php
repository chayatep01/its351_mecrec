<?php
   session_start();
   $error="";
   
       
        if (array_key_exists("logout", $_GET)) {
        
            unset($_SESSION);
            setcookie("id", "", time() - 60*60);
            $_COOKIE["id"] = "";  
           
        
        } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
            
            header("Location: loggedinpage.php");
            
        }
    
        
        if(array_key_exists("submit",$_POST)){
            include("connection.php");
            

        if(!$_POST['email']){
            $error .= "An Email is required <br>";
        }
        if(!$_POST['password']){
            $error .= "A Password is required <br>";
        }
        if($error !=""){
            $error = "<p>There were error(s) in your form:</p>".$error;
        } else {
            if ($_POST['signUp'] == '1') {
                
                    $query = " SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($db,$_POST['email'])."' LIMIT 1 " ;
                $result = mysqli_query($db,$query);

                if(mysqli_num_rows($result)>0){
                    echo "That email address is taken.";
                } else {
                    $query = "INSERT INTO `users`(`email`,`password`) VALUES ('".mysqli_real_escape_string($db,$_POST['email'])."','".mysqli_real_escape_string($db,$_POST['password'])."')";

                    if(!mysqli_query($db,$query)){
                        echo "<p>Could not sign up - please try again later.</p>";
                    }   else{
                        $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($db)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($db)." LIMIT 1 " ;
                        mysqli_query($db,$query);
                        $_SESSION = mysqli_insert_id($db);

                            if($_POST['stayedLoggedIn'] == '1'){
                                setcookie("id",mysqli_insert_id($db),time()+60*60*24);
                            }
                        header("Location: loggedinpage.php");
                        }
                }
            } else {
                $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($db, $_POST['email'])."'";
                
                $result = mysqli_query($db, $query); 
                
                $row = mysqli_fetch_array($result);
                
                if (isset($row)) {
                    
                    $hashedPassword = md5(md5($row['id']).$_POST['password']);
                    
                    if ($hashedPassword == $row['password']) {
                        
                        $_SESSION['id'] = $row['id'];
                        
                        if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", $row['id'], time() + 60*60*24*365);

                        } 

                        header("Location: loggedinpage.php");
                            
                    } else {
                        
                        $error = "That email/password combination could not be found.";
                        
                    }
                } else {
                        
                    $error = "That email/password combination could not be found.";
                
                }
        }
    }
}

?>




<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <title>Med rec</title>
    <link rel="shortcut icon" type="image/png" href="medrec.png" />
   

    <style>
        .container {
            text-align:center;
            width:400px;
            margin-top:150px;
        }
        body { 
            font-weight: 300;
            background: url(background.jpeg) no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        #signupForm {
            display:none;
        }
        h3 {
            font-weight:400;
        }
    </style>

  </head>
  <body>
    <div class="container">
        <h1>  Medicine Record <i class="fas fa-capsules"></i></h1>
        <p> <strong>Recording your dispensing historyy </strong></p>
        <div id="error"><?php echo $error?></div>

    <form method="post" id="signupForm">
        <p>Interested ? , Sign up with your <i class="far fa-heart"></i></p>
        <fieldset class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Your Email">
        </fieldset>

        <fieldset class="form-group">
            <input class="form-control" type="password" name="password" placeholder="Password">
        </fieldset>


        <div class="checkbox">
            <label>
                <input  type="checkbox" name="stayedLoggedIn" value=1>
                Stay logged in
            </label>
            <input type="hidden" name="signUp" value="1">
        </div>


        <fieldset class="form-group">
                
                <input class="btn btn-primary" type="submit" name="submit" value="Sign up !">
        </fieldset>
        <p><a href="#" class="toggleForms"> Log in</a></p>
    </form>
   
    <form method="post" id="loginForm">
        <p>Log in  with your email and password <i class="fas fa-sign-in-alt"></i> </p>
        
        <fieldset class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Your Email">
        </fieldset>

        <fieldset class="form-group">
            <input class="form-control" type="password" name="password" placeholder="Password">
        </fieldset>

        <div class="checkbox">
            <label>
                <input  type="checkbox" name="stayedLoggedIn" value=1>
                Stay logged in
            </label>
            <input type="hidden" name="signUp" value="0">
        </div>


        <fieldset class="form-group">
               
                <input class="btn btn-success"type="submit" name="submit" value="Log in">
        </fieldset>
        <p><a href="#" class="toggleForms"> Sign up</a></p>
    </form>
    


    </div>

<?php inculde("footer.php"); ?>