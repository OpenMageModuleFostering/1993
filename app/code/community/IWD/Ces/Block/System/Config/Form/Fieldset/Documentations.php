<?php
class IWD_Ces_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="https://www.iwdagency.com/help/customer-engagement-suite" target="_blank">Customer Engagement Suite</span>';
    }
}