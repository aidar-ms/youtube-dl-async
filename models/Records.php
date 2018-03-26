<?php
namespace app\models;

use yii\db\ActiveRecord;

class Records extends ActiveRecord {

    public static function tableName() {
        return '{{download_records}}';
    }

    public function makeRecord($email, $fileName, $ytVideoId) {

        $this->email = $email;
        $this->file_name = $fileName;
        $this->yt_video_id = $ytVideoId;

       if($this->save()) {
            return 1;
       }

       return 0;    
    }
}


?>