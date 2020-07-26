
<!DOCTYPE html>
<html>
<head>
    
<title>Annuaire des médecins</title>
<link rel="stylesheet" type="text/css" href="tp4.css">
</head>
<body>
    
    <h1>Annuaire des médecins</h1>

<?php
    header('Content-Type: text/html;charset=UTF-8'); // force encoding utf8 (accents)
    // connexion à la bdd
    try {
        $db = new PDO('mysql:host=localhost;dbname=annuaire_medecins;charset=utf8mb4', 'root', 'root');
        } catch(Exception $ex) {
        $erreur = $erreur."Connexion à la base de données impossible";
    }
    $page=1;
    $limite = 10;
    if(isset($_GET['page'])){    $page = $_GET['page'];}
    if(isset($_GET['limite'])){    $limite = (int) $_GET['limite'];}
    $sql_count='select count(*) as compte from medecin left join specialite on spe_id = med_specialite';
    $sql_select='select * from medecin left join specialite on spe_id = med_specialite order by med_nom limit '.$limite.' offset '.($limite*($page-1));
    $sql_insert='insert into medecin (med_civilite, med_nom, med_prenom, med_adresse, med_secteur) values(:sex, :nom, :prenom, :adresse, :secteur)';
    $sql_delete='delete from medecin where med_nom=:nom_delete)';
    // récupération des médecins à afficher
try{
    $select = $db->query($sql_select);
    } catch(PDOException $ex){
    $erreur = $erreur."Problème lors de la requête : " . $ex->getMessage();
}

    //COUNT
try{
    $compte = $db->query($sql_count);
    } catch(PDOException $ex){
        $erreur = "Problème lors de la requête : " . $ex->getMessage();
}
    $rowCompte = $compte->fetch(PDO::FETCH_ASSOC); 
    
//INSERT
try{
        $insert = $db->query($sql_insert);
    } catch(PDOException $ex){
        $erreur = $erreur."Problème lors de la requête : " . $ex->getMessage();
}
    // affichage d'erreur
    if(isset($erreur)){ 
        echo '<b>Une erreur est survenue : <?=$erreur?></b>';
    }
    
    $sth2= $db->prepare($sql_insert);
    $sth2->bindParam(':sex', $_POST['sex'], PDO::PARAM_STR);
    $sth2->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
    $sth2->bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
    $sth2->bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);
    $sth2->bindParam(':secteur', $_POST['secteur'], PDO::PARAM_INT);
    $sth2->execute();

    
    //DELETE
    try{
            $insert = $db->query($sql_delete);
        } catch(PDOException $ex){
            $erreur = $erreur."Problème lors de la requête : " . $ex->getMessage();
    }
    // affichage d'erreur
    if(isset($erreur)){ 
        echo '<b>Une erreur est survenue : <?=$erreur?></b>';
    }
    $sth2= $db->prepare($sql_delete);
    $sth2->bindParam(':nom_delete', $_GET['delete_Input'], PDO::PARAM_STR);
    $sth2->execute();
?>
    <table>
    <tr >
        <th>Civilité</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Spécialité</th>
        <th>Adresse</th>
        <th>Ville</th>
        <th>Secteur</th>
    </tr>
<?php while($row = $select->fetch(PDO::FETCH_ASSOC)) { ?>
    <tr>
        <td><?=$row['med_civilite']?></td>
        <td><?=$row['med_nom']?></td>
        <td><?=$row['med_prenom']?></td>
        <td><?=$row['spe_nom']?></td>
        <td><?=$row['med_adresse']?></td>
        <td><?=$row['med_ville']?></td>
        <td>Secteur <?=$row['med_secteur']?></td>
    </tr>
<?php } ?>
    </table>
    <labe>nbMedecins: <?=$rowCompte['compte']?></label>
    <labe>nbpage : <?=ceil($rowCompte['compte']/$limite)?></label>
    
    <h1>Critères de recherche</h1>

    <form method="GET" submit="annuaire_params.php">
        <input type="hidden" name="page" value="1"/>
        Nombre de résultats par page :
        <select name="limite">
            <option <?=($limite==10?"selected":"")?>>10</option>
            <option <?=($limite==25?"selected":"")?>>25</option>
            <option <?=($limite==50?"selected":"")?>>50</option>
        </select>
        <input type="submit" value="Changer nombre de résultats par page"/>
    </form>
    <form method="GET" submit="annuaire_params.php">
        <input type="hidden" name="page" value="<?=($page>1?$page-1:$page)?>"/>
        <input type="hidden" name="limite" value="<?=$limite?>"/>
        <input type="submit" value="Page Précédente" /> 
    </form>
    <form method="GET" submit="annuaire_params.php">
        <input type="hidden" name="page" value="<?=$page<ceil($rowCompte['compte']/$limite)?$page+1:$page?>"/>
        <input type="hidden" name="limite" value="<?=$limite?>"/>
        <input type="submit" value="Page suivante" />
    </form>
    <form method="POST" submit="annuaire_params.php" action="FormMedecins.php">
        <label> Sex :</label>
        <input type="radio" name="sex" value="Monsieur"> Monsieur
        <input type="radio" name="sex" value="Madame"> Madame
        <br>
        <label > nom :</label><input type="text" name="nom">
        <br>
        <label > prenom :</label><input type="text" size=30 name="prenom">
        <br>
        <label > adresse :</label><input type="text" size=30 name="adresse">
        <br> 
        <label > secteur :</label><input type="number" min="0" max="10" name="secteur">
        <br>
        <input type="submit" value="Insert" size=30/>
    </form>
    <label>qry Insert: </label><?php echo ''.$sql_insert;?>
       <br>   <br>
        <form method="GET" submit="annuaire_params  .php" action="FormMedecins.php">
            <select >
                <option value="nom">nom</option>
            </select>
            <label > delete :</label><input type="text" name="delete_Input">
            <input type="submit" value="Delete" size=30/>

    </form>
    <br>
</body>
</html>