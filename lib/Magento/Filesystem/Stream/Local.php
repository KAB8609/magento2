<?php
/**
 * Magento filesystem local stream
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Local implements Magento_Filesystem_StreamInterface
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Stream path
     *
     * @var string
     */
    protected $_path;

    /**
     * Stream mode
     *
     * @var Magento_Filesystem_Stream_Mode
     */
    protected $_mode;

    /**
     * Stream file resource handle
     *
     * @var
     */
    protected $_fileHandle;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     * Opens the stream in the specified mode
     *
     * @param Magento_Filesystem_Stream_Mode $mode
     * @throws Magento_Filesystem_Exception If stream cannot be opened
     */
    public function open(Magento_Filesystem_Stream_Mode $mode)
    {
        $fileHandle = @fopen($this->_path, $mode->getMode());
        if (false === $fileHandle) {
            throw new Magento_Filesystem_Exception(sprintf('The stream "%s" cannot be opened', $this->_path));
        }
        $this->_mode = $mode;
        $this->_fileHandle = $fileHandle;
    }

    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param integer $count The number of bytes to read
     * @return string
     * @throws Magento_Filesystem_Exception If stream wasn't read.
     */
    public function read($count)
    {
        $this->_assertReadable();
        $result = @fread($this->_fileHandle, $count);
        if ($result === false) {
            throw new Magento_Filesystem_Exception('Read of the stream caused an error.');
        }
        return $result;
    }

    /**
     * Reads one CSV row from the stream
     *
     * @param int $count [optional] <p>
     * Must be greater than the longest line (in characters) to be found in
     * the CSV file (allowing for trailing line-end characters). It became
     * optional in PHP 5. Omitting this parameter (or setting it to 0 in PHP
     * 5.0.4 and later) the maximum line length is not limited, which is
     * slightly slower.
     * @param string $delimiter
     * @param string $enclosure
     * @return array|bool false on end of file
     * @throws Magento_Filesystem_Exception
     */
    public function readCsv($count = 0, $delimiter = ',', $enclosure = '"')
    {
        $this->_assertReadable();
        $result = @fgetcsv($this->_fileHandle, $count);
        if ($result === false && $this->eof()) {
            return false;
        }
        if (!is_array($result)) {
            throw new Magento_Filesystem_Exception('Read of the stream caused an error.');
        }
        return $result;
    }

    /**
     * Writes the data to stream.
     *
     * @param string $data
     * @return integer
     * @throws Magento_Filesystem_Exception
     */
    public function write($data)
    {
        $this->_assertWritable();
        $result = @fwrite($this->_fileHandle, $data);
        if (false === $result) {
            throw new Magento_Filesystem_Exception('Write to the stream caused an error.');
        }
        return $result;
    }

    /**
     * Writes one CSV row to the stream.
     *
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return integer
     * @throws Magento_Filesystem_Exception
     */
    public function writeCsv(array $data, $delimiter = ',', $enclosure = '"')
    {
        $this->_assertWritable();
        $result = fputcsv($this->_fileHandle, $data, $delimiter, $enclosure);
        if (false === $result) {
            throw new Magento_Filesystem_Exception('Write to the stream caused an error.');
        }
        return $result;
    }

    /**
     * Closes the stream.
     *
     * @throws Magento_Filesystem_Exception
     */
    public function close()
    {
        $this->_assertOpened();
        $result = @fclose($this->_fileHandle);

        if (false === $result) {
            throw new Magento_Filesystem_Exception('Close of the stream caused an error.');
        }

        $this->_mode = null;
        $this->_fileHandle = null;
    }

    /**
     * Flushes the output.
     *
     * @throws Magento_Filesystem_Exception
     */
    public function flush()
    {
        $this->_assertOpened();
        $result = @fflush($this->_fileHandle);
        if (!$result) {
            throw new Magento_Filesystem_Exception('Flush of the stream caused an error.');
        }
    }

    /**
     * Seeks to the specified offset
     *
     * @param int $offset
     * @param int $whence
     * @throws Magento_Filesystem_Exception
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->_assertOpened();
        $result = fseek($this->_fileHandle, $offset, $whence);
        if (0 !== $result) {
            throw new Magento_Filesystem_Exception('seek operation on the stream caused an error.');
        }
    }

    /**
     * Returns the current position
     *
     * @return int
     * @throws Magento_Filesystem_Exception
     */
    public function tell()
    {
        $this->_assertOpened();
        $result = ftell($this->_fileHandle);
        if (false === $result) {
            throw new Magento_Filesystem_Exception('tell operation on the stream caused an error.');
        }
        return $result;
    }

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof()
    {
        $this->_assertOpened();
        return (bool)@feof($this->_fileHandle);
    }

    /**
     * Asserts the stream is readable
     *
     * @throws Magento_Filesystem_Exception
     */
    protected function _assertReadable()
    {
        $this->_assertOpened();
        if (false === $this->_mode->allowsRead()) {
            throw new Magento_Filesystem_Exception('The stream does not allow read.');
        }
    }

    /**
     * Asserts the stream is writable
     *
     * @throws Magento_Filesystem_Exception
     */
    protected function _assertWritable()
    {
        $this->_assertOpened();
        if (false === $this->_mode->allowsWrite()) {
            throw new Magento_Filesystem_Exception('The stream does not allow write.');
        }
    }

    /**
     * Asserts the stream is opened
     *
     * @throws Magento_Filesystem_Exception
     */
    protected function _assertOpened()
    {
        if (!$this->_fileHandle) {
            throw new Magento_Filesystem_Exception(sprintf('The stream "%s" is not opened', $this->_path));
        }
    }
}
