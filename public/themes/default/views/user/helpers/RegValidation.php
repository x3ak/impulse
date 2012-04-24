<?php

class User_View_Helper_RegValidation extends Zend_View_Helper_Abstract
{
    public function RegValidation(Zend_Form $form)
    {
        $role = $form->getElement('role_id');

        if($role->getValue() == 8) {
            $form->getElement('ownership')->setRequired(false);
            $form->getElement('company')->setRequired(false);
            $form->getElement('web')->setRequired(false);
        }
    }
}