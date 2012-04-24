<?php
class Slys_Form_Wizard extends Zend_Form
{
	/**
	 * @see Zend/Zend_Form::addSubForm()
	 * @param int $currentStep
	 * @return Zend_Form
	 */
	public function addSubForm(Zend_Form $form, $name, $order = null, $step = 1)
	{
		if ($form !== null) {
			if ( empty( $form->getElement('wizard_form_step') ) )
				$form->addElement('hidden', 'wizard_form_step', array('value' => $currentStep));
		}

		return parent::addSubForm($form, $name, $order);
	}

	/**
	 * Get page subform
	 * @param int $currentPage Current page form
	 * @return Zend_Form
	 */
	public function getForm($currentStep = 1)
	{
		$subForms = $this->getSubFormNames();

		$currentFormName = '';
		for ($i = 0; $i < $currentPage; $i++)
			$currentFormName = $subForms[$i];

		return $this->getSubForm($currentFormName);
	}

	/**
	 * Get names of the all subforms in the form
	 * @return array
	 */
	public function getSubFormNames()
	{
		return array_keys($this->getSubForms());
	}

	/**
	 * Get the page number for the question by id
	 * @return int page number; null if the page was not found
	 */
	public function getStepNumberByFormElementName($elementName, $value)
	{
		$step = 0;
		$subForms = $this->getSubForms();

		foreach ($subForms as $form) {
			if ($form->getElement($elementName)->getValue() == $value) {
				if ( !empty( $form->getElement('wizard_form_step') ) )
					$step = $form->getElement('wizard_form_step')->getValue();

				break;
			}
		}

		return $step == 0 ? null : $step;
	}
}