<?php
 namespace app\models;


 class YtdlGearmanClient {

    private $grmnClient;

    public function __construct($url, $email) {

        $this->grmnClient = new \GearmanClient();
        $this->grmnClient->addServer('gearman', 4730);
        $this->grmnClient->doBackground("make_routine", json_encode(array(

          'url' => $url,
          'email' => $email

        )));

        /*
        $grmnClient = new \GearmanClient(); 
        $grmnClient->addServer('127.0.0.1', 4730); 
        $grmnClient->doBackground("make_routine", json_encode(array("url"=>"https://www.youtube.com/watch?v=CR2NGSHwnA4", "email"=>"mamytovaydar@gmail.com")));
        */

    }

 }



?>
