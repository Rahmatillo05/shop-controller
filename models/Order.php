<?php

namespace app\models;

use app\models\search\OrderGoodQuery;
use app\models\search\UserQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property int $type
 * @property string $payment_type
 * @property int|null $customer_id
 * @property int|null $accepted_at
 * @property string|null $comment
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property float|null $orderSum
 * @property float|null $orderSumMin
 *
 * @property Customer $customer
 * @property OrderGood[] $orderGoods
 * @property User $user
 */
class Order extends BaseModel
{
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE = 1;
    const TYPE_SOLD = 1;
    const TYPE_RETURNED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'orders';
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        return ArrayHelper::merge($behaviors, [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['payment_type'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['user_id', 'customer_id', 'accepted_at', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['user_id', 'status', 'payment_type', 'customer_id', 'accepted_at', 'deleted_at', 'created_at', 'updated_at', 'type'], 'integer'],
            [['comment'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'status' => 'Status',
            'payment_type' => 'Payment Type',
            'customer_id' => 'Customer ID',
            'accepted_at' => 'Accepted At',
            'comment' => 'Comment',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[OrderGoods]].
     *
     * @return ActiveQuery|OrderGoodQuery
     */
    public function getOrderGoods(): ActiveQuery|search\OrderGoodQuery
    {
        return $this->hasMany(OrderGood::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getTransaction(): ActiveQuery
    {
        return $this->hasOne(Transaction::class, ['model_id' => 'id'])
            ->andWhere(['transactions.model_class' => self::class])
            ->andWhere(['transactions.relation_id' => null]);
    }

    public function fields(): array
    {
        $fields = parent::fields();
        return ArrayHelper::merge($fields, []);
    }

    public function getOrderSum(): float|null
    {
        return $this->getOrderGoods()->sum('(price * amount)');
    }
    public function getOrderSumMin(): float|null
    {
        return $this->getOrderGoods()->sum('(price_sale * amount)');
    }

    public function getOrderGoodsCount()
    {
        return $this->getOrderGoods()->count();
    }

    public function extraFields(): array
    {
        return [
            'user',
            'customer',
            'orderGoods',
            'transaction',
            'orderGoodsCount',
            'orderSum'
        ];
    }
}
