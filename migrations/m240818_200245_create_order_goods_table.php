<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_goods}}`.
 */
class m240818_200245_create_order_goods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%order_goods}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer(),
            'amount' => $this->double(),
            'price' => $this->double(),
            'price_sale' => $this->double(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->addForeignKey('fk_order_goods_order_id', 'order_goods', 'order_id', 'orders', 'id', 'CASCADE');
        $this->addForeignKey('fk_order_goods_product_id', 'order_goods', 'product_id', 'products', 'id');
        $this->createIndex('idx_order_goods_order_id', 'order_goods', 'order_id');
        $this->createIndex('idx_order_goods_product_id', 'order_goods', 'product_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_goods_order_id', 'order_goods');
        $this->dropForeignKey('fk_order_goods_product_id', 'order_goods');
        $this->dropIndex('idx_order_goods_order_id', 'order_goods');
        $this->dropIndex('idx_order_goods_product_id', 'order_goods');
        $this->dropTable('{{%order_goods}}');
    }
}
