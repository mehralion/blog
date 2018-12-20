<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 21.11.13
 * Time: 21:45
 *
 * @var Poll $model
 * @var PollAnswer[] $results
 */
?>
<nav class="leftMenu" style="margin-top: 5px;">
    <div class="sidebar" style="background-color: #d6d2b9;padding: 3px;border: 1px solid #c1bead;">
        <div class="" style="background-color: #f0ecd6;">
            <h2 class="title">Статистика (для админов)</h2>
            <div class="stats" style="padding: 10px;">
                <div style="margin-bottom: 5px;">Гостей: <?= $guests ?></div>
                <div style="margin-bottom: 5px;">Пользователей онлайн: <?= $count; ?></div>
                <?php foreach($users as $key => $user): ?>
                    <div>
                        <?= $user['login'] ?>
                        <?php if(Yii::app()->user->isAdmin()): ?>
                            <i title="<?= $user['ip_address'] ?>" rel="tooltip" class="icon blog2"></i>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>