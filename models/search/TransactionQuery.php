<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transaction;

/**
 * TransactionQuery represents the model behind the search form of `app\models\Transaction`.
 */
class TransactionQuery extends Transaction
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'date', 'customer_id', 'type', 'payment_type', 'status', 'model_id', 'deleted_at', 'created_at', 'updated_at', 'is_cash'], 'integer'],
            [['amount'], 'number'],
            [['comment', 'model_class'], 'safe'],
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
        $query = Transaction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
            'customer_id' => $this->customer_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'payment_type' => $this->payment_type,
            'status' => $this->status,
            'model_id' => $this->model_id,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'comment', $this->comment])
            ->andFilterWhere(['ilike', 'model_class', $this->model_class]);

        return $dataProvider;
    }
}
