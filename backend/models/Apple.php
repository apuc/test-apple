<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property int $color_id
 * @property double $size
 * @property int $status
 * @property string $created_at
 * @property string $fallen_at
 *
 * @property string $color
 * @property string $statusText
 * @property array $statuses
 *
 * @property Color $colorModel
 */
class Apple extends \yii\db\ActiveRecord
{
    const STATUS_ON_TREE = 1;
    const STATUS_FALLEN = 2;
    const STATUS_ROTTED = 3;
    const STATUS_EATEN = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color_id'], 'required'],
            [['color_id'], 'integer'],
            [['size'], 'number', 'min' => 0, 'max' => 1],
            [['size'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => array_keys($this->statuses)],
            [['status'], 'default', 'value' => self::STATUS_ON_TREE],
            //[['created_at', 'fallen_at'], 'safe'],
            [['color_id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::class, 'targetAttribute' => ['color_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'color_id' => 'Цвет',
            'size' => 'Остаток',
            'status' => 'Статус',
            'created_at' => 'Дата появления',
            'fallen_at' => 'Дата падения',
        ];
    }

    /**
     * Роняет яблоко на землю
     *
     * @return bool
     */
    public function fallToGround()
    {
        if ($this->status == self::STATUS_ON_TREE) {
            $this->status = self::STATUS_FALLEN;
            $this->fallen_at = new Expression('NOW()');

            return $this->save() && $this->refresh();
        } else {
            $this->addError('status', 'Мы не можем уронить уже упавшее яблоко');
        }

        return false;
    }

    /**
     * Откусывает кусок яблока
     *
     * @param int $percent
     * @return bool
     */
    public function eat($percent)
    {
        if ($this->status == self::STATUS_FALLEN) {
            if ($percent > 0 && $percent <= 100) {
                if ($this->size >= $percent/100) {
                    $this->size -= round($percent/100, 2);
                    if ($this->size == 0)
                        $this->status = self::STATUS_EATEN;

                    return $this->save();
                } else $this->addError('size', 'Мы не можем откусить больше, чем у нас осталось');
            } else $this->addError('size', 'Мы не можем откусить меньше 1% или больше 100%');
        } else $this->addError('size', 'Мы не можем кусать яблоко со статусом: '.$this->statusText);

        return false;
    }

    public function getStatusText()
    {
        return $this->statuses[$this->status];
    }

    public function getColor()
    {
        return $this->colorModel->name;
    }

    public function getColorTitle()
    {
        return $this->colorModel->title;
    }

    /**
     * Возвращает список всех возможных статусов
     *
     * @return array
     */
    public function getStatuses()
    {
        return [
            self::STATUS_ON_TREE => 'На дереве',
            self::STATUS_FALLEN => 'Упало/Сорвано',
            self::STATUS_ROTTED => 'Испорчено',
            self::STATUS_EATEN => 'Съедено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorModel()
    {
        return $this->hasOne(Color::class, ['id' => 'color_id']);
    }

    public function afterFind()
    {
        if ($this->status == self::STATUS_FALLEN) {
            $diff = date_diff(date_create($this->fallen_at), date_create('now'));
            if ($diff->h >= 5 || $diff->days > 0) {
                $this->status = self::STATUS_ROTTED;
                $this->save();
            }
        }
    }
}
