<?php
namespace frontend\models;

use frontend\exceptions\InvalidMethodException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "Apple".
 *
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

    /**
     * Apple constructor.
     * @param mixed $config
     */
    public function __construct($config)
    {
        if (!is_array($config)){
            $config = ['color' => $config];
        }
        $config['state'] = self::STATE_HANG;
        $config['integrity'] = 100;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color', 'state'], 'required'],
            [['state', 'integrity'], 'integer'],
            [['created_at', 'fall_at'], 'date', 'format' => 'php:d.m.Y']
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
            'fall_at' => 'Когда упало',
            'state' => 'Состояние',
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

    public function getLiveTime(){
        return $this->created_at - (new \DateTime())->getTimestamp();
    }

    public function fallToGround(){
        if ($this->getLiveTime() > 5){
            $this->state = self::STATE_FALL;
            return true;
        }
        return false;
    }

    /**
     * @param $pct
     * @return $this
     * @throws InvalidMethodException
     */
    public function eat($pct){
        if ($this->integrity - $pct < 0){
            throw new InvalidMethodException("Нельзя столько съесть");
        }
        if ($this->state === self::STATE_ROTTEN){
            throw new InvalidMethodException("Съесть нельзя, яблоко гнилое");
        }
        if ($this->state !== self::STATE_FALL){
            throw new InvalidMethodException("Съесть нельзя, яблоко на дереве");
        }
        $this->integrity -=$pct;
        return $this;
    }

    public function getSize(){
        return $this->integrity/100;
    }
}