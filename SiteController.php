<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Materials;
use app\models\Subscriptions;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\db\Expression;

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
     * Главная страница со всеми материалами на сайте
     * @param int $sort Приходит при нажатии кнопки по get
     * @return string
     */
    public function actionIndex(int $sort = 0): string
    {
        //Получаем общее количество статей
        $page = count(Materials::find()->all());

        //Подключаем пагинацию
        $pagination = new Pagination(['totalCount' => $page, 'pageSize' => 3]);

        $pagination->pageSizeParam = false;
        $pagination->forcePageParam = false;

        //Выставляем ограничение на количество статей на одной странице
        $materials = Materials::find()->offset($pagination->offset)
            ->limit($pagination->limit);

        /*
         * Сортировка, если при нажатии на главном экране кнопки сортировки, то передается переменна $sort
           по которой и будет выбрана та сортировка которая стоит по нужной цифрой
        */
        match ($sort) {
            // Сортировать по количеству комментариев desc
            1 => $materials->alias('mat')->select([
                'title',
                'mat.content',
                'blog_id',
                (new Expression(' COUNT(title) as count')),
            ])
                ->innerJoin(['com' => 'comments'], 'com.material_id = mat.id')
                ->groupBy([
                    'title',
                    'mat.content',
                    'blog_id',
                ])->orderBy([
                    'count' => SORT_DESC,
                ])->all(),
            // Сортировать по количеству комментариев asc
            2 => $materials->alias('mat')->select([
                'title',
                'mat.content',
                'blog_id',
                (new Expression(' COUNT(title) as count')),
            ])
                ->innerJoin(['com' => 'comments'], 'com.material_id = mat.id')
                ->groupBy([
                    'title',
                    'mat.content',
                    'blog_id',
                ])->orderBy([
                    'count' => SORT_ASC,
                ])->all(),
            // Сортировать по количеству материалов asc
            3 => $materials->alias('mat')->select([
                'title',
                'mat.content',
                'blog_id',
                (new Expression(' COUNT(blog_id) as count')),
            ])->groupBy([
                'title',
                'mat.content',
                'blog_id',
            ])->orderBy([
                'count' => SORT_ASC,
            ])->all(),
            // Сортировать по количеству материалов desc
            4 => $materials->alias('mat')->select([
                'title',
                'mat.content',
                'blog_id',
                (new Expression(' COUNT(blog_id) as count')),
            ])->groupBy([
                'title',
                'mat.content',
                'blog_id',
            ])->orderBy([
                'count' => SORT_DESC,
            ])->all(),
            default => $materials->all(),
        };


        return $this->render('index', [
            'materials' => $materials,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Materials model.
     * Страница определенного материала с комментариями по нему
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewMaterials(int $id)
    {
        //Получить все комментарии для этого материала
        $comments = Comments::find()->where(['material_id' => $id])->all();

        //Создать новый комментарий
        $new_comment = new Comments();
        //Если запрос к странице был типа post значит можно обратиться к функции создания комментариев
        if ($this->request->isPost) {
            //Поместить в $new_comment все что получили из post
            if ($new_comment->load($this->request->post())) {
                //Функция по сохранению комментария
                $new_comment->getNewComment($new_comment['content'], Yii::$app->user->getId(), $id);
                //Вернуться на страницу материала который содержит этот комментарий
                return $this->redirect('view-materials?id=' . $id);
            }
        }
        return $this->render('view-materials', [
            'model' => $this->findModel($id),
            'new_comment' => $new_comment,
            'comments' => $comments,
        ]);
    }

    /**
     * @param int $id
     * @return void|Response
     * @throws Exception
     */
    public function actionUpdateComment(int $id)
    {
        //Получить комментарий
        $up_comment = Comments::find()->where(['material_id' => $id]);

        //Если текущий пользователь оставил этот комментарий, то обратиться к функции сохранения
        if (Yii::$app->user->getId() == $up_comment->user_id) {
            if ($this->request->isPost) {
                //Поместить в $up_comment все что получили из post
                if ($up_comment->load($this->request->post())) {
                    //Функция по сохранению комментария
                    $up_comment->getNewComment($up_comment['content'], Yii::$app->user->getId(), $id);
                    //Вернуться на страницу материала который содержит этот комментарий
                    return $this->redirect('view-materials?id=' . $id);
                }
            }
        } else {
            //Выводит ошибку пользователю
            throw new Exception('Этот комментарий оставлен другим пользователем');
        }
    }

    /**
     * Удалить комментарий
     * @param int $id
     * @param int $material_id
     * @return Response
     * @throws Exception
     */
    public function actionDeleteComment(int $id, int $material_id)
    {
        //Получить комментарий по id
        $del_comment = Comments::find()->where(['id' => $id]);

        //Если текущий пользователь оставил этот комментарий, то обратиться к функции сохранения
        if (Yii::$app->user->getId() == $del_comment->user_id) {
            //удалить комментарий
            $del_comment->delete();
        } else {
            //Выводит ошибку пользователю
            throw new Exception('Этот комментарий оставлен другим пользователем');
        }

        return $this->redirect('view-materials?id=' . $material_id);
    }


    /**
     * Подписаться на автора материалов
     * @param int $id
     * @param int $blog_id
     * @param int $user_id
     * @return Response
     */
    public function actionSubscription(int $id, int $blog_id, int $user_id)
    {
        $subscriptions = new Subscriptions;
        //Обратиться к функции "Подписаться"
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
     * Показать все материалы на которые подписан пользователь
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
