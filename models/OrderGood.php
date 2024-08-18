<?php

namespace app\models;

use app\models\search\ProductQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "order_goods".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property float|null $amount
 * @property float|null $price
 * @property float|null $price_sale
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Order $order
 * @property Product $product
 */
class OrderGood extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'product_id', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['order_id', 'product_id', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'price', 'price_sale'], 'number'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'price' => 'Price',
            'price_sale' => 'Price Sale',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function extraFields(): array
    {
        return ['product', 'order'];
    }
}
