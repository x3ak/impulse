<?php
class Slys_Controller_Action_Helper_Wizard extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * session namespace name for questionnaire data
	 */
	protected $_namespace = 'WizardForm';
	/**
	 * @var Zend_Session_Namespace
	 */
    protected $_session;

    public function init()
    {
    	parent::init();
    	$this->setSessionNamespace($this->_namespace);
    }

    /**
     * Allow to change standart session namespace
     * @param string $sessionNamespace
     */
    public function setSessionNamespace($sessionNamespace)
    {
    	if (!empty($sessionNamespace))
    		$this->_namespace = $sessionNamespace;

    	$this->_session = new Zend_Session_Namespace($this->_namespace);

    	// if wizard is just started (or it was resseted)
		if (empty($this->_session->currentStep))
			// set the default step
			$this->_session->currentStep = 1;

		if (empty($this->_session->skipSteps))
			$this->clearSkipStep();
    }

    /**
     * Get current session namespace
     * @param string $sessionNamespace
     */
    public function getSessionNamespace()
    {
    	return $this->_namespace;
    }

    /**
     * Set up current step for the wizard
     * @param $pageNumber
     */
    public function setCurrentStep($stepNumber)
    {
    	if ($stepNumber !== null and $stepNumber > 0)
    		$this->_session->currentStep = $stepNumber;
    }

    /**
     * Get current step of the wizard
     * @return int
     */
    public function getCurrentStep()
    {
    	return $this->_session->currentStep;
    }

    /**
     * Set amount of steps in a wizard
     * @param int $amount of the steps
     */
    public function setStepsAmount($steps)
    {
    	$this->_session->steps = $steps;
    }

    /**
     * Get steps amount in the current wizard
     * @return int
     */
    public function getStepsAmount()
    {
    	return $this->_session->steps;
    }

    /**
     * Skip step(s)
     * If $stepNumber is null or empty array is passed - current step will be set as skipped
     * @param int|array|null $stepNumber
     */
    public function skipStep($stepNumber = null)
    {
    	if ($stepNumber === null)
    		$stepNumber = $this->getCurrentStep();
    	elseif (is_array($stepNumber) and !empty($stepNumber)) {
    		foreach ($stepNumber as $step) {
    			if (empty($this->_session->skipSteps[$step])) {
    				$this->_session->skipSteps[$step] = true;
    			}
    		}

    		return;
    	}

    	$this->_session->skipSteps[$stepNumber] = true;
    }

    /**
     * Allows to clear skipping of the one, series or all steps
     * @param int|array|null $step
     */
    public function clearSkipStep($step = null)
    {
    	if ($step === null) {
    		unset($this->_session->skipSteps);
    		$this->_session->skipSteps = array();
    	}
    	elseif (is_array($step) and !empty($step)) {
    		foreach ($step as $st)
	    		unset($this->_session->skipSteps[$st]);
    	}
    	else
    		unset($this->_session->skipSteps[intval($step)]);
    }

    /**
     * Saves step data. If $stepNumber is null - data will be saved for current step
     * @param mixed $data
     * @param int|null $stepNumber
     */
    public function setStepData($data, $stepNumber = null)
    {
    	$this->_session->wizardData[$stepNumber] = $data;
    }

    /**
     * Returns step data
     * @param int $stepNumber
     * @return mixed
     */
    public function getStepData($stepNumber)
    {
    	return $this->_session->wizardData[$stepNumber];
    }
}