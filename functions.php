<?php

//etablit la connexion al la base de donnees
function dbConnection () {
    try {
		$connexion = new PDO('mysql:host=localhost; dbname=gestion_ventes','root','');
		$connexion->setattribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connexion->exec("SET NAMES 'utf8'");
	} catch (PDOException $th) {
		echo $th->getMessage();
		exit;
	}
    return $connexion;
}

function LoginForm () {
    echo '<center> <div class="LoginForm"> 
    <center>
    <form method="POST" action="Server.php">
        <p>
            <label for="login"><b>login : </b></label>
            <input type="text" name="login" id="login">
        </p>
        <p>
            <label for="password"><b>Password : </b></label>
            <input type="password" name="Password" id="password">
        </p>
        <p>
            <input type="submit" value="connexion">
        </p>
    </form>
    </center> </div></center>
    ';
}

function HeaderP() {
    echo '
        <p align=center>
            <b>
                <font size=6> Gestion Commerciale - GestCom </font>
            </b>
        </p>
        <p align=center >
            <b>
                <font size=4>Gestion de Commandes</font>
            </b>
            <hr width=100% size=5 margin-top=40px>
        </p>               
    ';
}

function menuCli ($name) {
    echo "   
    <form method='POST' action=''>
    <center>

        <h1 class='header'> Gestion Commercial - GestCom </h1>
        <h2> Gestion des Commandes </h2>
        <div style='width:100%;height:5px;border:1px solid #000; margin-top:30px;'></div>
        <div align=right>
        <p align=right>
            Client :".$name."</br>
        </p>    
        <a href='Client.php?logout' align=right> Logout </a>
        </div>
        <div class='div'>
            <div class='d1'>

                <h2 class='title'>Recapitulatif de Commandes : :</h2>
                <form action='' method='POST'>
                <p>
                    <b>A partir de :</b>
                    <input type='date' name='date' value=1991-01-01 required>
                </p>
                <input class='ef' type='Submit' name='subDate' value='Afficher'>
                </form>
            </div>

            <div class='d2'>
                <h2 class='title' >Passation des Commandes :</h2>
                <form action='' method='POST'>
                <input class='ef' type='Submit' value='Afficher' name='sub'>
                </form>

            </div>
        </div>

       </center> 
    ";
}

function ListeCommandes($DateChiffre, $Code_Client, $connexion) {
	echo "<p> A partir de :" .$DateChiffre." </p> </font>";
	echo "<div style=\"width:100%;height:5px;border:1px solid #000;\"></div>";

	$selection = $connexion->prepare("SELECT Numéro_Commande,Date 
        FROM commande 
        WHERE Code_Client='$Code_Client' AND Date>='$DateChiffre'");
	$selection->execute();
	$resultSelection = $selection->fetchAll();

	$chifreTotale = 0;
			
	if(!empty($resultSelection)){
		echo "<table class=\"table\">";
		echo "<tr>";
		echo "<th>N° Commande</th>";
		echo "<th>Date</th>";
		echo "<th>Code Produit</th>";
		echo "<th>Désignation</th>";
		echo "<th>PU</th>";
		echo "<th>Qte</th>";
		echo "<th>Total Produits</th>";
		echo "</tr>";

		for($comteur=0;$comteur<count($resultSelection);$comteur++) {
			$prixCommande = 0;

			$numCommande = $resultSelection[$comteur]['Numéro_Commande'];
			$products = $connexion->prepare("SELECT Code_Produit,Qte 
                FROM ligne_commande 
                WHERE Numéro_Commande='$numCommande'");
			$products->execute();
			$resultProducts = $products->fetchAll();

			$nbProducts = count($resultProducts);
            $boolean = false;

			for($i=0;$i<count($resultProducts);$i++){		
				$codeProduct = $resultProducts[$i]['Code_Produit'];
				$productName = $connexion->prepare("SELECT Désignation,Prix_Unitaire 
                    FROM produit 
                    WHERE Code_Produit='$codeProduct'");
				$productName->execute();
				$resultProductName = $productName->fetch();

				echo "<tr>";
				if($boolean==false){
					echo "<td rowspan=\"".$nbProducts."\">".$resultSelection[$comteur]['Numéro_Commande']."</td>";
					echo "<td rowspan=\"".$nbProducts."\">".$resultSelection[$comteur]['Date']."</td>";
					$boolean = true;
				}

				echo "<td>".$codeProduct."</td>";
				echo "<td>".$resultProductName['Désignation']."</td>";
				echo "<td>".$resultProductName['Prix_Unitaire']."</td>";
				echo "<td>".$resultProducts[$i]['Qte']."</td>";
				echo "<td>". number_format($resultProducts[$i]['Qte']*$resultProductName['Prix_Unitaire'],2)."</td>";
				$prixCommande+=$resultProducts[$i]['Qte']*$resultProductName['Prix_Unitaire'];
			}
			echo "</tr>";
			echo "<tr style=\"background-color:#ebbeed;\">";
			echo "<td style=\"text-align:right;\" colspan=\"6\">Total commande N° ".$resultSelection[$comteur]['Numéro_Commande']."</td>";
			echo "<td>". $prixCommande."</td>";
			echo "</tr>";
			$chifreTotale+=$prixCommande;
		}
		echo "</table>";
		echo "<center><h2>Chiffre d'affaire du client (". $Code_Client ." - ". $result['Nom'] .") :". $chifreTotale ."</h2></center>";
	}else{
		echo "<center><h2>Le client de code ". $Code_Client ." n'a pas passé de commandes à partir de la date ". $DateChiffre."</h2></center>";
	}
    
}

function ServerBody ($bool , $setlogin, $setpassword) {
    if ($bool) {
        $login = $_POST['login'];
        $password = $_POST['Password'];
    } else {
        $login = $setlogin;
        $password = $setpassword;
    }

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
            echo '<span style="color:red; display:block;">login inconnu ou password incorrect!</br></span>';
            LoginForm();
        }
    } else {
        echo '<span style="color:red; display:block;">login inconnu ou password incorrect!</br></span>';
        LoginForm();
    }
}

function creatOrder($Code_Client, $connexion) {
    $DateChiffre = date('d-m-y');

    $sql = "INSERT INTO Commande(Code_Client, Date) 
        VALUES  ('$Code_Client','$DateChiffre')";
    $prepareOrder=$connexion->prepare($sql);
    $prepareOrder->execute();    

}



function getIdOrder ($connexion, $Code_Client) {

    $sql_NOrder = "SELECT Numéro_Commande From Commande WHERE Code_Client ='$Code_Client'";
    $requete=$connexion->prepare($sql_NOrder);
    $requete->execute();
    $Norder = $requete->fetchall();

    //foreach ($Norder as $n)
    //echo $Norder[count($Norder) - 1]['Numéro_Commande'];
    return $Norder[count($Norder) - 1]['Numéro_Commande'];
}

function insertOrder ($connexion, $OrderId, $Code_Produit, $qte) {

    $sql = "INSERT INTO Ligne_Commande(Numéro_Commande, Code_Produit, Qte)
        VALUES ('$OrderId','$Code_Produit','$qte')";
    $prepareOrder=$connexion->prepare($sql);
    $prepareOrder->execute();     
}

function getOrderLine($connexion, $OrderId) {
    $sql = "SELECT * FROM Ligne_Commande WHERE Numéro_Commande = '$OrderId'";
    $requete=$connexion->prepare($sql);
    $requete->execute();
    $orderlines = $requete->fetchall();

    return $orderlines;
}

function OrderForm ($name, $connexion, $np) {

    $sql = "SELECT * From Produit";
    $requete=$connexion->prepare($sql);
    $requete->execute();
    $Products = $requete->fetchall();

    echo "      
    <form method='GET' action=''>
    <center>

        <h1 class='header'> Gestion Commercial - GestCom </h1>
        <h2> Gestion des Commandes </h2>
        <div style='width:100%;height:5px;border:1px solid #000; margin-top:30px;'></div>
        <div align=right>
        <p align=right>
            Client :".$name."</br>
        </p>    
        <a href='Client.php' align=right> Logout </a>
        </div>
        <div class='div'>
            <h2 class='title'>Passation des Commandes :</h2>
            <form action='' method='GET'>
                <p>
                    <b>Add Product To Order :</b>";


                    for ($i=0; $i < $np; $i++) { 
                        $pro = $i + 1;
                        echo "<br><b>Product ".$pro.":</b>";
                        
                        echo "
                        <p> <select name='product".$pro."' id='product'><option value'0'>Chose Product</option>";

                        foreach($Products as $Product){
                            echo "<option value='".$Product['Code_Produit']."'>".$Product['Désignation']."</option>";
                        }
                        
                        echo "</p> 
                                <br> 
                                <p id='dragon'>
                                <input type='text' name='qte".$pro."' id='qte' value='Quantité'>  
                                </p>
                        ";
                    }



                echo "  
                <br> 
                <input class='ef' type='Submit' name='Save' value='Save Order'>
            </form>
        </div>
       </center> 
    ";
}


function ProductNumberForm ($name) { 
    echo "      
    <form method='GET' action=''>
    <center>

        <h1 class='header'> Gestion Commercial - GestCom </h1>
        <h2> Gestion des Commandes </h2>
        <div style='width:100%;height:5px;border:1px solid #000; margin-top:30px;'></div>
        <div align=right>
        <p align=right>
            Client :".$name."</br>
        </p>    
        <a href='Client.php' align=right> Logout </a>
        </div>
        <div class='div'>
            <h2 class='title'>Passation des Commandes :</h2>
            <form action='' method='GET'>
                <p>
                    <b>how many Product u want in ur order :</b>
                </p>

                <br> 
                <p id='dragon'>
                    <input type='text' name='numPro' id='numPro' value='Product Number'>  
                </p>
                    
                <input class='ef' type='Submit' name='Add' value='Submit'>
            </form>
        </div>
       </center> 
    ";
}

function signinForm () {
    echo '<center> <div class="signin"> 
    <center>
    <form method="POST" action="Client.php">
        <p>
            <label for="nom"><b>Nom : </b></label>
            <input type="text" name="nom" id="nom">
        </p>
        <p>
            <label for="pseuu"><b>Pseudo : </b></label>
            <input type="text" name="pseuu" id="pseuu">
        </p>
        <p>
            <label for="password"><b>Password : </b></label>
            <input type="password" name="Password" id="password">
        </p>
        <p>
            <input type="submit" value="signin">
        </p>
    </form>
    </center> </div></center>
    ';
}

function signin ($name, $psw, $login) {
    
    $connexion = dbConnection();
    
    $sqler = "SELECT * FROM Client WHERE Pseudo='$login'";
    $selection=$connexion->prepare($sqler);
    $selection->execute();
	$resultSelection = $selection->fetchAll();

    if (count($resultSelection)!=0) {
        HeaderP();
		signinForm();
	    echo '<center><span style="color:red; display:block;">nom déjà pris!!!!!</br></span></center>';
        exit();
    } else {
        $sql = "INSERT INTO Client(Nom, Pseudo, Password)
        VALUES ('$name','$login','$psw')";
        $prepareOrder=$connexion->prepare($sql);
        $prepareOrder->execute();
    }
}

function cnfm() {
    
	echo " 
        <center>
            <form method='GET' action=''>
                <div class='dis'>
                    <p>
                        <input type='submit' name='keep' value='Sauvgarder'>
                        <input type='submit' name='delete' value='Supprimer'>
                    </p>
                </div>  
            <form>       
        </center>
    ";

}

function deleteOrder ($connexion, $Code_Client) {
    $OrderId = getIdOrder ($connexion, $Code_Client);
    $sqllign = "DELETE FROM Ligne_Commande WHERE Numéro_Commande = '$OrderId'";
    $prepareOrderlign=$connexion->prepare($sqllign);
    $prepareOrderlign->execute();

    $sql = "DELETE FROM Commande WHERE Numéro_Commande = '$OrderId'";
    $prepareOrder=$connexion->prepare($sql);
    $prepareOrder->execute();
}


function config ($Code_Client, $connexion,$name) {

    echo "<center><h1 class='header'> Gestion Commercial - GestCom </h1>
        <h2> Gestion des Commandes </h2>
        <div style='width:100%;height:5px;border:1px solid #000; margin-top:30px;'></div>
        <div align=right>
        <p align=right>
            Client :".$name."</br>
    </p></center>";

    echo "<p> votre commande a été enregistrée avec succès !!!! </p> </font>";
	echo "<div style=\"width:100%;height:5px;border:1px solid #000;\"></div>";

    $OrderId = getIdOrder ($connexion, $Code_Client);

	$selection = $connexion->prepare("SELECT Numéro_Commande,Date 
        FROM commande 
        WHERE Numéro_Commande='$OrderId'");
	$selection->execute();
	$resultSelection = $selection->fetchAll();

	$chifreTotale = 0;
			
	if(!empty($resultSelection)){
		echo "<table class=\"table\">";
		echo "<tr>";
		echo "<th>N° Commande</th>";
		echo "<th>Date</th>";
		echo "<th>Code Produit</th>";
		echo "<th>Désignation</th>";
		echo "<th>PU</th>";
		echo "<th>Qte</th>";
		echo "<th>Total Produits</th>";
		echo "</tr>";

		for($comteur=0;$comteur<count($resultSelection);$comteur++) {
			$prixCommande = 0;

			$numCommande = $resultSelection[$comteur]['Numéro_Commande'];
			$products = $connexion->prepare("SELECT Code_Produit,Qte 
                FROM ligne_commande 
                WHERE Numéro_Commande='$numCommande'");
			$products->execute();
			$resultProducts = $products->fetchAll();

			$nbProducts = count($resultProducts);
            $boolean = false;

			for($i=0;$i<count($resultProducts);$i++){		
				$codeProduct = $resultProducts[$i]['Code_Produit'];
				$productName = $connexion->prepare("SELECT Désignation,Prix_Unitaire 
                    FROM produit 
                    WHERE Code_Produit='$codeProduct'");
				$productName->execute();
				$resultProductName = $productName->fetch();

				echo "<tr>";
				if($boolean==false){
					echo "<td rowspan=\"".$nbProducts."\">".$resultSelection[$comteur]['Numéro_Commande']."</td>";
					echo "<td rowspan=\"".$nbProducts."\">".$resultSelection[$comteur]['Date']."</td>";
					$boolean = true;
				}

				echo "<td>".$codeProduct."</td>";
				echo "<td>".$resultProductName['Désignation']."</td>";
				echo "<td>".$resultProductName['Prix_Unitaire']."</td>";
				echo "<td>".$resultProducts[$i]['Qte']."</td>";
				echo "<td>". number_format($resultProducts[$i]['Qte']*$resultProductName['Prix_Unitaire'],2)."</td>";
				$prixCommande+=$resultProducts[$i]['Qte']*$resultProductName['Prix_Unitaire'];
			}
			echo "</tr>";
			echo "<tr style=\"background-color:#ebbeed;\">";
			echo "<td style=\"text-align:right;\" colspan=\"6\">Total commande N° ".$resultSelection[$comteur]['Numéro_Commande']."</td>";
			echo "<td>". $prixCommande."</td>";
			echo "</tr>";
			$chifreTotale+=$prixCommande;
		}
		echo "</table>";
        echo "<br>";
        cnfm();
	}else{
		echo "<center><h2>erreur</h2></center>";
	}
}






