<?php

use yii\db\Migration;

/**
 * Class m240907_092214_add_column_relation_id_to_transactions_table
 */
class m240907_092214_add_column_relation_id_to_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('transactions', 'relation_id', $this->integer());
        $this->addColumn('transactions', 'transaction_date', $this->integer());

        $this->createIndex('idx-relation_id-transactions', 'transactions', 'relation_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropIndex('idx-relation_id-transactions', 'transactions');

        $this->dropColumn('transactions', 'relation_id');
        $this->dropColumn('transactions', 'transaction_date');
    }
}
