<?php

namespace app\models;

use app\DTOs\CustomerBalance;
use app\models\search\ProductListQuery;
use app\models\search\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string|null $full_name
 * @property string|null $phone_number
 * @property string|null $address
 * @property int|null $status
 * @property int|null $user_id
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property ProductList[] $productLists
 * @property User $user
 * @property Transaction[] $transactions
 * @property CustomerBalance $balance
 *
 */
class Customer extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['status', 'user_id', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'user_id', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'phone_number', 'address'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'full_name' => 'Full Name',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'status' => 'Status',
            'user_id' => 'User ID',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'balance' => function ($model) {
                return $model->getBalance();
            }
        ]);
    }

    public function extraFields(): array
    {
        return [
            'user',
            'productLists',
        ];
    }

    /**
     * Gets query for [[ProductLists]].
     *
     * @return ActiveQuery|ProductListQuery
     */
    public function getProductLists(): ActiveQuery|search\ProductListQuery
    {
        return $this->hasMany(ProductList::class, ['customer_id' => 'id']);
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

    public function getTransactions(): ActiveQuery
    {
        return $this->hasMany(Transaction::class, ['customer_id' => 'id']);
    }

    public function getBalance(): CustomerBalance
    {
        $credit = (float)$this->getTransactions()->andWhere(['type' => Transaction::TYPE_INCOME])->sum('amount');
        $debit = (float)$this->getTransactions()->where(['type' => Transaction::TYPE_OUTCOME])->sum('amount');
        return new CustomerBalance($credit, $debit);
    }

}
