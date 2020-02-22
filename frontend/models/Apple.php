<?php
namespace frontend\models;

use frontend\exceptions\InvalidMethodException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Apple".
 *
 * @property int $id ID
 * @property string $color Цвет
 * @property int $created_at Добавлено
 * @property int $fall_at Когда упал
 * @property int $state Состояние
 * @property int $integrity Сколько съели в %
*/

class Apple extends ActiveRecord
{
    const STATE_HANG = 0;
    const STATE_FALL = 1;
    const STATE_ROTTEN = 2;

    const STATES = [
        self::STATE_HANG => 'Висит на дереве',
        self::STATE_FALL => 'Упало на землю',
        self::STATE_ROTTEN => 'Гнилое'
    ];

    const COLORS = [
        'IndianRed',
        'LightCoral',
        'Salmon',
        'DarkSalmon',
        'LightSalmon',
        'Crimson',
        'FireBrick',
        'DarkRed',
        'LightSalmon',
        'Coral',
        'Tomato',
        'OrangeRed',
        'DarkOrange',
        'Orange',
        'Gold',
        'Yellow',
        'LightYellow',
        'LemonChiffon',
        'LightGoldenrodYellow',
        'PapayaWhip',
        'Moccasin',
        'PeachPuff',
        'PaleGoldenrod',
        'Khaki'
    ];
    /**
     * Время жизни
     */
    const LIVE_TIME = 5 * 60 * 60 * 1000;

    /**
     * Apple constructor.
     * @param mixed $config
     */
    public function __construct($config = [])
    {
        if (!is_array($config)){
            $config = ['color' => $config];
        }
        if (!isset($config['color'])){
            $config['color'] = self::COLORS[rand(0, count(self::COLORS) - 1)];
        }
        $config['state'] = self::STATE_HANG;
        $config['integrity'] = 100;
        $config['created_at'] = (new \DateTime())->getTimestamp();
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color', 'state'], 'required'],
            [['state', 'integrity'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'created_at' => 'Добавлено',
            'dateCreateFormatted' => 'Появилось',
            'fall_at' => 'Когда упало',
            'state' => 'Состояние',
            'stateName' => 'Состояние',
            'integrity' => 'Целостность'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * Время жизни яблока
     * @return int
     */
    public function getLiveTime(){
        return $this->created_at - (new \DateTime())->getTimestamp();
    }

    /**
     * Обновить и вернуть состояние
     * @return int
     */
    public function getState(){
        if ($this->state === self::STATE_FALL && $this->getLiveTime() >= self::LIVE_TIME){
            $this->state = self::STATE_ROTTEN;
        }
        return $this->state;
    }

    /**
     * Получить текстовое значение состояния
     * @param $state
     * @return mixed
     */
    public function getStateName(){
        return self::STATES[$this->state];
    }

    /**
     * Установить состояние
     * @param $state
     */
    public function setState($state){
        $this->state = $state;
    }

    /**
     * Получить отформатированное значение даты появления
     * @return string
     */
    public function getDateCreateFormatted(){
        return (new \DateTime())->setTimestamp($this->created_at)->format("H:i:s");
    }

    /**
     * Действие "уронить на землю" и "сгнить, если на земле"
     * @return bool
     */
    public function fallToGround(){
        if ($this->getLiveTime() > 5){
            $this->state = self::STATE_FALL;
            return true;
        }
        return false;
    }

    /**
     * Действие "съесть яблоко"
     * @param int $pct
     * @return float
     * @throws InvalidMethodException
     */
    public function eat(int $pct = 25){
        if ($this->integrity - $pct < 0){
            throw new InvalidMethodException("Нельзя столько съесть");
        }
        $state = $this->getState();
        if ($state === self::STATE_ROTTEN){
            throw new InvalidMethodException("Съесть нельзя, яблоко гнилое");
        }
        if ($state !== self::STATE_FALL){
            throw new InvalidMethodException("Съесть нельзя, яблоко на дереве");
        }
        $this->integrity -=$pct;
        return $this->getSize();
    }

    /**
     * Текущий размер яблока
     * @return float
     */
    public function getSize(){
        return (float) $this->integrity/100;
    }
}