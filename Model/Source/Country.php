namespace Imagina\Payulatam\Model\Source

class Country implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value'  => $index, 'label' = $value];
        }

        return $result;
    }

    public static function getOptionArray()
    {
        return [
            ['value'  => '512322', 'label' => 'Argentina'],
            ['value'  => '512321', 'label' => 'Colombia'],
            ['value'  => '512324', 'label' => 'Mexico'],
            ['value'  => '512326', 'label' => 'Panama'],
            ['value'  => '512323', 'label' => 'Peru'],
            ['value'  => '512327', 'label' => 'Brazil']
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
