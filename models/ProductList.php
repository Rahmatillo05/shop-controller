<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "product_lists".
 *
 * @property int $id
 * @property int|null $date
 * @property int|null $customer_id
 * @property string|null $comment
 * @property int|null $status
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Customer $customer
 */
class ProductList extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_lists';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'customer_id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['date', 'customer_id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'customer_id' => 'Customer ID',
            'comment' => 'Comment',
            'status' => 'Status',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery|\app\models\search\CustomerQuery
     */
    public function getCustomer(): ActiveQuery|search\CustomerQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function extraFields()
    {
        return [
            'customer'
        ];
    }

}
