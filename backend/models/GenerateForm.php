<?php
namespace backend\models;

use Yii;
use yii\base\Model;

class GenerateForm extends Model
{
    public $count;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['count', 'required'],
            ['count', 'integer', 'min' => 1, 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'count' => 'Кол-во яблок',
        ];
    }
}
