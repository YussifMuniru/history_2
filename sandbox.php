<?php



try{

   $div =  new PDO("mysql:host=localhost;dbname=lottery_tes", "enzerhub","enzerhub");

}catch(ErrorException $e){
    echo "Error: " . $e->getMessage();

}catch(Exception $e){
    echo "Exception: " . $e->getMessage().": ". $e->getCode();
}catch(Throwable $e){

    echo "Throwable: " . $e->getMessage();

}