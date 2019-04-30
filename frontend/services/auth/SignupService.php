<?php

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;
use RuntimeException;
use Yii;

class SignupService
{
    public function signup(SignupForm $form)
    {
        $user = User::signup(
            $form->username,
            $form->email,
            $form->password
        );

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}