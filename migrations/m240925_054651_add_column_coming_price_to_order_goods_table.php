<?php

use yii\db\Migration;

/**
 * Class m240925_054651_add_column_coming_price_to_order_goods_table
 */
class m240925_054651_add_column_coming_price_to_order_goods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_goods', 'coming_price', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order_goods', 'coming_price');
    }
}
