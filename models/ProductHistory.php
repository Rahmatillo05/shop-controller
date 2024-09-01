<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "product_histories".
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $order_id
 * @property float|null $price
 * @property float|null $sale_price
 * @property float|null $amount
 * @property int|null $status
 * @property int|null $type
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Product $product
 * @property Order $order
 * @property ProductList $productList
 */
class ProductHistory extends \app\models\BaseModel
{

    const TYPE_INCOME = 1;
    const TYPE_OUTCOME = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'product_histories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['product_id', 'amount', 'price'], 'required'],
            [['product_id', 'product_list_id', 'order_id', 'status', 'type', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['product_id', 'product_list_id', 'order_id', 'status', 'type', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['price', 'sale_price', 'amount'], 'number'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['product_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductList::class, 'targetAttribute' => ['product_list_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'amount' => 'Amount',
            'status' => 'Status',
            'type' => 'Type',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return ActiveQuery|\app\models\search\ProductQuery
     */
    public function getProduct(): ActiveQuery|search\ProductQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getProductList(): ActiveQuery
    {
        return $this->hasOne(ProductList::class, ['id' => 'product_list_id']);
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function extraFields(): array
    {
        return [
            'product',
            'productList',
            'order'
        ];
    }
}
