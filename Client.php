<html>
    <head>
        <title>Gestio_Ventes</title>
        <meta charset="utf-8"/>
        <style type="text/css">
            body {
                text-align: center;
                margin-top:40px;
            }
            .LoginForm{
				margin:3%;
				position:center;
				width:400px;
				height:200px;
				border:4px solid black;
				border-radius: 10px;
                padding-top: 30px;
			}

			.signin{
				margin:3%;
				position:center;
				width:400px;
				height:220px;
				border:4px solid black;
				border-radius: 10px;
                padding-top: 30px;
			}

            input[type=text]{
                margin-left:34px;
                width: 50%;
				height:10%;
				border: 2px solid #ccc;
				border-radius: 4px;
				font-size: 16px;
				padding: 12px 20px 12px 30px;
            }
            input[type=password]{
                width: 50%;
				height:10%;
				border: 2px solid #ccc;
				border-radius: 4px;
				font-size: 16px;
				padding: 12px 20px 12px 30px;
            }
            input[type=submit] {
				background-color: black;
				border: 1px solid black;
				border-radius:5px;
				color: white;
				width:90px;
				height:40px;
				margin:20px;
				transition-duration: 0.3s;
				cursor: pointer;
			}
			input[type=submit]:hover{
				background-color: #ccc;
				border: 1px solid #ccc;
  				color: black;
			}
        </style>
    </head>
    <body>
        <?php
			include "functions.php";
			if (isset($_GET['logininco'])) {
				HeaderP();
                LoginForm();
				echo '<br>';
				echo '<center><span style="color:red; display:block;">login inconnu ou password incorrect!</br></span></center>';
			}else if (!empty($_POST['nom'])) {
				echo "zinoun";
			    if (!empty($_POST['nom']) and !empty($_POST['Password']) and !empty($_POST['pseuu']) and !is_numeric($_POST['nom']) and !is_numeric($_POST['pseuu'])) {
					echo "bn";
					$login = $_POST['pseuu'];
					$password = $_POST['Password'];
					$name = $_POST['nom'];
					
					signin($name, $password, $login);
					HeaderP();
				    LoginForm();
				} else {
					HeaderP();
				    signinForm();
					echo '<center><span style="color:red; display:block;">vous devez remplir tous les formulaires!!!!!</br></span></center>';
				}
			}else if (isset($_GET['signin'])) {	
				HeaderP();
				signinForm();
			}else if (isset($_GET['logout'])) {

				session_start();
				session_unset();
				$_SESSION = array();
				session_destroy();
				header('Location: Client.php', true);

			}else {
				HeaderP();
				LoginForm();
				echo '<center><span style="color:red; display:block;"><a href="Client.php?signin">Signin</a></br></span></center>';
			}
        ?>
    </body>
</html>