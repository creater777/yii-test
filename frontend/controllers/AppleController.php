<?php
namespace frontend\controllers;


use common\models\Apple;
use common\exceptions\InvalidMethodException;
use \Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class AppleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     *  Генерация массива яблок
     * @return \yii\web\Response
     */
    public function actionGenerate(){
        Apple::deleteAll();
        $data=[];
        for($i=0; $i < 10; $i++){
            $data[] = new Apple(['index' => $i]);
        }
        try {
            Yii::$app->db->createCommand()->batchInsert(Apple::tableName(), Apple::getTableSchema()->columnNames, $data)->execute();
        } catch (InvalidConfigException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect("/");
    }

    /**
     * Уронить яблоко
     * @param $id
     * @return \yii\web\Response
     */
    public function actionFall($id){
        $apple = Apple::findOne($id);
        $apple !== null && $apple->fallToGround() && $apple->save();
        return $this->redirect("/");
    }

    /**
     * Откусить яблоко
     * @param $id
     * @return \yii\web\Response
     */
    public function actionEat($id){
        $apple = Apple::findOne(['id'=>$id]);
        try {
            $apple !== null && $apple->eat() && $apple->save();
        } catch (InvalidMethodException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect("/");
    }

    /**
     * Удаление яблока
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id){
        $apple = Apple::findOne(['id'=>$id]);
        try {
            $apple !== null && $apple->remove();
        } catch (InvalidMethodException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect("/");
    }
}