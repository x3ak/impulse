<?php

/**
 * Slys
 *
 * @version    $Id: Layouts.php 1134 2011-01-28 14:31:15Z deeper $
 */
class Templater_Model_Layouts
{

    /**
     * Return all layouts collection
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return Doctrine_Query::create()
            ->select('lay.*')
            ->from('Templater_Model_Mapper_Layout lay')
            ->execute();
    }

    /**
     * Return single layout by id
     * @param int $id
     * @return Templater_Model_Mapper_Layout
     */
    public function getLayout($id, $forEdit = false)
    {
        if(empty($id))
            return new Templater_Model_Mapper_Layout();

        $layout = Templater_Model_DbTable_Layout::getInstance()->getLayoutWithLayoutPoints($id);

        if(empty($layout))
            return new Templater_Model_Mapper_Layout();
        else
            return $layout;
    }

    /**
     * Return layouts list pager
     *
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getLayoutsPager($page = 1, $maxPerPage = 20, array $where = array())
    {
        return Templater_Model_DbTable_Layout::getInstance()
            ->getPager($page, $maxPerPage, $where);
    }

    /**
     * Return list of layouts found in Theme directory
     * and save it if not found in DB
     *
     * @param Templater_Model_Mapper_Theme $theme
     * @return array
     */
    public function importFromTheme(Templater_Model_Mapper_Theme $theme, $import = false)
    {
        $options = Zend_Controller_Front::getInstance()
                ->getParam("bootstrap")
                ->getOption('templater');

        $path = $options['directory'] . DIRECTORY_SEPARATOR .
            $theme->name . DIRECTORY_SEPARATOR .
            $options['layout']['directory'];

        $layouts = $this->getLayoutsFiles($path);

        if($import)
            foreach (array_keys($layouts) as $name) {
                $exist = Templater_Model_DbTable_Layout::getInstance()
                        ->findByDql('theme_id = ? AND name = ?',
                            array($theme->id, $name));
                if ($exist->count() == 0) {
                    $layout = new Templater_Model_Mapper_Layout();
                    $layout->name = $name;
                    $layout->theme_id = $theme->id;
                    $layout->title = ucfirst($name);
                    $layout->published = true;
                    $layout->save();

                    if ($name == $options['layout']['default']) {

                        $layPoint = new Templater_Model_Mapper_LayoutPoint();
                        $layPoint->set('map_id', '0-816563134a61e1b2c7cd7899b126bde4');
                        $layPoint->set('layout_id', $layout->id);
                        $layPoint->save();
                    }
                    
                    $layout->free();
                }
            }
        return $layouts;
    }

    /**
     * Return list of files which found in tempalte directory
     * @param string $path
     * @return array
     */
    public function getLayoutsFiles($path)
    {
        $result = array();
        $dirIterator = new DirectoryIterator($path);
        foreach ($dirIterator as $dir) {
            if (!$dir->isDir() && $dir->isFile()
                && strripos($dir->getBasename(), '.') !== 0) {
                $result[$dir->getBasename('.phtml')] = $dir->getBasename();
            }
        }
        return $result;
    }

    /**
     * Save layout
     * @param Templater_Model_Mapper_Layout $layout
     * @param array $values
     * @return boolean
     */
    public function saveLayout(Templater_Model_Mapper_Layout $layout, $values)
    {
        Templater_Model_DbTable_LayoutPoint::getInstance()
            ->deleteUnusedPoints($layout->id, $values['map_id']);

        $layout->fromArray($values);
        $result = $layout->save();

        if(!empty($values['map_id'])) {
            foreach($values['map_id'] as $key=>$mapId) {
                
                $layPoint = Templater_Model_DbTable_LayoutPoint::getInstance()->findByDql("map_id = ? AND layout_id = ?", array($mapId, $layout->id));
                
                if($layPoint->count() == 0) {
                    $layPoint = new Templater_Model_Mapper_LayoutPoint();
                    $layPoint->set('map_id', $mapId);
                    $layPoint->set('layout_id', $layout->id);
                    $layPoint->save();
                } else {

                }
            }

        }

        return $result;
    }

    /**
     * Delete layout
     * @param id $id
     * @return boolean
     */
    public function deleteLayout($id, Zend_Controller_Request_Abstract $request)
    {
        $widget = new Templater_Model_Mapper_Layout();
        $currentLayout = Templater_Model_DbTable_Layout::getInstance()->getCurrentLayout($request);
        if($currentLayout->id == $id)
            throw new Zend_Exception ('You can\'t delete current layout');
        $widget->assignIdentifier($id);
        return $widget->delete();
    }

}