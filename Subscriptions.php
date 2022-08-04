<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int|null $user_id
 * @property int|null $blog_id
 *
 * @property Blogs $blog
 * @property User $user
 */
class Subscriptions extends \yii\db\ActiveRecord
{
    public int $mat_id;
    public string $title;
    public string $content;
    public string $blog_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscriptions';
    }

    public static function primaryKey(): array
    {
        return ['user_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'blog_id'], 'integer'],
            [
                ['blog_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Blogs::className(),
                'targetAttribute' => ['blog_id' => 'id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'blog_id' => 'Blog ID',
        ];
    }

    /**
     * Gets query for [[Blog]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlog(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Blogs::className(), ['id' => 'blog_id']);
    }

    public function getSubscript(int $blog_id, int $user_id)
    {
        $this->blog_id = $blog_id;
        $this->user_id = $user_id;

        if ($this->save()) {
            return true;
        } else {
            die('Error save');
        }

    }

    public function getUserSub(int $user_id): array
    {
        return $this::find()->alias('sub')
            ->select([
                'mat.id as mat_id',
                'mat.title as title',
                'mat.content as content',
                'blogs.name as blog_name'
            ])
            ->distinct()
            ->innerJoin(['mat' => 'materials'],'mat.blog_id = sub.blog_id')
            ->leftJoin('blogs','blogs.id = sub.blog_id')
            ->where(['sub.user_id' => $user_id])
            ->all();
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
