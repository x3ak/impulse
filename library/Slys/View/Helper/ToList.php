<?php

/**
 * SlyS Framework
 *
 * @category   SlyS
 * @package    Slys_View
 * @subpackage Helper
 * @version    $Id: Tree.php 1003 2011-01-11 10:43:38Z deeper $
 */
class Slys_View_Helper_ToList extends Zend_View_Helper_Abstract
{

    /**
     * Convert data into select list
     * @param array $data
     * @param string $valueIndex
     * @param string $textIndex
     */
    public function toList(array $data, $valueIndex, $labelIndex)
    {
        $result = array();
        if(!empty($valueIndex) && !empty($labelIndex)) {
            foreach($data as $item) {
                if(!empty($item[$valueIndex]) && !empty($item[$labelIndex])) {
                    $result[$item[$valueIndex]] = $item[$labelIndex];
                }
            }
        }

        return $result;
    }

}