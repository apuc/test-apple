<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
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

    public function actionFall($id, $page = null)
    {
        $apple = Apple::findOne($id);
        $apple->fallToGround();
        return $this->redirect(Url::to(['index', 'page' => $page]));
    }

    public function actionEat($id, $count, $page = null)
    {
        $apple = Apple::findOne($id);
        return $apple->eat((int)$count) ? json_encode(['status' => 'success']) : json_encode(['status' => 'error']+$apple->errors);
        //return $this->redirect(Url::to(['index', 'page' => $page]));
    }
}
