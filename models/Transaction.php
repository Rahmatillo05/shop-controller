<?php

namespace app\models;

use app\models\search\CustomerQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int|null $date
 * @property int|null $customer_id
 * @property int|null $type
 * @property float|null $amount
 * @property int|null $payment_type
 * @property int|null $status
 * @property string|null $comment
 * @property int|null $model_id
 * @property string|null $model_class
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $transaction_date
 * @property int|null $relation_id
 *
 * @property Customer $customer
 */
class Transaction extends \app\models\BaseModel
{

    const TYPE_INCOME = 1;
    const TYPE_OUTCOME = 2;

    const PAYMENT_TYPE_CASH = 1;
    const PAYMENT_TYPE_CARD = 2;
    const PAYMENT_TYPE_MIX = 3;
    const PAYMENT_OUTGO = 4;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['type', 'amount'], 'required'],
            [['date', 'customer_id', 'relation_id', 'transaction_date', 'type', 'payment_type', 'status', 'model_id', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['date', 'customer_id', 'type', 'payment_type', 'status', 'model_id', 'deleted_at', 'created_at', 'updated_at', 'relation_id', 'transaction_date'], 'integer'],
            [['amount'], 'number'],
            [['comment'], 'string'],
            [['model_class'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['relation_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['relation_id' => 'id']],
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
            'type' => 'Type',
            'amount' => 'Amount',
            'payment_type' => 'Payment Type',
            'status' => 'Status',
            'comment' => 'Comment',
            'model_id' => 'Model ID',
            'model_class' => 'Model Class',
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

    public function getRelations(): ActiveQuery
    {
        return $this->hasMany(self::class, ['relation_id' => 'id']);
    }

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'relation_id']);
    }

    public function extraFields(): array
    {
        return [
            'customer',
            'parent',
            'relations'
        ];
    }
}
