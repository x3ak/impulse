<?php

class Slys_View_Helper_Form extends Zend_View_Helper_FormElement
{

    protected $_additionalContent = array();

    /**
     * Render HTML form
     *
     * @param  string $name Form name
     * @param  null|array $attribs HTML form attributes
     * @param  false|string $content Form content
     * @return string
     */
    public function form($name, $attribs = null, $content = false)
    {
        $info = $this->_getInfo($name, $content, $attribs);
        extract($info);

        if (!empty($id)) {
            $this->_id = $id;
            $id = ' id="' . $this->view->escape($id) . '"';
        } else {
            $this->_id = uniqid();
            $id = ' id="' . $this->_id . '"';
        }

        if (array_key_exists('id', $attribs) && empty($attribs['id'])) {
            unset($attribs['id']);
        }

        $xhtml = '<form ' . $id . $this->_htmlAttribs($attribs) . '>';

        if (!empty($this->_additionalContents))
            $content .= implode($this->_additionalContents);

        if (false !== $content) {
            $xhtml .= $content
                    . '</form>';
        }

        return $xhtml;
    }

    /**
     * Set current form marker
     *
     * @param string $field
     * @param string $value
     * @return Slys_View_Helper_Form
     */
    public function setMarker($field, $value)
    {
        $this->clearMarkers();
        $this->_additionalContents[] = '<input type="hidden" name="' . $field . '" value="' . $value . '"/>';
        return $this;
    }

    /**
     * Add current form marker to already exist markers
     * @param string $field
     * @param string $value
     * @return Slys_View_Helper_Form
     */
    public function addMarker($field, $value)
    {
        $this->_additionalContents[] = '<input type="hidden" name="' . $field . '" value="' . $value . '"/>';
        return $this;
    }

    /**
     * Remove all current form markers
     * @return Slys_View_Helper_Form
     */
    public function clearMarkers()
    {
        $this->_additionalContents = array();
        return $this;
    }

}
