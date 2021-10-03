<?php

require_once("requires/bd.php");
if (isset($_POST['localizacao'], $_POST['data'], $_POST['coordlong'], $_POST['coordlat'])) {

    //gather all the data from the submission process
    $localizacao   = $_POST['localizacao'];
    $data   = $_POST['data'];
    $coordlong   = $_POST['coordlong'];
    $coordlat   = $_POST['coordlat'];

    //Descrições em várias linguas
    $check_query = $bd->query("SELECT * FROM Linguagem");

    while ($row = $check_query->fetchArray()) {
        ${"nomeedif" . $row["id"]}   = $_POST['nomeedif' . $row["id"]];
        ${"descedif" . $row["id"]}   = $_POST['descedif' . $row["id"]];
        ${"tipoedif" . $row["id"]}   = $_POST['tipoedif' . $row["id"]];
        ${"autores" . $row["id"]}   = $_POST['autores' . $row["id"]];
    }

    /* Insere o Edíficio */
    $query = "INSERT INTO Edificios(Localizacao, `data`, CoordLongEdif, CoordLatEdif) VALUES('" . $localizacao . "','" . $data . "','" . $coordlong . "','" . $coordlat . "')";
    $result = $bd->exec($query);

    /* Obtém o ID do edificio acabado de adicionar */
    $query = "SELECT * FROM Edificios ORDER BY idEdificio DESC LIMIT 1;";
    $result = $bd->query($query);
    $row = $result->fetchArray();
    $idEdif = $row["idEdificio"];

    /* Insere o nome e descrição nas linguagens todas */
    $check_query = $bd->query("SELECT * FROM Linguagem");

    while ($row = $check_query->fetchArray()) {
        $query = "INSERT INTO DescEdif(idEdificio, TipoEdif, NomeEdif, Autores, descedif, ling) VALUES('" . $idEdif . "','" . ${"tipoedif" . $row["id"]} . "' ,'" . ${"nomeedif" . $row["id"]} . "' ,'" . ${"autores" . $row["id"]} . "' ,'" . ${"descedif" . $row["id"]} . "','".$row["id"]."')";
        $result = $bd->exec($query);
        
    }

    /* Insere os pontos do polígono */
    $i = 0;
    foreach ($_POST["poliLong"] as $poli) {
        $query = "INSERT INTO Coordenadas(idEdificio, CoordLong, CoordLat, idCoord) VALUES('" . $idEdif . "','" . $poli . "','" . $_POST['poliLat'][$i] . "','" . $i . "')";
        $result = $bd->exec($query);
        $i++;
    }

    //echo count($_FILES["fileToUpload"]["name"]);
    /* Upload de Imagens */
    
    
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
    


    echo '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <img src="..." class="rounded mr-2" alt="...">
      <strong class="mr-auto">Bootstrap</strong>
      <small>11 mins ago</small>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>';
}
?>


<h1 class="h1"><?= _ADDBUILDING ?></h1>
</div>
<div class="row col-md-12">
    <div class="col-md-9">

        <form action="?p=1" method="POST" enctype="multipart/form-data">
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _BUILDINGNAME ?>(<?php echo $row["Nome"] ?>)</span>
                </div>
                <input name="nomeedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Joaquim Manuel" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            }
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _LOCATION ?></span>
                </div>
                <input name="localizacao" type="text" class="form-control" placeholder="ex: Tomar" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _AUTHOR ?>(<?php echo $row["Nome"] ?>)</span>
                </div>
                <input name="autores<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Joaquim Manuel" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            }
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _DATEOFOUNDING ?></span>
                </div>
                <input name="data" type="text" class="form-control" placeholder="ex: 21/02/1935" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= _BUILDINGTYPE ?>(<?php echo $row["Nome"] ?>)</span>
                </div>
                <input name="tipoedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Monumento" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            }
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Longitude</span>
                </div>
                <input name="coordlong" type="text" class="form-control" placeholder="º" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Latitude</span>
                </div>
                <input name="coordlat" type="text" class="form-control" placeholder="º" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <?php
            $check_query = $bd->query("SELECT * FROM Linguagem");
            while ($row = $check_query->fetchArray()) {
            ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><?= _DESCRIPTION ?> (<?php echo $row["Nome"] ?>)</span>
                    </div>
                    <textarea name="descedif<?php echo $row["id"] ?>" type="text" class="form-control" placeholder="ex: Lorem ipsum..." aria-label="Username" aria-describedby="basic-addon1"></textarea>
                </div>
            <?php
            }
            ?>
            <div class="input-group" id="poliPoints">
                <div class="input-group-prepend">
                    <span class="input-group-text">Coord. <?= _POLYGON ?> (Long | Lat)</span>
                </div>
                <input name="poliLong[]" type="text" aria-label="Long" class="form-control">
                <input name="poliLat[]" type="text" aria-label="Lat" class="form-control">
                <script>
                    var morePoli = '<div class="input-group" id="poliPoints"><div class="input-group-prepend"><span class="input-group-text">Coord. <?= _POLYGON ?> (Long | Lat)</span></div><input name="poliLong[]" type="text" aria-label="Long" class="form-control"><input name="poliLat[]" type="text" aria-label="Lat" class="form-control"></div></div>';
                </script>
                <button class="btn btn-warning" type="button" onclick='$("#poliPoints").append(morePoli);'>+</button>
            </div>
            <div class="input-group mt-3 mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= _ADDIMAGE ?></span>
                </div>
            </div>
            <!--INACABADO -->
            <!-- Adição da imagem com preview -->
            <div id="imageUploads">
                <input name="fileToUpload[]" id="ImagUpload" type="file" multiple="multiple" />
                <script>
                    var moreImages = '<input name="fileToUpload" id="ImagUpload" type="file" multiple="multiple" />';
                </script>
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
            <div>
                <button id="submit" class="btn btn-success mt-3" type="submit"><?= _ADD ?></button>
            </div>

        </form>