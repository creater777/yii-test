<?php

use yii\db\Migration;

/**
 * Class m200221_132815_add_apple_table
 */
class m200221_132815_add_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull(),
            'fall_at' => $this->integer()->notNull()->defaultValue(0),
            'state' => $this->smallInteger()->notNull()->defaultValue(0),
            'color' => $this->string(32)->notNull(),
            'integrity' => $this->smallInteger()->notNull()->defaultValue(100),
            'index' => $this->smallInteger()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
