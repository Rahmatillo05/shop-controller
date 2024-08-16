<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $value
 * @property int|null $type
 * @property string|null $key
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Setting extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'settings';
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->cache->delete("setting:$this->id");
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title'], 'string'],
            [['type', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['type', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['value', 'key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'value' => 'Value',
            'type' => 'Type',
            'key' => 'Key',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findByKey(string $key): bool|string|null
    {
        $cache = Yii::$app->cache;
        $setting = $cache->get("setting:$key");
        if (!$setting) {
            $setting = self::findOne(['key' => $key]);
            $cache->set("setting:$key", $setting, 3600 * 24);
        }
        if ($setting) {
            return $setting->value;
        }
        return false;
    }
}
