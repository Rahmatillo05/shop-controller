<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categories}}`.
 */
class m240728_181558_create_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex(
            '{{%idx-categories-unit_id}}',
            '{{%categories}}',
            '{{%unit_id}}'
        );
        $this->addForeignKey('{{%fk-categories-unit_id}}', '{{%categories}}', 'unit_id', '{{%units}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-categories-unit_id}}', '{{%categories}}');
        $this->dropIndex('{{%idx-categories-unit_id}}', '{{%categories}}');
        $this->dropTable('{{%categories}}');
    }
}
