<?php
class IWD_Ces_Block_System_Config_Form_Fieldset_Import_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $import_title = Mage::helper('iwd_ces')->__("Import");

        $_secure = Mage::app()->getStore()->isCurrentlySecure();
        $import_link = Mage::helper("adminhtml")->getUrl('adminhtml/ces/import', ['_secure' => $_secure]);

        $model = Mage::getModel('iwd_ces/ces');

        $isBlocked = count($model->getCollection()) > 0;
        $block = $isBlocked ? 'class=\'blocked-button\' disabled' : '';
        return '<button '.$block.' type="button" onclick="setLocation(\''.$import_link.'\')">'.$import_title.'</button>';
    }
}