<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }


    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function login(): bool|User
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if(is_null($user->access_token)){
                $token = Yii::$app->security->generateRandomString();
                $user->access_token = $token;
            }
            if (!$user->save()){
                Yii::$app->response->statusCode = 422;
                return false;
            }
            return $user;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
