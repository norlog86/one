<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property int $material_id
 * @property int $user_id
 * @property string|null $content
 *
 * @property Materials $material
 * @property User $user
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_id', 'user_id'], 'required'],
            [['material_id', 'user_id'], 'integer'],
            [['content'], 'string'],
            [
                ['material_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Materials::className(),
                'targetAttribute' => ['material_id' => 'id'],
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
            'material_id' => 'Material ID',
            'user_id' => 'User ID',
            'content' => 'Content',
        ];
    }

    /**
     * Gets query for [[Material]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Materials::className(), ['id' => 'material_id']);
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

    public function getNewComment(string $content, int $user_id, int $material_id)
    {
        $new_comment = new Comments();

        $new_comment->material_id = $material_id;
        $new_comment->user_id = $user_id;
        $new_comment->content = $content;
        $new_comment->save();
    }

    public function getDelComment(int $id)
    {
        $comment = Comments::findOne(['id' => $id]);
        $comment->delete();
    }
}
