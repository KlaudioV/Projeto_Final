<?php

require_once("requires/bd.php");
if (isset($_POST['nomerota'],$_POST['pontos'],$_POST['idrota'])) {
    $idRota = $_POST['idrota'];
    $arrayPontos = $_POST['pontos'];
    $query = "SELECT id_Edificio FROM Pontos_Rota WHERE id_Rota = '{$idRota}'";
    $result = $bd->query($query);
    while($row = $result->fetchArray()){
        if(!in_array($row['id_Edificio'],$arrayPontos)){
            $deleteThis = $row['id_Edificio'];
            $query = "DELETE FROM Pontos_Rota WHERE id_Rota = '{$idRota}' AND id_Edificio = '{$deleteThis}'";
            $bd->exec($query);
        }else{
            $arrayPontos = \array_diff($arrayPontos, [$row['id_Edificio']]);
        }
        //echo $row['id_Edificio'];
    }

    foreach($arrayPontos as $ponto){
        $query = "INSERT INTO Pontos_Rota(id_Edificio,id_Rota) VALUES('" . $ponto . "','" . $idRota . "')";
        $result = $bd->exec($query);
    }

    $query = "UPDATE Rotas SET nome='{$_POST['nomerota']}' WHERE id = $idRota";
    $result = $bd->exec($query);
}
/* Obtém o ID do edificio acabado de adicionar */

$idRota = $_GET['id'];
$query = "SELECT * FROM Rotas WHERE id = $idRota";
$result = $bd->query($query);
$row = $result->fetchArray();
$nomerota = $row['nome'];

$query = "SELECT id_Edificio FROM Pontos_Rota WHERE id_Rota = $idRota";
$result = $bd->query($query);
$pontosDaRota = [];

while($row = $result->fetchArray()){
    array_push($pontosDaRota, $row['id_Edificio']);
}



   

?>


<h1 class="h1"><?= _EDITROUTE ?> <?php echo " - " . $nomerota ?></h1>
</div>
<div class="row col-md-12">
    <div class="col-md-6">

        <form action="?p=4&id=<?php echo $idRota ?>" method="POST">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _ROUTENAME ?></span>
                </div>
                <input name="idrota" value="<?php echo $idRota ?>" type="text" hidden class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                <input name="nomerota" value="<?php echo $nomerota ?>" type="text" class="form-control" placeholder="ex: Tascas de Tomar" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            
            <?php 
            $check_query = $bd->query("SELECT * FROM DescEdif as a,Edificios as b WHERE a.ling=1 AND a.idEdificio=b.idEdificio");

            $i = 0;
            while ($row = $check_query->fetchArray()) {
                if(in_array($row['idEdificio'],$pontosDaRota)){
                    echo '<div class="custom-control custom-checkbox">
                    <input checked class="custom-control-input" id="check'.$i.'" type="checkbox" name="pontos[]" value="'.$row["idEdificio"].'" />
                    <label class="custom-control-label" for="check'.$i.'">'.$row["NomeEdif"].'</label>
                </div>';
                }else{
                echo '<div class="custom-control custom-checkbox">
                <input class="custom-control-input" id="check'.$i.'" type="checkbox" name="pontos[]" value="'.$row["idEdificio"].'" />
                <label class="custom-control-label" for="check'.$i.'">'.$row["NomeEdif"].'</label>
              </div>';
                }
              $i++;
            }
            
            
            
            
            
            
            ?>

            <hr\>
            <button class="btn btn-warning mt-3" type="submit"><?= _EDIT ?></button>
    </div>

    </form>