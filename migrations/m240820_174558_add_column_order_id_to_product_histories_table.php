<?php

use yii\db\Migration;

/**
 * Class m240820_174558_add_column_order_id_to_product_histories_table
 */
class m240820_174558_add_column_order_id_to_product_histories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('product_histories', 'order_id', $this->integer());
        $this->addForeignKey('fk_product_histories_order_id', 'product_histories', 'order_id', 'orders', 'id', 'CASCADE');
        $this->createIndex('idx_product_histories_order_id', 'product_histories', 'order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_product_histories_order_id', 'product_histories');
        $this->dropIndex('idx_product_histories_order_id', 'product_histories');
        $this->dropColumn('product_histories', 'order_id');
    }
}
