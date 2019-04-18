<?php
$postData = array(
   'id_order' => 3,
   'pesanan_complain' => 'ini test'     
);

$id_order = 3;
$pesan_complain = "pesan test";


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

echo $textResponse;
    ?>
