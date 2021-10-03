<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type:application/json");



if (isset($_GET['edificios']) && isset($_GET['lang']) && $_GET['lang'] != "") {
    
    if($_GET['edificios']==='all'){
        $dados = json_decode(file_get_contents("info.json"),true);
       return response($dados,200,"OK");
        
    }
}

function response($edificios,$response_code,$response_desc){
    $response = $edificios;

    $response['response_code'] = $response_code;
    $response['response_desc'] = $response_desc;
    
    $json_response = json_encode($response);
    echo $json_response;
}
?>