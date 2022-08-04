<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "materials".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int|null $blog_id
 *
 * @property Blogs $blog
 * @property Comments $comments
 */
class Materials extends \yii\db\ActiveRecord
{
    public int $user_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['blog_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [
                ['blog_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Blogs::className(),
                'targetAttribute' => ['blog_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
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

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasOne(Comments::className(), ['material_id' => 'id']);
    }

    public function getIsSubBlog(int $user_id)
    {
        return $this::find()->alias('mat')
            ->select(['sub.user_id as user_id'])
            ->distinct()
            ->innerJoin(['sub' => 'subscriptions'], 'sub.blog_id = mat.blog_id')
            ->where(['sub.user_id' => $user_id])
            ->one();
    }
}
