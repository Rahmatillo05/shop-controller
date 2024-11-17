<?php

namespace app\models;

use app\models\search\ProductQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "order_goods".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property float|null $amount
 * @property float|null $price
 * @property float|null $coming_price
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

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['price'] = [
            'class' => AttributeBehavior::class,
            'attributes' => [
                self::EVENT_BEFORE_INSERT => 'price',
            ],
            'value' => function ($event) {
                if (is_null($this->price)) {
                    return $this->product->sale_price;
                }
                return $this->price;
            }
        ];

        $behaviors['price_sale'] = [
            'class' => AttributeBehavior::class,
            'attributes' => [
                self::EVENT_BEFORE_INSERT => 'price_sale',
            ],
            'value' => function ($event) {
                if (is_null($this->price_sale)) {
                    return $this->product->sale_price_min;
                }
                return $this->price_sale;
            }
        ];
        $behaviors['coming_price'] = [
            'class' => AttributeBehavior::class,
            'attributes' => [
                self::EVENT_BEFORE_INSERT => 'coming_price',
            ],
            'value' => function ($event) {
                if (is_null($this->coming_price)) {
                    return $this->product->price;
                }
                return $this->coming_price;
            }
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order_id', 'product_id', 'amount'], 'required'],
            [['order_id', 'product_id', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['order_id', 'product_id', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'price', 'price_sale', 'coming_price'], 'number'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['amount'], 'validateAmount'],
            [['price'], 'validatePrice']
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

    public function validateAmount(): void
    {
        if ($this->amount <= 0){
            $this->addError('amount', "Mahsulot miqdori noto'g'ri kiritildi!");
            return;
        }
        if (!is_null($this->product) && $this->amount > $this->product->remind) {
            $this->addError('amount', "Omborda buncha mahsulot mavjud emas! Qoldiq: {$this->product->remind} {$this->product->unit->name}");
        }
    }
    public function validatePrice(): void
    {
        if ($this->price <= 0){
            $this->addError('price', "Mahsulot narxi noto'g'ri kiritildi!");
            return;
        }
        if (!is_null($this->product) && $this->price < $this->product->sale_price_min) {
            $this->addError('price', "Sotish narxi minimal narxdan past kiritildi!");
        }
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
