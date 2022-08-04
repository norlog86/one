<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Materials;
use app\models\Subscriptions;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        //Получаем общее количество статей
        $page = count(Materials::find()->all());

        //Подключаем пагинацию
        $pagination = new Pagination(['totalCount' => $page, 'pageSize' => 3]);

        $pagination->pageSizeParam = false;
        $pagination->forcePageParam = false;

        //Выставляем ограничение на количество статей на одной странице
        $materials = Materials::find()->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'materials' => $materials,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Materials model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewMaterials(int $id)
    {
        $comments = Comments::find()->where(['material_id' => $id])->all();

        $new_comment = new Comments();
        if ($this->request->isPost) {
            if ($new_comment->load($this->request->post())) {
                $new_comment->getNewComment($new_comment['content'], Yii::$app->user->getId(), $id);
                return $this->redirect('view-materials?id=' . $id);
            }
        }
        return $this->render('view-materials', [
            'model' => $this->findModel($id),
            'new_comment' => $new_comment,
            'comments' => $comments,
        ]);
    }

    public function actionDeleteComment(int $id, int $material_id)
    {
        Comments::findOne(['id' => $id])->delete();

        return $this->redirect('view-materials?id=' . $material_id);
    }

    public function actionSubscription(int $id, int $blog_id, int $user_id)
    {
        $subscriptions = new Subscriptions;
        $subscriptions->getSubscript($blog_id, $user_id);

        return $this->redirect('view-materials?id=' . $id);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionSubscriptions(): string
    {
        $user_id = Yii::$app->user->identity->getId();

        $sub = new Subscriptions();

        $subs = $sub->getUserSub($user_id);

        return $this->render('subscriptions', [
            'subs' => $subs,
        ]);
    }

    /**
     * Finds the Materials model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Materials the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Materials
    {
        if (($model = Materials::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
