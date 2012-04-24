<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_Mapper_Visit extends Members_Model_Mapper_BaseVisit
{
    /**
     * Return true if visit was today
     * @return bool
     */
    public function isToday()
    {
        return date('Y-m-d') == date('Y-m-d', strtotime($this->day));
    }

}

