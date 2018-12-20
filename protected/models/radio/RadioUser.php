<?php

Yii::import('application.models._base.BaseRadioUser');

/**
 * Class Radio
 *
 */
class RadioUser extends BaseRadioUser
{
    /**
     * @param string $className
     * @return RadioUser
     */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDjId()
	{
		return $this->dj_id;
	}

	/**
	 * @param int $dj_id
	 *
	 * @return $this
	 */
	public function setDjId($dj_id)
	{
		$this->dj_id = $dj_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRadioType()
	{
		return $this->radio_type;
	}

	/**
	 * @param string $radio_type
	 *
	 * @return $this
	 */
	public function setRadioType($radio_type)
	{
		$this->radio_type = $radio_type;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStartedAt()
	{
		return $this->started_at;
	}

	/**
	 * @param int $started_at
	 *
	 * @return $this
	 */
	public function setStartedAt($started_at)
	{
		$this->started_at = $started_at;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEndedAt()
	{
		return $this->ended_at;
	}

	/**
	 * @param int $ended_at
	 *
	 * @return $this
	 */
	public function setEndedAt($ended_at)
	{
		$this->ended_at = $ended_at;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param string $client
	 *
	 * @return $this
	 */
	public function setClient($client)
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @param string $ip
	 *
	 * @return $this
	 */
	public function setIp($ip)
	{
		$this->ip = $ip;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAgent()
	{
		return $this->agent;
	}

	/**
	 * @param string $agent
	 *
	 * @return $this
	 */
	public function setAgent($agent)
	{
		$this->agent = $agent;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param int $duration
	 *
	 * @return $this
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIpChange()
	{
		return $this->ip_change;
	}

	/**
	 * @param string $ip_change
	 *
	 * @return $this
	 */
	public function setIpChange($ip_change)
	{
		$this->ip_change = $ip_change;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRadioSessionId()
	{
		return $this->radio_session_id;
	}

	/**
	 * @param int $radio_session_id
	 *
	 * @return $this
	 */
	public function setRadioSessionId($radio_session_id)
	{
		$this->radio_session_id = $radio_session_id;
		return $this;
	}
}