<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

class BaseModel extends ActiveRecord
{
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
            'delete' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => time()
                ],
                'replaceRegularDelete' => true
            ]
        ]);
    }

   public static function find(): ActiveQuery
   {
       return parent::find()->andWhere([static::tableName() . '.deleted_at' => null]);
   }
}