<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%color}}`.
 */
class m190416_103759_create_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $tableNameColor = '{{%color}}';
        $this->createTable($tableNameColor, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'title' => $this->string(),
        ], $tableOptions);
        $this->createIndex('name', $tableNameColor, 'name', true);

        //Заполняем базовый набор цветов
        $colors = [
            ['name' => 'red', 'title' => 'Красное'],
            ['name' => 'green', 'title' => 'Зеленое'],
            ['name' => 'yellow', 'title' => 'Желтое'],
            ['name' => 'orange', 'title' => 'Оранжевое'],
            ['name' => 'white', 'title' => 'Белое'],
        ];
        foreach ($colors as $row) {
            $this->insert($tableNameColor, $row);
        }

        $tableNameApple = '{{%apple}}';
        $this->addColumn($tableNameApple, 'color_id', $this->integer()->notNull()->after('id'));
        $this->createIndex('color_id', $tableNameApple, 'color_id');
        $this->addForeignKey('apple_ibfk_color', $tableNameApple, 'color_id', $tableNameColor, 'id', 'RESTRICT', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('apple_ibfk_color', '{{%apple}}');
        $this->dropColumn('{{%apple}}', 'color_id');
        $this->dropTable('{{%color}}');
    }
}
