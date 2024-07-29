<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240727_085100_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string()->notNull(),
            'username' => $this->string()->unique()->notNull(),
            'password' => $this->string()->notNull(),
            'phone_number' => $this->string(),
            'address' => $this->string(),
            'auth_key' => $this->string(),
            'user_role' => $this->string()->defaultValue('client'),
            'status' => $this->smallInteger()->defaultValue(10),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'access_token' => $this->string(),
        ]);
        $this->insert('users', [
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
            'full_name' => 'Admin',
            'phone_number' => '+966',
            'address' => 'Admin',
            'user_role' => 'admin',
            'status' => 10,
            'deleted_at' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%users}}');
    }
}
