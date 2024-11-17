<?php

namespace app\models;

use app\models\search\CategoryQuery;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string $barcode
 * @property string|null $description
 * @property float|null $price
 * @property float|null $sale_price
 * @property float|null $sale_price_min
 * @property float|null $min_amount
 * @property float|null $remind
 * @property int $category_id
 * @property int|null $status
 * @property int|null $deleted_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Category $category
 * @property Unit $unit
 */
class Product extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'category_id', 'price', 'sale_price'], 'required'],
            [['description', 'barcode'], 'string'],
            [['price', 'sale_price', 'sale_price_min', 'min_amount'], 'number'],
            [['category_id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['category_id', 'status', 'deleted_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'sale_price_min' => 'Sale Price Min',
            'category_id' => 'Category ID',
            'status' => 'Status',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'remind' => function ($model) {
                return $model->getRemind();
            }
        ]);
    }

    public function extraFields(): array
    {
        return ['category', 'unit'];
    }

    public static function findByBarcode($barcode): ?Product
    {
        $barcode = (string)$barcode;
        return self::findOne(['barcode' => $barcode]);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery|CategoryQuery
     */
    public function getCategory(): search\CategoryQuery|ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getUnit(): ActiveQuery
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id'])
            ->via('category');
    }

    public function getProductHistory(): ActiveQuery
    {
        return $this->hasMany(ProductHistory::class, ['product_id' => 'id'])
            ->andWhere(['status' => ProductHistory::STATUS_ACTIVE]);
    }

    public function getRemind(): float
    {
        $income = $this->getProductHistory()
            ->andWhere(['type' => ProductHistory::TYPE_INCOME])->sum('amount');
        $outgoing = $this->getProductHistory()
            ->andWhere(['type' => ProductHistory::TYPE_OUTCOME])->sum('amount');
        return round($income - $outgoing, 2);
    }

    /**
     * @throws Exception
     */
    public function addAmount(float $amount, $productListId = null, $status = ProductHistory::STATUS_ACTIVE): bool
    {
        $history = new ProductHistory();
        $history->type = ProductHistory::TYPE_INCOME;
        $history->product_id = $this->id;
        $history->amount = $amount;
        $history->price = $this->price;
        $history->sale_price = $this->sale_price;
        $history->status = $status;
        $history->product_list_id = $productListId;
        if ($history->save()) {
            return true;
        }
        throw new \DomainException("Mahsulot qoldig'í saqlanmadi");
    }
}
