<?php
namespace common\models;

use common\exceptions\InvalidMethodException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "Apple".
 *
 * @property int $id ID
 * @property string $color Цвет
 * @property int $created_at Добавлено
 * @property int $fall_at Когда упал
 * @property int $state Состояние
 * @property int $integrity Сколько съели в %
 * @property int $index Индекс в массиве положений
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

    const POS = [
        [0, 250],
        [80, 170],
        [140, 300],
        [220, 230],
        [250, 380],
        [260, 150],
        [340, 230],
        [360, 100],
        [430, 160],
        [490, 50]
    ];
    const ON_GROUND_POS = 525;
    /**
     * Время жизни
     */
    const LIVE_TIME = 5 * 60 * 1000;

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
            'dateFallFormatted' => 'Упало',
            'state' => 'Состояние',
            'stateName' => 'Состояние',
            'integrity' => 'Целостность'
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
     * Получить отформатированное значение даты падения
     * @return string
     */
    public function getDateFallFormatted(){
        return (new \DateTime())->setTimestamp($this->fall_at)->format("H:i:s");
    }

    /**
     * Получить координаты на дереве
     * @return array
     */
    public function getPos(){
        $index = $this->index % count(self::POS);
        return $this->state === self::STATE_HANG
            ? self::POS[$index]
            : [self::POS[$index][0], self::ON_GROUND_POS];
    }

    /**
     * Действие "уронить на землю" и "сгнить, если на земле"
     * @return bool
     */
    public function fallToGround(){
        if ($this->state === self::STATE_FALL){
            return false;
        }
        $this->state = self::STATE_FALL;
        $this->fall_at = (new \DateTime())->getTimestamp();
        return true;
    }

    /**
     * Действие "съесть яблоко"
     * @param int $pct
     * @return float
     * @throws InvalidMethodException
     */
    public function eat($pct = 25){
        $state = $this->getState();
        if ($state === self::STATE_ROTTEN){
            throw new InvalidMethodException("Съесть нельзя, яблоко гнилое");
        }
        if ($state !== self::STATE_FALL){
            throw new InvalidMethodException("Съесть нельзя, яблоко на дереве");
        }
        $this->integrity -=$pct;
        if ($this->integrity <= 0){
            return $this->remove();
        }
        return true;
    }

    /**
     * Удаление
     * @param $id
     * @return false|int
     * @throws InvalidMethodException
     */
    public function remove(){
        if ($this->getState() === self::STATE_ROTTEN || $this->integrity <= 0){
            try {
                return !$this->delete();
            } catch (StaleObjectException $e) {
                throw new InvalidMethodException($e->getMessage());
            } catch (\Throwable $e) {
                throw new InvalidMethodException($e->getMessage());
            }
        } else {
            throw new InvalidMethodException("Нельзя убрать. Яблоко не гнилое.");
        }
    }

    /**
     * Текущий размер яблока
     * @return float
     */
    public function getSize(){
        return (float) $this->integrity/100;
    }
}