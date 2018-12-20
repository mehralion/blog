<?php
class IcecastCommand extends CConsoleCommand
{
	public function run($args)
	{
        $iceStatus = Yii::app()->radio->command('old', 'icestart');
        MyException::logTxt("Запуска крона Icecast. Icecast: {$iceStatus}", 'cron_icecast');

    }
}
