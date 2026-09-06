<?php
// SPDX-License-Identifier: AGPL-3.0-only
// Isolated component harness: real Yii/SQLite, small HumHub boundary doubles.
// It is not a replacement for a complete HumHub/MySQL integration test.
namespace {
    $yii = getenv('YII_FRAMEWORK');
    if (!$yii || !is_file($yii . '/Yii.php')) {
        fwrite(STDERR, "Set YII_FRAMEWORK to a Yii 2.0.55 framework directory.\n"); exit(2);
    }
    require $yii . '/Yii.php';
    \Yii::setAlias('@humhub/modules/sociocraticGovernance', dirname(__DIR__));
}
namespace humhub\modules\space\models {
    class Space extends \yii\db\ActiveRecord {
        public const VISIBILITY_NONE = 0;
        public static array $members = [];
        public static array $disabled = [];
        public static array $blocked = [];
        public static array $archived = [];
        public static function tableName() { return '{{%space}}'; }
        public function isMember($id = null) { return in_array((int) ($id ?? \Yii::$app->user->id), self::$members[$this->id] ?? [], true); }
        public function isArchived() { return in_array((int) $this->id, self::$archived, true); }
        public function isBlockedForUser() { return in_array((int) $this->id, self::$blocked, true); }
        public function getModuleManager() {
            return new class($this->id) {
                public function __construct(private $id) {}
                public function isEnabled($module) { return !in_array((int) $this->id, Space::$disabled, true); }
            };
        }
        public function getMemberListService() {
            return new class($this->id) {
                public function __construct(private $id) {}
                public function getQuery() { return \humhub\modules\user\models\User::find()->where(['id' => Space::$members[$this->id] ?? [], 'status' => 1]); }
            };
        }
        public function createUrl($route) { return '/index.php?r=' . urlencode($route) . '&cguid=' . $this->id; }
    }
}
namespace humhub\modules\user\models {
    class User extends \yii\db\ActiveRecord {
        public const STATUS_ENABLED = 1;
        public static function tableName() { return '{{%user}}'; }
        public function getDisplayName() { return $this->name; }
        public function getUrl() { return '/user/' . $this->id; }
    }
}
namespace humhub\components {
    class Widget extends \yii\base\Widget {}
}
namespace {
    class TestIdentity extends \yii\base\Component {
        public $id = 1;
        public $isGuest = false;
        public $admin = false;
        public function isAdmin() { return $this->admin; }
    }
    new \yii\console\Application([
        'id' => 'governance-tests', 'basePath' => __DIR__,
        'components' => [
            'db' => ['class' => \yii\db\Connection::class, 'dsn' => 'sqlite::memory:', 'tablePrefix' => 'test_'],
            'user' => new TestIdentity(),
        ],
    ]);
    $db = \Yii::$app->db;
    $db->open();
    $db->createCommand('PRAGMA foreign_keys=ON')->execute();
    $db->createCommand()->createTable('{{%space}}', ['id' => 'pk', 'name' => 'string', 'visibility' => 'integer'])->execute();
    $db->createCommand()->createTable('{{%user}}', ['id' => 'pk', 'name' => 'string', 'status' => 'integer'])->execute();
    require dirname(__DIR__) . '/migrations/m260906_120000_initial.php';
    ob_start();
    (new \m260906_120000_initial())->up();
    ob_end_clean();
    foreach ([1 => 'Kern', 2 => 'Technik', 3 => 'Privater Kreis'] as $id => $name) {
        $db->createCommand()->insert('{{%space}}', ['id' => $id, 'name' => $name, 'visibility' => $id === 3 ? 0 : 1])->execute();
    }
    foreach ([1 => 'Alex', 2 => 'Robin', 3 => 'Sam'] as $id => $name) {
        $db->createCommand()->insert('{{%user}}', ['id' => $id, 'name' => $name, 'status' => 1])->execute();
    }
    \humhub\modules\space\models\Space::$members = [1 => [1, 2], 2 => [1, 2], 3 => [2]];
}
