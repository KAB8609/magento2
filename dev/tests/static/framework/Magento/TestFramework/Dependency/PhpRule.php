<?php
/**
 * Rule for searching php file dependency
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Dependency;

class PhpRule implements \Magento\TestFramework\Dependency\RuleInterface
{
    /**
     * Gets alien dependencies information for current module by analyzing file's contents
     *
     * @param string $currentModule
     * @param string $fileType
     * @param string $file
     * @param string $contents
     * @return array
     */
    public function getDependencyInfo($currentModule, $fileType, $file, &$contents)
    {
        if (!in_array($fileType, array('php'))) {
            return array();
        }

        $pattern = '~\b(?<class>(?<module>(' . implode('_|',
                \Magento\TestFramework\Utility\Files::init()->getNamespaces()) .
                '[_\\\\])[a-zA-Z0-9]+)[a-zA-Z0-9_\\\\]*)\b~';

        $dependenciesInfo = array();
        if (preg_match_all($pattern, $contents, $matches)) {
            $matches['module'] = array_unique($matches['module']);
            foreach ($matches['module'] as $i => $referenceModule) {
                $referenceModule = str_replace('_', '\\', $referenceModule);
                if ($currentModule == $referenceModule || $referenceModule == 'Magento\MagentoException') {
                    continue;
                }
                $dependenciesInfo[] = array(
                    'module' => $referenceModule,
                    'type'   => \Magento\TestFramework\Dependency\RuleInterface::TYPE_HARD,
                    'source' => trim($matches['class'][$i]),
                );
            }
        }
        return $dependenciesInfo;
    }
}