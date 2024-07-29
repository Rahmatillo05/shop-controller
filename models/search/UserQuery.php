<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserQuery represents the model behind the search form of `app\models\User`.
 */
class UserQuery extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'username', 'password', 'phone_number', 'address', 'auth_key', 'user_role', 'access_token'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'full_name', $this->full_name])
            ->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'phone_number', $this->phone_number])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['ilike', 'auth_key', $this->auth_key])
            ->andFilterWhere(['ilike', 'user_role', $this->user_role])
            ->andFilterWhere(['ilike', 'access_token', $this->access_token]);

        return $dataProvider;
    }
}
