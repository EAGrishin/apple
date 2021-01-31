<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class TempController extends Controller
{
    public function actionCreateRootUser() {
        $username = 'root';
        $password = '1234';
        if (!User::findByUsername($username)) {
            $user = new User();
            $user->username = $username;
            $user->email = $username . '@mail.ru';
            $user->setPassword($password);
            $user->generateAuthKey();
            $user->save();
        }
    }
}