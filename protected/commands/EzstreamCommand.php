<?php
class EzstreamCommand extends CConsoleCommand
{
	public function run($args)
	{
        $oldStatus = Yii::app()->radio->command('old', 'status');
        $rusStatus = Yii::app()->radio->command('rus', 'status');

        if($oldStatus == "false") {
            Yii::app()->radio->command('old', 'start');
            MyException::logTxt("Запуска крона Ezstream. Start OLD", 'cron_ezstream');
        }

        if($rusStatus == "false") {
            Yii::app()->radio->command('rus', 'start');
            MyException::logTxt("Запуска крона Ezstream. Start RUS", 'cron_ezstream');
        }

        MyException::logTxt("Запуска крона Ezstream. Old: {$oldStatus}, Rus: {$rusStatus}", 'cron_ezstream');

    }
}
