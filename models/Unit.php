<?php

namespace app\models;

use app\models\search\CategoryQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "units".
 *
 * @property int $id
 * @property string $name
 * @property int|null $value_type
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Category[] $categories
 */
class Unit extends \app\models\BaseModel
{
    const VALUE_TYPE_INTEGER = 1;
    const VALUE_TYPE_FLOAT = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'units';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value_type'], 'required'],
            [['value_type', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['value_type', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value_type' => 'Value Type',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return ActiveQuery
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['unit_id' => 'id']);
    }
}
