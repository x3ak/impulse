<?php

/**
 * Slys
 *
 * @version    $Id: Themes.php 1134 2011-01-28 14:31:15Z deeper $
 */
class Templater_Model_Themes
{

    /**
     * Return collection of all tempaltes
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return Doctrine_Query::create()
            ->select('tpl.*')
            ->from('Templater_Model_Mapper_Theme tpl')
            ->execute();
    }

    /**
     * Return themes paginator
     *
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getThemesPager($page = 1, $maxPerPage = 20)
    {
        return Templater_Model_DbTable_Theme::getInstance()
            ->getPager($page, $maxPerPage);
    }

    /**
     * Return single theme
     * @param int $id
     * @param boolean $forUpdate
     * @return Templater_Model_Mapper_Theme
     */
    public function getTheme($id = null, $forUpdate = false)
    {
        if (!empty($id))
            $theme = Templater_Model_DbTable_Theme::getInstance()
                    ->findOneById($id);
        else
            $theme = false;

        if (empty($theme) && $forUpdate)
            $theme = new Templater_Model_Mapper_Theme();

        return $theme;
    }

    /**
     * Disable all active tempaltes
     * @return boolean
     */
    public function disableAllThemes()
    {
        $activeThemes = Templater_Model_DbTable_Theme::getInstance()
                ->findByDql('current = ?', array(true));

        foreach ($activeThemes as $theme) {
            $theme->current = false;
            $theme->save();
        }
        return true;
    }

    /**
     * Return filled edit form for theme
     *
     * @param Templater_Model_Mapper_Theme $theme
     * @return Templater_Form_Theme
     */
    public function getThemeEditForm(Templater_Model_Mapper_Theme $theme)
    {
        $themesDirs = $this->getThemesDirectoriesFromFS();

        $form = new Templater_Form_Theme();
        if (empty($theme->id))
            $form->getElement('import_layouts')->setValue(true);

        $form->getElement('name')->addMultiOptions($themesDirs);
        $form->populate($theme->toArray());
        return $form;
    }

    /**
     * Save theme object into DB
     * @param Templater_Model_Mapper_Theme $theme
     * @param array $values
     * @return boolean
     */
    public function saveTheme(Templater_Model_Mapper_Theme $theme, array $values)
    {
        $theme->fromArray($values);
        $layoutsModel = new Templater_Model_Layouts();
        $current = false;
        if ($theme->current == true) {
            $current = true;
            $theme->current = false;
        }

        $result = $theme->save();

        if (!empty($values['import_layouts'])) {
            $layoutsModel->importFromTheme($theme, true);
        }

        if($current === true) {

            $front = false;
            foreach($theme->Layouts as $layout) {
                /* @var $layout Templater_Model_Mapper_Layout */
                foreach($layout->Points as $point) {
                    if($point->map_id == '0-816563134a61e1b2c7cd7899b126bde4')
                            $front = true;
                }
            }

            if($front) {
                $this->disableAllThemes();
                $theme->current = true;
                $result = $theme->save();
            } else {
                throw new Zend_Exception('Theme can\'t be activated because, '.
                        'published default public and administrator layouts not found for this theme');
            }
        }

        return $result;
    }

    /**
     * Generate list of all directories which placed in 'themes' directory
     * @return array
     */
    public function getThemesDirectoriesFromFS()
    {
        $options = Zend_Controller_Front::getInstance()
                ->getParam("bootstrap")
                ->getOption('templater');
        $dirIterator = new DirectoryIterator($options['directory']);
        $result = array();
        foreach ($dirIterator as $dir) {
            if ($dir->isDir()
                && !$dir->isDot()
                && strripos($dir->getBasename(), '.') !== 0)
                $result[$dir->getBasename()] = $dir->getBasename();
        }
        return $result;
    }

    /**
     * Delete theme
     * @return boolean
     */
    public function deleteTheme($id)
    {
        $theme = Templater_Model_DbTable_Theme::getInstance()->findOneBy('id', $id);
        if(empty($theme))
            return false;
        if($theme->current == true)
            throw new Zend_Exception('You can\'t delete active theme.');
        return $theme->delete();
    }

}