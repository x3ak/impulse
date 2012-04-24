<?php
        /**
         * Author: Pavel
         * $Id:$
         */

class Members_Form_Visit_New extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $this->setAction($this->getView()->url());


        $member = new Zend_Form_Element_Hidden('member_id');
        $member->setRequired();
        $member->setAllowEmpty(false);
        $member->removeDecorator('HtmlTag');
        $member->removeDecorator('Label');
        $this->addElement($member);


        $subscription = new Zend_Form_Element_Hidden('subscription_id');
        $subscription->setRequired();
        $subscription->setAllowEmpty(false);
        $subscription->removeDecorator('HtmlTag');
        $subscription->removeDecorator('Label');
        $this->addElement($subscription);


        $day = new Zend_Form_Element_Hidden('day');
        $day->setRequired();
        $day->setAllowEmpty(false);
        $day->removeDecorator('HtmlTag');
        $day->removeDecorator('Label');
        $day->setValue(date('Y-m-d'));
        $this->addElement($day);


        $enterTime = new Zend_Form_Element_Hidden('enter_time');
        $enterTime->setRequired();
        $enterTime->setAllowEmpty(false);
        $enterTime->removeDecorator('HtmlTag');
        $enterTime->removeDecorator('Label');
        $this->addElement($enterTime);
    }

    public function setMember(Members_Model_Mapper_Member $member)
    {
        $this->getElement('member_id')->setValue($member->id);

        $activeSubsciption = $member->getActiveSubscription();
        if(empty($activeSubsciption)) {
            $activeSubsciption = $member->getLastSubscription();
        }


        $this->getElement('subscription_id')->setValue($activeSubsciption->id);

        $this->getElement('enter_time')->setValue(date('H:i:s'));

    }


}
