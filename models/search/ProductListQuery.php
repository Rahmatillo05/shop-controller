<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductList;

/**
 * ProductListQuery represents the model behind the search form of `app\models\ProductList`.
 */
class ProductListQuery extends ProductList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'date', 'customer_id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'safe'],
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
        $query = ProductList::find();

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
            'status' => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'comment', $this->comment]);

        return $dataProvider;
    }
}
