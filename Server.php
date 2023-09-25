<?php
    session_start();
?>
<html>
    <head>
        <title>connection</title>
        <script type="text/js">

        </script>
        <style type="text/css">
			body{
				margin-top:40px;
			}
			.div{
				margin:3%;
				position:center;
				width:75%;
				
				border:4px solid black;
				border-radius: 10px;
			}
			.d1, .d2{
				margin:2%;
				width:46%;
				height:200px;
				border:2px solid black;
				float:right;
				margin-left:1%;
				border-radius: 8px;
			}
			.d1{
				float:left;
				margin-left:2%;
				margin-right:1%
			}
			.title{
				margin-top:8%;
			}
			.class1{
				margin:5%;
				margin-top:8%
			}
			.class2{
				margin-top:10%;
			}
			input[type=text], input[type=date] {
				width: 50%;
				height:40px;
				border: 2px solid #ccc;
				border-radius: 4px;
				font-size: 16px;
				padding: 12px 20px 12px 30px;
			}
            select {
                width: 50%;
				height:40px;
				border: 2px solid #ccc;
				border-radius: 4px;
				font-size: 16px;
				padding: 0px 20px 0px 30px;
                margin-left:1px;
                margin-bottom:10px;
            }
			input[type=submit] {
				background-color: black;
				border: 1px solid black;
				border-radius:8px;
				color: white;
				width:25%;
				height:40px;
				margin:4%;
				transition-duration: 0.3s;
				cursor: pointer;
			}
			input[type=submit]:hover{
				background-color: #ccc;
				border: 1px solid #ccc;
  				color: black;
			}
            body{
				margin-top:3%;
				margin-left:8%;
				margin-right:8%;
			}
			table {
				border:5px solid black;
				border-collapse: collapse;
				width: 100%;
				margin-top: 3%;
				background-color: #e4e7e7;
				
			}
			th, td {
				border: 2px solid black;
				text-align: left;
				padding: 14px;
			}
			th {
				text-align:center;
				border: 2px solid black;
				background-color: #9A9AB8;
				color: white;
			}
        </style>
    </head>
    <body>
        <?php
            include "functions.php";
            //HeaderP();
            if (!empty($_POST['date'])) {
                $login = $_SESSION['login'];
                $password = $_SESSION['Password'];
                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }
                if (count($result)!=0) {
                    if ($password == $result[0]['Password']) {
                        menuCli($result[0]['Nom']);
                        $date = $_POST['date'];
                        $Code_Client = $result[0]['Code_Client'];
                        ListeCommandes($date, $Code_Client, $connexion);
                    } 
                }
                
            }else if (isset($_GET['logout'])) {         
                //session_unset();
                //$_SESSION = array();
		        //session_destroy();   
                //header('Location: Client.php?logout', true);
                //exit();
            }else if (!empty($_POST['sub'])) { 
                $login = $_SESSION['login'];
                $password = $_SESSION['Password'];

                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }
                $Code_Client = $result[0]['Code_Client'];
                //creatOrder($Code_Client, $connexion);
                //OrderForm ($result[0]['Nom'], $connexion);
                ProductNumberForm($result[0]['Nom']);
                //$OrderId = getIdOrder ($connexion, $Code_Client);

            } else if (!empty($_GET['numPro'])) {     
                $login = $_SESSION['login'];
                    $password = $_SESSION['Password'];
                    $np = $_GET['numPro'];

                    try {
                        $connexion = dbConnection();
                        $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                        $requete=$connexion->prepare($dbLogin);
                        $requete->execute();
                        $result = $requete->fetchall();
                    } catch (PDDException $e) {
                        echo "Problem de traitment" .$e->getMessage();
                    }
                    $Code_Client = $result[0]['Code_Client'];
                    $_SESSION['ProductNumber'] = $np;
                if (is_numeric($_GET['numPro'])) {
                    OrderForm($result[0]['Nom'], $connexion, $np);
                } else {
                    ProductNumberForm($result[0]['Nom']);
                    //echo "vous devez entrer un nombre";
                    echo '<center><span style="color:red; display:block;">vous devez entrer un nombre!!!!!</br></span></center>';

                }     
            } else if (!empty($_GET['Save'])) {
                //echo "sraniyo"; 
                $pn=$_SESSION['ProductNumber'];
                //echo $_SESSION['ProductNumber'];
                //unset($_SESSION['ProductNumber']);
                //echo "nani";
                //echo $_SESSION['ProductNumber'];
                //echo "tm7a";


                $login = $_SESSION['login'];
                $password = $_SESSION['Password'];

                //$Code_Produit = $_GET['product'];
                //$qte = $_GET['qte'];

                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }

                $Code_Client = $result[0]['Code_Client'];
                
                $bool = true;

                $errr = 0;
                $i = 0;

                while($i < $pn) { 
                    $p=$i+1;
                    if (!empty($_GET['product'.$p.'']) and !empty($_GET['qte'.$p.'']) and is_numeric($_GET['qte'.$p.'']) and is_numeric($_GET['product'.$p.''])) {
                        
                        $Code_Produit = $_GET['product'.$p.''];
                        $qte = $_GET['qte'.$p.''];

                        if ($bool) {
                            creatOrder($Code_Client, $connexion);
                        }
                        //creatOrder($Code_Client, $connexion);
                    
                        $OrderId = getIdOrder ($connexion, $Code_Client);
                        insertOrder ($connexion, $OrderId, $Code_Produit, $qte);
                        //menuCli($result[0]['Nom']);
                    } else {
                        OrderForm($result[0]['Nom'], $connexion, $pn);
                        //echo "vous devez remplir tous les formulaires!!!!!";
                        echo '<center><span style="color:red; display:block;">vous devez remplir tous les formulaires!!!!!</br></span></center>';
                        $errr = 1 ;

                        break;
                    } 
                    
                    $bool = false;
                    $i++;
                }
                //menuCli($result[0]['Nom']);
                if ($errr == 0) {
                    unset($_SESSION['ProductNumber']);
                    header('Location: Server.php?insertion', true);
                }
            }else if (isset($_GET['insertion'])) {

                $login = $_SESSION['login'];
                $password = $_SESSION['Password'];

                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }
                $Code_Client = $result[0]['Code_Client'];

                config ($Code_Client, $connexion, $result[0]['Nom']);  

            } else if (isset($_GET['keep'])) {
                //header('Location: Server.php', true);
                menuCli($result[0]['Nom']);
            } else if (isset($_GET['delete'])) {  
                $login = $_SESSION['login'];
                $password = $_SESSION['Password'];

                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }
                $Code_Client = $result[0]['Code_Client'];
                deleteOrder ($connexion, $Code_Client);
                //header('Location: Server.php?', true);
                menuCli($result[0]['Nom']);
            }else {
                $login = $_POST['login'];
                $password = $_POST['Password'];

                $_SESSION['login'] = $login;
                $_SESSION['Password'] = $password;
            
                try {
                    $connexion = dbConnection();
                    $dbLogin = "SELECT * From client WHERE Pseudo='$login'";
                    $requete=$connexion->prepare($dbLogin);
                    $requete->execute();
                    $result = $requete->fetchall();
                } catch (PDDException $e) {
                    echo "Problem de traitment" .$e->getMessage();
                }
                //$connexion = NULL;
                echo "</br>";
            
                if (count($result)!=0) {
                    if ($password == $result[0]['Password']) {
                        menuCli($result[0]['Nom']);
                    } else {
                        //header("Client.php?logininco");
                        //header("Client.php");
                        header('Location: Client.php?logininco', true);
                    }
                } else {
                    header('Location: Client.php?logininco', true);
                }
            }
        ?>
    </body>
</html>