<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with remote REST interface
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Rest
{
    /**
     * Paths for xml config files
     */
    const CHANNELS_XML = "channels.xml";
    const CHANNEL_XML = "channel.xml";
    const PACKAGES_XML = "packages.xml";
    const RELEASES_XML = "releases.xml";
    const PACKAGE_XML = "package.xml";
    const EXT = "tgz";

    /**
     * HTTP Loader
     * @var Magento_HTTP_IClient
     */
    protected $_loader = null;

    /**
     * XML parser
     *
     * @var Magento_Xml_Parser
     */
    protected $_parser = null;

    /**
     * Channel URI
     *
     * @var string
     */
    protected $_chanUri = '';

    /**
     * Protocol HTTP or FTP
     *
     * @var string http or ftp
     */
    protected $_protocol = '';

    /**
     * States interpretation
     *
     * @var array
     */
    protected $states = array('b'=>'beta', 'd'=>'dev', 's'=>'stable', 'a'=>'alpha');

    /**
     * Constructor sets default protocol
     *
     * @param string $protocol
     */
    public function __construct($protocol="http")
    {
        switch ($protocol) {
            case 'ftp':
                $this->_protocol = 'ftp';
                break;
            case 'http':
                $this->_protocol = 'http';
                break;
            default:
                $this->_protocol = 'http';
                break;
        }
    }

    /**
     * Set channel URI
     *
     * @param string $uri
     * @return void
     */
    public function setChannel($uri)
    {
        $this->_chanUri = $uri;
    }

    /**
     * Get HTTP loader
     *
     * @return Magento_HTTP_IClient|Magento_Connect_Loader_Ftp
     */
    public function getLoader()
    {
        if (is_null($this->_loader)) {
            $this->_loader = Magento_Connect_Loader::getInstance($this->_protocol);
        }
        return $this->_loader;
    }

    /**
     * Get parser
     *
     * @return Magento_Xml_Parser
     */
    protected function getParser()
    {
        if (is_null($this->_parser)) {
            $this->_parser = new Magento_Xml_Parser();
        }
        return $this->_parser;
    }

    /**
     * Load URI response
     *
     * @param string $uriSuffix
     * @return bool|string
     */
    protected function loadChannelUri($uriSuffix)
    {
        $url = $this->_chanUri . "/" . $uriSuffix;
        $this->getLoader()->get($url);
        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            return false;
        }
        return $this->getLoader()->getBody();
    }

    /**
     * Get channels list of URI
     *
     * @return array
     */
    public function getChannelInfo()
    {
        $out = $this->loadChannelUri(self::CHANNEL_XML);
        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            throw new Exception("Invalid server response for {$this->_chanUri}");
        }
        $parser = $this->getParser();
        $out = $parser->loadXML($out)->xmlToArray();

        $vo = new Magento_Connect_Channel_VO();
        $vo->fromArray($out['channel']);
        if (!$vo->validate()) {
            throw new Exception("Invalid channel.xml file");
        }
        return $vo;
    }

    /**
     * Get packages list of channel
     *
     * @return array
     */
    public function getPackages()
    {
        $out = $this->loadChannelUri(self::PACKAGES_XML);
        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            return false;
        }
        $parser = $this->getParser();
        $out = $parser->loadXML($out)->xmlToArray();

        if (!isset($out['data']['p'])) {
            return array();
        }
        if (isset($out['data']['p'][0])) {
            return $out['data']['p'];
        }
        if (is_array($out['data']['p'])) {
            return array($out['data']['p']);
        }
        return array();
    }

    /**
     * Return Channel Packages loaded from Channel Server
     *
     * @return array|bool|string
     */
    public function getPackagesHashed()
    {
        $out = $this->loadChannelUri(self::PACKAGES_XML);
        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            return false;
        }
        $parser = $this->getParser();
        $out = $parser->loadXML($out)->xmlToArray();

        $return = array();
        if (!isset($out['data']['p'])) {
            return $return;
        }
        if (isset($out['data']['p'][0])) {
            $return = $out['data']['p'];
        } elseif (is_array($out['data']['p'])) {
            $return = array($out['data']['p']);
        }
        $c = count($return);
        if ($c) {
            $output = array();
            for ($i=0; $i<$c; $i++) {
                $element = $return[$i];
                $output[$element['n']] = $element['r'];
            }
            $return = $output;
        }

        $out = array();
        foreach ($return as $name=>$package) {
            $stabilities = array_map(array($this, 'shortStateToLong'), array_keys($package));
            $versions = array_map('trim', array_values($package));
            $package = array_combine($versions, $stabilities);
            ksort($package);
            $out[$name] = $package;
        }
        return $out;
    }

    /**
     * Stub
     *
     * @param $n
     * @return unknown_type
     */
    public function escapePackageName($n)
    {
        return $n;
    }

    /**
     * Get releases list of package on current channel
     *
     * @param string $package package name
     * @return array|bool
     */
    public function getReleases($package)
    {
        $out = $this->loadChannelUri($this->escapePackageName($package) . "/" . self::RELEASES_XML);
        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            return false;
        }
        $parser = $this->getParser();
        $out = $parser->loadXML($out)->xmlToArray();
        if (!isset($out['releases']['r'])) {
            return array();
        }
        $src = $out['releases']['r'];
        if (!array_key_exists(0, $src)) {
            return array($src);
        }
        $this->sortReleases($src);
        return $src;
    }

    /**
     * Sort releases
     *
     * @param array $releases
     * @return void
     */
    public function sortReleases(array &$releases)
    {
        usort($releases, array($this, 'sortReleasesCallback'));
        $releases = array_reverse($releases);
    }

    /**
     * Sort releases callback
     *
     * @param string $a
     * @param srting $b
     * @return int
     */
    protected function sortReleasesCallback($a, $b)
    {
        return version_compare($a['v'],$b['v']);
    }

    /**
     * Get package info (package.xml)
     *
     * @param $package
     * @return unknown_type
     */
    public function getPackageInfo($package)
    {
        $out = $this->loadChannelUri($this->escapePackageName($package) . "/" . self::PACKAGE_XML);
        if (false === $out) {
            return false;
        }
        return new Magento_Connect_Package($out);
    }

    /**
     * Retrieve information on Package Release from the Channel Server
     *
     * @param $package
     * @param $version
     * @return Magento_Connect_Package|bool
     */
    public function getPackageReleaseInfo($package, $version)
    {
        $out = $this->loadChannelUri($this->escapePackageName($package) . "/" . $version . "/" . self::PACKAGE_XML);
        if (false === $out) {
            return false;
        }
        return new Magento_Connect_Package($out);
    }

    /**
     * Get package archive file of release
     *
     * @throws Exception
     * @param string $package package name
     * @param string $version package version
     * @param string $targetFile
     * @return bool
     */
    public function downloadPackageFileOfRelease($package, $version, $targetFile)
    {
        $package = $this->escapePackageName($package);
        $version = $this->escapePackageName($version);

        if (file_exists($targetFile)) {
            $chksum = $this->loadChannelUri($package . "/" . $version . "/checksum");
            $statusCode = $this->getLoader()->getStatus();
            if ($statusCode == 200) {
                if (md5_file($targetFile) == $chksum) {
                    return true;
                }
            }
        }

        $out = $this->loadChannelUri($package . "/" . $version . "/" . $package . "-" . $version . "." . self::EXT);

        $statusCode = $this->getLoader()->getStatus();
        if ($statusCode != 200) {
            throw new Exception("Package not found: {$package} {$version}");
        }
        $dir = dirname($targetFile);
        @mkdir($dir, 0777, true);
        $result = @file_put_contents($targetFile, $out);
        if (false === $result) {
            throw new Exception("Cannot write to file {$targetFile}");
        }
        return true;
    }

    /**
     * Decode state
     *
     * @param string $s
     * @return string
     */
    public function shortStateToLong($s)
    {
        return isset($this->states[$s]) ? $this->states[$s] : 'dev';
    }
}