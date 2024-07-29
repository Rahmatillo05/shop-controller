<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customers}}`.
 */
class m240729_101441_create_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customers}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(),
            'phone_number' => $this->string(),
            'address' => $this->string(),
            'status' => $this->integer(),
            'user_id' => $this->integer(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('idx-customers-user_id', '{{%customers}}', 'user_id');
        $this->addForeignKey('fk-customers-user_id', '{{%customers}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-customers-user_id', '{{%customers}}');
        $this->dropIndex('idx-customers-user_id', '{{%customers}}');
        $this->dropTable('{{%customers}}');
    }
}
