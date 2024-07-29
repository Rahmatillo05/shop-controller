<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_histories}}`.
 */
class m240729_110700_create_product_histories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_histories}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'product_list_id' => $this->integer(),
            'price' => $this->double(),
            'sale_price' => $this->double(),
            'amount' => $this->double(),
            'status' => $this->integer()->defaultValue(0),
            'type' => $this->integer()->defaultValue(1),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex(
            'idx-product_histories-product_id',
            '{{%product_histories}}',
            'product_id'
        );

        $this->addForeignKey(
            'fk-product_histories-product_id',
            '{{%product_histories}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-product_histories-product_list_id',
            '{{%product_histories}}',
            'product_list_id'
        );

        $this->addForeignKey(
            'fk-product_histories-product_list_id',
            '{{%product_histories}}',
            'product_list_id',
            '{{%product_lists}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_histories-product_id', '{{%product_histories}}');
        $this->dropIndex('idx-product_histories-product_id', '{{%product_histories}}');
        $this->dropForeignKey('fk-product_histories-product_list_id', '{{%product_histories}}');
        $this->dropIndex('idx-product_histories-product_list_id', '{{%product_histories}}');
        $this->dropTable('{{%product_histories}}');
    }
}
