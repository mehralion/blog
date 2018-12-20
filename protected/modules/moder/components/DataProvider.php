<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 11.07.13
 * Time: 19:45
 * To change this template use File | Settings | File Templates.
 *
 * @package application.moder.components
 */
class DataProvider extends CActiveDataProvider
{
    /**
     * @return array|CActiveRecord[]
     */
    protected function fetchData()
    {
        $criteria=clone $this->getCriteria();
        $criteria->alias = 't';
        if(($pagination=$this->getPagination())!==false)
        {
            $pagination->setItemCount($this->getTotalItemCount());
            $pagination->applyLimit($criteria);
        }

        $baseCriteria=$this->model->getDbCriteria(false);

        if(($sort=$this->getSort())!==false)
        {
            // set model criteria so that CSort can use its table alias setting
            if($baseCriteria!==null)
            {
                $c=clone $baseCriteria;
                $c->mergeWith($criteria);
                $this->model->setDbCriteria($c);
            }
            else
                $this->model->setDbCriteria($criteria);
            $sort->applyOrder($criteria);
        }

        $this->model->setDbCriteria($baseCriteria!==null ? clone $baseCriteria : null);

        $sql = "select {$criteria->select}
                from (select {$criteria->select} from {$this->model->getTableSchema()->name} order by create_datetime desc) as `t` ";
        if($criteria->condition)
            $sql .= ' where '.$criteria->condition;
        if($criteria->group)
            $sql .= ' group by '.$criteria->group;
        if($criteria->order)
            $sql .= ' order by '.$criteria->order;
        if($criteria->limit) {
            $sql .= ' limit '.$criteria->limit;
            if($criteria->offset)
                $sql .= ', offset '.$criteria->offset;
        }


        $data = $this->model->findAllBySql($sql, $criteria->params);

        $this->model->setDbCriteria($baseCriteria);  // restore original criteria
        return $data;
    }
}