<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 21.11.12
 * Time: 15:19
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base.ajax
 */
class RadioInfo extends CApplicationComponent
{
    public $host = 'blog.oldbk.com';
    public $port = '8000';

    public $login = 'admin';
    public $password = 'Gnngzwctk013RKieTZiG';

    private $_vars = array();

    public function init()
    {
        $str = "http://{$this->host}:{$this->port}/info.xsl?".time();
        $curl = Yii::app()->curl;
        $data = $curl->run($str);
        if($data === false || ($data = CJSON::decode($data)) == null)
            return false;

        foreach($data as $stream => $values)
            $this->_vars[trim($stream, '/')] = new RadioContainer($values);
    }

    public function __get($name)
    {
        if(isset($this->_vars[$name]))
            return $this->_vars[$name];
    }

    //http://chat.oldbk.com/pingdj.php?login=%C1%E0%E9%F2&key=4654272uw4atsr537q43&alias=asdasdasd&aa
    private $_hash = '4654272uw4atsr537q43';
    public function sendLink($login, $alias)
    {
        $login = @iconv('UTF-8', 'CP1251', $login);

        $curl = Yii::app()->curl;
        $curl->run('https://oldbk.com');
        $curl->run("http://chat.oldbk.com/pingdj.php?login={$login}&key={$this->_hash}&alias={$alias}");
    }

    //http://192.168.1.123:8000/admin/killsource.xsl?mount=/oldfm_default
    public function streamOff($radio_type)
    {
        $name = Radio::getRadioName($radio_type);
        if(!$this->{$name} === null)
            return;

        $curl = Yii::app()->curl;
        $r = $curl->run("http://{$this->getPostData()}@{$this->getHostString()}/admin/killsource.xsl?mount=/{$name}");

        ob_start();
        var_dump($r);
        $result = ob_get_clean();

        $Log = new \LogRadio();
        $Log->log_level = \Log::LEVEL_0;
        $Log->description1 = "Выключили стрим.";
        $Log->description2 = "Результат: {$result}";
        $Log->description3 = "http://{$this->getPostData()}@{$this->getHostString()}/admin/killsource.xsl?mount=/{$name}";
        $Log->custom_id = $radio_type;
        $Log->create_datetime = date('Y-m-d H:i:s', time());
        $Log->save();
    }

    public function getListeners($radio_type)
    {
        $name = Radio::getRadioName($radio_type);
        if(!$this->{$name} === null)
            return [];

        $users = [];

        $curl = Yii::app()->curl;
        $response = $curl->run("http://{$this->getPostData()}@{$this->getHostString()}/admin/listclients.xsl?mount=/{$name}");
        $phpQuery = \phpQuery::newDocumentHTML($response);
        $trList = $phpQuery->find('table.colortable tr');
        foreach ($trList as $tr) {
            $tr = \phpQuery::pq($tr);

            if(trim($tr->find('td')->eq(3)->text()) != 'Kick') {
                continue;
            }
            $users[] = [
                'ip' => trim($tr->find('td')->eq(0)->text()),
                'duration' => trim($tr->find('td')->eq(1)->text()),
                'user-gent' => trim($tr->find('td')->eq(2)->text()),
            ];
        }

        return $users;
    }

    public function getRusTitle()
    {
        $rusName = Radio::getRadioName(Radio::RADIO_TYPE_RUSFM);
        $rusNameD = Radio::getRadioName(Radio::RADIO_TYPE_RUSFM_D);
        if($this->{$rusName} !== null)
            return $this->{$rusName}->title;
        elseif($this->{$rusNameD} !== null)
            return $this->{$rusNameD}->title;
        else
            return null;
    }

    public function getRusInfo()
    {
        $rusName = Radio::getRadioName(Radio::RADIO_TYPE_RUSFM);
        if($this->{$rusName} !== null)
            return $this->{$rusName};
        else
            return false;
    }

    //listeners
    public function getRusListeners()
    {
        $rusName = Radio::getRadioName(Radio::RADIO_TYPE_RUSFM);
        $rusNameD = Radio::getRadioName(Radio::RADIO_TYPE_RUSFM_D);
        if($this->{$rusName} !== null)
            return $this->{$rusName}->listeners;
        elseif($this->{$rusNameD} !== null)
            return $this->{$rusNameD}->listeners;
        else
            return 0;
    }

    public function getOldTitle()
    {
        $oldName = Radio::getRadioName(Radio::RADIO_TYPE_OLDFM);
        $oldNameD = Radio::getRadioName(Radio::RADIO_TYPE_OLDFM_D);
        if($this->{$oldName} !== null)
            return $this->{$oldName}->title;
        elseif($this->{$oldNameD} !== null)
            return $this->{$oldNameD}->title;
        else
            return null;
    }

    public function getOldInfo()
    {
        $oldName = Radio::getRadioName(Radio::RADIO_TYPE_OLDFM);
        if($this->{$oldName} !== null)
            return $this->{$oldName};
        else
            return false;
    }

    //listeners
    public function getOldListeners()
    {
        $oldName = Radio::getRadioName(Radio::RADIO_TYPE_OLDFM);
        $oldNameD = Radio::getRadioName(Radio::RADIO_TYPE_OLDFM_D);
        if($this->{$oldName} !== null)
            return $this->{$oldName}->listeners;
        elseif($this->{$oldNameD} !== null)
            return $this->{$oldNameD}->listeners;
        else
            return 0;
    }

    public function command($radio, $command)
    {
        return trim(shell_exec('/www/default.sh '.$radio.' '.$command));
    }

    private function getHostString()
    {
        return $this->host.':'.$this->port;
    }

    private function getPostData()
    {
        return $this->login.':'.$this->password;
    }

}
