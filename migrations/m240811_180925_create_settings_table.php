<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m240811_180925_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'title'=> $this->text(),
            'value' => $this->string(),
            'type' => $this->integer(),
            'key' => $this->string(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->insert('{{%settings}}', [
            'title'=> 'Plastik foizi',
            'value' => '1.6',
            'type' => 2,
            'key' => 'card_percentage'
        ]);
        $this->insert('{{%settings}}', [
            'title'=> "To'lov",
            'value' => "on",
            'type' => 2,
            'key' => 'subscription'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
