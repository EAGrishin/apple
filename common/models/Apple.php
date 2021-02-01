<?php


namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

/**
 * Apple model
 *
 * @property integer $id
 * @property integer $state
 * @property integer $user_id
 * @property string $color
 * @property integer $eat_percent
 * @property string $created_at
 * @property string $date_fall
 */

class Apple extends ActiveRecord {

    const STATE_HANG = 0;
    const STATE_FELL = 1;
    const STATE_ROTTEN = 2;


    static public array $state_apple = [
        self::STATE_HANG => 'На дереве',
        self::STATE_FELL => 'Упало с дерева',
        self::STATE_ROTTEN => 'Испортилось',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%apple}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'eat_percent'], 'integer'],
            ['user_id', 'required'],
            ['state', 'default', 'value' => self::STATE_HANG],
            ['state', 'in', 'range' => [self::STATE_HANG, self::STATE_FELL, self::STATE_ROTTEN]],
            [['created_at', 'date_fall'], 'safe'],
        ];
    }


    public static function createRandom() {
        $rand = rand(1, 10);
        for ($i = 1; $i <= $rand; $i++) {
            $hour = rand(1, 48);
            $apple = new self();
            $apple->created_at = date('Y-m-d H:i:s', strtotime(" - {$hour} HOUR"));
            $apple->user_id = Yii::$app->user->id;
            $apple->color = self::randColor();
            $apple->save();
        }
    }

    public static function randColor() {
        $rand = ['32CD32', '98FB98', 'ADFF2F', '7CFC00', '9ACD32', 'FFFF00', 'A52A2A', '800000', 'B22222', 'F0E68C', 'FFD700'];
        return $rand[rand(0, 10)];
    }

    public function eat($percent) {
        if ($this->state != Apple::STATE_FELL) {
            throw new Exception('Яблоко не может быть съедено, так как оно ' . Apple::$state_apple[$this->state]);
        }
        $eating = $this->eat_percent + $percent;
        ($eating > 100) ? $this->eat_percent = 100 : $this->eat_percent = $eating;
        $this->save();
    }

    public function fallToGround() {
        if ($this->state != Apple::STATE_HANG) {
            throw new Exception('Яблоко не на дереве, упасть не может!');
        }
        $this->state = self::STATE_FELL;
        $this->date_fall = new Expression('NOW()');
        $this->save();
    }

    protected function findModel($id) {
        $model = self::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException("Яблока #{$id} не существует.");
        }
        return $model;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}