<?php

namespace Icyd\Payulatam\Model\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value'  => '512322', 'label' => __('Argentina')],
            ['value'  => '512321', 'label' => __('Colombia')],
            ['value'  => '512324', 'label' => __('Mexico')],
            ['value'  => '512326', 'label' => __('Panama')],
            ['value'  => '512323', 'label' => __('Peru')],
            ['value'  => '512327', 'label' => __('Brazil')]
        ];
    }

    public function getAllOptions()
    {
        return self::toOptionArray();
    }

    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
?>
