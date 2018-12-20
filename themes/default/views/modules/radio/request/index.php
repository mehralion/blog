<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 13:37
 *
 * @var Radio $oldOnline
 * @var Radio $rusOnline
 * @var Radio[] $rusfm
 * @var Radio[] $oldfm
 */ ?>
<style>
    .efir ul {
        list-style: none;
    }
    .community_block {
        vertical-align: top;
    }
    .dark_block p {
        text-indent: 30px;
    }
</style>
<div class="dark_block" style="width: 550px;margin: 0 auto;margin-bottom: 10px;padding-bottom:5px;text-align: center;">
    <p>
    <b>Радиостанции ОлдБК для вас!</b><br><br>

    Слушайте нас в прямом эфире, как внутри игры, так и вне игры!<br>
    Напишите в приват диджею и закажите трек или передайте привет друзьям и любимым!
    </p>
</div>
<article class="community_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>RusFM
    </h3>
    <div class="content efir">
        <div class="dark_block">
            <p>
                Русская музыка различных направлений - рок, поп, танцевальная. Различные времена и стили -  от современных композиций до ностальжи и русской классики 70-80х годов.
            </p>
        </div>
        <div class="dark_block" style="text-align: center;">
            <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/icons/player/winamp.png'); ?>
            <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/icons/player/aimp.png'); ?>
            <?php echo CHtml::link('Прослушать в плеере ', Yii::app()->baseUrl.'/radio/rus.pls'); ?>
            <div class="clear" style="margin-bottom: 5px;"></div>
            <?php echo CHtml::link('Прослушать в онлайнe', Yii::app()->createAbsoluteUrl('/radio/request/rusview'), array('class' => 'radioWin', 'data-name' => 'RusFM')); ?>
        </div>
        <div class="efir">
            В эфире:
            <ul class="">
                <?php if($rusOnline === null): ?>
                    <li>Играет плейлист</li>
                <?php else: ?>
                    <li><?php echo $rusOnline->user->getFullLogin(true).' '.User::buildIcq($rusOnline->user->icq).
                        ' '.User::buildSkype($rusOnline->user->skype); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="description">
            RDJ:
            <ul>
                <?php foreach($rusfm as $user): ?>
                    <li><?php echo $user->user->getFullLogin(true).' '.User::buildIcq($user->user->icq).
                            ' '.User::buildSkype($user->user->skype); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="info"></div>
</article>

<article class="community_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>OldFM
    </h3>
    <div class="content efir">
        <div class="dark_block">
            <p>
                Разнообразие стилей и направлений: главным образом качественная поп- и рок-музыка, а так же электронная музыка, джаз, босанова, соул, регги и т.д. От музыки 60-х гг. до последних новинок
            </p>
        </div>
        <div class="dark_block" style="text-align: center;">
            <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/icons/player/winamp.png'); ?>
            <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/icons/player/aimp.png'); ?>
            <?php echo CHtml::link('Прослушать в плеере ', Yii::app()->baseUrl.'/radio/old.pls'); ?>
            <div class="clear" style="margin-bottom: 5px;"></div>
            <?php echo CHtml::link('Прослушать в онлайнe', Yii::app()->createAbsoluteUrl('/radio/request/oldview'), array('class' => 'radioWin', 'data-name' => 'OldFM')); ?>
        </div>
        <div class="efir">
            В эфире:
            <ul class="">
                <?php if($oldOnline === null): ?>
                    <li>Играет плейлист</li>
                <?php else: ?>
                    <li><?php echo $oldOnline->user->getFullLogin(true).' '.User::buildIcq($oldOnline->user->icq).
                            ' '.User::buildSkype($oldOnline->user->skype); ?></li>
                <?php endif; ?>
            </ul>

        </div>
        <div class="description">
            RDJ:
            <ul>
                <?php foreach($oldfm as $user): ?>
                    <li><?php echo $user->user->getFullLogin(true).' '.User::buildIcq($user->user->icq).
                            ' '.User::buildSkype($user->user->skype); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="info"></div>
</article>
<script>
    $(function(){
        $(document.body).on('click', '.radioWin', function(event){
            event.preventDefault();
            window.open($(this).attr('href'), $(this).attr('data-name'), "status=1,width=410,height=140,scrollbars=0,resizable=0");
        });
    });
</script>