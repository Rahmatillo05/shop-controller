<?php

namespace app\models;

use app\models\search\OrderGoodQuery;
use app\models\search\UserQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property string $payment_type
 * @property int|null $customer_id
 * @property int|null $accepted_at
 * @property string|null $comment
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $customer
 * @property OrderGood[] $orderGoods
 * @property User $user
 */
class Order extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'payment_type'], 'required'],
            [['user_id', 'status', 'customer_id', 'accepted_at', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['user_id', 'status', 'customer_id', 'accepted_at', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['payment_type'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
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
        return $this->hasOne(User::class, ['id' => 'customer_id']);
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

    public function extraFields()
    {
        return [
            'user',
            'customer'
        ];
    }
}
