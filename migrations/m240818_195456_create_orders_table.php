<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m240818_195456_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'payment_type' => $this->string()->notNull(),
            'customer_id' => $this->integer(),
            'accepted_at' => $this->integer(),
            'comment' => $this->text(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->addForeignKey('fk_orders_user_id', 'orders', 'user_id', 'users', 'id');
        $this->addForeignKey('fk_orders_customer_id', 'orders', 'customer_id', 'users', 'id');
        $this->createIndex('idx_orders_customer_id', 'orders', 'customer_id');
        $this->createIndex('idx_orders_user_id', 'orders', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_orders_user_id', 'orders');
        $this->dropForeignKey('fk_orders_customer_id', 'orders');
        $this->dropIndex('idx_orders_customer_id', 'orders');
        $this->dropIndex('idx_orders_user_id', 'orders');
        $this->dropTable('{{%orders}}');
    }
}
