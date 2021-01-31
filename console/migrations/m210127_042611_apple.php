<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m210127_042611_apple
 */
class m210127_042611_apple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'state' =>  $this->smallInteger(1) . ' NOT NULL DEFAULT 0',
            'user_id' => $this->integer() . ' NOT NULL',
            'color' => $this->string() . ' NOT NULL',
            'eat_percent' => $this->smallInteger(3) . ' NOT NULL DEFAULT 0',
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_fall' =>$this->timestamp(),
        ]);

        $this->addForeignKey('fk_apple_user', '{{%apple}}', 'user_id', '{{%user}}', 'id');
        $this->createIndex('idx_user_id', '{{%apple}}', 'user_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_042611_apple cannot be reverted.\n";

        return false;
    }
    */
}
