<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m240729_122404_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(),
            'customer_id' => $this->integer(),
            'type' => $this->integer(),
            'amount' => $this->double(),
            'payment_type' => $this->integer(),
            'status' => $this->integer(),
            'comment' => $this->text(),
            'model_id' => $this->integer(),
            'model_class' => $this->string(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex('idx-transactions-customer_id', '{{%transactions}}', 'customer_id');
        $this->addForeignKey('fk-transactions-customer_id', '{{%transactions}}', 'customer_id', '{{%customers}}', 'id', 'CASCADE', 'RESTRICT');
        $this->createIndex('idx-transactions-model_id', '{{%transactions}}', 'model_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-transactions-customer_id', '{{%transactions}}');
        $this->dropIndex('idx-transactions-customer_id', '{{%transactions}}');
        $this->dropIndex('idx-transactions-model_id', '{{%transactions}}');
        $this->dropTable('{{%transactions}}');
    }
}
