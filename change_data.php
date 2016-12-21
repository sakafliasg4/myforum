<?php
    require_once('utils.php');
    if(isset($_POST['submit'])){
        if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])){
            $error="<p class='alert alert-danger' id='error'><b>Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία (*)</b></p>";
        }else{
            if(!empty($_FILES['avatar']['name'])){ // AN EXEI EXEI STALEI EIKONA
                $target = "img/" .basename($_FILES['avatar']['name']);
                $check = getimagesize($_FILES["avatar"]["tmp_name"]);
                if($check !== false) { //ELENXOS AN EINAI EIKONA
                    if ($_FILES["avatar"]["size"] > 2048000) { // ELENXOS MEGETHOUS EIKONAS
                        $error= "<p class='alert alert-danger' id='error'><b>Συγνώμη, η εικόνα είναι αρετά μεγάλη πρέπει να είναι το πολύ 2 MB </b></p>";
                    }
                } else {
                    $error = "<p class='alert alert-danger' id='error'><b>Το αρχείο που επιλέξατε δεν είναι εικόνα</b></p>";
                }

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target)) {
                    $error = "<p class='alert alert-danger' id='success'><b>Η εικόνα ανέβηκε επιτυχώς</b></p>";
                } else {
                    $error = "<p class='alert alert-danger' id='error'><b>ΣΦΑΛΜΑ, η εικόνα δεν ανέβηκε</b></p>";
                }
            }

            if(preg_match("/\\s/", $_POST['username']) == true){
               $error="<p class='alert alert-danger' id='error'><b>Το username σας δεν πρέπει να περιέχει κενά </b></p>";
            }
            if(preg_match("/\\s/", $_POST['email']) == true){
               $error="<p class='alert alert-danger' id='error'><b>Το email σας δεν πρέπει να περιέχει κενά </b></p>";
            }
            $connect = db_connect();

            //metafora se metavlites
            $usr = mysqli_real_escape_string($connect, $_POST['username']);
            $email = mysqli_real_escape_string($connect, $_POST['email']);
            $pass = md5(mysqli_real_escape_string($connect, $_POST['password']));
            $image = $_FILES['avatar']['name'];

           if($pass != $users['password']){
               $error="<p class='alert alert-danger' id='error'><b>Ο κωδικός είναι λανθασμένος</b></p>";
           }else{
               if(strlen($_POST['password']) < 6){
                   $error="<p class='alert alert-danger' id='error'><b>Ο κωδικός σας πρέπει να έχει τουλάχιστον 6 χαρακτήρες</b></p>";
               }else{
                                    //check email dublicate
                   $question="SELECT * FROM users WHERE email LIKE '{$email}' OR username LIKE '{$usr}'";
                   //ektelesh erwthmatos
                   $result = mysqli_query($connect, $question) or die(mysql_error());
                   $users = mysqli_fetch_array($result);
                   if(@mysqli_num_rows($result)==1){
                        //eisagwgh sth vash
                        $user_id= $users['user_id'];
                        if(empty($image)){ //           an den yparxei eikona
                            $reg="UPDATE users SET username='$usr', email='$email' WHERE user_id='$user_id'";
                        }else{
                            $reg="UPDATE users SET username='$usr', email='$email', avatar='$target' WHERE user_id='$user_id'";
                        }
                        mysqli_query($connect, $reg) or die(mysql_error());
                        mysqli_close($connect);


                       foreach($users as $key=>$value){
                           unset($_SESSION[$key]);
                           $_SESSION[$key]=$value;
                       }

                        $success ="<p class='alert alert-success' id='error'><b>Η αλλαγή των στοιχείων έγινε με επιτυχία<br>Kάντε <a href='logout.php'>αποσύνδεση</a> και ύστερα συνδεθείτε ξανά με τα νέα σας στοιχεία</b></p>";

                   }else{
                       if($users['username'] == $usr && $users['email'] == $email){
                           $error = "<p class='alert alert-danger' id='error'>Το email και το username που επιλέξατε χρησιμοποιείται ήδη</p>";
                       }else if($users['username'] == $usr){
                            $error = "<p class='alert alert-danger' id='error'><b>Το username που επιλέξατε χρησιμοποιείται ήδη</b</p>";
                        }else{
                            $error = "<p class='alert alert-danger' id='error'><b>Το email που επιλέξατε χρησιμοποιείται ήδη</b></p>";
                        }
                   }
               }

            }

        }

    }


?>