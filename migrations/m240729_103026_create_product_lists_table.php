<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_lists}}`.
 */
class m240729_103026_create_product_lists_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_lists}}', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(),
            'customer_id' => $this->integer(),
            'comment' => $this->text(),
            'status' => $this->integer()->defaultValue(0),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex('idx-product_lists-customer_id', '{{%product_lists}}', 'customer_id');
        $this->addForeignKey('fk-product_lists-customer_id', '{{%product_lists}}', 'customer_id', '{{%customers}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_lists-customer_id', '{{%product_lists}}');
        $this->dropIndex('idx-product_lists-customer_id', '{{%product_lists}}');
        $this->dropTable('{{%product_lists}}');
    }
}
