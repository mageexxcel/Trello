<?php

namespace Excellence\Trello\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;


/**
 * Backend system config connect(add to trello) field renderer
 */
class Startsetup extends \Magento\Config\Block\System\Config\Form\Field {

   /**
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context
    ) {
        parent::__construct($context);
       
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getElementHtml(AbstractElement $element) {
      
        $this->setType('button');
        $url=$this->getUrlAction();
        $this->setClass("$url");
        $html='<button id="'.$this->getHtmlId().'"   " ' .   $this->serialize( $this->getHtmlAttributes()) . ' " ><span>Start Setup Wizard</sapn></button>';
        return $html;
    }

    /**
     * @return mixed
     */
     public function getUrlAction() {
        $setupurl=$this->_urlBuilder->getUrl("trello/trello/setupwizard/");
        return $setupurl;
    }

    /**
     * Get the Html Id.
     *
     * @return string
     */
    public function getHtmlId() {
         $htmlid = 'startsetupwizard';
        return $htmlid;
    }

    /**
     * Return the attributes for Html.
     *
     * @return string[]
     */
    public function getHtmlAttributes() {
        return [
            'type',
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'disabled',
            'readonly',
            'tabindex',
            'placeholder',
            'data-form-part',
            'data-role',
            'data-action',
            'checked',
        ];
    }

    /**
     * Add a class.
     *
     * @param string $class
     * @return $this
     */
    public function addClass($class) {
        $oldClass = $this->getClass();
        $this->setClass($oldClass . ' ' . $class);
        return $this;
    }

}
