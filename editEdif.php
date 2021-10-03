<?php


require_once("requires/bd.php");

if (isset($_GET["remove"]) && $_GET["remove"] == 1) {
    $query = "DELETE FROM Coordenadas WHERE idEdificio = '" . $_POST['idedif'] . "'";
    $result = $bd->exec($query);
    $query = "DELETE FROM DescEdif WHERE idEdificio = '" . $_POST['idedif'] . "'";
    $result = $bd->exec($query);
    $query = "DELETE FROM Edificios WHERE idEdificio = '" . $_POST['idedif'] . "'";
    $result = $bd->exec($query);
    header("Location: http://ram.ipt.pt/");
}

if (isset($_GET["removeImage"])) {
    echo _EDITBUILDING . $_GET["removeImage"];
    unlink("uploads/".$_GET["removeImage"]);
}

if (isset($_POST['idedif'], $_POST['localizacao'], $_POST['data'], $_POST['coordlong'], $_POST['coordlat'])) {
    
    //gather all the data from the submission process
    $idEdif = $_POST['idedif'];
    $localizacao   = $_POST['localizacao'];
    $data   = $_POST['data'];
    $coordlong   = $_POST['coordlong'];
    $coordlat   = $_POST['coordlat'];

    //Descrições em várias linguas
    $check_query = $bd->query("SELECT * FROM Linguagem");

    while ($row = $check_query->fetchArray()) {
        ${"nomeedif" . $row["id"]}   = $_POST['nomeedif' . $row["id"]];
        ${"tipoedif" . $row["id"]}   = $_POST['tipoedif' . $row["id"]];
        ${"autores" . $row["id"]}   = $_POST['autores' . $row["id"]];
        ${"descedif" . $row["id"]}   = $_POST['descedif' . $row["id"]];
    }

    /* Edita o Edíficio */
    $query = "UPDATE Edificios
   SET Localizacao = '" . $localizacao . "',
       `data` = '" . $data . "',
       CoordLongEdif = '" . $coordlong . "',
       CoordLatEdif = '" . $coordlat . "'
   WHERE idEdificio='" . $idEdif . "'";
    $result = $bd->exec($query);

    /* Edita a DescEdif*/
    $check_query = $bd->query("SELECT * FROM Linguagem");
    while ($row = $check_query->fetchArray()) {
        $nomeedif = ${"nomeedif" . $row["id"]};
        $tipoedif = ${"tipoedif" . $row["id"]};
        $autores = ${"autores" . $row["id"]};
        $descri = ${"descedif" . $row["id"]};

        $query = "UPDATE DescEdif
              SET NomeEdif = '" . $nomeedif . "',
                  TipoEdif ='". $tipoedif ."',
                  Autores ='".  $autores ."',
                  Descricao = '" . $descri . "'
              WHERE idEdificio='" . $idEdif . "' AND ling='".$row["id"]."'";
        $result = $bd->exec($query);
    }

    $i = 0;
    foreach ($_POST["poliLong"] as $poli) {
        $query = "UPDATE Coordenadas SET CoordLong='" . $poli . "', CoordLat='" . $_POST["poliLat"][$i] . "' WHERE idCoord='" . $i . "' AND idEdificio='" . $idEdif . "'";
        $result = $bd->exec($query);
        $i++;
    }


$fileNames = array_filter($_FILES['fileToUpload']['name']); 
if(!empty($fileNames)){    
foreach($_FILES['fileToUpload']['name'] as $key=>$val){ 
$target_dir = "uploads/";
$target_file = $target_dir . $idEdif . "_" . basename($_FILES["fileToUpload"]["name"][$key]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image

$check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$key]);
if ($check !== false) {
    $uploadOk = 1;
} else {
    $uploadOk = 0;
}


// Check if file already exists
if (file_exists($target_file)) {
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"][$key] > 500000) {
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $target_file)) {
    } else {
    }
}
}
}
}
if (isset($_GET['id'])) {

    //gather all the data from the submission process
    $idEdif   = $_GET['id'];


    /* Procura o Edíficio a editar */
    $query = "SELECT * FROM Edificios as a, DescEdif as b WHERE a.idEdificio=" . $idEdif . " AND b.idEdificio=a.idEdificio and b.ling = 1";
    $result = $bd->query($query);
    $row = $result->fetchArray();
    $localizacao = $row["Localizacao"];
    $data = $row["Data"];
    $CoordLong = $row["CoordLongEdif"];
    $CoordLat = $row["CoordLatEdif"];

    $check_query = $bd->query("SELECT * FROM DescEdif WHERE idEdificio=" . $idEdif . "");
    $result = $bd->query($query);

    while ($row = $check_query->fetchArray()) {
        ${"tipoedif" . $row["ling"]}   = $row['TipoEdif'];
        ${"nomeedif" . $row["ling"]}   = $row['NomeEdif'];
        ${"autores" . $row["ling"]}   = $row['Autores'];
        ${"descedif" . $row["ling"]}   = $row['Descricao'];
    }
} else {
    header("Location: http://ram.ipt.pt/");
}
?>


<h1 class="h1"><?= _EDITBUILDING ?></h1>
</div>
<div class="row col-md-12">
    <div class="col-md-9">

        <form  action="?p=2&id=<?php echo $_GET["id"] ?>" method="POST" enctype="multipart/form-data">
            <input style="display:none;" value="<?php echo $idEdif ?>" name="idedif" type="text" class="form-control" placeholder="ex: Joaquim Manuel" aria-label="Username" aria-describedby="basic-addon1">
            
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _BUILDINGNAME ?> (<?php echo $row["Nome"] ?>)</span>
                </div>
                <textarea name="nomeedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Joaquim Manuel" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo ${"nomeedif" . $row["id"]} ?>"><?php echo ${"nomeedif" . $row["id"]} ?></textarea>
            </div>
            <?php
            }
            ?>
            
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _LOCATION ?></span>
                </div>
                <input value="<?php echo $localizacao ?>" name="localizacao" type="text" class="form-control" placeholder="ex: Tomar" aria-label="Username" aria-describedby="basic-addon1">
            </div>

            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _AUTHOR ?>(<?php echo $row["Nome"] ?>)</span>
                </div>
                <textarea name="autores<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Lorem ipsum..." aria-label="Username" aria-describedby="basic-addon1" value="<?php echo ${"autores" . $row["id"]} ?>"><?php echo ${"autores" . $row["id"]} ?></textarea>
            </div>
            <?php
            }
            ?>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _DATEOFOUNDING ?></span>
                </div>
                <input value="<?php echo $data ?>" name="data" type="text" class="form-control" placeholder="ex: 21/02/1935" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");

            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _BUILDINGTYPE ?></span>
                </div>
                <textarea name="tipoedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Monumento" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo ${"tipoedif" . $row["id"]} ?>"><?php echo ${"tipoedif" . $row["id"]} ?></textarea>
            </div>
            <?php
            }
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Longitude</span>
                </div>
                <input value="<?php echo $CoordLong ?>" name="coordlong" type="text" class="form-control" placeholder="º" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Latitude</span>
                </div>
                <input value="<?php echo $CoordLat ?>" name="coordlat" type="text" class="form-control" placeholder="º" aria-label="Username" aria-describedby="basic-addon1">
            </div>

            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><?= _DESCRIPTION ?> (<?php echo $row["Nome"] ?>)</span>
                    </div>
                    <textarea name="descedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Lorem ipsum..." aria-label="Username" aria-describedby="basic-addon1" value="<?php echo ${"descedif" . $row["id"]} ?>"><?php echo ${"descedif" . $row["id"]} ?></textarea>
                </div>
            <?php
            }
            ?>
            <?php

            $query = "SELECT * FROM Coordenadas WHERE idEdificio=" . $idEdif;
            $result = $bd->query($query);
            while ($row = $result->fetchArray()) {
            ?>
                <div class="input-group mb-2" id="poliPoints">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Coord. <?= _POLYGON ?> (Long | Lat)</span>
                    </div>
                    <input value="<?php echo $row["CoordLong"] ?>" name="poliLong[]" type="text" aria-label="Long" class="form-control">
                    <input value="<?php echo $row["CoordLat"] ?>" name="poliLat[]" type="text" aria-label="Lat" class="form-control">
                </div>
            <?php } ?>
            
            <div id="imageUploads">
                <input name="fileToUpload[]" id="ImagUpload" type="file" multiple="multiple" />
                
                <div id="ImagPrev">
                
                </div>
            </div>

            <script>
                window.onload = function() {
                    var ImagUpload = document.getElementById("ImagUpload");
                    ImagUpload.onchange = function() {
                        if (typeof(FileReader) != "undefined") {
                            var ImagPrev = document.getElementById("ImagPrev");
                            ImagPrev.innerHTML = "";
                            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.bmp)$/;
                            for (var i = 0; i < ImagUpload.files.length; i++) {
                                var file = ImagUpload.files[i];
                                if (regex.test(file.name.toLowerCase())) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var img = document.createElement("IMG");
                                        img.height = "100";
                                        img.width = "100";
                                        img.src = e.target.result;
                                        ImagPrev.appendChild(img);
                                    }
                                    reader.readAsDataURL(file);
                                } else {
                                    alert(file.name + " is not a valid image file.");
                                    ImagPrev.innerHTML = "";
                                    return false;
                                }
                            }
                        } else {
                            alert("This browser does not support HTML5 FileReader.");
                        }
                    }
                };
            </script>
  

            
                <hr/>
            <button class="btn btn-warning mt-3" type="submit"><?= _EDIT ?></button>
        </form>
        <form class="mb-3" action="?p=2&remove=1" method="POST">
            <input style="display:none;" value="<?php echo $idEdif ?>" name="idedif" type="text" class="form-control" placeholder="ex: Joaquim Manuel" aria-label="Username" aria-describedby="basic-addon1">
            <button class="btn btn-danger" type="submit"><?= _DELETEBUILDING ?></button>
        </form>
        
        <?php
                $d = 'uploads/';
                foreach(glob($d.$idEdif.'_*.{jpg,JPG,jpeg,JPEG,png,PNG}',GLOB_BRACE) as $file){
                    $imag[] =  basename($file);
                    
                    ?>
                    <div class="card" style="width: 18rem;">
                        <img class="card-img-top" style="width:100%;height:auto;" src="<?php echo $file ?>"/>
                        <div class="card-body">
                        <form action="?p=2&id=<?php echo $idEdif ?>&removeImage=<?php echo str_replace("uploads/","",$file) ?>" method="POST">
                            <button id="deleteImage" class="btn btn-block btn-info" type="submit"><?= _DELETEIMAGE ?></button>
                                
                            </div>
                        </form>
                    </div>
                    <?php
                }
                ?>
        <script>
          
        </script></div>