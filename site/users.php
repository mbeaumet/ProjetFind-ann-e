<?php

require_once("db_connect.php");
require("function.php");
is_Connected();

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
   $method= $_POST;
} else {
    $method= $_GET;
}

switch($method["opt"]) {
    case "select_id":
        if(isset($_SESSION["users_id"]) && !empty(trim($_SESSION["users_id"]))){
            $req=$db->prepare("SELECT * FROM users WHERE users_id=?");
            $req->execute([$_SESSION["users_id"]]);
            $user=$req->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["success"=> true, "user"=>$user]); 
        }
        break;
    
    case "update_id":
        echo "br";
        if(!isset($_POST["firstname"], $_POST["lastname"], $_POST["email"],$_POST["user_name"],$_POST["pdw"]) && !empty(trim($_POST["firstname"])) && !empty(trim($_POST["lastname"])) && !empty(trim($_POST["email"])) && !empty(trim($_POST["user_name"]))) { 
            $password = "";
    
            if(isset($_POST["pwd"]) && !empty(trim($_POST["pwd"]))) {
                $password=",pwd = :pwd";
                $regex = "/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[a-zA-Z0-9]{8,12}$/";
                if (!preg_match($regex, $_POST["pwd"])) {
                    echo json_encode(["success" => false, "error" => "Mot de passe au mauvais format"]);
                    die;
                }
                $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
            } 
            $req=$db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, user_name=:user_name, $password WHERE users_id = :users_id");
            
            $req->bindValue(":firstname", $_POST["firstname"]);
            $req->bindValue(":lastname" , $_POST["lastname"]);
            $req->bindValue(":email" , $_POST["email"]);
            $req->bindValue(":user_name" , $_POST["user_name"]);

            if($password !="") {
                $req->bindValue(":pwd" , $hash);

            }

            $req->bindValue(":users_id",$_SESSION["users_id"]);
            $req->execute();
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "erreur de MAJ"]);
        }
        break;

        case 'delete_id':
            if (isset($_SESSION["users_id"]) && !empty(trim($_SESSION["users_id"]))){
                $req=$db->prepare("DELETE FROM users WHERE users_id=?");
                $req->execute([$_SESSION["users_id"]]);
                echo json_encode((["success"=>true ]));
    
            }else{
                echo json_encode((["success"=>false, "error"=>"erreur de suppression"]));
            }
        die;

    default:
        echo json_encode((["success"=>false, "error"=>"demand inconnu"]));
            
        break;
}