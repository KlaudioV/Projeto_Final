<?php

require_once("requires/bd.php");
if (isset($_POST['nomerota'],$_POST['pontos'])) {

    $query = "INSERT INTO Rotas(id_utilizador, nome) VALUES('1111','".$_POST['nomerota']."')";
    $result = $bd->exec($query);

/* Obtém o ID do edificio acabado de adicionar */
$query = "SELECT * FROM Rotas ORDER BY id DESC LIMIT 1;";
$result = $bd->query($query);
$row = $result->fetchArray();
$idRota = $row["id"];

    //gather all the data from the submission process
    $nomerota   = $_POST['nomerota'];
    foreach($_POST['pontos'] as $ponto){
        $query = "INSERT INTO Pontos_Rota(id_Edificio,id_Rota) VALUES('" . $ponto . "','" . $idRota . "')";
        $result = $bd->exec($query);
    
    }

   
}
?>


<h1 class="h1"><?= _CREATEROUTE ?></h1>
</div>
<div class="row col-md-12">
    <div class="col-md-6">

        <form action="?p=3" method="POST">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _ROUTENAME ?></span>
                </div>
                <input style="width:100%;" name="nomerota" type="text" class="form-control" placeholder="ex: Tascas de Tomar" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            
            <?php 
            $check_query = $bd->query("SELECT * FROM DescEdif as a,Edificios as b WHERE a.ling=1 AND a.idEdificio=b.idEdificio");

            $checked_query = 0;
            $i = 0;
            while ($row = $check_query->fetchArray()) {
                echo '<div class="custom-control custom-checkbox">
                <input class="custom-control-input" id="check'.$i.'" type="checkbox" name="pontos[]" value="'.$row["idEdificio"].'" />
                <label class="custom-control-label" for="check'.$i.'">'.$row["NomeEdif"].'</label>
              </div>';
              $i++;
            }
            
            
            
            
            
            
            ?>

            <hr\>
            <button class="btn btn-success mt-3" type="submit"><?= _CREATE ?></button>
    </div>

    </form>