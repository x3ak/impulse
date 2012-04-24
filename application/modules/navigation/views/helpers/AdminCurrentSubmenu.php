<?php

class Navigation_View_Helper_AdminCurrentSubmenu extends Zend_View_Helper_Abstract
{

    /**
     * Render admin submenu. If no navigation container is added helper will automatically gets the current
     * navigation using Esb. Conditions allows to filter what navigation entries can be displayed
     *
     * @param Zend_Navigation $navigation
     * @param array|null $conditions
     * @param int $depth
     * @param string $className
     *
     * @return string
     */
    public function adminCurrentSubmenu(Zend_Navigation $navigation = null, $conditions = null, $depth = 3, $className = 'adminSubmenuNavigation')
    {
        if ($conditions !== null) {
            $menu = $this->view->navigation()->menu()->setRenderParents(false)->setOnlyActiveBranch(false);
        } else {
            $menu = $this->view->navigation()->menu()
                            ->setOnlyActiveBranch(true)
                            ->setRenderParents(false)
                            ->setMinDepth(1)
                            ->setMaxDepth($depth);
        }

        $html = $menu->render();

        return $html;
    }

}