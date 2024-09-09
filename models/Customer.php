<?php

namespace app\models;

use app\DTOs\CustomerBalance;
use app\models\search\ProductListQuery;
use app\models\search\UserQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;
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

    public function fields(): array
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
        return $this->hasMany(Transaction::class, ['transactions.customer_id' => 'id']);
    }

    /**
     * @throws Exception
     */
    public function getBalance(): CustomerBalance
    {
        $statusInActive = Transaction::STATUS_INACTIVE;
        $sql = <<<SQL
select
    case when type=1 then sum(amount) end as credit,
    case when type=2 then sum(amount) end as debit
    from transactions
where customer_id = {$this->id}
  and status <> {$statusInActive}
group by type
SQL;
        $balance = Yii::$app->db->createCommand($sql)->queryOne();
        if ($balance){
            return new CustomerBalance((float)$balance['credit'], (float)$balance['debit']);
        }
        return new CustomerBalance(0, 0);
    }

}
