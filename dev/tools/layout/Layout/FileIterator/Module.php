<?php
class Layout_FileIterator_Module implements IteratorAggregate
{
    protected $_rootDir;
    protected $_iterator = null;

    public function __construct($rootDir)
    {
        $this->_rootDir = $rootDir;
    }

    public function getIterator()
    {
        if (!$this->_iterator) {
            $this->_iterator = new GlobIterator($this->_rootDir . '/app/code/*/*/view/*/*.xml',
                FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::UNIX_PATHS);
        }
        return $this->_iterator;
    }
}
