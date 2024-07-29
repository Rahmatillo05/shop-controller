<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $name
 * @property int $unit_id
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Unit $unit
 */
class Category extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'unit_id'], 'required'],
            [['unit_id', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['unit_id', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::class, 'targetAttribute' => ['unit_id' => 'id']],
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
            'unit_id' => 'Unit ID',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Unit]].
     *
     * @return ActiveQuery
     */
    public function getUnit(): ActiveQuery
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    public function extraFields(): array
    {
        return [
            'unit'
        ];
    }
}
