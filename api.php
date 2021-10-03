<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type:application/json");


$checkedRoutes = [];
if (isset($_GET['edificios']) && isset($_GET['lang']) && $_GET['lang'] != "") {
    include("requires/bd.php");

    if($_GET['edificios']==='all'){
        $dir    = 'uploads/';
        $pics = scandir($dir);
        
        // Nome Edif + Info
        //$result = $bd->query("SELECT * FROM Edificios as A, DescEdif as B WHERE B.ling={$_GET['lang']} AND A.idEdificio=B.idEdificio;");
        $result = $bd->query("SELECT * FROM Edificios as A INNER JOIN DescEdif as B ON A.idEdificio = B.idEdificio WHERE  B.ling={$_GET['lang']}");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Get Coordenadas
            $coordenadas = [];
            $coords = $bd->query("SELECT C.idCoord, C.CoordLong, C.CoordLat FROM Edificios as A INNER JOIN Coordenadas as C ON A.idEdificio = C.idEdificio WHERE A.idEdificio={$row['idEdificio']};");
            while ($row2 = $coords->fetchArray(SQLITE3_ASSOC)) {
                array_push($coordenadas,$row2);
                
            }
            
            // Get pictures
            $images = [];
            foreach(glob($dir . $row["idEdificio"] . "_*") as $pic){
                array_push($images,$pic);
            }

            $rotasEdif = [];
            
            $bRotas = $bd->query("SELECT * FROM Pontos_Rota as A INNER JOIN Rotas as B ON A.id_Rota = B.id WHERE  A.id_Edificio={$row['idEdificio']};");
            while ($row5 = $bRotas->fetchArray(SQLITE3_ASSOC)) {
                $rotaPoints = $bd->query("SELECT * FROM Pontos_Rota as A WHERE A.id_Rota={$row5['id_Rota']};");
                $pontosDaRota = [];
                if(!in_array($row5['id_Rota'],$checkedRoutes)){
                while ($row6 = $rotaPoints->fetchArray(SQLITE3_ASSOC)) {
                    
                        array_push($pontosDaRota,$row6);
                    }
                    $row5["pivot"] = $pontosDaRota;
                array_push($rotasEdif,$row5);
                }    
                array_push($checkedRoutes,$row5['id_Rota']);
                
                
            }

            $row["Rotas"] = $rotasEdif;
            $row["Images"] = $images;
            $row["Coordenadas"]=$coordenadas;
            $edificios[] = $row;
        }

        $result = $bd->query("SELECT * FROM Rotas");
        while ($row3 = $result->fetchArray(SQLITE3_ASSOC)) {
            $pontos = [];
            $pts = $bd->query("SELECT id_Edificio FROM Pontos_Rota WHERE id_Rota = {$row3['id']};");
            while ($row4 = $pts->fetchArray(SQLITE3_ASSOC)) {
                array_push($pontos,$row4);
                
            }
            $row3["id_pontos"]=$pontos;
            $rotas[] = $row3;
        }

        $dados["Edificios"] = $edificios;
        //$dados["Rotas"] = $rotas;
        // DescEdif Stuff ^ fazer coisas dentro daquelee while
        //$coords = $bd->query("SELECT C.idCoord, C.CoordLong, C.CoordLat FROM Edificios as A INNER JOIN Coordenadas as C ON A.idEdificio = C.idEdificio WHERE A.idEdificio={$_GET['edificio']};");
        //while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        //    $edificios[]=$row;
       // }
       return response($dados,200,"OK");
        
    }
    
    $result = $bd->query("SELECT A.idEdificio, A.Localizacao, B.Autores, A.Data, B.TipoEdif, A.CoordLongEdif, A.CoordLatEdif, B.NomeEdif FROM Edificios as A, DescEdif as B WHERE B.ling=1 AND A.idEdificio=B.idEdificio;");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $edificios[]=$row;
    }
    if(!empty($edificios)){
        response($edificios,200,"OK");
        
    }else{
        response(NULL,200,"No buildings found.");
    }
}else if(isset($_GET['edificio']) && $_GET['edificio'] != ""){
    include("requires/bd.php");
    $dir    = 'uploads/';
    $pics = scandir($dir);
    
    if(isset($_GET['lang']) && $_GET['lang'] != ""){
        $result = $bd->query("SELECT * FROM Edificios as A INNER JOIN DescEdif as B ON A.idEdificio = B.idEdificio WHERE A.idEdificio={$_GET['edificio']} AND B.ling={$_GET['lang']}");
    }else{
    
    $result = $bd->query("SELECT * FROM Edificios as A INNER JOIN DescEdif as B ON A.idEdificio = B.idEdificio WHERE A.idEdificio={$_GET['edificio']};");
}
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $edificios[]=$row;
    
    $coords = $bd->query("SELECT C.idCoord, C.CoordLong, C.CoordLat FROM Edificios as A INNER JOIN Coordenadas as C ON A.idEdificio = C.idEdificio WHERE A.idEdificio={$_GET['edificio']};");

    while ($row = $coords->fetchArray(SQLITE3_ASSOC)) {
        $coordenadas[] = $row;
    }
    $edificios[0]["Coordenadas"]=$coordenadas;

    foreach(glob($dir . $_GET['edificio'] . "_*") as $pic){
        $images[] = $pic;
    }
    $edificios[0]["images"] = $images;
    if(!empty($edificios)){
        response($edificios,200,"OK");
    }else{
        response(NULL,200,"No building found.");
    }
}else{
    response(NULL,400,"Invalid Request");
}






function response($edificios,$response_code,$response_desc){
    $response = $edificios;

    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    
    $json_response = json_encode($response);
    echo $json_response;
}
?>