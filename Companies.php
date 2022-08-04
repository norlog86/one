<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "companies".
 *
 * @property int $id
 * @property string $title
 * @property string $website
 * @property string $address
 *
 * @property Blogs[] $blogs
 */
class Companies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'website', 'address'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['website', 'address'], 'string', 'max' => 50],
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
            'website' => 'Website',
            'address' => 'Address',
        ];
    }

    /**
     * Gets query for [[Blogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Blogs::className(), ['company_id' => 'id']);
    }
}
