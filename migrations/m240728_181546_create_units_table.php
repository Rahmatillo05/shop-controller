<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%units}}`.
 */
class m240728_181546_create_units_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%units}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'value_type' => $this->smallInteger(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%units}}');
    }
}
