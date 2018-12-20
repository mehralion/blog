<?php if (!empty($friendMenu) && $this->friend): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $friendMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($guestMenu) && $this->guest): ?>
    <nav class="leftMenu">
        <?php $this->widget('application.modules.user.widgets.menu.LoginWidget'); ?>
    </nav>
<?php endif; ?>
<?php if (!empty($userMenu) && $this->user): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $userMenu,
            'activateParents' => true,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($communityMenu) && $this->community): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $communityMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar communityMenu'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($mainMenu) && $this->main): ?>
    <h2 class="title">Главное меню</h2>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'items' => $mainMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($ratingMenu) && $this->rating): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $ratingMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($moderMenu) && $this->moder): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $moderMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($eventMenu) && $this->event): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'items' => $eventMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if (!empty($subscribeMenu) && $this->subscribe): ?>
    <nav class="leftMenu">
        <?php
        $this->widget('ext.menu.Menu', array(
            'encodeLabel' => false,
            'items' => $subscribeMenu,
            'htmlOptions' => array(
                'class' => 'menu sidebar'
            )
        ));
        ?>
    </nav>
<?php endif; ?>
<?php if ($this->advert && ((date('H') == 23 && date('i') < 55) || (date('H') == 0 && date('i') > 8) || (date('H') < 23 && date('H') > 0))): ?>
    <nav class="leftMenu">
        <div class="sidebar" style="background-color: #d6d2b9;padding: 3px;border: 1px solid #c1bead;">
            <div class="" style="background-color: #f0ecd6;">
                <h2 class="title">Реклама</h2>
                <div id="banner_block_1"></div>
                <div id="banner_block_2"></div>
                <div id="banner_block_3"></div>
            </div>
        </div>
        <script type="text/javascript" src="https://blogadv.oldbk.com/api/advert/js?h=141514189554595a074cf4f"></script>
    </nav>
<?php endif; ?>