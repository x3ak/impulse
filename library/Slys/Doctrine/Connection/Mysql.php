<?php

class Slys_Doctrine_Connection_Mysql extends Doctrine_Connection_Mysql
{

	public function getTable($name)
	{
		if (isset($this->tables[$name])) {
			return $this->tables[$name];
		}

		if(strstr($name, '_Mapper_'))
			$class = str_replace('_Mapper_', '_DbTable_', $name);
		else
			$class = sprintf($this->getAttribute(Doctrine_Core::ATTR_TABLE_CLASS_FORMAT), $name);
		
		if (class_exists($class, $this->getAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES)) &&
				in_array('Doctrine_Table', class_parents($class))) {
			$table = new $class($name, $this, true);
		} else {
			$tableClass = $this->getAttribute(Doctrine_Core::ATTR_TABLE_CLASS);
			$table = new $tableClass($name, $this, true);
		}

		return $table;
	}
}