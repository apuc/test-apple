<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use backend\models\Apple;
use backend\models\GenerateForm;

class ApplesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $form_model = new GenerateForm();
        if ($form_model->load(Yii::$app->request->post()) && $form_model->validate()) {
            \backend\components\Apple::generate($form_model->count);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Apple::find()->where(['!=', 'status', Apple::STATUS_EATEN]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', ['form_model' => $form_model, 'dataProvider' => $dataProvider]);
    }
}
