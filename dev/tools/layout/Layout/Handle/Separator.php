<?php
class Layout_Handle_Separator
{
    protected $_layoutGroupIterator;
    protected $_layoutAnalyzer;
    protected $_layoutInheritance;

    public function __construct(Traversable $layoutGroupIterator, Layout_Analyzer $layoutAnalyzer,
        Layout_Inheritance $layoutInheritance)
    {
        $this->_layoutGroupIterator = $layoutGroupIterator;
        $this->_layoutAnalyzer = $layoutAnalyzer;
        $this->_layoutInheritance = $layoutInheritance;
    }

    /**
     * Go through all the layout files in the system and break them down into handles at appropriate places
     */
    public function performTheJob()
    {
        $emptyFileContents = sprintf($this->_layoutAnalyzer->getTemplate(), '');

        foreach ($this->_layoutGroupIterator as $dir => $layoutFilesGroup) {
            $handleContents = $this->_layoutAnalyzer->aggregateHandles($layoutFilesGroup);
            $overriddenHandles = array_keys($handleContents);
            $fullyInheritedHandles = $this->_getFullyInheritedHandles($dir, $layoutFilesGroup);

            /// Put overriding handles to the appropriate locations
            foreach ($handleContents as $handle => $fileContents) {
                if (in_array($handle, $fullyInheritedHandles)) {
                    continue; // No need to duplicate the handle, which can be inherited from parents
                }
                $this->_overrideHandle($handle, $fileContents, $dir);
            }

            // Wipe the handles that did not exist in the processed files, but were present in inherited themes/bases
            // I.e. previously, theme just removed them
            if ($this->_layoutInheritance->isThemePath($dir)) {
                $inheritedHandles = $this->_layoutInheritance->getInheritedHandles($dir);
                $emptiedHandles = array_diff($inheritedHandles, $overriddenHandles);
                foreach ($emptiedHandles as $handle) {
                    $this->_overrideHandle($handle, $emptyFileContents, $dir);
                }
            }
        }
    }

    protected function _getFullyInheritedHandles($dir, $files)
    {
        if ($this->_layoutInheritance->isBasePath($dir)) {
            return array();
        }

        $themePath = $dir . '/';
        $declaredHandles = array();
        $inheritedHandles = array();
        foreach ($files as $file) {
            $handles = $this->_layoutAnalyzer->getHandles($file);
            if (substr($file, 0, strlen($themePath)) == $themePath) {
                $declaredHandles = array_merge($declaredHandles, $handles);
            } else {
                $inheritedHandles = array_merge($inheritedHandles, $handles);
            }
        }

        return array_diff($inheritedHandles, $declaredHandles);
    }

    protected function _overrideHandle($handle, $fileContents, $originalDir)
    {
        $filePath = $this->_layoutInheritance->getOverridingFilePath($handle, $originalDir);
        $handleDir = dirname($filePath);
        if (!is_dir($handleDir)) {
            mkdir($handleDir, 0666, true);
        }
        file_put_contents($filePath, $fileContents);
    }
}
