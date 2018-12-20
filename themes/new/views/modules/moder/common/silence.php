<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 11.07.13
 * Time: 15:23
 * To change this template use File | Settings | File Templates.
 *
 * @var ModerLog $model
 * @var UserSilence $Silence
 */
echo 'Модератор '.$model->moder->getFullLogin().' ';
if($model->operation_type == ModerLog::ITEM_OPERATION_SILENCE) {
    echo 'использовал заклятие молчания';

} else
    echo 'снял заклятие молчания';