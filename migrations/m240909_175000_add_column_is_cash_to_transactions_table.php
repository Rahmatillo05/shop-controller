<?php

use yii\db\Migration;

/**
 * Class m240909_175000_add_column_is_cash_to_transactions_table
 */
class m240909_175000_add_column_is_cash_to_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('transactions', 'is_cash', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('transactions', 'is_cash');
    }
}
