<?php

use yii\db\Migration;

/**
 * Class m241201_152452_add_column_hidden_to_settings_table
 */
class m241201_152452_add_column_hidden_to_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('settings', 'hidden', $this->tinyInteger()->defaultValue(0)->after('value'));
        $this->insert('{{%settings}}', [
            'title'=> "To'lov",
            'value' => "on",
            'type' => 2,
            'key' => 'subscription',
            'hidden' => 1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('settings', 'hidden');
    }
}
