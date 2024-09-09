<?php

namespace app\models;

use app\models\search\CustomerQuery;
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
 * @property ProductHistory[] $products
 * @property float $totalSum
 */
class ProductList extends \app\models\BaseModel
{
    const STATUS_UNPAID = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_WAIT = 2;
    const STATUS_COMPLETE = 10;


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
    public function attributeLabels(): array
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
     * @return ActiveQuery|CustomerQuery
     */
    public function getCustomer(): ActiveQuery|search\CustomerQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(ProductHistory::class, ['product_list_id' => 'id']);
    }

    public function getTotalSum(): float
    {
        return (float)$this->getProducts()->sum("(price*amount)");
    }

    public function getTotalProductCount(): int
    {
        return (int)$this->getProducts()->count();
    }
    public function extraFields(): array
    {
        return [
            'customer',
            'totalSum',
            'totalProductCount',
        ];
    }

}
