<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;

class UserForm extends Model
{
    public $full_name;
    public $username;
    public $password;
    public $phone_number;
    public $address;
    public $auth_key;
    public $user_role = 'seller';
    public $status = 10;

    public function rules(): array
    {
        return [
            [['full_name', 'username', 'password', 'phone_number'], 'required'],
            [['full_name', 'username', 'password', 'phone_number', 'address'], 'string'],
            [['auth_key'], 'string', 'max' => 32],
            [['user_role'], 'string', 'max' => 255],
            [['username'], 'unique', 'targetClass' => User::class, 'targetAttribute' => ['username' => 'username']],
        ];
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString(16);
    }

    public function generatePasswordHash(): void
    {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
    }

    /**
     * @throws Exception
     */
    public function create(): ?User
    {
        $user = new User();
        $user->username = $this->username;
        $user->full_name = $this->full_name;
        $user->phone_number = $this->phone_number;
        $user->address = $this->address;
        $user->user_role = $this->user_role;
        $this->generateAuthKey();
        $this->generatePasswordHash();
        $user->password = $this->password;
        $user->auth_key = $this->auth_key;
        $user->status = User::STATUS_ACTIVE;
        if ($user->save()) {
            return $user;
        }
        $this->addErrors($user->errors);
        return null;
    }


}