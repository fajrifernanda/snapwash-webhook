<?php

$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update["result"]["metadata"]["intentName"])) {
    checkIntentName($update);
}


function checkIntentName($update) {
    $intentName = $update["result"]["metadata"]["intentName"];
    switch($intentName){
        case "Paket Laundry": 
            paketLaundryResponse($update);
            break;
        case "Pesan Komplain":
            pesanKomplainSimpan($update);
            break;
        default :
            defaultResponse($update);
    }
}

function paketLaundryResponse($update) {
        $response = file_get_contents('http://localhost:8000/snapwash-backend/1.0/app_packet_laundry');
        $paket = json_decode($response,true);
        $paketlength = count($paket["data"]);
        $textResponse = "Di Snapwash ada beberapa pilihan paket : \n";
        for($x=0 ; $x<$paketlength ; $x++){
            $textResponse .= $paket["data"][$x]["name_packet"]." , durasi :".$paket["data"][$x]["duration"]." hari \n";
            foreach($paket["data"][$x]["sub_packet"] as $subpacket){
                $textResponse .= "  ".$subpacket["type_packet"]." Rp. ".$subpacket["price_per_kilo"]."\n";
            }
        }
        
        sendMessage(array(
            "source" => $update["result"]["source"],
            "speech" => $textResponse,
            "displayText" => $textResponse ,
            "contextOut" => array()
        ));
    
}

function pesanKomplainSimpan($update) {

    $id_order = $update['result']['contexts'][0]['parameters']['id_order'];
    $pesan_complain = $update['result']['contexts'][0]['parameters']['user_input'];

    $postData = array(
        'id_order' => $id_order,
        'pesanan_complain' => $pesan_complain     
     );

     $url = 'http://localhost:8000/snapwash-backend/1.0/cb_insert_complain';
     $myvars = 'id_order=' . $id_order . '&pesan_complain=' . $pesan_complain;
     
     $ch = curl_init( $url );
     curl_setopt( $ch, CURLOPT_POST, 1);
     curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
     curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
     curl_setopt( $ch, CURLOPT_HEADER, 0);
     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
     
     $response = curl_exec( $ch );
     if($response === FALSE){
        die('Error');
     }   

    $textResponse = "Komplain anda sudah diterima. \nPada pesanan".$id_order." : ".$pesan_complain."\nSnapwash akan menghubungi anda.";


    sendMessage(array(
        "source" => $update["result"]["source"],
        "speech" => $textResponse,
        "displayText" => $textResponse ,
        "contextOut" => array()
    ));

}


function defaultResponse($update) {
    sendMessage(array(
        "source" => $update["result"]["source"],
        "speech" => "ini default response",
        "displayText" => "ini default response" ,
        "contextOut" => array()
    ));

}


function sendMessage($parameters) {
    echo json_encode($parameters);
}


    
?>
