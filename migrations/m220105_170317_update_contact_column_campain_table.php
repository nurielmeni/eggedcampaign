<?php

use yii\db\Migration;

/**
 * Class m220105_170317_update_contact_column_campain_table
 */
class m220105_170317_update_contact_column_campain_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('campain', 'contact', $this->string(1024));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220105_170317_update_contact_column_campain_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220105_170317_update_contact_column_campain_table cannot be reverted.\n";

        return false;
    }
    */
}
