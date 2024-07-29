<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $full_name
 * @property string $username
 * @property string $password
 * @property string|null $phone_number
 * @property string|null $address
 * @property string|null $auth_key
 * @property string|null $user_role
 * @property int|null $status
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string|null $access_token
 */
class User extends BaseModel implements IdentityInterface
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->cache->delete($this->id);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['full_name', 'username', 'password'], 'required'],
            [['status', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'username', 'password', 'phone_number', 'address', 'auth_key', 'user_role', 'access_token'], 'string', 'max' => 255],
            [['username'], 'unique', 'targetAttribute' => ['username']],
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
            'username' => 'Username',
            'password' => 'Password',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'auth_key' => 'Auth Key',
            'user_role' => 'User Role',
            'status' => 'Status',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'access_token' => 'Access Token',
        ];
    }
    public static function findByUsername($username): ?User
    {
        return static::findOne(['username' => $username]);
    }
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): User|IdentityInterface|null
    {
        return self::findOne(['access_token' => $token]);
    }

    public function getId(): int
    {
       return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
