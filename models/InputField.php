<?php
namespace app\models;

use yii\base\UserException;

class InputField extends \yii\base\Model
{
    /* const SCENARIO_PSAT = 'P_sat';
    const SCENARIO_TSAT = 'T_sat'; */

    public $url;

    private $rx = '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x';

    /*public function scenarios() {
        return [
            self::SCENARIO_PSAT => ['tt','sval'],
            self::SCENARIO_TSAT => ['tt','sval'],
        ];
    } */

    public function rules() {
        return [
            ['url', 'required'],
            ['url', 'match', 'pattern' => $this->rx]
        ];
    }
}