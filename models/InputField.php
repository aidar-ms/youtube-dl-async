<?php
namespace app\models;

use yii\base\exception;
use yii\db\ActiveRecord;


class InputField extends \yii\base\Model
{
    public $url;
    public $email;

    private $rx = '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x';

    public function rules() {
        return [
            [['url','email'], 'required'],
            ['url', 'match', 'pattern' => $this->rx],
            ['email', 'email'],
        ];
    }

}