<?php
 namespace app\models;


 class YtdlGearmanClient {

    private $grmnClient;

    public function __construct($url, $email) {

        $this->grmnClient = new \GearmanClient();
        $this->grmnClient->addServer();
        $launchRoutine = $this->grmnClient->doBackground("make_routine", json_encode(array(

          'url' => $url,
          'email' => $email

        )));

    }

 }



?>