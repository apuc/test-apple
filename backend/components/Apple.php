<?php

namespace backend\components;

use yii\base\Component;
use yii\base\ErrorException;
use backend\models\Apple as AppleModel;
use backend\models\Color as ColorModel;

class Apple extends Component
{
    private $model = null;

    /**
     * Apple constructor.
     * Добавляет новое яблоко
     * @param string $color
     * @param array $config
     * @throws ErrorException
     */
    public function __construct($color, array $config = [])
    {
        $this->model = new AppleModel();
        $this->model->color_id = ColorModel::getId($color);
        if (!$this->model->save() || !$this->model->refresh())
            throw new ErrorException('Не удалось создать новое яблоко');

        parent::__construct($config);
    }

    /**
     * Роняет яблоко на землю
     *
     * @return bool
     */
    public function fallToGround()
    {
        if (!$this->model->fallToGround()) {
            $last_error = array_shift(array_shift($this->model->errors));
            throw new ErrorException($last_error);
        }

        return true;
    }

    /**
     * Откусывает кусок яблока
     *
     * @param int $percent
     * @return bool
     */
    public function eat($percent)
    {
        if (!$this->model->eat($percent)) {
            $last_error = array_shift(array_shift($this->model->errors));
            throw new ErrorException($last_error);
        }

        return true;
    }

    /**
     * Возвращает атрибуты модели
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->model->attributes;
    }

    /**
     * Генерирует новые яблоки случайного цвета
     *
     * @param $count
     */
    public static function generate($count)
    {
        $colors = ColorModel::find()->asArray()->all();
        for ($i = 0; $i < $count; $i++) {
            new self($colors[array_rand($colors)]['name']);
        }
    }
}
