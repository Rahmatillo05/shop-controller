<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m240728_184458_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'barcode' => $this->string(),
            'price' => $this->double(),
            'sale_price' => $this->double(),
            'sale_price_min' => $this->double(),
            'min_amount' => $this->double(),
            'category_id' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(1),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex('idx_products_category_id', '{{%products}}', 'category_id');
        $this->createIndex('idx_products_barcode', '{{%products}}', 'barcode');
        $this->addForeignKey('fk-to-category_id', '{{%products}}', 'category_id', '{{%categories}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_products_category_id', '{{%products}}');
        $this->dropForeignKey('fk-to-category_id', '{{%products}}');
        $this->dropTable('{{%products}}');
    }
}
