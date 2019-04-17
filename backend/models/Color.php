<?php

namespace backend\models;

use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "color".
 *
 * @property int $id
 * @property string $name
 * @property string $title
 *
 * @property Apple[] $apples
 */
class Color extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'title'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'name' => 'Цвет',
            'title' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApples()
    {
        return $this->hasMany(Apple::class, ['color_id' => 'id']);
    }

    /**
     * Достает из таблицы или создает новый цвет и возвращает его id
     *
     * @param string $color
     * @return int
     * @throws ErrorException
     */
    public static function getId($color)
    {
        $model = self::findOne(['name' => $color]);
        if ($model === null) {
            $model = new self();
            $model->name = $color;

            if (!$model->save())
                throw new ErrorException('Не удалось добавить новый цвет!');
        }

        return $model->id;
    }
}
