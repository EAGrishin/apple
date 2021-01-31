<?php
namespace backend\controllers;

use Yii;
use yii\db\Expression;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Apple;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'create-apple', 'eat-apple', 'fall-apple', 'rotten-apple'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['create-apple', 'eat-apple', 'fall-apple', 'rotten-apple'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string {
        //Проверяем, есть ли испроченные яблоки, если есть, то обновляем статус и меняем цвет на испорченное яблоко
        $this->updateOnRottenState();
        //Собираем массив яблок
        $apples = Apple::findAll(['user_id' => Yii::$app->user->id]);

        return $this->render('index', [
            'apples' => $apples
        ]);
    }

    //Геренируем случайное кол-во яблок от 1 до 10
    public function actionCreateApple(): array {
        try {
            //Перед тем как сгенерировать яблоки, удаляем старые у данного пользователя
            Apple::deleteAll(['user_id' => Yii::$app->user->id]);
            Apple::createRandom();
            $apples = Apple::findAll(['user_id' => Yii::$app->user->id]);
            return [
                'success' => true,
                'html' => $this->renderPartial('apples', [
                    'apples' => $apples
                ])
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    //Откусить от яблака определенный процент
    public function actionEatApple ($id, $percent): array {
        try {
            $apple = $this->findModel($id);
            $apple->eat($percent);
            if ($apple->eat_percent >= 100) {
                $apple->delete();
                return ['success' => false, 'delete' => true];
            } else {
                return [
                    'success' => true,
                    'html' => $this->renderPartial('_progress', [
                        'apple' => $apple
                    ])
                ];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    //Яблоко падает на землю
    public function actionFallApple($id): array {
        try {
            $apple = $this->findModel($id);
            $apple->fallToGround();
            return [
                'success' => true,
                'state' => Apple::$state_apple[$apple->state]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    //Проверяем есть ли испорченные яблоки (аякс запросом каждые 5 минут)
    public function actionRottenApple(): array {
        if ($this->updateOnRottenState() > 0) {
            $appleIds = Apple::find()->select('id')
                ->where(['state' => Apple::STATE_ROTTEN, 'user_id' => Yii::$app->user->id])
                ->asArray()->column();
            return [
                'success' => true,
                'ids' => $appleIds,
                'state' => Apple::$state_apple[Apple::STATE_ROTTEN],
                'color' => 'BDB76B'
            ];
        }
        return ['success' => false];
    }

    // Функция обновления состояния яблок, если они уже испортились
    private function updateOnRottenState(): int {
        return Apple::updateAll(['state' => Apple::STATE_ROTTEN, 'color' => 'BDB76B', 'user_id' => Yii::$app->user->id],
            ['<=', 'date_fall', new Expression('NOW() - INTERVAL 5 HOUR')
        ]);
    }

    private function findModel($id) {
        if (($model = Apple::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Яблока #{$id} не существует.");
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
