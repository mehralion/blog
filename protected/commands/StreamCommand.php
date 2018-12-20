<?php
class StreamCommand extends CConsoleCommand
{
	public function run($args)
	{
        if(Yii::app()->radio->command('old', 'status') == 'true')
            Yii::app()->radio->command('old', 'update');
        if(Yii::app()->radio->command('rus', 'status') == 'true')
            Yii::app()->radio->command('rus', 'update');
	}
}
