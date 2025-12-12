<?php

use yii\db\Migration;

class m251210_063520_register_users extends Migration
{
    private $users = [
        [
            'id' => 3,
            'name' => 'kihara.nobuo',
            'dispname' => '木原伸夫',
            'password' => 'isa563rigami',
        ],
        [
            'id' => 4,
            'name' => 'kihara.hitoshi',
            'dispname' => '木原均',
        ],
        [
            'id' => 5,
            'name' => 'kihara.hironori',
            'dispname' => '木原浩則',
        ],
        [
            'id' => 6,
            'name' => 'yasuda.toshio',
            'dispname' => '安田利雄',
        ],
        [
            'id' => 7,
            'name' => 'kihara.tokuyoshi',
            'dispname' => '木原徳吉',
        ],
        [
            'id' => 8,
            'name' => 'kanzaki.takanori',
            'dispname' => '神崎貴則',
        ],
        [
            'id' => 9,
            'name' => 'yasuda.shuuzou',
            'dispname' => '安田修三',
        ],
        [
            'id' => 10,
            'name' => 'hosokawa.aiichirou',
            'dispname' => '細川愛一郎',
        ],
        [
            'id' => 11,
            'name' => 'murata.yuuki',
            'dispname' => '村田裕樹',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->users as $user) {
            $tokens = explode( '.', $user['name']);
            $password = (isset($user['password'])) ? $user['password'] : $tokens[1] . '()' . $tokens[0];
            $this->insert('{{%user}}', [
                'id' => $user['id'],
                'username' => $user['name'],
                'dispname' => $user['dispname'],
                'password_hash' => Yii::$app->security->generatePasswordHash($password),
                'auth_key' => Yii::$app->security->generateRandomString(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach($this->users as $user) {
            $this->delete('{{%user}}', ['id' => $user['id']]);
        }
    }
}
