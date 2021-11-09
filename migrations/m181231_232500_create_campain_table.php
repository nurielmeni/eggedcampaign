<?php

use yii\db\Migration;

/**
 * Handles the creation of table `campain`.
 */
class m181231_232500_create_campain_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('campain', [
            'id' => $this->primaryKey(),
            'fbf' => $this->integer(1),
            'name' => $this->string(64)->notNull(),
            'start_date' => $this->integer(),
            'end_date' => $this->integer(),
            'campain' => $this->string(1024),
            'image' => $this->string(256),
            'logo' => $this->string(256),
            'sid' => $this->string(64),
            'show_licanse' => $this->integer(1),
            'show_cv' => $this->integer(1),
            'button_color' => $this->string(16),
            'contact' => $this->string(30),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('campain');
    }
}
