<?php
error_reporting(1803);
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('ASCII');
}
if (!class_exists('PHP_Archive')) {/**
 * PHP_Archive Class (implements .phar)
 *
 * @package PHP_Archive
 * @category PHP
 */
/**
 * PHP_Archive Class (implements .phar)
 *
 * PHAR files a singular archive from which an entire application can run.
 * To use it, simply package it using {@see PHP_Archive_Creator} and use phar://
 * URIs to your includes. i.e. require_once 'phar://config.php' will include config.php
 * from the root of the PHAR file.
 *
 * Gz code borrowed from the excellent File_Archive package by Vincent Lascaux.
 *
 * @copyright Copyright David Shafik and Synaptic Media 2004. All rights reserved.
 * @author Davey Shafik <davey@synapticmedia.net>
 * @author Greg Beaver <cellog@php.net>
 * @link http://www.synapticmedia.net Synaptic Media
 * @version Id$
 * @package PHP_Archive
 * @category PHP
 */

class PHP_Archive
{
    const GZ = 0x00001000;
    const BZ2 = 0x00002000;
    const SIG = 0x00010000;
    const SHA1 = 0x0002;
    const MD5 = 0x0001;
    const SHA256 = 0x0003;
    const SHA512 = 0x0004;
    const OPENSSL = 0x0010;
    /**
     * Whether this archive is compressed with zlib
     *
     * @var bool
     */
    private $_compressed;
    /**
     * @var string Real path to the .phar archive
     */
    private $_archiveName = null;
    /**
     * Current file name in the phar
     * @var string
     */
    protected $currentFilename = null;
    /**
     * Length of current file in the phar
     * @var string
     */
    protected $internalFileLength = null;
    /**
     * true if the current file is an empty directory
     * @var string
     */
    protected $isDir = false;
    /**
     * Current file statistics (size, creation date, etc.)
     * @var string
     */
    protected $currentStat = null;
    /**
     * @var resource|null Pointer to open .phar
     */
    protected $fp = null;
    /**
     * @var int Current Position of the pointer
     */
    protected $position = 0;

    /**
     * Map actual realpath of phars to meta-data about the phar
     *
     * Data is indexed by the alias that is used by internal files.  In other
     * words, if a file is included via:
     * <code>
     * require_once 'phar://PEAR.phar/PEAR/Installer.php';
     * </code>
     * then the alias is "PEAR.phar"
     *
     * Information stored is a boolean indicating whether this .phar is compressed
     * with zlib, another for bzip2, phar-specific meta-data, and
     * the precise offset of internal files
     * within the .phar, used with the {@link $_manifest} to load actual file contents
     * @var array
     */
    private static $_pharMapping = array();
    /**
     * Map real file paths to alias used
     *
     * @var array
     */
    private static $_pharFiles = array();
    /**
     * File listing for the .phar
     *
     * The manifest is indexed per phar.
     *
     * Files within the .phar are indexed by their relative path within the
     * .phar.  Each file has this information in its internal array
     *
     * - 0 = uncompressed file size
     * - 1 = timestamp of when file was added to phar
     * - 2 = offset of file within phar relative to internal file's start
     * - 3 = compressed file size (actual size in the phar)
     * @var array
     */
    private static $_manifest = array();
    /**
     * Absolute offset of internal files within the .phar, indexed by absolute
     * path to the .phar
     *
     * @var array
     */
    private static $_fileStart = array();
    /**
     * file name of the phar
     *
     * @var string
     */
    private $_basename;


    /**
     * Default MIME types used for the web front controller
     *
     * @var array
     */
    public static $defaultmimes = array(
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'arc' => 'application/octet-stream',
            'arj' => 'application/octet-stream',
            'art' => 'image/x-jg',
            'asf' => 'video/x-ms-asf',
            'asx' => 'video/x-ms-asf',
            'avi' => 'video/avi',
            'bin' => 'application/octet-stream',
            'bm' => 'image/bmp',
            'bmp' => 'image/bmp',
            'bz2' => 'application/x-bzip2',
            'css' => 'text/css',
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'dv' => 'video/x-dv',
            'dvi' => 'application/x-dvi',
            'eps' => 'application/postscript',
            'exe' => 'application/octet-stream',
            'gif' => 'image/gif',
            'gz' => 'application/x-gzip',
            'gzip' => 'application/x-gzip',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ico' => 'image/x-icon',
            'jpe' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'js' => 'application/x-javascript',
            'log' => 'text/plain',
            'mid' => 'audio/x-midi',
            'mov' => 'video/quicktime',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg3',
            'mpg' => 'audio/mpeg',
            'pdf' => 'aplication/pdf',
            'png' => 'image/png',
            'rtf' => 'application/rtf',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'txt' => 'text/plain',
            'xml' => 'text/xml',
        );

    public static $defaultphp = array(
        'php' => true
        );

    public static $defaultphps = array(
        'phps' => true
        );

    public static $deny = array('/.+\.inc$/');

    public static function viewSource($archive, $file)
    {
        // security, idea borrowed from PHK
        if (!file_exists($archive . '.introspect')) {
            header("HTTP/1.0 404 Not Found");
            return false;
        }
        if (self::_fileExists($archive, $_GET['viewsource'])) {
            $source = highlight_file('phar://go-pear.phar/' .
                $_GET['viewsource'], true);
            header('Content-Type: text/html');
            header('Content-Length: ' . strlen($source));
            echo '<html><head><title>Source of ',
                htmlspecialchars($_GET['viewsource']), '</title></head>';
            echo '<body><h1>Source of ',
                htmlspecialchars($_GET['viewsource']), '</h1>';
            if (isset($_GET['introspect'])) {
                echo '<a href="', htmlspecialchars($_SERVER['PHP_SELF']),
                    '?introspect=', urlencode(htmlspecialchars($_GET['introspect'])),
                    '">Return to ', htmlspecialchars($_GET['introspect']), '</a><br />';
            }
            echo $source;
            return false;
        } else {
            header("HTTP/1.0 404 Not Found");
            return false;
        }

    }

    public static function introspect($archive, $dir)
    {
        // security, idea borrowed from PHK
        if (!file_exists($archive . '.introspect')) {
            header("HTTP/1.0 404 Not Found");
            return false;
        }
        if (!$dir) {
            $dir = '/';
        }
        $dir = self::processFile($dir);
        if ($dir[0] != '/') {
            $dir = '/' . $dir;
        }
        try {
            $self = htmlspecialchars($_SERVER['PHP_SELF']);
            $iterate = new DirectoryIterator('phar://go-pear.phar' . $dir);
            echo '<html><head><title>Introspect ', htmlspecialchars($dir),
                '</title></head><body><h1>Introspect ', htmlspecialchars($dir),
                '</h1><ul>';
            if ($dir != '/') {
                echo '<li><a href="', $self, '?introspect=',
                    htmlspecialchars(dirname($dir)), '">..</a></li>';
            }
            foreach ($iterate as $entry) {
                if ($entry->isDot()) continue;
                $name = self::processFile($entry->getPathname());
                $name = str_replace('phar://go-pear.phar/', '', $name);
                if ($entry->isDir()) {
                    echo '<li><a href="', $self, '?introspect=',
                        urlencode(htmlspecialchars($name)),
                        '">',
                        htmlspecialchars($entry->getFilename()), '/</a> [directory]</li>';
                } else {
                    echo '<li><a href="', $self, '?introspect=',
                        urlencode(htmlspecialchars($dir)), '&viewsource=',
                        urlencode(htmlspecialchars($name)),
                        '">',
                        htmlspecialchars($entry->getFilename()), '</a></li>';
                }
            }
            return false;
        } catch (Exception $e) {
            echo '<html><head><title>Directory not found: ',
                htmlspecialchars($dir), '</title></head>',
                '<body><h1>Directory not found: ', htmlspecialchars($dir), '</h1>',
                '<p>Try <a href="', htmlspecialchars($_SERVER['PHP_SELF']), '?introspect=/">',
                'This link</a></p></body></html>';
            return false;
        }
    }

    public static function webFrontController($initfile)
    {
        if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
            $uri = parse_url($_SERVER['REQUEST_URI']);
            $archive = realpath($_SERVER['SCRIPT_FILENAME']);
            $subpath = str_replace('/' . basename($archive), '', $uri['path']);
            if (!$subpath || $subpath == '/') {
                if (isset($_GET['viewsource'])) {
                    return self::viewSource($archive, $_GET['viewsource']);
                }
                if (isset($_GET['introspect'])) {
                    return self::introspect($archive, $_GET['introspect']);
                }
                $subpath = '/' . $initfile;
            }
            if (!self::_fileExists($archive, substr($subpath, 1))) {
                header("HTTP/1.0 404 Not Found");
                return false;
            }
            foreach (self::$deny as $pattern) {
                if (preg_match($pattern, $subpath)) {
                    header("HTTP/1.0 404 Not Found");
                    return false;
                }
            }
            $inf = pathinfo(basename($subpath));
            if (!isset($inf['extension'])) {
                header('Content-Type: text/plain');
                header('Content-Length: ' .
                    self::_filesize($archive, substr($subpath, 1)));
                readfile('phar://go-pear.phar' . $subpath);
                return false;
            }
            if (isset(self::$defaultphp[$inf['extension']])) {
                include 'phar://go-pear.phar' . $subpath;
                return false;
            }
            if (isset(self::$defaultmimes[$inf['extension']])) {
                header('Content-Type: ' . self::$defaultmimes[$inf['extension']]);
                header('Content-Length: ' .
                    self::_filesize($archive, substr($subpath, 1)));
                readfile('phar://go-pear.phar' . $subpath);
                return false;
            }
            if (isset(self::$defaultphps[$inf['extension']])) {
                header('Content-Type: text/html');
                $c = highlight_file('phar://go-pear.phar' . $subpath, true);
                header('Content-Length: ' . strlen($c));
                echo $c;
                return false;
            }
            header('Content-Type: text/plain');
            header('Content-Length: ' .
                    self::_filesize($archive, substr($subpath, 1)));
            readfile('phar://go-pear.phar' . $subpath);
        }
    }

    /**
     * Detect end of stub
     *
     * @param string $buffer stub past '__HALT_'.'COMPILER();'
     * @return end of stub, prior to length of manifest.
     */
    private static final function _endOfStubLength($buffer)
    {
        $pos = 0;
        if (!strlen($buffer)) {
            return $pos;
        }
        if (($buffer[0] == ' ' || $buffer[0] == "\n") && @substr($buffer, 1, 2) == '')
        {
            $pos += 3;
            if ($buffer[$pos] == "\r" && $buffer[$pos+1] == "\n") {
                $pos += 2;
            }
            else if ($buffer[$pos] == "\n") {
                $pos += 1;
            }
        }
        return $pos;
    }

    /**
     * Allows loading an external Phar archive without include()ing it
     *
     * @param string $file  phar package to load
     * @param string $alias alias to use
     * @throws Exception
     */
    public static final function loadPhar($file, $alias = NULL)
    {
        $file = realpath($file);
        if ($file) {
            $fp = fopen($file, 'rb');
            $buffer = '';
            while (!feof($fp)) {
                $buffer .= fread($fp, 8192);
                // don't break phars
                if ($pos = strpos($buffer, '__HALT_COMPI' . 'LER();')) {
                    $buffer .= fread($fp, 5);
                    fclose($fp);
                    $pos += 18;
                    $pos += self::_endOfStubLength(substr($buffer, $pos));
                    return self::_mapPhar($file, $pos, $alias);
                }
            }
            fclose($fp);
        }
    }

    /**
     * Map a full real file path to an alias used to refer to the .phar
     *
     * This function can only be called from the initialization of the .phar itself.
     * Any attempt to call from outside the .phar or to re-alias the .phar will fail
     * as a security measure.
     * @param string $alias
     * @param int $dataoffset the value of 43508
     */
    public static final function mapPhar($alias = NULL, $dataoffset = NULL)
    {
        try {
            $trace = debug_backtrace();
            $file = $trace[0]['file'];
            // this ensures that this is safe
            if (!in_array($file, get_included_files())) {
                die('SECURITY ERROR: PHP_Archive::mapPhar can only be called from within ' .
                    'the phar that initiates it');
            }
            $file = realpath($file);
            if (!isset($dataoffset)) {
                $dataoffset = constant('__COMPILER_HALT_OFFSET'.'__');
                $fp = fopen($file, 'rb');
                fseek($fp, $dataoffset, SEEK_SET);
                $dataoffset = $dataoffset + self::_endOfStubLength(fread($fp, 5));
                fclose($fp);
            }

            self::_mapPhar($file, $dataoffset);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Sub-function, allows recovery from errors
     *
     * @param unknown_type $file
     * @param unknown_type $dataoffset
     */
    private static function _mapPhar($file, $dataoffset, $alias = NULL)
    {
        $file = realpath($file);
        if (isset(self::$_manifest[$file])) {
            return;
        }
        if (!is_array(self::$_pharMapping)) {
            self::$_pharMapping = array();
        }
        $fp = fopen($file, 'rb');
        // seek to __HALT_COMPILER_OFFSET__
        fseek($fp, $dataoffset);
        $manifest_length = unpack('Vlen', fread($fp, 4));
        $manifest = '';
        $last = '1';
        while (strlen($last) && strlen($manifest) < $manifest_length['len']) {
            $read = 8192;
            if ($manifest_length['len'] - strlen($manifest) < 8192) {
                $read = $manifest_length['len'] - strlen($manifest);
            }
            $last = fread($fp, $read);
            $manifest .= $last;
        }
        if (strlen($manifest) < $manifest_length['len']) {
            throw new Exception('ERROR: manifest length read was "' .
                strlen($manifest) .'" should be "' .
                $manifest_length['len'] . '"');
        }
        $info = self::_unserializeManifest($manifest);
        if ($info['alias']) {
            $alias = $info['alias'];
            $explicit = true;
        } else {
            if (!isset($alias)) {
                $alias = $file;
            }
            $explicit = false;
        }
        self::$_manifest[$file] = $info['manifest'];
        $compressed = $info['compressed'];
        self::$_fileStart[$file] = ftell($fp);
        fclose($fp);
        if ($compressed & 0x00001000) {
            if (!function_exists('gzinflate')) {
                throw new Exception('Error: zlib extension is not enabled - gzinflate() function needed' .
                    ' for compressed .phars');
            }
        }
        if ($compressed & 0x00002000) {
            if (!function_exists('bzdecompress')) {
                throw new Exception('Error: bzip2 extension is not enabled - bzdecompress() function needed' .
                    ' for compressed .phars');
            }
        }
        if (isset(self::$_pharMapping[$alias])) {
            throw new Exception('ERROR: PHP_Archive::mapPhar has already been called for alias "' .
                $alias . '" cannot re-alias to "' . $file . '"');
        }
        self::$_pharMapping[$alias] = array($file, $compressed, $dataoffset, $explicit,
            $info['metadata']);
        self::$_pharFiles[$file] = $alias;
    }

    /**
     * extract the manifest into an internal array
     *
     * @param string $manifest
     * @return false|array
     */
    private static function _unserializeManifest($manifest)
    {
        // retrieve the number of files in the manifest
        $info = unpack('V', substr($manifest, 0, 4));
        $apiver = substr($manifest, 4, 2);
        $apiver = bin2hex($apiver);
        $apiver_dots = hexdec($apiver[0]) . '.' . hexdec($apiver[1]) . '.' . hexdec($apiver[2]);
        $majorcompat = hexdec($apiver[0]);
        $calcapi = explode('.', self::APIVersion());
        if ($calcapi[0] != $majorcompat) {
            throw new Exception('Phar is incompatible API version ' . $apiver_dots . ', but ' .
                'PHP_Archive is API version '.self::APIVersion());
        }
        if ($calcapi[0] === '0') {
            if (self::APIVersion() != $apiver_dots) {
                throw new Exception('Phar is API version ' . $apiver_dots .
                    ', but PHP_Archive is API version '.self::APIVersion(), E_USER_ERROR);
            }
        }
        $flags = unpack('V', substr($manifest, 6, 4));
        $ret = array('compressed' => $flags[1] & 0x00003000);
        // signature is not verified by default in PHP_Archive, phar is better
        $ret['hassignature'] = $flags & 0x00010000;
        $aliaslen = unpack('V', substr($manifest, 10, 4));
        if ($aliaslen) {
            $ret['alias'] = substr($manifest, 14, $aliaslen[1]);
        } else {
            $ret['alias'] = false;
        }
        $manifest = substr($manifest, 14 + $aliaslen[1]);
        $metadatalen = unpack('V', substr($manifest, 0, 4));
        if ($metadatalen[1]) {
            $ret['metadata'] = unserialize(substr($manifest, 4, $metadatalen[1]));
            $manifest = substr($manifest, 4 + $metadatalen[1]);
        } else {
            $ret['metadata'] = null;
            $manifest = substr($manifest, 4);
        }
        $offset = 0;
        $start = 0;
        for ($i = 0; $i < $info[1]; $i++) {
            // length of the file name
            $len = unpack('V', substr($manifest, $start, 4));
            $start += 4;
            // file name
            $savepath = substr($manifest, $start, $len[1]);
            $start += $len[1];
            // retrieve manifest data:
            // 0 = uncompressed file size
            // 1 = timestamp of when file was added to phar
            // 2 = compressed filesize
            // 3 = crc32
            // 4 = flags
            // 5 = metadata length
            $ret['manifest'][$savepath] = array_values(unpack('Va/Vb/Vc/Vd/Ve/Vf', substr($manifest, $start, 24)));
            $ret['manifest'][$savepath][3] = sprintf('%u', $ret['manifest'][$savepath][3]
                & 0xffffffff);
            if ($ret['manifest'][$savepath][5]) {
                $ret['manifest'][$savepath][6] = unserialize(substr($manifest, $start + 24,
                    $ret['manifest'][$savepath][5]));
            } else {
                $ret['manifest'][$savepath][6] = null;
            }
            $ret['manifest'][$savepath][7] = $offset;
            $offset += $ret['manifest'][$savepath][2];
            $start += 24 + $ret['manifest'][$savepath][5];
        }
        return $ret;
    }

    /**
     * @param string
     */
    private static function processFile($path)
    {
        if ($path == '.') {
            return '';
        }
        $std = str_replace("\\", "/", $path);
        while ($std != ($std = preg_replace("/[^\/:?]+\/\.\.\//", "", $std))) ;
        $std = str_replace("/./", "", $std);
        if (strlen($std) > 1 && $std[0] == '/') {
            $std = substr($std, 1);
        }
        if (strncmp($std, "./", 2) == 0) {
            return substr($std, 2);
        } else {
            return $std;
        }
    }

    /**
     * Seek in the master archive to a matching file or directory
     * @param string
     */
    protected function selectFile($path, $allowdirs = true)
    {
        $std = self::processFile($path);
        if (isset(self::$_manifest[$this->_archiveName][$path])) {
            if ($path[strlen($path)-1] == '/') {
                // directory
                if (!$allowdirs) {
                    return 'Error: "' . $path . '" is a directory in phar "' . $this->_basename . '"';
                }
                $this->_setCurrentFile($path, true);
            } else {
                $this->_setCurrentFile($path);
            }
            return true;
        }
        if (!$allowdirs) {
            return 'Error: "' . $path . '" is not a file in phar "' . $this->_basename . '"';
        }
        foreach (self::$_manifest[$this->_archiveName] as $file => $info) {
            if (empty($std) ||
                  //$std is a directory
                  strncmp($std.'/', $path, strlen($std)+1) == 0) {
                $this->currentFilename = $this->internalFileLength = $this->currentStat = null;
                return true;
            }
        }
        return 'Error: "' . $path . '" not found in phar "' . $this->_basename . '"';
    }

    private function _setCurrentFile($path, $dir = false)
    {
        if ($dir) {
            $this->currentStat = array(
                2 => 040777, // directory mode, readable by all, writeable by none
                4 => 0, // uid
                5 => 0, // gid
                7 => 0, // size
                9 => self::$_manifest[$this->_archiveName][$path][1], // creation time
                );
            $this->internalFileLength = 0;
            $this->isDir = true;
        } else {
            $this->currentStat = array(
                2 => 0100444, // file mode, readable by all, writeable by none
                4 => 0, // uid
                5 => 0, // gid
                7 => self::$_manifest[$this->_archiveName][$path][0], // size
                9 => self::$_manifest[$this->_archiveName][$path][1], // creation time
                );
            $this->internalFileLength = self::$_manifest[$this->_archiveName][$path][2];
            $this->isDir = false;
        }
        $this->currentFilename = $path;
        // seek to offset of file header within the .phar
        if (is_resource(@$this->fp)) {
            fseek($this->fp, self::$_fileStart[$this->_archiveName] + self::$_manifest[$this->_archiveName][$path][7]);
        }
    }

    private static function _fileExists($archive, $path)
    {
        return isset(self::$_manifest[$archive]) &&
            isset(self::$_manifest[$archive][$path]);
    }

    private static function _filesize($archive, $path)
    {
        return self::$_manifest[$archive][$path][0];
    }

    /**
     * Seek to a file within the master archive, and extract its contents
     * @param string
     * @return array|string an array containing an error message string is returned
     *                      upon error, otherwise the file contents are returned
     */
    public function extractFile($path)
    {
        $this->fp = @fopen($this->_archiveName, "rb");
        if (!$this->fp) {
            return array('Error: cannot open phar "' . $this->_archiveName . '"');
        }
        if (($e = $this->selectFile($path, false)) === true) {
            $data = '';
            $count = $this->internalFileLength;
            while ($count) {
                if ($count < 8192) {
                    $data .= @fread($this->fp, $count);
                    $count = 0;
                } else {
                    $count -= 8192;
                    $data .= @fread($this->fp, 8192);
                }
            }
            @fclose($this->fp);
            if (self::$_manifest[$this->_archiveName][$path][4] & self::GZ) {
                $data = gzinflate($data);
            } elseif (self::$_manifest[$this->_archiveName][$path][4] & self::BZ2) {
                $data = bzdecompress($data);
            }
            if (!isset(self::$_manifest[$this->_archiveName][$path]['ok'])) {
                if (strlen($data) != $this->currentStat[7]) {
                    return array("Not valid internal .phar file (size error {$size} != " .
                        $this->currentStat[7] . ")");
                }
                if (self::$_manifest[$this->_archiveName][$path][3] != sprintf("%u", crc32($data) & 0xffffffff)) {
                    return array("Not valid internal .phar file (checksum error)");
                }
                self::$_manifest[$this->_archiveName][$path]['ok'] = true;
            }
            return $data;
        } else {
            @fclose($this->fp);
            return array($e);
        }
    }

    /**
     * Parse urls like phar:///fullpath/to/my.phar/file.txt
     *
     * @param string $file
     * @return false|array
     */
    static protected function parseUrl($file)
    {
        if (substr($file, 0, 7) != 'phar://') {
            return false;
        }
        $file = substr($file, 7);

        $ret = array('scheme' => 'phar');
        $pos_p = strpos($file, '.phar.php');
        $pos_z = strpos($file, '.phar.gz');
        $pos_b = strpos($file, '.phar.bz2');
        if ($pos_p) {
            if ($pos_z) {
                return false;
            }
            $ret['host'] = substr($file, 0, $pos_p + strlen('.phar.php'));
            $ret['path'] = substr($file, strlen($ret['host']));
        } elseif ($pos_z) {
            $ret['host'] = substr($file, 0, $pos_z + strlen('.phar.gz'));
            $ret['path'] = substr($file, strlen($ret['host']));
        } elseif ($pos_b) {
            $ret['host'] = substr($file, 0, $pos_z + strlen('.phar.bz2'));
            $ret['path'] = substr($file, strlen($ret['host']));
        } elseif (($pos_p = strpos($file, ".phar")) !== false) {
            $ret['host'] = substr($file, 0, $pos_p + strlen('.phar'));
            $ret['path'] = substr($file, strlen($ret['host']));
        } else {
            return false;
        }
        if (!$ret['path']) {
            $ret['path'] = '/';
        }
        return $ret;
    }

    /**
     * Locate the .phar archive in the include_path and detect the file to open within
     * the archive.
     *
     * Possible parameters are phar://pharname.phar/filename_within_phar.ext
     * @param string a file within the archive
     * @return string the filename within the .phar to retrieve
     */
    public function initializeStream($file)
    {
        $file = self::processFile($file);
        $info = @parse_url($file);
        if (!$info) {
            $info = self::parseUrl($file);
        }
        if (!$info) {
            return false;
        }
        if (!isset($info['host'])) {
            // malformed internal file
            return false;
        }
        if (!isset(self::$_pharFiles[$info['host']]) &&
              !isset(self::$_pharMapping[$info['host']])) {
            try {
                self::loadPhar($info['host']);
                // use alias from here out
                $info['host'] = self::$_pharFiles[$info['host']];
            } catch (Exception $e) {
                return false;
            }
        }
        if (!isset($info['path'])) {
            return false;
        } elseif (strlen($info['path']) > 1) {
            $info['path'] = substr($info['path'], 1);
        }
        if (isset(self::$_pharMapping[$info['host']])) {
            $this->_basename = $info['host'];
            $this->_archiveName = self::$_pharMapping[$info['host']][0];
            $this->_compressed = self::$_pharMapping[$info['host']][1];
        } elseif (isset(self::$_pharFiles[$info['host']])) {
            $this->_archiveName = $info['host'];
            $this->_basename = self::$_pharFiles[$info['host']];
            $this->_compressed = self::$_pharMapping[$this->_basename][1];
        }
        $file = $info['path'];
        return $file;
    }

    /**
     * Open the requested file - PHP streams API
     *
     * @param string $file String provided by the Stream wrapper
     * @access private
     */
    public function stream_open($file)
    {
        return $this->_streamOpen($file);
    }

    /**
     * @param string filename to opne, or directory name
     * @param bool if true, a directory will be matched, otherwise only files
     *             will be matched
     * @uses trigger_error()
     * @return bool success of opening
     * @access private
     */
    private function _streamOpen($file, $searchForDir = false)
    {
        $path = $this->initializeStream($file);
        if (!$path) {
            trigger_error('Error: Unknown phar in "' . $file . '"', E_USER_ERROR);
        }
        if (is_array($this->file = $this->extractFile($path))) {
            trigger_error($this->file[0], E_USER_ERROR);
            return false;
        }
        if ($path != $this->currentFilename) {
            if (!$searchForDir) {
                trigger_error("Cannot open '$file', is a directory", E_USER_ERROR);
                return false;
            } else {
                $this->file = '';
                return true;
            }
        }

        if (!is_null($this->file) && $this->file !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read the data - PHP streams API
     *
     * @param int
     * @access private
     */
    public function stream_read($count)
    {
        $ret = substr($this->file, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    /**
     * Whether we've hit the end of the file - PHP streams API
     * @access private
     */
    function stream_eof()
    {
        return $this->position >= $this->currentStat[7];
    }

    /**
     * For seeking the stream - PHP streams API
     * @param int
     * @param SEEK_SET|SEEK_CUR|SEEK_END
     * @access private
     */
    public function stream_seek($pos, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($pos < 0) {
                    return false;
                }
                $this->position = $pos;
                break;
            case SEEK_CUR:
                if ($pos + $this->currentStat[7] < 0) {
                    return false;
                }
                $this->position += $pos;
                break;
            case SEEK_END:
                if ($pos + $this->currentStat[7] < 0) {
                    return false;
                }
                $this->position = $pos + $this->currentStat[7];
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * The current position in the stream - PHP streams API
     * @access private
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * The result of an fstat call, returns mod time from creation, and file size -
     * PHP streams API
     * @uses _stream_stat()
     * @access private
     */
    public function stream_stat()
    {
        return $this->_stream_stat();
    }

    /**
     * Retrieve statistics on a file or directory within the .phar
     * @param string file/directory to stat
     * @access private
     */
    public function _stream_stat($file = null)
    {
        $std = $file ? self::processFile($file) : $this->currentFilename;
        if ($file) {
            if (isset(self::$_manifest[$this->_archiveName][$file])) {
                $this->_setCurrentFile($file);
                $isdir = false;
            } else {
                do {
                    $isdir = false;
                    if ($file == '/') {
                        break;
                    }
                    foreach (self::$_manifest[$this->_archiveName] as $path => $info) {
                        if (strpos($path, $file) === 0) {
                            if (strlen($path) > strlen($file) &&
                                  $path[strlen($file)] == '/') {
                                break 2;
                            }
                        }
                    }
                    // no files exist and no directories match this string
                    return false;
                } while (false);
                $isdir = true;
            }
        } else {
            $isdir = false; // open streams must be files
        }
        $mode = $isdir ? 0040444 : 0100444;
        // 040000 = dir, 010000 = file
        // everything is readable, nothing is writeable
        return array(
           0, 0, $mode, 0, 0, 0, 0, 0, 0, 0, 0, 0, // non-associative indices
           'dev' => 0, 'ino' => 0,
           'mode' => $mode,
           'nlink' => 0, 'uid' => 0, 'gid' => 0, 'rdev' => 0, 'blksize' => 0, 'blocks' => 0,
           'size' => $this->currentStat[7],
           'atime' => $this->currentStat[9],
           'mtime' => $this->currentStat[9],
           'ctime' => $this->currentStat[9],
           );
    }

    /**
     * Stat a closed file or directory - PHP streams API
     * @param string
     * @param int
     * @access private
     */
    public function url_stat($url, $flags)
    {
        $path = $this->initializeStream($url);
        return $this->_stream_stat($path);
    }

    /**
     * Open a directory in the .phar for reading - PHP streams API
     * @param string directory name
     * @access private
     */
    public function dir_opendir($path)
    {
        $info = @parse_url($path);
        if (!$info) {
            $info = self::parseUrl($path);
            if (!$info) {
                trigger_error('Error: "' . $path . '" is a file, and cannot be opened with opendir',
                    E_USER_ERROR);
                return false;
            }
        }
        $path = !empty($info['path']) ?
            $info['host'] . $info['path'] : $info['host'] . '/';
        $path = $this->initializeStream('phar://' . $path);
        if (isset(self::$_manifest[$this->_archiveName][$path])) {
            trigger_error('Error: "' . $path . '" is a file, and cannot be opened with opendir',
                E_USER_ERROR);
            return false;
        }
        if ($path == false) {
            trigger_error('Error: Unknown phar in "' . $file . '"', E_USER_ERROR);
            return false;
        }
        $this->fp = @fopen($this->_archiveName, "rb");
        if (!$this->fp) {
            trigger_error('Error: cannot open phar "' . $this->_archiveName . '"');
            return false;
        }
        $this->_dirFiles = array();
        foreach (self::$_manifest[$this->_archiveName] as $file => $info) {
            if ($path == '/') {
                if (strpos($file, '/')) {
                    $a = explode('/', $file);
                    $this->_dirFiles[array_shift($a)] = true;
                } else {
                    $this->_dirFiles[$file] = true;
                }
            } elseif (strpos($file, $path) === 0) {
                $fname = substr($file, strlen($path) + 1);
                if ($fname == '/' || $fname[strlen($fname)-1] == '/') {
                    continue; // empty directory
                }
                if (strpos($fname, '/')) {
                    // this is a directory
                    $a = explode('/', $fname);
                    $this->_dirFiles[array_shift($a)] = true;
                } elseif ($file[strlen($path)] == '/') {
                    // this is a file
                    $this->_dirFiles[$fname] = true;
                }
            }
        }
        @fclose($this->fp);
        if (!count($this->_dirFiles)) {
            return false;
        }
        @uksort($this->_dirFiles, 'strnatcmp');
        return true;
    }

    /**
     * Read the next directory entry - PHP streams API
     * @access private
     */
    public function dir_readdir()
    {
        $ret = key($this->_dirFiles);
        @next($this->_dirFiles);
        if (!$ret) {
            return false;
        }
        return $ret;
    }

    /**
     * Close a directory handle opened with opendir() - PHP streams API
     * @access private
     */
    public function dir_closedir()
    {
        $this->_dirFiles = array();
        return true;
    }

    /**
     * Rewind to the first directory entry - PHP streams API
     * @access private
     */
    public function dir_rewinddir()
    {
        @reset($this->_dirFiles);
        return true;
    }

    /**
     * API version of this class
     * @return string
     */
    public static final function APIVersion()
    {
        return '1.1.0';
    }

    /**
     * Retrieve Phar-specific metadata for a Phar archive
     *
     * @param string $phar full path to Phar archive, or alias
     * @return null|mixed The value that was serialized for the Phar
     *                    archive's metadata
     * @throws Exception
     */
    public static function getPharMetadata($phar)
    {
        if (isset(self::$_pharFiles[$phar])) {
            $phar = self::$_pharFiles[$phar];
        }
        if (!isset(self::$_pharMapping[$phar])) {
            throw new Exception('Unknown Phar archive: "' . $phar . '"');
        }
        return self::$_pharMapping[$phar][4];
    }

    /**
     * Retrieve File-specific metadata for a Phar archive file
     *
     * @param string $phar full path to Phar archive, or alias
     * @param string $file relative path to file within Phar archive
     * @return null|mixed The value that was serialized for the Phar
     *                    archive's metadata
     * @throws Exception
     */
    public static function getFileMetadata($phar, $file)
    {
        if (!isset(self::$_pharFiles[$phar])) {
            if (!isset(self::$_pharMapping[$phar])) {
                throw new Exception('Unknown Phar archive: "' . $phar . '"');
            }
            $phar = self::$_pharMapping[$phar][0];
        }
        if (!isset(self::$_manifest[$phar])) {
            throw new Exception('Unknown Phar: "' . $phar . '"');
        }
        $file = self::processFile($file);
        if (!isset(self::$_manifest[$phar][$file])) {
            throw new Exception('Unknown file "' . $file . '" within Phar "'. $phar . '"');
        }
        return self::$_manifest[$phar][$file][6];
    }

    /**
     * @return list of supported signature algorithmns.
     */
    public static function getSupportedSignatures()
    {
        $ret = array('MD5', 'SHA-1');
        if (extension_loaded('hash')) {
            $ret[] = 'SHA-256';
            $ret[] = 'SHA-512';
        }
        if (extension_loaded('openssl')) {
            $ret[] = 'OpenSSL';
        }
        return $ret;
    }
}}
if (!class_exists('Phar')) {
    PHP_Archive::mapPhar(null, 43508                   );
} else {
    try {
        Phar::mapPhar();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
if (class_exists('PHP_Archive') && !in_array('phar', stream_get_wrappers())) {
    stream_wrapper_register('phar', 'PHP_Archive');
}

@ini_set('memory_limit', -1);
if (extension_loaded('phar')) {if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI']);
    $archive = realpath($_SERVER['SCRIPT_FILENAME']);
    $subpath = str_replace('/' . basename($archive), '', $uri['path']);
    $mimetypes = array (
  'aif' => 'audio/x-aiff',
  'aiff' => 'audio/x-aiff',
  'arc' => 'application/octet-stream',
  'arj' => 'application/octet-stream',
  'art' => 'image/x-jg',
  'asf' => 'video/x-ms-asf',
  'asx' => 'video/x-ms-asf',
  'avi' => 'video/avi',
  'bin' => 'application/octet-stream',
  'bm' => 'image/bmp',
  'bmp' => 'image/bmp',
  'bz2' => 'application/x-bzip2',
  'css' => 'text/css',
  'doc' => 'application/msword',
  'dot' => 'application/msword',
  'dv' => 'video/x-dv',
  'dvi' => 'application/x-dvi',
  'eps' => 'application/postscript',
  'exe' => 'application/octet-stream',
  'gif' => 'image/gif',
  'gz' => 'application/x-gzip',
  'gzip' => 'application/x-gzip',
  'htm' => 'text/html',
  'html' => 'text/html',
  'ico' => 'image/x-icon',
  'jpe' => 'image/jpeg',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg',
  'js' => 'application/x-javascript',
  'log' => 'text/plain',
  'mid' => 'audio/x-midi',
  'mov' => 'video/quicktime',
  'mp2' => 'audio/mpeg',
  'mp3' => 'audio/mpeg3',
  'mpg' => 'audio/mpeg',
  'pdf' => 'aplication/pdf',
  'png' => 'image/png',
  'rtf' => 'application/rtf',
  'tif' => 'image/tiff',
  'tiff' => 'image/tiff',
  'txt' => 'text/plain',
  'xml' => 'text/xml',
);
    $phpfiles = array (
  'php' => true,
);
    $phpsfiles = array (
  'phps' => true,
);
    $deny = array (
  0 => '/.+\\.inc$/',
);
    $subpath = str_replace('/' . basename($archive), '', $uri['path']);
    if (!$subpath || $subpath == '/') {
        $subpath = '/PEAR.php';
    }
    if ($subpath[0] != '/') {
        $subpath = '/' . $subpath;
    }
    if (!@file_exists('phar://' . $archive . $subpath)) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    foreach ($deny as $pattern) {
        if (preg_match($pattern, $subpath)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }
    $inf = pathinfo(basename($subpath));
    if (!isset($inf['extension'])) {
        header('Content-Type: text/plain');
        header('Content-Length: ' . filesize('phar://' . $archive . $subpath));
        readfile('phar://' . $archive . $subpath);
        exit;
    }
    if (isset($phpfiles[$inf['extension']])) {
        include 'phar://' . $archive . '/' . $subpath;
        exit;
    }
    if (isset($mimetypes[$inf['extension']])) {
        header('Content-Type: ' . $mimetypes[$inf['extension']]);
        header('Content-Length: ' . filesize('phar://' . $archive . $subpath));
        readfile('phar://' . $archive . $subpath);
        exit;
    }
    if (isset($phpsfiles[$inf['extension']])) {
        header('Content-Type: text/html');
        $c = highlight_file('phar://' . $archive . $subpath, true);
        header('Content-Length: ' . strlen($c));
        echo $c;
        exit;
    }
    header('Content-Type: text/plain');
    header('Content-Length: ' . filesize('phar://' . $archive . '/' . $subpath));
    readfile('phar://' . $archive . '/' . $subpath);
    exit;
}} else {if (!empty($_SERVER['REQUEST_URI'])) {PHP_Archive::webFrontController('PEAR.php');exit;}}



require_once 'phar://go-pear.phar/index.php';
__HALT_COMPILER();=��E����������go-pear.phar�������Archive/Tar.php�@��."V�@��e��m���������Console/Getopt.php}4���."V}4��e��om������   ���index.php�����."V����A�#m���������OS/Guess.phps)���."Vs)���� m���������PEAR.php�����."V�������Gm���������PEAR/ChannelFile.phpJ����."VJ�������m���������PEAR/ChannelFile/Parser.php����."V���ʄv�m���������PEAR/Command.php�0���."V�0���^�9m���������PEAR/Command/Common.php6 ���."V6 ������m���������PEAR/Command/Install.php�����."V�������m���������PEAR/Command/Install.xml~!���."V~!��2�Vm���������PEAR/Common.phpGh���."VGh���iTm���������PEAR/Config.php��."V�k��m���������PEAR/Dependency2.phpz����."Vz����`�m���������PEAR/DependencyDB.php*^���."V*^��K-�m���������PEAR/Downloader.php��."V���u*m���������PEAR/Downloader/Package.php3*��."V3*��%�m���������PEAR/ErrorStack.php����."V���R�f�m���������PEAR/Frontend.php
���."V
��1�)�m���������PEAR/Frontend/CLI.phped���."Ved���m������+���PEAR/go-pear-tarballs/Archive_Tar-1.4.0.tar����."V���i�hm������.���PEAR/go-pear-tarballs/Console_Getopt-1.4.1.tar�t���."V�t���H��m������%���PEAR/go-pear-tarballs/PEAR-1.10.1.tar�(��."V�(���%.m������0���PEAR/go-pear-tarballs/Structures_Graph-1.1.1.tar�6��."V�6���j�m������(���PEAR/go-pear-tarballs/XML_Util-1.3.0.tar����."V���&jHFm���������PEAR/Installer.phpR��."VR�#� lm���������PEAR/Installer/Role.php���."V��Il�lm���������PEAR/Installer/Role/Common.phpF���."VF�����m���������PEAR/Installer/Role/Data.php���."V��z(��m���������PEAR/Installer/Role/Data.xml����."V���f�szm���������PEAR/Installer/Role/Doc.php���."V��㔺mm���������PEAR/Installer/Role/Doc.xml����."V���h&P*m���������PEAR/Installer/Role/Php.php���."V��*1��m���������PEAR/Installer/Role/Php.xml����."V���z�q�m���������PEAR/Installer/Role/Script.php���."V��J0H�m���������PEAR/Installer/Role/Script.xml����."V���@v��m���������PEAR/Installer/Role/Test.php���."V�����m���������PEAR/Installer/Role/Test.xml����."V���B] m���������PEAR/PackageFile.phpZ>���."VZ>��ns�m������!���PEAR/PackageFile/Generator/v1.php�����."V����WYJ�m������!���PEAR/PackageFile/Generator/v2.php����."V���M
�-m���������PEAR/PackageFile/Parser/v1.php�@���."V�@����Юm���������PEAR/PackageFile/Parser/v2.phpv���."Vv��j���m���������PEAR/PackageFile/v1.php����."V���ى3�m���������PEAR/PackageFile/v2.php���."V�����m������!���PEAR/PackageFile/v2/Validator.phpzL��."VzL���m���������PEAR/Registry.phpq)��."Vq)���@
m������
���PEAR/REST.php"F���."V"F��@ �m���������PEAR/REST/10.php����."V����C;m���������PEAR/Start.php�9���."V�9��3��m���������PEAR/Start/CLI.phpHS���."VHS��>iZ m���������PEAR/Task/Common.php7���."V7���4�[m���������PEAR/Task/Postinstallscript.phpG9���."VG9���H>�m������"���PEAR/Task/Postinstallscript/rw.php;���."V;��<�Hm���������PEAR/Task/Replace.php���."V������m���������PEAR/Task/Replace/rw.php/���."V/���ξSm���������PEAR/Task/Unixeol.php ���."V ��
t�8m���������PEAR/Task/Unixeol/rw.php4���."V4��AAO�m���������PEAR/Task/Windowseol.php    ���."V ���<��m���������PEAR/Task/Windowseol/rw.phpA���."VA���1g�m���������PEAR/Validate.php&V���."V&V���e�.m���������PEAR/Validator/PECL.phpQ���."VQ��K�]m���������PEAR/XMLParser.php0���."V0���>@�m���������Structures/Graph.phpQ���."VQ��4�T�m������,���Structures/Graph/Manipulator/AcyclicTest.php����."V���
�;�m������2���Structures/Graph/Manipulator/TopologicalSorter.php����."V���3�sm���������Structures/Graph/Node.phpR+���."VR+��5~�m������
���System.phprO���."VrO���
��m���������XML/Util.php\w���."V\w����    �m������<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::CSV
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 1997-2008,
 * Vincent Blavet <vincent@phpconcept.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  File_Formats
 * @package   Archive_Tar
 * @author    Vincent Blavet <vincent@phpconcept.net>
 * @copyright 1997-2010 The Authors
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Archive_Tar
 */

require_once 'phar://go-pear.phar/' . 'PEAR.php';

define('ARCHIVE_TAR_ATT_SEPARATOR', 90001);
define('ARCHIVE_TAR_END_BLOCK', pack("a512", ''));

if (!function_exists('gzopen') && function_exists('gzopen64')) {
    function gzopen($filename, $mode, $use_include_path = 0)
    {
        return gzopen64($filename, $mode, $use_include_path);
    }
}

if (!function_exists('gztell') && function_exists('gztell64')) {
    function gztell($zp)
    {
        return gztell64($zp);
    }
}

if (!function_exists('gzseek') && function_exists('gzseek64')) {
    function gzseek($zp, $offset, $whence = SEEK_SET)
    {
        return gzseek64($zp, $offset, $whence);
    }
}

/**
 * Creates a (compressed) Tar archive
 *
 * @package Archive_Tar
 * @author  Vincent Blavet <vincent@phpconcept.net>
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version $Revision$
 */
class Archive_Tar extends PEAR
{
    /**
     * @var string Name of the Tar
     */
    public $_tarname = '';

    /**
     * @var boolean if true, the Tar file will be gzipped
     */
    public $_compress = false;

    /**
     * @var string Type of compression : 'none', 'gz', 'bz2' or 'lzma2'
     */
    public $_compress_type = 'none';

    /**
     * @var string Explode separator
     */
    public $_separator = ' ';

    /**
     * @var file descriptor
     */
    public $_file = 0;

    /**
     * @var string Local Tar name of a remote Tar (http:// or ftp://)
     */
    public $_temp_tarname = '';

    /**
     * @var string regular expression for ignoring files or directories
     */
    public $_ignore_regexp = '';

    /**
     * @var object PEAR_Error object
     */
    public $error_object = null;

    /**
     * Archive_Tar Class constructor. This flavour of the constructor only
     * declare a new Archive_Tar object, identifying it by the name of the
     * tar file.
     * If the compress argument is set the tar will be read or created as a
     * gzip or bz2 compressed TAR file.
     *
     * @param string $p_tarname The name of the tar archive to create
     * @param string $p_compress can be null, 'gz', 'bz2' or 'lzma2'. This
     *               parameter indicates if gzip, bz2 or lzma2 compression
     *               is required.  For compatibility reason the
     *               boolean value 'true' means 'gz'.
     *
     * @return bool
     */
    public function __construct($p_tarname, $p_compress = null)
    {
        parent::__construct();

        $this->_compress = false;
        $this->_compress_type = 'none';
        if (($p_compress === null) || ($p_compress == '')) {
            if (@file_exists($p_tarname)) {
                if ($fp = @fopen($p_tarname, "rb")) {
                    // look for gzip magic cookie
                    $data = fread($fp, 2);
                    fclose($fp);
                    if ($data == "\37\213") {
                        $this->_compress = true;
                        $this->_compress_type = 'gz';
                        // No sure it's enought for a magic code ....
                    } elseif ($data == "BZ") {
                        $this->_compress = true;
                        $this->_compress_type = 'bz2';
                    } elseif (file_get_contents($p_tarname, false, null, 1, 4) == '7zXZ') {
                        $this->_compress = true;
                        $this->_compress_type = 'lzma2';
                    }
                }
            } else {
                // probably a remote file or some file accessible
                // through a stream interface
                if (substr($p_tarname, -2) == 'gz') {
                    $this->_compress = true;
                    $this->_compress_type = 'gz';
                } elseif ((substr($p_tarname, -3) == 'bz2') ||
                    (substr($p_tarname, -2) == 'bz')
                ) {
                    $this->_compress = true;
                    $this->_compress_type = 'bz2';
                } else {
                    if (substr($p_tarname, -2) == 'xz') {
                        $this->_compress = true;
                        $this->_compress_type = 'lzma2';
                    }
                }
            }
        } else {
            if (($p_compress === true) || ($p_compress == 'gz')) {
                $this->_compress = true;
                $this->_compress_type = 'gz';
            } else {
                if ($p_compress == 'bz2') {
                    $this->_compress = true;
                    $this->_compress_type = 'bz2';
                } else {
                    if ($p_compress == 'lzma2') {
                        $this->_compress = true;
                        $this->_compress_type = 'lzma2';
                    } else {
                        $this->_error(
                            "Unsupported compression type '$p_compress'\n" .
                            "Supported types are 'gz', 'bz2' and 'lzma2'.\n"
                        );
                        return false;
                    }
                }
            }
        }
        $this->_tarname = $p_tarname;
        if ($this->_compress) { // assert zlib or bz2 or xz extension support
            if ($this->_compress_type == 'gz') {
                $extname = 'zlib';
            } else {
                if ($this->_compress_type == 'bz2') {
                    $extname = 'bz2';
                } else {
                    if ($this->_compress_type == 'lzma2') {
                        $extname = 'xz';
                    }
                }
            }

            if (!extension_loaded($extname)) {
                PEAR::loadExtension($extname);
            }
            if (!extension_loaded($extname)) {
                $this->_error(
                    "The extension '$extname' couldn't be found.\n" .
                    "Please make sure your version of PHP was built " .
                    "with '$extname' support.\n"
                );
                return false;
            }
        }
    }

    public function __destruct()
    {
        $this->_close();
        // ----- Look for a local copy to delete
        if ($this->_temp_tarname != '') {
            @unlink($this->_temp_tarname);
        }
    }

    /**
     * This method creates the archive file and add the files / directories
     * that are listed in $p_filelist.
     * If a file with the same name exist and is writable, it is replaced
     * by the new tar.
     * The method return false and a PEAR error text.
     * The $p_filelist parameter can be an array of string, each string
     * representing a filename or a directory name with their path if
     * needed. It can also be a single string with names separated by a
     * single blank.
     * For each directory added in the archive, the files and
     * sub-directories are also added.
     * See also createModify() method for more details.
     *
     * @param array $p_filelist An array of filenames and directory names, or a
     *              single string with names separated by a single
     *              blank space.
     *
     * @return true on success, false on error.
     * @see    createModify()
     */
    public function create($p_filelist)
    {
        return $this->createModify($p_filelist, '', '');
    }

    /**
     * This method add the files / directories that are listed in $p_filelist in
     * the archive. If the archive does not exist it is created.
     * The method return false and a PEAR error text.
     * The files and directories listed are only added at the end of the archive,
     * even if a file with the same name is already archived.
     * See also createModify() method for more details.
     *
     * @param array $p_filelist An array of filenames and directory names, or a
     *              single string with names separated by a single
     *              blank space.
     *
     * @return true on success, false on error.
     * @see    createModify()
     * @access public
     */
    public function add($p_filelist)
    {
        return $this->addModify($p_filelist, '', '');
    }

    /**
     * @param string $p_path
     * @param bool $p_preserve
     * @return bool
     */
    public function extract($p_path = '', $p_preserve = false)
    {
        return $this->extractModify($p_path, '', $p_preserve);
    }

    /**
     * @return array|int
     */
    public function listContent()
    {
        $v_list_detail = array();

        if ($this->_openRead()) {
            if (!$this->_extractList('', $v_list_detail, "list", '', '')) {
                unset($v_list_detail);
                $v_list_detail = 0;
            }
            $this->_close();
        }

        return $v_list_detail;
    }

    /**
     * This method creates the archive file and add the files / directories
     * that are listed in $p_filelist.
     * If the file already exists and is writable, it is replaced by the
     * new tar. It is a create and not an add. If the file exists and is
     * read-only or is a directory it is not replaced. The method return
     * false and a PEAR error text.
     * The $p_filelist parameter can be an array of string, each string
     * representing a filename or a directory name with their path if
     * needed. It can also be a single string with names separated by a
     * single blank.
     * The path indicated in $p_remove_dir will be removed from the
     * memorized path of each file / directory listed when this path
     * exists. By default nothing is removed (empty path '')
     * The path indicated in $p_add_dir will be added at the beginning of
     * the memorized path of each file / directory listed. However it can
     * be set to empty ''. The adding of a path is done after the removing
     * of path.
     * The path add/remove ability enables the user to prepare an archive
     * for extraction in a different path than the origin files are.
     * See also addModify() method for file adding properties.
     *
     * @param array $p_filelist An array of filenames and directory names,
     *                             or a single string with names separated by
     *                             a single blank space.
     * @param string $p_add_dir A string which contains a path to be added
     *                             to the memorized path of each element in
     *                             the list.
     * @param string $p_remove_dir A string which contains a path to be
     *                             removed from the memorized path of each
     *                             element in the list, when relevant.
     *
     * @return boolean true on success, false on error.
     * @see addModify()
     */
    public function createModify($p_filelist, $p_add_dir, $p_remove_dir = '')
    {
        $v_result = true;

        if (!$this->_openWrite()) {
            return false;
        }

        if ($p_filelist != '') {
            if (is_array($p_filelist)) {
                $v_list = $p_filelist;
            } elseif (is_string($p_filelist)) {
                $v_list = explode($this->_separator, $p_filelist);
            } else {
                $this->_cleanFile();
                $this->_error('Invalid file list');
                return false;
            }

            $v_result = $this->_addList($v_list, $p_add_dir, $p_remove_dir);
        }

        if ($v_result) {
            $this->_writeFooter();
            $this->_close();
        } else {
            $this->_cleanFile();
        }

        return $v_result;
    }

    /**
     * This method add the files / directories listed in $p_filelist at the
     * end of the existing archive. If the archive does not yet exists it
     * is created.
     * The $p_filelist parameter can be an array of string, each string
     * representing a filename or a directory name with their path if
     * needed. It can also be a single string with names separated by a
     * single blank.
     * The path indicated in $p_remove_dir will be removed from the
     * memorized path of each file / directory listed when this path
     * exists. By default nothing is removed (empty path '')
     * The path indicated in $p_add_dir will be added at the beginning of
     * the memorized path of each file / directory listed. However it can
     * be set to empty ''. The adding of a path is done after the removing
     * of path.
     * The path add/remove ability enables the user to prepare an archive
     * for extraction in a different path than the origin files are.
     * If a file/dir is already in the archive it will only be added at the
     * end of the archive. There is no update of the existing archived
     * file/dir. However while extracting the archive, the last file will
     * replace the first one. This results in a none optimization of the
     * archive size.
     * If a file/dir does not exist the file/dir is ignored. However an
     * error text is send to PEAR error.
     * If a file/dir is not readable the file/dir is ignored. However an
     * error text is send to PEAR error.
     *
     * @param array $p_filelist An array of filenames and directory
     *                             names, or a single string with names
     *                             separated by a single blank space.
     * @param string $p_add_dir A string which contains a path to be
     *                             added to the memorized path of each
     *                             element in the list.
     * @param string $p_remove_dir A string which contains a path to be
     *                             removed from the memorized path of
     *                             each element in the list, when
     *                             relevant.
     *
     * @return true on success, false on error.
     */
    public function addModify($p_filelist, $p_add_dir, $p_remove_dir = '')
    {
        $v_result = true;

        if (!$this->_isArchive()) {
            $v_result = $this->createModify(
                $p_filelist,
                $p_add_dir,
                $p_remove_dir
            );
        } else {
            if (is_array($p_filelist)) {
                $v_list = $p_filelist;
            } elseif (is_string($p_filelist)) {
                $v_list = explode($this->_separator, $p_filelist);
            } else {
                $this->_error('Invalid file list');
                return false;
            }

            $v_result = $this->_append($v_list, $p_add_dir, $p_remove_dir);
        }

        return $v_result;
    }

    /**
     * This method add a single string as a file at the
     * end of the existing archive. If the archive does not yet exists it
     * is created.
     *
     * @param string $p_filename A string which contains the full
     *                           filename path that will be associated
     *                           with the string.
     * @param string $p_string The content of the file added in
     *                           the archive.
     * @param bool|int $p_datetime A custom date/time (unix timestamp)
     *                           for the file (optional).
     * @param array $p_params An array of optional params:
     *                               stamp => the datetime (replaces
     *                                   datetime above if it exists)
     *                               mode => the permissions on the
     *                                   file (600 by default)
     *                               type => is this a link?  See the
     *                                   tar specification for details.
     *                                   (default = regular file)
     *                               uid => the user ID of the file
     *                                   (default = 0 = root)
     *                               gid => the group ID of the file
     *                                   (default = 0 = root)
     *
     * @return true on success, false on error.
     */
    public function addString($p_filename, $p_string, $p_datetime = false, $p_params = array())
    {
        $p_stamp = @$p_params["stamp"] ? $p_params["stamp"] : ($p_datetime ? $p_datetime : time());
        $p_mode = @$p_params["mode"] ? $p_params["mode"] : 0600;
        $p_type = @$p_params["type"] ? $p_params["type"] : "";
        $p_uid = @$p_params["uid"] ? $p_params["uid"] : "";
        $p_gid = @$p_params["gid"] ? $p_params["gid"] : "";
        $v_result = true;

        if (!$this->_isArchive()) {
            if (!$this->_openWrite()) {
                return false;
            }
            $this->_close();
        }

        if (!$this->_openAppend()) {
            return false;
        }

        // Need to check the get back to the temporary file ? ....
        $v_result = $this->_addString($p_filename, $p_string, $p_datetime, $p_params);

        $this->_writeFooter();

        $this->_close();

        return $v_result;
    }

    /**
     * This method extract all the content of the archive in the directory
     * indicated by $p_path. When relevant the memorized path of the
     * files/dir can be modified by removing the $p_remove_path path at the
     * beginning of the file/dir path.
     * While extracting a file, if the directory path does not exists it is
     * created.
     * While extracting a file, if the file already exists it is replaced
     * without looking for last modification date.
     * While extracting a file, if the file already exists and is write
     * protected, the extraction is aborted.
     * While extracting a file, if a directory with the same name already
     * exists, the extraction is aborted.
     * While extracting a directory, if a file with the same name already
     * exists, the extraction is aborted.
     * While extracting a file/directory if the destination directory exist
     * and is write protected, or does not exist but can not be created,
     * the extraction is aborted.
     * If after extraction an extracted file does not show the correct
     * stored file size, the extraction is aborted.
     * When the extraction is aborted, a PEAR error text is set and false
     * is returned. However the result can be a partial extraction that may
     * need to be manually cleaned.
     *
     * @param string $p_path The path of the directory where the
     *                               files/dir need to by extracted.
     * @param string $p_remove_path Part of the memorized path that can be
     *                               removed if present at the beginning of
     *                               the file/dir path.
     * @param boolean $p_preserve Preserve user/group ownership of files
     *
     * @return boolean true on success, false on error.
     * @see    extractList()
     */
    public function extractModify($p_path, $p_remove_path, $p_preserve = false)
    {
        $v_result = true;
        $v_list_detail = array();

        if ($v_result = $this->_openRead()) {
            $v_result = $this->_extractList(
                $p_path,
                $v_list_detail,
                "complete",
                0,
                $p_remove_path,
                $p_preserve
            );
            $this->_close();
        }

        return $v_result;
    }

    /**
     * This method extract from the archive one file identified by $p_filename.
     * The return value is a string with the file content, or NULL on error.
     *
     * @param string $p_filename The path of the file to extract in a string.
     *
     * @return a string with the file content or NULL.
     */
    public function extractInString($p_filename)
    {
        if ($this->_openRead()) {
            $v_result = $this->_extractInString($p_filename);
            $this->_close();
        } else {
            $v_result = null;
        }

        return $v_result;
    }

    /**
     * This method extract from the archive only the files indicated in the
     * $p_filelist. These files are extracted in the current directory or
     * in the directory indicated by the optional $p_path parameter.
     * If indicated the $p_remove_path can be used in the same way as it is
     * used in extractModify() method.
     *
     * @param array $p_filelist An array of filenames and directory names,
     *                               or a single string with names separated
     *                               by a single blank space.
     * @param string $p_path The path of the directory where the
     *                               files/dir need to by extracted.
     * @param string $p_remove_path Part of the memorized path that can be
     *                               removed if present at the beginning of
     *                               the file/dir path.
     * @param boolean $p_preserve Preserve user/group ownership of files
     *
     * @return true on success, false on error.
     * @see    extractModify()
     */
    public function extractList($p_filelist, $p_path = '', $p_remove_path = '', $p_preserve = false)
    {
        $v_result = true;
        $v_list_detail = array();

        if (is_array($p_filelist)) {
            $v_list = $p_filelist;
        } elseif (is_string($p_filelist)) {
            $v_list = explode($this->_separator, $p_filelist);
        } else {
            $this->_error('Invalid string list');
            return false;
        }

        if ($v_result = $this->_openRead()) {
            $v_result = $this->_extractList(
                $p_path,
                $v_list_detail,
                "partial",
                $v_list,
                $p_remove_path,
                $p_preserve
            );
            $this->_close();
        }

        return $v_result;
    }

    /**
     * This method set specific attributes of the archive. It uses a variable
     * list of parameters, in the format attribute code + attribute values :
     * $arch->setAttribute(ARCHIVE_TAR_ATT_SEPARATOR, ',');
     *
     * @return true on success, false on error.
     */
    public function setAttribute()
    {
        $v_result = true;

        // ----- Get the number of variable list of arguments
        if (($v_size = func_num_args()) == 0) {
            return true;
        }

        // ----- Get the arguments
        $v_att_list = & func_get_args();

        // ----- Read the attributes
        $i = 0;
        while ($i < $v_size) {

            // ----- Look for next option
            switch ($v_att_list[$i]) {
                // ----- Look for options that request a string value
                case ARCHIVE_TAR_ATT_SEPARATOR :
                    // ----- Check the number of parameters
                    if (($i + 1) >= $v_size) {
                        $this->_error(
                            'Invalid number of parameters for '
                            . 'attribute ARCHIVE_TAR_ATT_SEPARATOR'
                        );
                        return false;
                    }

                    // ----- Get the value
                    $this->_separator = $v_att_list[$i + 1];
                    $i++;
                    break;

                default :
                    $this->_error('Unknown attribute code ' . $v_att_list[$i] . '');
                    return false;
            }

            // ----- Next attribute
            $i++;
        }

        return $v_result;
    }

    /**
     * This method sets the regular expression for ignoring files and directories
     * at import, for example:
     * $arch->setIgnoreRegexp("#CVS|\.svn#");
     *
     * @param string $regexp regular expression defining which files or directories to ignore
     */
    public function setIgnoreRegexp($regexp)
    {
        $this->_ignore_regexp = $regexp;
    }

    /**
     * This method sets the regular expression for ignoring all files and directories
     * matching the filenames in the array list at import, for example:
     * $arch->setIgnoreList(array('CVS', '.svn', 'bin/tool'));
     *
     * @param array $list a list of file or directory names to ignore
     *
     * @access public
     */
    public function setIgnoreList($list)
    {
        $regexp = str_replace(array('#', '.', '^', '$'), array('\#', '\.', '\^', '\$'), $list);
        $regexp = '#/' . join('$|/', $list) . '#';
        $this->setIgnoreRegexp($regexp);
    }

    /**
     * @param string $p_message
     */
    public function _error($p_message)
    {
        $this->error_object = $this->raiseError($p_message);
    }

    /**
     * @param string $p_message
     */
    public function _warning($p_message)
    {
        $this->error_object = $this->raiseError($p_message);
    }

    /**
     * @param string $p_filename
     * @return bool
     */
    public function _isArchive($p_filename = null)
    {
        if ($p_filename == null) {
            $p_filename = $this->_tarname;
        }
        clearstatcache();
        return @is_file($p_filename) && !@is_link($p_filename);
    }

    /**
     * @return bool
     */
    public function _openWrite()
    {
        if ($this->_compress_type == 'gz' && function_exists('gzopen')) {
            $this->_file = @gzopen($this->_tarname, "wb9");
        } else {
            if ($this->_compress_type == 'bz2' && function_exists('bzopen')) {
                $this->_file = @bzopen($this->_tarname, "w");
            } else {
                if ($this->_compress_type == 'lzma2' && function_exists('xzopen')) {
                    $this->_file = @xzopen($this->_tarname, 'w');
                } else {
                    if ($this->_compress_type == 'none') {
                        $this->_file = @fopen($this->_tarname, "wb");
                    } else {
                        $this->_error(
                            'Unknown or missing compression type ('
                            . $this->_compress_type . ')'
                        );
                        return false;
                    }
                }
            }
        }

        if ($this->_file == 0) {
            $this->_error(
                'Unable to open in write mode \''
                . $this->_tarname . '\''
            );
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _openRead()
    {
        if (strtolower(substr($this->_tarname, 0, 7)) == 'http://') {

            // ----- Look if a local copy need to be done
            if ($this->_temp_tarname == '') {
                $this->_temp_tarname = uniqid('tar') . '.tmp';
                if (!$v_file_from = @fopen($this->_tarname, 'rb')) {
                    $this->_error(
                        'Unable to open in read mode \''
                        . $this->_tarname . '\''
                    );
                    $this->_temp_tarname = '';
                    return false;
                }
                if (!$v_file_to = @fopen($this->_temp_tarname, 'wb')) {
                    $this->_error(
                        'Unable to open in write mode \''
                        . $this->_temp_tarname . '\''
                    );
                    $this->_temp_tarname = '';
                    return false;
                }
                while ($v_data = @fread($v_file_from, 1024)) {
                    @fwrite($v_file_to, $v_data);
                }
                @fclose($v_file_from);
                @fclose($v_file_to);
            }

            // ----- File to open if the local copy
            $v_filename = $this->_temp_tarname;
        } else {
            // ----- File to open if the normal Tar file

            $v_filename = $this->_tarname;
        }

        if ($this->_compress_type == 'gz' && function_exists('gzopen')) {
            $this->_file = @gzopen($v_filename, "rb");
        } else {
            if ($this->_compress_type == 'bz2' && function_exists('bzopen')) {
                $this->_file = @bzopen($v_filename, "r");
            } else {
                if ($this->_compress_type == 'lzma2' && function_exists('xzopen')) {
                    $this->_file = @xzopen($v_filename, "r");
                } else {
                    if ($this->_compress_type == 'none') {
                        $this->_file = @fopen($v_filename, "rb");
                    } else {
                        $this->_error(
                            'Unknown or missing compression type ('
                            . $this->_compress_type . ')'
                        );
                        return false;
                    }
                }
            }
        }

        if ($this->_file == 0) {
            $this->_error('Unable to open in read mode \'' . $v_filename . '\'');
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _openReadWrite()
    {
        if ($this->_compress_type == 'gz') {
            $this->_file = @gzopen($this->_tarname, "r+b");
        } else {
            if ($this->_compress_type == 'bz2') {
                $this->_error(
                    'Unable to open bz2 in read/write mode \''
                    . $this->_tarname . '\' (limitation of bz2 extension)'
                );
                return false;
            } else {
                if ($this->_compress_type == 'lzma2') {
                    $this->_error(
                        'Unable to open lzma2 in read/write mode \''
                        . $this->_tarname . '\' (limitation of lzma2 extension)'
                    );
                    return false;
                } else {
                    if ($this->_compress_type == 'none') {
                        $this->_file = @fopen($this->_tarname, "r+b");
                    } else {
                        $this->_error(
                            'Unknown or missing compression type ('
                            . $this->_compress_type . ')'
                        );
                        return false;
                    }
                }
            }
        }

        if ($this->_file == 0) {
            $this->_error(
                'Unable to open in read/write mode \''
                . $this->_tarname . '\''
            );
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _close()
    {
        //if (isset($this->_file)) {
        if (is_resource($this->_file)) {
            if ($this->_compress_type == 'gz') {
                @gzclose($this->_file);
            } else {
                if ($this->_compress_type == 'bz2') {
                    @bzclose($this->_file);
                } else {
                    if ($this->_compress_type == 'lzma2') {
                        @xzclose($this->_file);
                    } else {
                        if ($this->_compress_type == 'none') {
                            @fclose($this->_file);
                        } else {
                            $this->_error(
                                'Unknown or missing compression type ('
                                . $this->_compress_type . ')'
                            );
                        }
                    }
                }
            }

            $this->_file = 0;
        }

        // ----- Look if a local copy need to be erase
        // Note that it might be interesting to keep the url for a time : ToDo
        if ($this->_temp_tarname != '') {
            @unlink($this->_temp_tarname);
            $this->_temp_tarname = '';
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _cleanFile()
    {
        $this->_close();

        // ----- Look for a local copy
        if ($this->_temp_tarname != '') {
            // ----- Remove the local copy but not the remote tarname
            @unlink($this->_temp_tarname);
            $this->_temp_tarname = '';
        } else {
            // ----- Remove the local tarname file
            @unlink($this->_tarname);
        }
        $this->_tarname = '';

        return true;
    }

    /**
     * @param mixed $p_binary_data
     * @param integer $p_len
     * @return bool
     */
    public function _writeBlock($p_binary_data, $p_len = null)
    {
        if (is_resource($this->_file)) {
            if ($p_len === null) {
                if ($this->_compress_type == 'gz') {
                    @gzputs($this->_file, $p_binary_data);
                } else {
                    if ($this->_compress_type == 'bz2') {
                        @bzwrite($this->_file, $p_binary_data);
                    } else {
                        if ($this->_compress_type == 'lzma2') {
                            @xzwrite($this->_file, $p_binary_data);
                        } else {
                            if ($this->_compress_type == 'none') {
                                @fputs($this->_file, $p_binary_data);
                            } else {
                                $this->_error(
                                    'Unknown or missing compression type ('
                                    . $this->_compress_type . ')'
                                );
                            }
                        }
                    }
                }
            } else {
                if ($this->_compress_type == 'gz') {
                    @gzputs($this->_file, $p_binary_data, $p_len);
                } else {
                    if ($this->_compress_type == 'bz2') {
                        @bzwrite($this->_file, $p_binary_data, $p_len);
                    } else {
                        if ($this->_compress_type == 'lzma2') {
                            @xzwrite($this->_file, $p_binary_data, $p_len);
                        } else {
                            if ($this->_compress_type == 'none') {
                                @fputs($this->_file, $p_binary_data, $p_len);
                            } else {
                                $this->_error(
                                    'Unknown or missing compression type ('
                                    . $this->_compress_type . ')'
                                );
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return null|string
     */
    public function _readBlock()
    {
        $v_block = null;
        if (is_resource($this->_file)) {
            if ($this->_compress_type == 'gz') {
                $v_block = @gzread($this->_file, 512);
            } else {
                if ($this->_compress_type == 'bz2') {
                    $v_block = @bzread($this->_file, 512);
                } else {
                    if ($this->_compress_type == 'lzma2') {
                        $v_block = @xzread($this->_file, 512);
                    } else {
                        if ($this->_compress_type == 'none') {
                            $v_block = @fread($this->_file, 512);
                        } else {
                            $this->_error(
                                'Unknown or missing compression type ('
                                . $this->_compress_type . ')'
                            );
                        }
                    }
                }
            }
        }
        return $v_block;
    }

    /**
     * @param null $p_len
     * @return bool
     */
    public function _jumpBlock($p_len = null)
    {
        if (is_resource($this->_file)) {
            if ($p_len === null) {
                $p_len = 1;
            }

            if ($this->_compress_type == 'gz') {
                @gzseek($this->_file, gztell($this->_file) + ($p_len * 512));
            } else {
                if ($this->_compress_type == 'bz2') {
                    // ----- Replace missing bztell() and bzseek()
                    for ($i = 0; $i < $p_len; $i++) {
                        $this->_readBlock();
                    }
                } else {
                    if ($this->_compress_type == 'lzma2') {
                        // ----- Replace missing xztell() and xzseek()
                        for ($i = 0; $i < $p_len; $i++) {
                            $this->_readBlock();
                        }
                    } else {
                        if ($this->_compress_type == 'none') {
                            @fseek($this->_file, $p_len * 512, SEEK_CUR);
                        } else {
                            $this->_error(
                                'Unknown or missing compression type ('
                                . $this->_compress_type . ')'
                            );
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function _writeFooter()
    {
        if (is_resource($this->_file)) {
            // ----- Write the last 0 filled block for end of archive
            $v_binary_data = pack('a1024', '');
            $this->_writeBlock($v_binary_data);
        }
        return true;
    }

    /**
     * @param array $p_list
     * @param string $p_add_dir
     * @param string $p_remove_dir
     * @return bool
     */
    public function _addList($p_list, $p_add_dir, $p_remove_dir)
    {
        $v_result = true;
        $v_header = array();

        // ----- Remove potential windows directory separator
        $p_add_dir = $this->_translateWinPath($p_add_dir);
        $p_remove_dir = $this->_translateWinPath($p_remove_dir, false);

        if (!$this->_file) {
            $this->_error('Invalid file descriptor');
            return false;
        }

        if (sizeof($p_list) == 0) {
            return true;
        }

        foreach ($p_list as $v_filename) {
            if (!$v_result) {
                break;
            }

            // ----- Skip the current tar name
            if ($v_filename == $this->_tarname) {
                continue;
            }

            if ($v_filename == '') {
                continue;
            }

            // ----- ignore files and directories matching the ignore regular expression
            if ($this->_ignore_regexp && preg_match($this->_ignore_regexp, '/' . $v_filename)) {
                $this->_warning("File '$v_filename' ignored");
                continue;
            }

            if (!file_exists($v_filename) && !is_link($v_filename)) {
                $this->_warning("File '$v_filename' does not exist");
                continue;
            }

            // ----- Add the file or directory header
            if (!$this->_addFile($v_filename, $v_header, $p_add_dir, $p_remove_dir)) {
                return false;
            }

            if (@is_dir($v_filename) && !@is_link($v_filename)) {
                if (!($p_hdir = opendir($v_filename))) {
                    $this->_warning("Directory '$v_filename' can not be read");
                    continue;
                }
                while (false !== ($p_hitem = readdir($p_hdir))) {
                    if (($p_hitem != '.') && ($p_hitem != '..')) {
                        if ($v_filename != ".") {
                            $p_temp_list[0] = $v_filename . '/' . $p_hitem;
                        } else {
                            $p_temp_list[0] = $p_hitem;
                        }

                        $v_result = $this->_addList(
                            $p_temp_list,
                            $p_add_dir,
                            $p_remove_dir
                        );
                    }
                }

                unset($p_temp_list);
                unset($p_hdir);
                unset($p_hitem);
            }
        }

        return $v_result;
    }

    /**
     * @param string $p_filename
     * @param mixed $p_header
     * @param string $p_add_dir
     * @param string $p_remove_dir
     * @param null $v_stored_filename
     * @return bool
     */
    public function _addFile($p_filename, &$p_header, $p_add_dir, $p_remove_dir, $v_stored_filename = null)
    {
        if (!$this->_file) {
            $this->_error('Invalid file descriptor');
            return false;
        }

        if ($p_filename == '') {
            $this->_error('Invalid file name');
            return false;
        }

        if (is_null($v_stored_filename)) {
            // ----- Calculate the stored filename
            $p_filename = $this->_translateWinPath($p_filename, false);
            $v_stored_filename = $p_filename;

            if (strcmp($p_filename, $p_remove_dir) == 0) {
                return true;
            }

            if ($p_remove_dir != '') {
                if (substr($p_remove_dir, -1) != '/') {
                    $p_remove_dir .= '/';
                }

                if (substr($p_filename, 0, strlen($p_remove_dir)) == $p_remove_dir) {
                    $v_stored_filename = substr($p_filename, strlen($p_remove_dir));
                }
            }

            $v_stored_filename = $this->_translateWinPath($v_stored_filename);
            if ($p_add_dir != '') {
                if (substr($p_add_dir, -1) == '/') {
                    $v_stored_filename = $p_add_dir . $v_stored_filename;
                } else {
                    $v_stored_filename = $p_add_dir . '/' . $v_stored_filename;
                }
            }

            $v_stored_filename = $this->_pathReduction($v_stored_filename);
        }

        if ($this->_isArchive($p_filename)) {
            if (($v_file = @fopen($p_filename, "rb")) == 0) {
                $this->_warning(
                    "Unable to open file '" . $p_filename
                    . "' in binary read mode"
                );
                return true;
            }

            if (!$this->_writeHeader($p_filename, $v_stored_filename)) {
                return false;
            }

            while (($v_buffer = fread($v_file, 512)) != '') {
                $v_binary_data = pack("a512", "$v_buffer");
                $this->_writeBlock($v_binary_data);
            }

            fclose($v_file);
        } else {
            // ----- Only header for dir
            if (!$this->_writeHeader($p_filename, $v_stored_filename)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $p_filename
     * @param string $p_string
     * @param bool $p_datetime
     * @param array $p_params
     * @return bool
     */
    public function _addString($p_filename, $p_string, $p_datetime = false, $p_params = array())
    {
        $p_stamp = @$p_params["stamp"] ? $p_params["stamp"] : ($p_datetime ? $p_datetime : time());
        $p_mode = @$p_params["mode"] ? $p_params["mode"] : 0600;
        $p_type = @$p_params["type"] ? $p_params["type"] : "";
        $p_uid = @$p_params["uid"] ? $p_params["uid"] : 0;
        $p_gid = @$p_params["gid"] ? $p_params["gid"] : 0;
        if (!$this->_file) {
            $this->_error('Invalid file descriptor');
            return false;
        }

        if ($p_filename == '') {
            $this->_error('Invalid file name');
            return false;
        }

        // ----- Calculate the stored filename
        $p_filename = $this->_translateWinPath($p_filename, false);

        // ----- If datetime is not specified, set current time
        if ($p_datetime === false) {
            $p_datetime = time();
        }

        if (!$this->_writeHeaderBlock(
            $p_filename,
            strlen($p_string),
            $p_stamp,
            $p_mode,
            $p_type,
            $p_uid,
            $p_gid
        )
        ) {
            return false;
        }

        $i = 0;
        while (($v_buffer = substr($p_string, (($i++) * 512), 512)) != '') {
            $v_binary_data = pack("a512", $v_buffer);
            $this->_writeBlock($v_binary_data);
        }

        return true;
    }

    /**
     * @param string $p_filename
     * @param string $p_stored_filename
     * @return bool
     */
    public function _writeHeader($p_filename, $p_stored_filename)
    {
        if ($p_stored_filename == '') {
            $p_stored_filename = $p_filename;
        }
        $v_reduce_filename = $this->_pathReduction($p_stored_filename);

        if (strlen($v_reduce_filename) > 99) {
            if (!$this->_writeLongHeader($v_reduce_filename)) {
                return false;
            }
        }

        $v_info = lstat($p_filename);
        $v_uid = sprintf("%07s", DecOct($v_info[4]));
        $v_gid = sprintf("%07s", DecOct($v_info[5]));
        $v_perms = sprintf("%07s", DecOct($v_info['mode'] & 000777));

        $v_mtime = sprintf("%011s", DecOct($v_info['mtime']));

        $v_linkname = '';

        if (@is_link($p_filename)) {
            $v_typeflag = '2';
            $v_linkname = readlink($p_filename);
            $v_size = sprintf("%011s", DecOct(0));
        } elseif (@is_dir($p_filename)) {
            $v_typeflag = "5";
            $v_size = sprintf("%011s", DecOct(0));
        } else {
            $v_typeflag = '0';
            clearstatcache();
            $v_size = sprintf("%011s", DecOct($v_info['size']));
        }

        $v_magic = 'ustar ';

        $v_version = ' ';

        if (function_exists('posix_getpwuid')) {
            $userinfo = posix_getpwuid($v_info[4]);
            $groupinfo = posix_getgrgid($v_info[5]);

            $v_uname = $userinfo['name'];
            $v_gname = $groupinfo['name'];
        } else {
            $v_uname = '';
            $v_gname = '';
        }

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack(
            "a100a8a8a8a12a12",
            $v_reduce_filename,
            $v_perms,
            $v_uid,
            $v_gid,
            $v_size,
            $v_mtime
        );
        $v_binary_data_last = pack(
            "a1a100a6a2a32a32a8a8a155a12",
            $v_typeflag,
            $v_linkname,
            $v_magic,
            $v_version,
            $v_uname,
            $v_gname,
            $v_devmajor,
            $v_devminor,
            $v_prefix,
            ''
        );

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i = 0; $i < 148; $i++) {
            $v_checksum += ord(substr($v_binary_data_first, $i, 1));
        }
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i = 148; $i < 156; $i++) {
            $v_checksum += ord(' ');
        }
        // ..... Last part of the header
        for ($i = 156, $j = 0; $i < 512; $i++, $j++) {
            $v_checksum += ord(substr($v_binary_data_last, $j, 1));
        }

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%06s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        return true;
    }

    /**
     * @param string $p_filename
     * @param int $p_size
     * @param int $p_mtime
     * @param int $p_perms
     * @param string $p_type
     * @param int $p_uid
     * @param int $p_gid
     * @return bool
     */
    public function _writeHeaderBlock(
        $p_filename,
        $p_size,
        $p_mtime = 0,
        $p_perms = 0,
        $p_type = '',
        $p_uid = 0,
        $p_gid = 0
    ) {
        $p_filename = $this->_pathReduction($p_filename);

        if (strlen($p_filename) > 99) {
            if (!$this->_writeLongHeader($p_filename)) {
                return false;
            }
        }

        if ($p_type == "5") {
            $v_size = sprintf("%011s", DecOct(0));
        } else {
            $v_size = sprintf("%011s", DecOct($p_size));
        }

        $v_uid = sprintf("%07s", DecOct($p_uid));
        $v_gid = sprintf("%07s", DecOct($p_gid));
        $v_perms = sprintf("%07s", DecOct($p_perms & 000777));

        $v_mtime = sprintf("%11s", DecOct($p_mtime));

        $v_linkname = '';

        $v_magic = 'ustar ';

        $v_version = ' ';

        if (function_exists('posix_getpwuid')) {
            $userinfo = posix_getpwuid($p_uid);
            $groupinfo = posix_getgrgid($p_gid);

            $v_uname = $userinfo['name'];
            $v_gname = $groupinfo['name'];
        } else {
            $v_uname = '';
            $v_gname = '';
        }

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack(
            "a100a8a8a8a12A12",
            $p_filename,
            $v_perms,
            $v_uid,
            $v_gid,
            $v_size,
            $v_mtime
        );
        $v_binary_data_last = pack(
            "a1a100a6a2a32a32a8a8a155a12",
            $p_type,
            $v_linkname,
            $v_magic,
            $v_version,
            $v_uname,
            $v_gname,
            $v_devmajor,
            $v_devminor,
            $v_prefix,
            ''
        );

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i = 0; $i < 148; $i++) {
            $v_checksum += ord(substr($v_binary_data_first, $i, 1));
        }
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i = 148; $i < 156; $i++) {
            $v_checksum += ord(' ');
        }
        // ..... Last part of the header
        for ($i = 156, $j = 0; $i < 512; $i++, $j++) {
            $v_checksum += ord(substr($v_binary_data_last, $j, 1));
        }

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%06s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        return true;
    }

    /**
     * @param string $p_filename
     * @return bool
     */
    public function _writeLongHeader($p_filename)
    {
        $v_size = sprintf("%11s ", DecOct(strlen($p_filename)));

        $v_typeflag = 'L';

        $v_linkname = '';

        $v_magic = '';

        $v_version = '';

        $v_uname = '';

        $v_gname = '';

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack(
            "a100a8a8a8a12a12",
            '././@LongLink',
            0,
            0,
            0,
            $v_size,
            0
        );
        $v_binary_data_last = pack(
            "a1a100a6a2a32a32a8a8a155a12",
            $v_typeflag,
            $v_linkname,
            $v_magic,
            $v_version,
            $v_uname,
            $v_gname,
            $v_devmajor,
            $v_devminor,
            $v_prefix,
            ''
        );

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i = 0; $i < 148; $i++) {
            $v_checksum += ord(substr($v_binary_data_first, $i, 1));
        }
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i = 148; $i < 156; $i++) {
            $v_checksum += ord(' ');
        }
        // ..... Last part of the header
        for ($i = 156, $j = 0; $i < 512; $i++, $j++) {
            $v_checksum += ord(substr($v_binary_data_last, $j, 1));
        }

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%06s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        // ----- Write the filename as content of the block
        $i = 0;
        while (($v_buffer = substr($p_filename, (($i++) * 512), 512)) != '') {
            $v_binary_data = pack("a512", "$v_buffer");
            $this->_writeBlock($v_binary_data);
        }

        return true;
    }

    /**
     * @param mixed $v_binary_data
     * @param mixed $v_header
     * @return bool
     */
    public function _readHeader($v_binary_data, &$v_header)
    {
        if (strlen($v_binary_data) == 0) {
            $v_header['filename'] = '';
            return true;
        }

        if (strlen($v_binary_data) != 512) {
            $v_header['filename'] = '';
            $this->_error('Invalid block size : ' . strlen($v_binary_data));
            return false;
        }

        if (!is_array($v_header)) {
            $v_header = array();
        }
        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i = 0; $i < 148; $i++) {
            $v_checksum += ord(substr($v_binary_data, $i, 1));
        }
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i = 148; $i < 156; $i++) {
            $v_checksum += ord(' ');
        }
        // ..... Last part of the header
        for ($i = 156; $i < 512; $i++) {
            $v_checksum += ord(substr($v_binary_data, $i, 1));
        }

        if (version_compare(PHP_VERSION, "5.5.0-dev") < 0) {
            $fmt = "a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/" .
                "a8checksum/a1typeflag/a100link/a6magic/a2version/" .
                "a32uname/a32gname/a8devmajor/a8devminor/a131prefix";
        } else {
            $fmt = "Z100filename/Z8mode/Z8uid/Z8gid/Z12size/Z12mtime/" .
                "Z8checksum/Z1typeflag/Z100link/Z6magic/Z2version/" .
                "Z32uname/Z32gname/Z8devmajor/Z8devminor/Z131prefix";
        }
        $v_data = unpack($fmt, $v_binary_data);

        if (strlen($v_data["prefix"]) > 0) {
            $v_data["filename"] = "$v_data[prefix]/$v_data[filename]";
        }

        // ----- Extract the checksum
        $v_header['checksum'] = OctDec(trim($v_data['checksum']));
        if ($v_header['checksum'] != $v_checksum) {
            $v_header['filename'] = '';

            // ----- Look for last block (empty block)
            if (($v_checksum == 256) && ($v_header['checksum'] == 0)) {
                return true;
            }

            $this->_error(
                'Invalid checksum for file "' . $v_data['filename']
                . '" : ' . $v_checksum . ' calculated, '
                . $v_header['checksum'] . ' expected'
            );
            return false;
        }

        // ----- Extract the properties
        $v_header['filename'] = rtrim($v_data['filename'], "\0");
        if ($this->_maliciousFilename($v_header['filename'])) {
            $this->_error(
                'Malicious .tar detected, file "' . $v_header['filename'] .
                '" will not install in desired directory tree'
            );
            return false;
        }
        $v_header['mode'] = OctDec(trim($v_data['mode']));
        $v_header['uid'] = OctDec(trim($v_data['uid']));
        $v_header['gid'] = OctDec(trim($v_data['gid']));
        $v_header['size'] = OctDec(trim($v_data['size']));
        $v_header['mtime'] = OctDec(trim($v_data['mtime']));
        if (($v_header['typeflag'] = $v_data['typeflag']) == "5") {
            $v_header['size'] = 0;
        }
        $v_header['link'] = trim($v_data['link']);
        /* ----- All these fields are removed form the header because
        they do not carry interesting info
        $v_header[magic] = trim($v_data[magic]);
        $v_header[version] = trim($v_data[version]);
        $v_header[uname] = trim($v_data[uname]);
        $v_header[gname] = trim($v_data[gname]);
        $v_header[devmajor] = trim($v_data[devmajor]);
        $v_header[devminor] = trim($v_data[devminor]);
        */

        return true;
    }

    /**
     * Detect and report a malicious file name
     *
     * @param string $file
     *
     * @return bool
     */
    private function _maliciousFilename($file)
    {
        if (strpos($file, '/../') !== false) {
            return true;
        }
        if (strpos($file, '../') === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $v_header
     * @return bool
     */
    public function _readLongHeader(&$v_header)
    {
        $v_filename = '';
        $v_filesize = $v_header['size'];
        $n = floor($v_header['size'] / 512);
        for ($i = 0; $i < $n; $i++) {
            $v_content = $this->_readBlock();
            $v_filename .= $v_content;
        }
        if (($v_header['size'] % 512) != 0) {
            $v_content = $this->_readBlock();
            $v_filename .= $v_content;
        }

        // ----- Read the next header
        $v_binary_data = $this->_readBlock();

        if (!$this->_readHeader($v_binary_data, $v_header)) {
            return false;
        }

        $v_filename = rtrim(substr($v_filename, 0, $v_filesize), "\0");
        $v_header['filename'] = $v_filename;
        if ($this->_maliciousFilename($v_filename)) {
            $this->_error(
                'Malicious .tar detected, file "' . $v_filename .
                '" will not install in desired directory tree'
            );
            return false;
        }

        return true;
    }

    /**
     * This method extract from the archive one file identified by $p_filename.
     * The return value is a string with the file content, or null on error.
     *
     * @param string $p_filename The path of the file to extract in a string.
     *
     * @return a string with the file content or null.
     */
    private function _extractInString($p_filename)
    {
        $v_result_str = "";

        while (strlen($v_binary_data = $this->_readBlock()) != 0) {
            if (!$this->_readHeader($v_binary_data, $v_header)) {
                return null;
            }

            if ($v_header['filename'] == '') {
                continue;
            }

            // ----- Look for long filename
            if ($v_header['typeflag'] == 'L') {
                if (!$this->_readLongHeader($v_header)) {
                    return null;
                }
            }

            if ($v_header['filename'] == $p_filename) {
                if ($v_header['typeflag'] == "5") {
                    $this->_error(
                        'Unable to extract in string a directory '
                        . 'entry {' . $v_header['filename'] . '}'
                    );
                    return null;
                } else {
                    $n = floor($v_header['size'] / 512);
                    for ($i = 0; $i < $n; $i++) {
                        $v_result_str .= $this->_readBlock();
                    }
                    if (($v_header['size'] % 512) != 0) {
                        $v_content = $this->_readBlock();
                        $v_result_str .= substr(
                            $v_content,
                            0,
                            ($v_header['size'] % 512)
                        );
                    }
                    return $v_result_str;
                }
            } else {
                $this->_jumpBlock(ceil(($v_header['size'] / 512)));
            }
        }

        return null;
    }

    /**
     * @param string $p_path
     * @param string $p_list_detail
     * @param string $p_mode
     * @param string $p_file_list
     * @param string $p_remove_path
     * @param bool $p_preserve
     * @return bool
     */
    public function _extractList(
        $p_path,
        &$p_list_detail,
        $p_mode,
        $p_file_list,
        $p_remove_path,
        $p_preserve = false
    ) {
        $v_result = true;
        $v_nb = 0;
        $v_extract_all = true;
        $v_listing = false;

        $p_path = $this->_translateWinPath($p_path, false);
        if ($p_path == '' || (substr($p_path, 0, 1) != '/'
                && substr($p_path, 0, 3) != "../" && !strpos($p_path, ':'))
        ) {
            $p_path = "./" . $p_path;
        }
        $p_remove_path = $this->_translateWinPath($p_remove_path);

        // ----- Look for path to remove format (should end by /)
        if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/')) {
            $p_remove_path .= '/';
        }
        $p_remove_path_size = strlen($p_remove_path);

        switch ($p_mode) {
            case "complete" :
                $v_extract_all = true;
                $v_listing = false;
                break;
            case "partial" :
                $v_extract_all = false;
                $v_listing = false;
                break;
            case "list" :
                $v_extract_all = false;
                $v_listing = true;
                break;
            default :
                $this->_error('Invalid extract mode (' . $p_mode . ')');
                return false;
        }

        clearstatcache();

        while (strlen($v_binary_data = $this->_readBlock()) != 0) {
            $v_extract_file = false;
            $v_extraction_stopped = 0;

            if (!$this->_readHeader($v_binary_data, $v_header)) {
                return false;
            }

            if ($v_header['filename'] == '') {
                continue;
            }

            // ----- Look for long filename
            if ($v_header['typeflag'] == 'L') {
                if (!$this->_readLongHeader($v_header)) {
                    return false;
                }
            }

            // ignore extended / pax headers
            if ($v_header['typeflag'] == 'x' || $v_header['typeflag'] == 'g') {
                $this->_jumpBlock(ceil(($v_header['size'] / 512)));
                continue;
            }

            if ((!$v_extract_all) && (is_array($p_file_list))) {
                // ----- By default no unzip if the file is not found
                $v_extract_file = false;

                for ($i = 0; $i < sizeof($p_file_list); $i++) {
                    // ----- Look if it is a directory
                    if (substr($p_file_list[$i], -1) == '/') {
                        // ----- Look if the directory is in the filename path
                        if ((strlen($v_header['filename']) > strlen($p_file_list[$i]))
                            && (substr($v_header['filename'], 0, strlen($p_file_list[$i]))
                                == $p_file_list[$i])
                        ) {
                            $v_extract_file = true;
                            break;
                        }
                    } // ----- It is a file, so compare the file names
                    elseif ($p_file_list[$i] == $v_header['filename']) {
                        $v_extract_file = true;
                        break;
                    }
                }
            } else {
                $v_extract_file = true;
            }

            // ----- Look if this file need to be extracted
            if (($v_extract_file) && (!$v_listing)) {
                if (($p_remove_path != '')
                    && (substr($v_header['filename'] . '/', 0, $p_remove_path_size)
                        == $p_remove_path)
                ) {
                    $v_header['filename'] = substr(
                        $v_header['filename'],
                        $p_remove_path_size
                    );
                    if ($v_header['filename'] == '') {
                        continue;
                    }
                }
                if (($p_path != './') && ($p_path != '/')) {
                    while (substr($p_path, -1) == '/') {
                        $p_path = substr($p_path, 0, strlen($p_path) - 1);
                    }

                    if (substr($v_header['filename'], 0, 1) == '/') {
                        $v_header['filename'] = $p_path . $v_header['filename'];
                    } else {
                        $v_header['filename'] = $p_path . '/' . $v_header['filename'];
                    }
                }
                if (file_exists($v_header['filename'])) {
                    if ((@is_dir($v_header['filename']))
                        && ($v_header['typeflag'] == '')
                    ) {
                        $this->_error(
                            'File ' . $v_header['filename']
                            . ' already exists as a directory'
                        );
                        return false;
                    }
                    if (($this->_isArchive($v_header['filename']))
                        && ($v_header['typeflag'] == "5")
                    ) {
                        $this->_error(
                            'Directory ' . $v_header['filename']
                            . ' already exists as a file'
                        );
                        return false;
                    }
                    if (!is_writeable($v_header['filename'])) {
                        $this->_error(
                            'File ' . $v_header['filename']
                            . ' already exists and is write protected'
                        );
                        return false;
                    }
                    if (filemtime($v_header['filename']) > $v_header['mtime']) {
                        // To be completed : An error or silent no replace ?
                    }
                } // ----- Check the directory availability and create it if necessary
                elseif (($v_result
                        = $this->_dirCheck(
                        ($v_header['typeflag'] == "5"
                            ? $v_header['filename']
                            : dirname($v_header['filename']))
                    )) != 1
                ) {
                    $this->_error('Unable to create path for ' . $v_header['filename']);
                    return false;
                }

                if ($v_extract_file) {
                    if ($v_header['typeflag'] == "5") {
                        if (!@file_exists($v_header['filename'])) {
                            if (!@mkdir($v_header['filename'], 0777)) {
                                $this->_error(
                                    'Unable to create directory {'
                                    . $v_header['filename'] . '}'
                                );
                                return false;
                            }
                        }
                    } elseif ($v_header['typeflag'] == "2") {
                        if (@file_exists($v_header['filename'])) {
                            @unlink($v_header['filename']);
                        }
                        if (!@symlink($v_header['link'], $v_header['filename'])) {
                            $this->_error(
                                'Unable to extract symbolic link {'
                                . $v_header['filename'] . '}'
                            );
                            return false;
                        }
                    } else {
                        if (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0) {
                            $this->_error(
                                'Error while opening {' . $v_header['filename']
                                . '} in write binary mode'
                            );
                            return false;
                        } else {
                            $n = floor($v_header['size'] / 512);
                            for ($i = 0; $i < $n; $i++) {
                                $v_content = $this->_readBlock();
                                fwrite($v_dest_file, $v_content, 512);
                            }
                            if (($v_header['size'] % 512) != 0) {
                                $v_content = $this->_readBlock();
                                fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
                            }

                            @fclose($v_dest_file);

                            if ($p_preserve) {
                                @chown($v_header['filename'], $v_header['uid']);
                                @chgrp($v_header['filename'], $v_header['gid']);
                            }

                            // ----- Change the file mode, mtime
                            @touch($v_header['filename'], $v_header['mtime']);
                            if ($v_header['mode'] & 0111) {
                                // make file executable, obey umask
                                $mode = fileperms($v_header['filename']) | (~umask() & 0111);
                                @chmod($v_header['filename'], $mode);
                            }
                        }

                        // ----- Check the file size
                        clearstatcache();
                        if (!is_file($v_header['filename'])) {
                            $this->_error(
                                'Extracted file ' . $v_header['filename']
                                . 'does not exist. Archive may be corrupted.'
                            );
                            return false;
                        }

                        $filesize = filesize($v_header['filename']);
                        if ($filesize != $v_header['size']) {
                            $this->_error(
                                'Extracted file ' . $v_header['filename']
                                . ' does not have the correct file size \''
                                . $filesize
                                . '\' (' . $v_header['size']
                                . ' expected). Archive may be corrupted.'
                            );
                            return false;
                        }
                    }
                } else {
                    $this->_jumpBlock(ceil(($v_header['size'] / 512)));
                }
            } else {
                $this->_jumpBlock(ceil(($v_header['size'] / 512)));
            }

            /* TBC : Seems to be unused ...
            if ($this->_compress)
              $v_end_of_file = @gzeof($this->_file);
            else
              $v_end_of_file = @feof($this->_file);
              */

            if ($v_listing || $v_extract_file || $v_extraction_stopped) {
                // ----- Log extracted files
                if (($v_file_dir = dirname($v_header['filename']))
                    == $v_header['filename']
                ) {
                    $v_file_dir = '';
                }
                if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == '')) {
                    $v_file_dir = '/';
                }

                $p_list_detail[$v_nb++] = $v_header;
                if (is_array($p_file_list) && (count($p_list_detail) == count($p_file_list))) {
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _openAppend()
    {
        if (filesize($this->_tarname) == 0) {
            return $this->_openWrite();
        }

        if ($this->_compress) {
            $this->_close();

            if (!@rename($this->_tarname, $this->_tarname . ".tmp")) {
                $this->_error(
                    'Error while renaming \'' . $this->_tarname
                    . '\' to temporary file \'' . $this->_tarname
                    . '.tmp\''
                );
                return false;
            }

            if ($this->_compress_type == 'gz') {
                $v_temp_tar = @gzopen($this->_tarname . ".tmp", "rb");
            } elseif ($this->_compress_type == 'bz2') {
                $v_temp_tar = @bzopen($this->_tarname . ".tmp", "r");
            } elseif ($this->_compress_type == 'lzma2') {
                $v_temp_tar = @xzopen($this->_tarname . ".tmp", "r");
            }


            if ($v_temp_tar == 0) {
                $this->_error(
                    'Unable to open file \'' . $this->_tarname
                    . '.tmp\' in binary read mode'
                );
                @rename($this->_tarname . ".tmp", $this->_tarname);
                return false;
            }

            if (!$this->_openWrite()) {
                @rename($this->_tarname . ".tmp", $this->_tarname);
                return false;
            }

            if ($this->_compress_type == 'gz') {
                $end_blocks = 0;

                while (!@gzeof($v_temp_tar)) {
                    $v_buffer = @gzread($v_temp_tar, 512);
                    if ($v_buffer == ARCHIVE_TAR_END_BLOCK || strlen($v_buffer) == 0) {
                        $end_blocks++;
                        // do not copy end blocks, we will re-make them
                        // after appending
                        continue;
                    } elseif ($end_blocks > 0) {
                        for ($i = 0; $i < $end_blocks; $i++) {
                            $this->_writeBlock(ARCHIVE_TAR_END_BLOCK);
                        }
                        $end_blocks = 0;
                    }
                    $v_binary_data = pack("a512", $v_buffer);
                    $this->_writeBlock($v_binary_data);
                }

                @gzclose($v_temp_tar);
            } elseif ($this->_compress_type == 'bz2') {
                $end_blocks = 0;

                while (strlen($v_buffer = @bzread($v_temp_tar, 512)) > 0) {
                    if ($v_buffer == ARCHIVE_TAR_END_BLOCK || strlen($v_buffer) == 0) {
                        $end_blocks++;
                        // do not copy end blocks, we will re-make them
                        // after appending
                        continue;
                    } elseif ($end_blocks > 0) {
                        for ($i = 0; $i < $end_blocks; $i++) {
                            $this->_writeBlock(ARCHIVE_TAR_END_BLOCK);
                        }
                        $end_blocks = 0;
                    }
                    $v_binary_data = pack("a512", $v_buffer);
                    $this->_writeBlock($v_binary_data);
                }

                @bzclose($v_temp_tar);
            } elseif ($this->_compress_type == 'lzma2') {
                $end_blocks = 0;

                while (strlen($v_buffer = @xzread($v_temp_tar, 512)) > 0) {
                    if ($v_buffer == ARCHIVE_TAR_END_BLOCK || strlen($v_buffer) == 0) {
                        $end_blocks++;
                        // do not copy end blocks, we will re-make them
                        // after appending
                        continue;
                    } elseif ($end_blocks > 0) {
                        for ($i = 0; $i < $end_blocks; $i++) {
                            $this->_writeBlock(ARCHIVE_TAR_END_BLOCK);
                        }
                        $end_blocks = 0;
                    }
                    $v_binary_data = pack("a512", $v_buffer);
                    $this->_writeBlock($v_binary_data);
                }

                @xzclose($v_temp_tar);
            }

            if (!@unlink($this->_tarname . ".tmp")) {
                $this->_error(
                    'Error while deleting temporary file \''
                    . $this->_tarname . '.tmp\''
                );
            }
        } else {
            // ----- For not compressed tar, just add files before the last
            //       one or two 512 bytes block
            if (!$this->_openReadWrite()) {
                return false;
            }

            clearstatcache();
            $v_size = filesize($this->_tarname);

            // We might have zero, one or two end blocks.
            // The standard is two, but we should try to handle
            // other cases.
            fseek($this->_file, $v_size - 1024);
            if (fread($this->_file, 512) == ARCHIVE_TAR_END_BLOCK) {
                fseek($this->_file, $v_size - 1024);
            } elseif (fread($this->_file, 512) == ARCHIVE_TAR_END_BLOCK) {
                fseek($this->_file, $v_size - 512);
            }
        }

        return true;
    }

    /**
     * @param $p_filelist
     * @param string $p_add_dir
     * @param string $p_remove_dir
     * @return bool
     */
    public function _append($p_filelist, $p_add_dir = '', $p_remove_dir = '')
    {
        if (!$this->_openAppend()) {
            return false;
        }

        if ($this->_addList($p_filelist, $p_add_dir, $p_remove_dir)) {
            $this->_writeFooter();
        }

        $this->_close();

        return true;
    }

    /**
     * Check if a directory exists and create it (including parent
     * dirs) if not.
     *
     * @param string $p_dir directory to check
     *
     * @return bool true if the directory exists or was created
     */
    public function _dirCheck($p_dir)
    {
        clearstatcache();
        if ((@is_dir($p_dir)) || ($p_dir == '')) {
            return true;
        }

        $p_parent_dir = dirname($p_dir);

        if (($p_parent_dir != $p_dir) &&
            ($p_parent_dir != '') &&
            (!$this->_dirCheck($p_parent_dir))
        ) {
            return false;
        }

        if (!@mkdir($p_dir, 0777)) {
            $this->_error("Unable to create directory '$p_dir'");
            return false;
        }

        return true;
    }

    /**
     * Compress path by changing for example "/dir/foo/../bar" to "/dir/bar",
     * rand emove double slashes.
     *
     * @param string $p_dir path to reduce
     *
     * @return string reduced path
     */
    private function _pathReduction($p_dir)
    {
        $v_result = '';

        // ----- Look for not empty path
        if ($p_dir != '') {
            // ----- Explode path by directory names
            $v_list = explode('/', $p_dir);

            // ----- Study directories from last to first
            for ($i = sizeof($v_list) - 1; $i >= 0; $i--) {
                // ----- Look for current path
                if ($v_list[$i] == ".") {
                    // ----- Ignore this directory
                    // Should be the first $i=0, but no check is done
                } else {
                    if ($v_list[$i] == "..") {
                        // ----- Ignore it and ignore the $i-1
                        $i--;
                    } else {
                        if (($v_list[$i] == '')
                            && ($i != (sizeof($v_list) - 1))
                            && ($i != 0)
                        ) {
                            // ----- Ignore only the double '//' in path,
                            // but not the first and last /
                        } else {
                            $v_result = $v_list[$i] . ($i != (sizeof($v_list) - 1) ? '/'
                                    . $v_result : '');
                        }
                    }
                }
            }
        }

        if (defined('OS_WINDOWS') && OS_WINDOWS) {
            $v_result = strtr($v_result, '\\', '/');
        }

        return $v_result;
    }

    /**
     * @param $p_path
     * @param bool $p_remove_disk_letter
     * @return string
     */
    public function _translateWinPath($p_path, $p_remove_disk_letter = true)
    {
        if (defined('OS_WINDOWS') && OS_WINDOWS) {
            // ----- Look for potential disk letter
            if (($p_remove_disk_letter)
                && (($v_position = strpos($p_path, ':')) != false)
            ) {
                $p_path = substr($p_path, $v_position + 1);
            }
            // ----- Change potential windows directory separator
            if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\')) {
                $p_path = strtr($p_path, '\\', '/');
            }
        }
        return $p_path;
    }
}
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * PHP Version 5
 *
 * Copyright (c) 1997-2004 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following url:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @category Console
 * @package  Console_Getopt
 * @author   Andrei Zmievski <andrei@php.net>
 * @license  http://www.php.net/license/3_0.txt PHP 3.0
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/Console_Getopt
 */

require_once 'phar://go-pear.phar/' . 'PEAR.php';

/**
 * Command-line options parsing class.
 *
 * @category Console
 * @package  Console_Getopt
 * @author   Andrei Zmievski <andrei@php.net>
 * @license  http://www.php.net/license/3_0.txt PHP 3.0
 * @link     http://pear.php.net/package/Console_Getopt
 */
class Console_Getopt
{

    /**
     * Parses the command-line options.
     *
     * The first parameter to this function should be the list of command-line
     * arguments without the leading reference to the running program.
     *
     * The second parameter is a string of allowed short options. Each of the
     * option letters can be followed by a colon ':' to specify that the option
     * requires an argument, or a double colon '::' to specify that the option
     * takes an optional argument.
     *
     * The third argument is an optional array of allowed long options. The
     * leading '--' should not be included in the option name. Options that
     * require an argument should be followed by '=', and options that take an
     * option argument should be followed by '=='.
     *
     * The return value is an array of two elements: the list of parsed
     * options and the list of non-option command-line arguments. Each entry in
     * the list of parsed options is a pair of elements - the first one
     * specifies the option, and the second one specifies the option argument,
     * if there was one.
     *
     * Long and short options can be mixed.
     *
     * Most of the semantics of this function are based on GNU getopt_long().
     *
     * @param array  $args          an array of command-line arguments
     * @param string $short_options specifies the list of allowed short options
     * @param array  $long_options  specifies the list of allowed long options
     * @param boolean $skip_unknown suppresses Console_Getopt: unrecognized option
     *
     * @return array two-element array containing the list of parsed options and
     * the non-option arguments
     */
    public static function getopt2($args, $short_options, $long_options = null, $skip_unknown = false)
    {
        return Console_Getopt::doGetopt(2, $args, $short_options, $long_options, $skip_unknown);
    }

    /**
     * This function expects $args to start with the script name (POSIX-style).
     * Preserved for backwards compatibility.
     *
     * @param array  $args          an array of command-line arguments
     * @param string $short_options specifies the list of allowed short options
     * @param array  $long_options  specifies the list of allowed long options
     *
     * @see getopt2()
     * @return array two-element array containing the list of parsed options and
     * the non-option arguments
     */
    public static function getopt($args, $short_options, $long_options = null, $skip_unknown = false)
    {
        return Console_Getopt::doGetopt(1, $args, $short_options, $long_options, $skip_unknown);
    }

    /**
     * The actual implementation of the argument parsing code.
     *
     * @param int    $version       Version to use
     * @param array  $args          an array of command-line arguments
     * @param string $short_options specifies the list of allowed short options
     * @param array  $long_options  specifies the list of allowed long options
     * @param boolean $skip_unknown suppresses Console_Getopt: unrecognized option
     *
     * @return array
     */
    public static function doGetopt($version, $args, $short_options, $long_options = null, $skip_unknown = false)
    {
        // in case you pass directly readPHPArgv() as the first arg
        if (PEAR::isError($args)) {
            return $args;
        }

        if (empty($args)) {
            return array(array(), array());
        }

        $non_opts = $opts = array();

        settype($args, 'array');

        if ($long_options) {
            sort($long_options);
        }

        /*
         * Preserve backwards compatibility with callers that relied on
         * erroneous POSIX fix.
         */
        if ($version < 2) {
            if (isset($args[0]{0}) && $args[0]{0} != '-') {
                array_shift($args);
            }
        }

        reset($args);
        while (list($i, $arg) = each($args)) {
            /* The special element '--' means explicit end of
               options. Treat the rest of the arguments as non-options
               and end the loop. */
            if ($arg == '--') {
                $non_opts = array_merge($non_opts, array_slice($args, $i + 1));
                break;
            }

            if ($arg{0} != '-' || (strlen($arg) > 1 && $arg{1} == '-' && !$long_options)) {
                $non_opts = array_merge($non_opts, array_slice($args, $i));
                break;
            } elseif (strlen($arg) > 1 && $arg{1} == '-') {
                $error = Console_Getopt::_parseLongOption(substr($arg, 2),
                                                          $long_options,
                                                          $opts,
                                                          $args,
                                                          $skip_unknown);
                if (PEAR::isError($error)) {
                    return $error;
                }
            } elseif ($arg == '-') {
                // - is stdin
                $non_opts = array_merge($non_opts, array_slice($args, $i));
                break;
            } else {
                $error = Console_Getopt::_parseShortOption(substr($arg, 1),
                                                           $short_options,
                                                           $opts,
                                                           $args,
                                                           $skip_unknown);
                if (PEAR::isError($error)) {
                    return $error;
                }
            }
        }

        return array($opts, $non_opts);
    }

    /**
     * Parse short option
     *
     * @param string     $arg           Argument
     * @param string[]   $short_options Available short options
     * @param string[][] &$opts
     * @param string[]   &$args
     * @param boolean    $skip_unknown suppresses Console_Getopt: unrecognized option
     *
     * @return void
     */
    protected static function _parseShortOption($arg, $short_options, &$opts, &$args, $skip_unknown)
    {
        for ($i = 0; $i < strlen($arg); $i++) {
            $opt     = $arg{$i};
            $opt_arg = null;

            /* Try to find the short option in the specifier string. */
            if (($spec = strstr($short_options, $opt)) === false || $arg{$i} == ':') {
                if ($skip_unknown === true) {
                    break;
                }

                $msg = "Console_Getopt: unrecognized option -- $opt";
                return PEAR::raiseError($msg);
            }

            if (strlen($spec) > 1 && $spec{1} == ':') {
                if (strlen($spec) > 2 && $spec{2} == ':') {
                    if ($i + 1 < strlen($arg)) {
                        /* Option takes an optional argument. Use the remainder of
                           the arg string if there is anything left. */
                        $opts[] = array($opt, substr($arg, $i + 1));
                        break;
                    }
                } else {
                    /* Option requires an argument. Use the remainder of the arg
                       string if there is anything left. */
                    if ($i + 1 < strlen($arg)) {
                        $opts[] = array($opt,  substr($arg, $i + 1));
                        break;
                    } else if (list(, $opt_arg) = each($args)) {
                        /* Else use the next argument. */;
                        if (Console_Getopt::_isShortOpt($opt_arg)
                            || Console_Getopt::_isLongOpt($opt_arg)) {
                            $msg = "option requires an argument --$opt";
                            return PEAR::raiseError("Console_Getopt: " . $msg);
                        }
                    } else {
                        $msg = "option requires an argument --$opt";
                        return PEAR::raiseError("Console_Getopt: " . $msg);
                    }
                }
            }

            $opts[] = array($opt, $opt_arg);
        }
    }

    /**
     * Checks if an argument is a short option
     *
     * @param string $arg Argument to check
     *
     * @return bool
     */
    protected static function _isShortOpt($arg)
    {
        return strlen($arg) == 2 && $arg[0] == '-'
               && preg_match('/[a-zA-Z]/', $arg[1]);
    }

    /**
     * Checks if an argument is a long option
     *
     * @param string $arg Argument to check
     *
     * @return bool
     */
    protected static function _isLongOpt($arg)
    {
        return strlen($arg) > 2 && $arg[0] == '-' && $arg[1] == '-' &&
               preg_match('/[a-zA-Z]+$/', substr($arg, 2));
    }

    /**
     * Parse long option
     *
     * @param string     $arg          Argument
     * @param string[]   $long_options Available long options
     * @param string[][] &$opts
     * @param string[]   &$args
     *
     * @return void|PEAR_Error
     */
    protected static function _parseLongOption($arg, $long_options, &$opts, &$args, $skip_unknown)
    {
        @list($opt, $opt_arg) = explode('=', $arg, 2);

        $opt_len = strlen($opt);

        for ($i = 0; $i < count($long_options); $i++) {
            $long_opt  = $long_options[$i];
            $opt_start = substr($long_opt, 0, $opt_len);

            $long_opt_name = str_replace('=', '', $long_opt);

            /* Option doesn't match. Go on to the next one. */
            if ($long_opt_name != $opt) {
                continue;
            }

            $opt_rest = substr($long_opt, $opt_len);

            /* Check that the options uniquely matches one of the allowed
               options. */
            if ($i + 1 < count($long_options)) {
                $next_option_rest = substr($long_options[$i + 1], $opt_len);
            } else {
                $next_option_rest = '';
            }

            if ($opt_rest != '' && $opt{0} != '=' &&
                $i + 1 < count($long_options) &&
                $opt == substr($long_options[$i+1], 0, $opt_len) &&
                $next_option_rest != '' &&
                $next_option_rest{0} != '=') {

                $msg = "Console_Getopt: option --$opt is ambiguous";
                return PEAR::raiseError($msg);
            }

            if (substr($long_opt, -1) == '=') {
                if (substr($long_opt, -2) != '==') {
                    /* Long option requires an argument.
                       Take the next argument if one wasn't specified. */;
                    if (!strlen($opt_arg) && !(list(, $opt_arg) = each($args))) {
                        $msg = "Console_Getopt: option requires an argument --$opt";
                        return PEAR::raiseError($msg);
                    }

                    if (Console_Getopt::_isShortOpt($opt_arg)
                        || Console_Getopt::_isLongOpt($opt_arg)) {
                        $msg = "Console_Getopt: option requires an argument --$opt";
                        return PEAR::raiseError($msg);
                    }
                }
            } else if ($opt_arg) {
                $msg = "Console_Getopt: option --$opt doesn't allow an argument";
                return PEAR::raiseError($msg);
            }

            $opts[] = array('--' . $opt, $opt_arg);
            return;
        }

        if ($skip_unknown === true) {
            return;
        }

        return PEAR::raiseError("Console_Getopt: unrecognized option --$opt");
    }

    /**
     * Safely read the $argv PHP array across different PHP configurations.
     * Will take care on register_globals and register_argc_argv ini directives
     *
     * @return mixed the $argv PHP array or PEAR error if not registered
     */
    public static function readPHPArgv()
    {
        global $argv;
        if (!is_array($argv)) {
            if (!@is_array($_SERVER['argv'])) {
                if (!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
                    $msg = "Could not read cmd args (register_argc_argv=Off?)";
                    return PEAR::raiseError("Console_Getopt: " . $msg);
                }
                return $GLOBALS['HTTP_SERVER_VARS']['argv'];
            }
            return $_SERVER['argv'];
        }
        return $argv;
    }

}<?php
require_once 'phar://go-pear.phar/PEAR/Start/CLI.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$a = new PEAR_Start_CLI;
$a->run();
?><?php
/**
 * The OS_Guess class
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since PEAR 0.1
 */

// {{{ uname examples

// php_uname() without args returns the same as 'uname -a', or a PHP-custom
// string for Windows.
// PHP versions prior to 4.3 return the uname of the host where PHP was built,
// as of 4.3 it returns the uname of the host running the PHP code.
//
// PC RedHat Linux 7.1:
// Linux host.example.com 2.4.2-2 #1 Sun Apr 8 20:41:30 EDT 2001 i686 unknown
//
// PC Debian Potato:
// Linux host 2.4.17 #2 SMP Tue Feb 12 15:10:04 CET 2002 i686 unknown
//
// PC FreeBSD 3.3:
// FreeBSD host.example.com 3.3-STABLE FreeBSD 3.3-STABLE #0: Mon Feb 21 00:42:31 CET 2000     root@example.com:/usr/src/sys/compile/CONFIG  i386
//
// PC FreeBSD 4.3:
// FreeBSD host.example.com 4.3-RELEASE FreeBSD 4.3-RELEASE #1: Mon Jun 25 11:19:43 EDT 2001     root@example.com:/usr/src/sys/compile/CONFIG  i386
//
// PC FreeBSD 4.5:
// FreeBSD host.example.com 4.5-STABLE FreeBSD 4.5-STABLE #0: Wed Feb  6 23:59:23 CET 2002     root@example.com:/usr/src/sys/compile/CONFIG  i386
//
// PC FreeBSD 4.5 w/uname from GNU shellutils:
// FreeBSD host.example.com 4.5-STABLE FreeBSD 4.5-STABLE #0: Wed Feb  i386 unknown
//
// HP 9000/712 HP-UX 10:
// HP-UX iq B.10.10 A 9000/712 2008429113 two-user license
//
// HP 9000/712 HP-UX 10 w/uname from GNU shellutils:
// HP-UX host B.10.10 A 9000/712 unknown
//
// IBM RS6000/550 AIX 4.3:
// AIX host 3 4 000003531C00
//
// AIX 4.3 w/uname from GNU shellutils:
// AIX host 3 4 000003531C00 unknown
//
// SGI Onyx IRIX 6.5 w/uname from GNU shellutils:
// IRIX64 host 6.5 01091820 IP19 mips
//
// SGI Onyx IRIX 6.5:
// IRIX64 host 6.5 01091820 IP19
//
// SparcStation 20 Solaris 8 w/uname from GNU shellutils:
// SunOS host.example.com 5.8 Generic_108528-12 sun4m sparc
//
// SparcStation 20 Solaris 8:
// SunOS host.example.com 5.8 Generic_108528-12 sun4m sparc SUNW,SPARCstation-20
//
// Mac OS X (Darwin)
// Darwin home-eden.local 7.5.0 Darwin Kernel Version 7.5.0: Thu Aug  5 19:26:16 PDT 2004; root:xnu/xnu-517.7.21.obj~3/RELEASE_PPC  Power Macintosh
//
// Mac OS X early versions
//

// }}}

/* TODO:
 * - define endianness, to allow matchSignature("bigend") etc.
 */

/**
 * Retrieves information about the current operating system
 *
 * This class uses php_uname() to grok information about the current OS
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class OS_Guess
{
    var $sysname;
    var $nodename;
    var $cpu;
    var $release;
    var $extra;

    function __construct($uname = null)
    {
        list($this->sysname,
             $this->release,
             $this->cpu,
             $this->extra,
             $this->nodename) = $this->parseSignature($uname);
    }

    function parseSignature($uname = null)
    {
        static $sysmap = array(
            'HP-UX' => 'hpux',
            'IRIX64' => 'irix',
        );
        static $cpumap = array(
            'i586' => 'i386',
            'i686' => 'i386',
            'ppc' => 'powerpc',
        );
        if ($uname === null) {
            $uname = php_uname();
        }
        $parts = preg_split('/\s+/', trim($uname));
        $n = count($parts);

        $release  = $machine = $cpu = '';
        $sysname  = $parts[0];
        $nodename = $parts[1];
        $cpu      = $parts[$n-1];
        $extra = '';
        if ($cpu == 'unknown') {
            $cpu = $parts[$n - 2];
        }

        switch ($sysname) {
            case 'AIX' :
                $release = "$parts[3].$parts[2]";
                break;
            case 'Windows' :
                switch ($parts[1]) {
                    case '95/98':
                        $release = '9x';
                        break;
                    default:
                        $release = $parts[1];
                        break;
                }
                $cpu = 'i386';
                break;
            case 'Linux' :
                $extra = $this->_detectGlibcVersion();
                // use only the first two digits from the kernel version
                $release = preg_replace('/^([0-9]+\.[0-9]+).*/', '\1', $parts[2]);
                break;
            case 'Mac' :
                $sysname = 'darwin';
                $nodename = $parts[2];
                $release = $parts[3];
                if ($cpu == 'Macintosh') {
                    if ($parts[$n - 2] == 'Power') {
                        $cpu = 'powerpc';
                    }
                }
                break;
            case 'Darwin' :
                if ($cpu == 'Macintosh') {
                    if ($parts[$n - 2] == 'Power') {
                        $cpu = 'powerpc';
                    }
                }
                $release = preg_replace('/^([0-9]+\.[0-9]+).*/', '\1', $parts[2]);
                break;
            default:
                $release = preg_replace('/-.*/', '', $parts[2]);
                break;
        }

        if (isset($sysmap[$sysname])) {
            $sysname = $sysmap[$sysname];
        } else {
            $sysname = strtolower($sysname);
        }
        if (isset($cpumap[$cpu])) {
            $cpu = $cpumap[$cpu];
        }
        return array($sysname, $release, $cpu, $extra, $nodename);
    }

    function _detectGlibcVersion()
    {
        static $glibc = false;
        if ($glibc !== false) {
            return $glibc; // no need to run this multiple times
        }
        $major = $minor = 0;
        include_once 'phar://go-pear.phar/' . "System.php";
        // Use glibc's <features.h> header file to
        // get major and minor version number:
        if (@file_exists('/usr/include/features.h') &&
              @is_readable('/usr/include/features.h')) {
            if (!@file_exists('/usr/bin/cpp') || !@is_executable('/usr/bin/cpp')) {
                $features_file = fopen('/usr/include/features.h', 'rb');
                while (!feof($features_file)) {
                    $line = fgets($features_file, 8192);
                    if (!$line || (strpos($line, '#define') === false)) {
                        continue;
                    }
                    if (strpos($line, '__GLIBC__')) {
                        // major version number #define __GLIBC__ version
                        $line = preg_split('/\s+/', $line);
                        $glibc_major = trim($line[2]);
                        if (isset($glibc_minor)) {
                            break;
                        }
                        continue;
                    }

                    if (strpos($line, '__GLIBC_MINOR__'))  {
                        // got the minor version number
                        // #define __GLIBC_MINOR__ version
                        $line = preg_split('/\s+/', $line);
                        $glibc_minor = trim($line[2]);
                        if (isset($glibc_major)) {
                            break;
                        }
                        continue;
                    }
                }
                fclose($features_file);
                if (!isset($glibc_major) || !isset($glibc_minor)) {
                    return $glibc = '';
                }
                return $glibc = 'glibc' . trim($glibc_major) . "." . trim($glibc_minor) ;
            } // no cpp

            $tmpfile = System::mktemp("glibctest");
            $fp = fopen($tmpfile, "w");
            fwrite($fp, "#include <features.h>\n__GLIBC__ __GLIBC_MINOR__\n");
            fclose($fp);
            $cpp = popen("/usr/bin/cpp $tmpfile", "r");
            while ($line = fgets($cpp, 1024)) {
                if ($line{0} == '#' || trim($line) == '') {
                    continue;
                }

                if (list($major, $minor) = explode(' ', trim($line))) {
                    break;
                }
            }
            pclose($cpp);
            unlink($tmpfile);
        } // features.h

        if (!($major && $minor) && @is_link('/lib/libc.so.6')) {
            // Let's try reading the libc.so.6 symlink
            if (preg_match('/^libc-(.*)\.so$/', basename(readlink('/lib/libc.so.6')), $matches)) {
                list($major, $minor) = explode('.', $matches[1]);
            }
        }

        if (!($major && $minor)) {
            return $glibc = '';
        }

        return $glibc = "glibc{$major}.{$minor}";
    }

    function getSignature()
    {
        if (empty($this->extra)) {
            return "{$this->sysname}-{$this->release}-{$this->cpu}";
        }
        return "{$this->sysname}-{$this->release}-{$this->cpu}-{$this->extra}";
    }

    function getSysname()
    {
        return $this->sysname;
    }

    function getNodename()
    {
        return $this->nodename;
    }

    function getCpu()
    {
        return $this->cpu;
    }

    function getRelease()
    {
        return $this->release;
    }

    function getExtra()
    {
        return $this->extra;
    }

    function matchSignature($match)
    {
        $fragments = is_array($match) ? $match : explode('-', $match);
        $n = count($fragments);
        $matches = 0;
        if ($n > 0) {
            $matches += $this->_matchFragment($fragments[0], $this->sysname);
        }
        if ($n > 1) {
            $matches += $this->_matchFragment($fragments[1], $this->release);
        }
        if ($n > 2) {
            $matches += $this->_matchFragment($fragments[2], $this->cpu);
        }
        if ($n > 3) {
            $matches += $this->_matchFragment($fragments[3], $this->extra);
        }
        return ($matches == $n);
    }

    function _matchFragment($fragment, $value)
    {
        if (strcspn($fragment, '*?') < strlen($fragment)) {
            $reg = '/^' . str_replace(array('*', '?', '/'), array('.*', '.', '\\/'), $fragment) . '\\z/';
            return preg_match($reg, $value);
        }
        return ($fragment == '*' || !strcasecmp($fragment, $value));
    }

}
/*
 * Local Variables:
 * indent-tabs-mode: nil
 * c-basic-offset: 4
 * End:
 */
<?php
/**
 * PEAR, the PHP Extension and Application Repository
 *
 * PEAR class and PEAR_Error class
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Sterling Hughes <sterling@php.net>
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V.Cox <cox@idecnet.com>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2010 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**#@+
 * ERROR constants
 */
define('PEAR_ERROR_RETURN',     1);
define('PEAR_ERROR_PRINT',      2);
define('PEAR_ERROR_TRIGGER',    4);
define('PEAR_ERROR_DIE',        8);
define('PEAR_ERROR_CALLBACK',  16);
/**
 * WARNING: obsolete
 * @deprecated
 */
define('PEAR_ERROR_EXCEPTION', 32);
/**#@-*/

if (substr(PHP_OS, 0, 3) == 'WIN') {
    define('OS_WINDOWS', true);
    define('OS_UNIX',    false);
    define('PEAR_OS',    'Windows');
} else {
    define('OS_WINDOWS', false);
    define('OS_UNIX',    true);
    define('PEAR_OS',    'Unix'); // blatant assumption
}

$GLOBALS['_PEAR_default_error_mode']     = PEAR_ERROR_RETURN;
$GLOBALS['_PEAR_default_error_options']  = E_USER_NOTICE;
$GLOBALS['_PEAR_destructor_object_list'] = array();
$GLOBALS['_PEAR_shutdown_funcs']         = array();
$GLOBALS['_PEAR_error_handler_stack']    = array();

@ini_set('track_errors', true);

/**
 * Base class for other PEAR classes.  Provides rudimentary
 * emulation of destructors.
 *
 * If you want a destructor in your class, inherit PEAR and make a
 * destructor method called _yourclassname (same name as the
 * constructor, but with a "_" prefix).  Also, in your constructor you
 * have to call the PEAR constructor: $this->PEAR();.
 * The destructor method will be called without parameters.  Note that
 * at in some SAPI implementations (such as Apache), any output during
 * the request shutdown (in which destructors are called) seems to be
 * discarded.  If you need to get any debug information from your
 * destructor, use error_log(), syslog() or something similar.
 *
 * IMPORTANT! To use the emulated destructors you need to create the
 * objects by reference: $obj =& new PEAR_child;
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V. Cox <cox@idecnet.com>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @see        PEAR_Error
 * @since      Class available since PHP 4.0.2
 * @link        http://pear.php.net/manual/en/core.pear.php#core.pear.pear
 */
class PEAR
{
    /**
     * Whether to enable internal debug messages.
     *
     * @var     bool
     * @access  private
     */
    var $_debug = false;

    /**
     * Default error mode for this object.
     *
     * @var     int
     * @access  private
     */
    var $_default_error_mode = null;

    /**
     * Default error options used for this object when error mode
     * is PEAR_ERROR_TRIGGER.
     *
     * @var     int
     * @access  private
     */
    var $_default_error_options = null;

    /**
     * Default error handler (callback) for this object, if error mode is
     * PEAR_ERROR_CALLBACK.
     *
     * @var     string
     * @access  private
     */
    var $_default_error_handler = '';

    /**
     * Which class to use for error objects.
     *
     * @var     string
     * @access  private
     */
    var $_error_class = 'PEAR_Error';

    /**
     * An array of expected errors.
     *
     * @var     array
     * @access  private
     */
    var $_expected_errors = array();

    /**
     * List of methods that can be called both statically and non-statically.
     * @var array
     */
    protected static $bivalentMethods = array(
        'setErrorHandling' => true,
        'raiseError' => true,
        'throwError' => true,
        'pushErrorHandling' => true,
        'popErrorHandling' => true,
    );

    /**
     * Constructor.  Registers this object in
     * $_PEAR_destructor_object_list for destructor emulation if a
     * destructor object exists.
     *
     * @param string $error_class  (optional) which class to use for
     *        error objects, defaults to PEAR_Error.
     * @access public
     * @return void
     */
    function __construct($error_class = null)
    {
        $classname = strtolower(get_class($this));
        if ($this->_debug) {
            print "PEAR constructor called, class=$classname\n";
        }

        if ($error_class !== null) {
            $this->_error_class = $error_class;
        }

        while ($classname && strcasecmp($classname, "pear")) {
            $destructor = "_$classname";
            if (method_exists($this, $destructor)) {
                global $_PEAR_destructor_object_list;
                $_PEAR_destructor_object_list[] = &$this;
                if (!isset($GLOBALS['_PEAR_SHUTDOWN_REGISTERED'])) {
                    register_shutdown_function("_PEAR_call_destructors");
                    $GLOBALS['_PEAR_SHUTDOWN_REGISTERED'] = true;
                }
                break;
            } else {
                $classname = get_parent_class($classname);
            }
        }
    }

    /**
     * Only here for backwards compatibility.
     * E.g. Archive_Tar calls $this->PEAR() in its constructor.
     *
     * @param string $error_class Which class to use for error objects,
     *                            defaults to PEAR_Error.
     */
    public function PEAR($error_class = null)
    {
        self::__construct($error_class);
    }

    /**
     * Destructor (the emulated type of...).  Does nothing right now,
     * but is included for forward compatibility, so subclass
     * destructors should always call it.
     *
     * See the note in the class desciption about output from
     * destructors.
     *
     * @access public
     * @return void
     */
    function _PEAR() {
        if ($this->_debug) {
            printf("PEAR destructor called, class=%s\n", strtolower(get_class($this)));
        }
    }

    public function __call($method, $arguments)
    {
        if (!isset(self::$bivalentMethods[$method])) {
            trigger_error(
                'Call to undefined method PEAR::' . $method . '()', E_USER_ERROR
            );
        }
        return call_user_func_array(
            array(get_class(), '_' . $method),
            array_merge(array($this), $arguments)
        );
    }

    public static function __callStatic($method, $arguments)
    {
        if (!isset(self::$bivalentMethods[$method])) {
            trigger_error(
                'Call to undefined method PEAR::' . $method . '()', E_USER_ERROR
            );
        }
        return call_user_func_array(
            array(get_class(), '_' . $method),
            array_merge(array(null), $arguments)
        );
    }

    /**
    * If you have a class that's mostly/entirely static, and you need static
    * properties, you can use this method to simulate them. Eg. in your method(s)
    * do this: $myVar = &PEAR::getStaticProperty('myclass', 'myVar');
    * You MUST use a reference, or they will not persist!
    *
    * @param  string $class  The calling classname, to prevent clashes
    * @param  string $var    The variable to retrieve.
    * @return mixed   A reference to the variable. If not set it will be
    *                 auto initialised to NULL.
    */
    public static function &getStaticProperty($class, $var)
    {
        static $properties;
        if (!isset($properties[$class])) {
            $properties[$class] = array();
        }

        if (!array_key_exists($var, $properties[$class])) {
            $properties[$class][$var] = null;
        }

        return $properties[$class][$var];
    }

    /**
    * Use this function to register a shutdown method for static
    * classes.
    *
    * @param  mixed $func  The function name (or array of class/method) to call
    * @param  mixed $args  The arguments to pass to the function
    *
    * @return void
    */
    public static function registerShutdownFunc($func, $args = array())
    {
        // if we are called statically, there is a potential
        // that no shutdown func is registered.  Bug #6445
        if (!isset($GLOBALS['_PEAR_SHUTDOWN_REGISTERED'])) {
            register_shutdown_function("_PEAR_call_destructors");
            $GLOBALS['_PEAR_SHUTDOWN_REGISTERED'] = true;
        }
        $GLOBALS['_PEAR_shutdown_funcs'][] = array($func, $args);
    }

    /**
     * Tell whether a value is a PEAR error.
     *
     * @param   mixed $data   the value to test
     * @param   int   $code   if $data is an error object, return true
     *                        only if $code is a string and
     *                        $obj->getMessage() == $code or
     *                        $code is an integer and $obj->getCode() == $code
     *
     * @return  bool    true if parameter is an error
     */
    public static function isError($data, $code = null)
    {
        if (!is_a($data, 'PEAR_Error')) {
            return false;
        }

        if (is_null($code)) {
            return true;
        } elseif (is_string($code)) {
            return $data->getMessage() == $code;
        }

        return $data->getCode() == $code;
    }

    /**
     * Sets how errors generated by this object should be handled.
     * Can be invoked both in objects and statically.  If called
     * statically, setErrorHandling sets the default behaviour for all
     * PEAR objects.  If called in an object, setErrorHandling sets
     * the default behaviour for that object.
     *
     * @param object $object
     *        Object the method was called on (non-static mode)
     *
     * @param int $mode
     *        One of PEAR_ERROR_RETURN, PEAR_ERROR_PRINT,
     *        PEAR_ERROR_TRIGGER, PEAR_ERROR_DIE,
     *        PEAR_ERROR_CALLBACK or PEAR_ERROR_EXCEPTION.
     *
     * @param mixed $options
     *        When $mode is PEAR_ERROR_TRIGGER, this is the error level (one
     *        of E_USER_NOTICE, E_USER_WARNING or E_USER_ERROR).
     *
     *        When $mode is PEAR_ERROR_CALLBACK, this parameter is expected
     *        to be the callback function or method.  A callback
     *        function is a string with the name of the function, a
     *        callback method is an array of two elements: the element
     *        at index 0 is the object, and the element at index 1 is
     *        the name of the method to call in the object.
     *
     *        When $mode is PEAR_ERROR_PRINT or PEAR_ERROR_DIE, this is
     *        a printf format string used when printing the error
     *        message.
     *
     * @access public
     * @return void
     * @see PEAR_ERROR_RETURN
     * @see PEAR_ERROR_PRINT
     * @see PEAR_ERROR_TRIGGER
     * @see PEAR_ERROR_DIE
     * @see PEAR_ERROR_CALLBACK
     * @see PEAR_ERROR_EXCEPTION
     *
     * @since PHP 4.0.5
     */
    protected static function _setErrorHandling(
        $object, $mode = null, $options = null
    ) {
        if ($object !== null) {
            $setmode     = &$object->_default_error_mode;
            $setoptions  = &$object->_default_error_options;
        } else {
            $setmode     = &$GLOBALS['_PEAR_default_error_mode'];
            $setoptions  = &$GLOBALS['_PEAR_default_error_options'];
        }

        switch ($mode) {
            case PEAR_ERROR_EXCEPTION:
            case PEAR_ERROR_RETURN:
            case PEAR_ERROR_PRINT:
            case PEAR_ERROR_TRIGGER:
            case PEAR_ERROR_DIE:
            case null:
                $setmode = $mode;
                $setoptions = $options;
                break;

            case PEAR_ERROR_CALLBACK:
                $setmode = $mode;
                // class/object method callback
                if (is_callable($options)) {
                    $setoptions = $options;
                } else {
                    trigger_error("invalid error callback", E_USER_WARNING);
                }
                break;

            default:
                trigger_error("invalid error mode", E_USER_WARNING);
                break;
        }
    }

    /**
     * This method is used to tell which errors you expect to get.
     * Expected errors are always returned with error mode
     * PEAR_ERROR_RETURN.  Expected error codes are stored in a stack,
     * and this method pushes a new element onto it.  The list of
     * expected errors are in effect until they are popped off the
     * stack with the popExpect() method.
     *
     * Note that this method can not be called statically
     *
     * @param mixed $code a single error code or an array of error codes to expect
     *
     * @return int     the new depth of the "expected errors" stack
     * @access public
     */
    function expectError($code = '*')
    {
        if (is_array($code)) {
            array_push($this->_expected_errors, $code);
        } else {
            array_push($this->_expected_errors, array($code));
        }
        return count($this->_expected_errors);
    }

    /**
     * This method pops one element off the expected error codes
     * stack.
     *
     * @return array   the list of error codes that were popped
     */
    function popExpect()
    {
        return array_pop($this->_expected_errors);
    }

    /**
     * This method checks unsets an error code if available
     *
     * @param mixed error code
     * @return bool true if the error code was unset, false otherwise
     * @access private
     * @since PHP 4.3.0
     */
    function _checkDelExpect($error_code)
    {
        $deleted = false;
        foreach ($this->_expected_errors as $key => $error_array) {
            if (in_array($error_code, $error_array)) {
                unset($this->_expected_errors[$key][array_search($error_code, $error_array)]);
                $deleted = true;
            }

            // clean up empty arrays
            if (0 == count($this->_expected_errors[$key])) {
                unset($this->_expected_errors[$key]);
            }
        }

        return $deleted;
    }

    /**
     * This method deletes all occurences of the specified element from
     * the expected error codes stack.
     *
     * @param  mixed $error_code error code that should be deleted
     * @return mixed list of error codes that were deleted or error
     * @access public
     * @since PHP 4.3.0
     */
    function delExpect($error_code)
    {
        $deleted = false;
        if ((is_array($error_code) && (0 != count($error_code)))) {
            // $error_code is a non-empty array here; we walk through it trying
            // to unset all values
            foreach ($error_code as $key => $error) {
                $deleted =  $this->_checkDelExpect($error) ? true : false;
            }

            return $deleted ? true : PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
        } elseif (!empty($error_code)) {
            // $error_code comes alone, trying to unset it
            if ($this->_checkDelExpect($error_code)) {
                return true;
            }

            return PEAR::raiseError("The expected error you submitted does not exist"); // IMPROVE ME
        }

        // $error_code is empty
        return PEAR::raiseError("The expected error you submitted is empty"); // IMPROVE ME
    }

    /**
     * This method is a wrapper that returns an instance of the
     * configured error class with this object's default error
     * handling applied.  If the $mode and $options parameters are not
     * specified, the object's defaults are used.
     *
     * @param mixed $message a text error message or a PEAR error object
     *
     * @param int $code      a numeric error code (it is up to your class
     *                  to define these if you want to use codes)
     *
     * @param int $mode      One of PEAR_ERROR_RETURN, PEAR_ERROR_PRINT,
     *                  PEAR_ERROR_TRIGGER, PEAR_ERROR_DIE,
     *                  PEAR_ERROR_CALLBACK, PEAR_ERROR_EXCEPTION.
     *
     * @param mixed $options If $mode is PEAR_ERROR_TRIGGER, this parameter
     *                  specifies the PHP-internal error level (one of
     *                  E_USER_NOTICE, E_USER_WARNING or E_USER_ERROR).
     *                  If $mode is PEAR_ERROR_CALLBACK, this
     *                  parameter specifies the callback function or
     *                  method.  In other error modes this parameter
     *                  is ignored.
     *
     * @param string $userinfo If you need to pass along for example debug
     *                  information, this parameter is meant for that.
     *
     * @param string $error_class The returned error object will be
     *                  instantiated from this class, if specified.
     *
     * @param bool $skipmsg If true, raiseError will only pass error codes,
     *                  the error message parameter will be dropped.
     *
     * @return object   a PEAR error object
     * @see PEAR::setErrorHandling
     * @since PHP 4.0.5
     */
    protected static function _raiseError($object,
                         $message = null,
                         $code = null,
                         $mode = null,
                         $options = null,
                         $userinfo = null,
                         $error_class = null,
                         $skipmsg = false)
    {
        // The error is yet a PEAR error object
        if (is_object($message)) {
            $code        = $message->getCode();
            $userinfo    = $message->getUserInfo();
            $error_class = $message->getType();
            $message->error_message_prefix = '';
            $message     = $message->getMessage();
        }

        if (
            $object !== null &&
            isset($object->_expected_errors) &&
            count($object->_expected_errors) > 0 &&
            count($exp = end($object->_expected_errors))
        ) {
            if ($exp[0] == "*" ||
                (is_int(reset($exp)) && in_array($code, $exp)) ||
                (is_string(reset($exp)) && in_array($message, $exp))
            ) {
                $mode = PEAR_ERROR_RETURN;
            }
        }

        // No mode given, try global ones
        if ($mode === null) {
            // Class error handler
            if ($object !== null && isset($object->_default_error_mode)) {
                $mode    = $object->_default_error_mode;
                $options = $object->_default_error_options;
            // Global error handler
            } elseif (isset($GLOBALS['_PEAR_default_error_mode'])) {
                $mode    = $GLOBALS['_PEAR_default_error_mode'];
                $options = $GLOBALS['_PEAR_default_error_options'];
            }
        }

        if ($error_class !== null) {
            $ec = $error_class;
        } elseif ($object !== null && isset($object->_error_class)) {
            $ec = $object->_error_class;
        } else {
            $ec = 'PEAR_Error';
        }

        if ($skipmsg) {
            $a = new $ec($code, $mode, $options, $userinfo);
        } else {
            $a = new $ec($message, $code, $mode, $options, $userinfo);
        }

        return $a;
    }

    /**
     * Simpler form of raiseError with fewer options.  In most cases
     * message, code and userinfo are enough.
     *
     * @param mixed $message a text error message or a PEAR error object
     *
     * @param int $code      a numeric error code (it is up to your class
     *                  to define these if you want to use codes)
     *
     * @param string $userinfo If you need to pass along for example debug
     *                  information, this parameter is meant for that.
     *
     * @return object   a PEAR error object
     * @see PEAR::raiseError
     */
    protected static function _throwError($object, $message = null, $code = null, $userinfo = null)
    {
        if ($object !== null) {
            $a = &$object->raiseError($message, $code, null, null, $userinfo);
            return $a;
        }

        $a = &PEAR::raiseError($message, $code, null, null, $userinfo);
        return $a;
    }

    public static function staticPushErrorHandling($mode, $options = null)
    {
        $stack       = &$GLOBALS['_PEAR_error_handler_stack'];
        $def_mode    = &$GLOBALS['_PEAR_default_error_mode'];
        $def_options = &$GLOBALS['_PEAR_default_error_options'];
        $stack[] = array($def_mode, $def_options);
        switch ($mode) {
            case PEAR_ERROR_EXCEPTION:
            case PEAR_ERROR_RETURN:
            case PEAR_ERROR_PRINT:
            case PEAR_ERROR_TRIGGER:
            case PEAR_ERROR_DIE:
            case null:
                $def_mode = $mode;
                $def_options = $options;
                break;

            case PEAR_ERROR_CALLBACK:
                $def_mode = $mode;
                // class/object method callback
                if (is_callable($options)) {
                    $def_options = $options;
                } else {
                    trigger_error("invalid error callback", E_USER_WARNING);
                }
                break;

            default:
                trigger_error("invalid error mode", E_USER_WARNING);
                break;
        }
        $stack[] = array($mode, $options);
        return true;
    }

    public static function staticPopErrorHandling()
    {
        $stack = &$GLOBALS['_PEAR_error_handler_stack'];
        $setmode     = &$GLOBALS['_PEAR_default_error_mode'];
        $setoptions  = &$GLOBALS['_PEAR_default_error_options'];
        array_pop($stack);
        list($mode, $options) = $stack[sizeof($stack) - 1];
        array_pop($stack);
        switch ($mode) {
            case PEAR_ERROR_EXCEPTION:
            case PEAR_ERROR_RETURN:
            case PEAR_ERROR_PRINT:
            case PEAR_ERROR_TRIGGER:
            case PEAR_ERROR_DIE:
            case null:
                $setmode = $mode;
                $setoptions = $options;
                break;

            case PEAR_ERROR_CALLBACK:
                $setmode = $mode;
                // class/object method callback
                if (is_callable($options)) {
                    $setoptions = $options;
                } else {
                    trigger_error("invalid error callback", E_USER_WARNING);
                }
                break;

            default:
                trigger_error("invalid error mode", E_USER_WARNING);
                break;
        }
        return true;
    }

    /**
     * Push a new error handler on top of the error handler options stack. With this
     * you can easily override the actual error handler for some code and restore
     * it later with popErrorHandling.
     *
     * @param mixed $mode (same as setErrorHandling)
     * @param mixed $options (same as setErrorHandling)
     *
     * @return bool Always true
     *
     * @see PEAR::setErrorHandling
     */
    protected static function _pushErrorHandling($object, $mode, $options = null)
    {
        $stack = &$GLOBALS['_PEAR_error_handler_stack'];
        if ($object !== null) {
            $def_mode    = &$object->_default_error_mode;
            $def_options = &$object->_default_error_options;
        } else {
            $def_mode    = &$GLOBALS['_PEAR_default_error_mode'];
            $def_options = &$GLOBALS['_PEAR_default_error_options'];
        }
        $stack[] = array($def_mode, $def_options);

        if ($object !== null) {
            $object->setErrorHandling($mode, $options);
        } else {
            PEAR::setErrorHandling($mode, $options);
        }
        $stack[] = array($mode, $options);
        return true;
    }

    /**
    * Pop the last error handler used
    *
    * @return bool Always true
    *
    * @see PEAR::pushErrorHandling
    */
    protected static function _popErrorHandling($object)
    {
        $stack = &$GLOBALS['_PEAR_error_handler_stack'];
        array_pop($stack);
        list($mode, $options) = $stack[sizeof($stack) - 1];
        array_pop($stack);
        if ($object !== null) {
            $object->setErrorHandling($mode, $options);
        } else {
            PEAR::setErrorHandling($mode, $options);
        }
        return true;
    }

    /**
    * OS independent PHP extension load. Remember to take care
    * on the correct extension name for case sensitive OSes.
    *
    * @param string $ext The extension name
    * @return bool Success or not on the dl() call
    */
    public static function loadExtension($ext)
    {
        if (extension_loaded($ext)) {
            return true;
        }

        // if either returns true dl() will produce a FATAL error, stop that
        if (
            function_exists('dl') === false ||
            ini_get('enable_dl') != 1
        ) {
            return false;
        }

        if (OS_WINDOWS) {
            $suffix = '.dll';
        } elseif (PHP_OS == 'HP-UX') {
            $suffix = '.sl';
        } elseif (PHP_OS == 'AIX') {
            $suffix = '.a';
        } elseif (PHP_OS == 'OSX') {
            $suffix = '.bundle';
        } else {
            $suffix = '.so';
        }

        return @dl('php_'.$ext.$suffix) || @dl($ext.$suffix);
    }
}

function _PEAR_call_destructors()
{
    global $_PEAR_destructor_object_list;
    if (is_array($_PEAR_destructor_object_list) &&
        sizeof($_PEAR_destructor_object_list))
    {
        reset($_PEAR_destructor_object_list);

        $destructLifoExists = PEAR::getStaticProperty('PEAR', 'destructlifo');

        if ($destructLifoExists) {
            $_PEAR_destructor_object_list = array_reverse($_PEAR_destructor_object_list);
        }

        while (list($k, $objref) = each($_PEAR_destructor_object_list)) {
            $classname = get_class($objref);
            while ($classname) {
                $destructor = "_$classname";
                if (method_exists($objref, $destructor)) {
                    $objref->$destructor();
                    break;
                } else {
                    $classname = get_parent_class($classname);
                }
            }
        }
        // Empty the object list to ensure that destructors are
        // not called more than once.
        $_PEAR_destructor_object_list = array();
    }

    // Now call the shutdown functions
    if (
        isset($GLOBALS['_PEAR_shutdown_funcs']) &&
        is_array($GLOBALS['_PEAR_shutdown_funcs']) &&
        !empty($GLOBALS['_PEAR_shutdown_funcs'])
    ) {
        foreach ($GLOBALS['_PEAR_shutdown_funcs'] as $value) {
            call_user_func_array($value[0], $value[1]);
        }
    }
}

/**
 * Standard PEAR error class for PHP 4
 *
 * This class is supserseded by {@link PEAR_Exception} in PHP 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V. Cox <cox@idecnet.com>
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/manual/en/core.pear.pear-error.php
 * @see        PEAR::raiseError(), PEAR::throwError()
 * @since      Class available since PHP 4.0.2
 */
class PEAR_Error
{
    var $error_message_prefix = '';
    var $mode                 = PEAR_ERROR_RETURN;
    var $level                = E_USER_NOTICE;
    var $code                 = -1;
    var $message              = '';
    var $userinfo             = '';
    var $backtrace            = null;

    /**
     * PEAR_Error constructor
     *
     * @param string $message  message
     *
     * @param int $code     (optional) error code
     *
     * @param int $mode     (optional) error mode, one of: PEAR_ERROR_RETURN,
     * PEAR_ERROR_PRINT, PEAR_ERROR_DIE, PEAR_ERROR_TRIGGER,
     * PEAR_ERROR_CALLBACK or PEAR_ERROR_EXCEPTION
     *
     * @param mixed $options   (optional) error level, _OR_ in the case of
     * PEAR_ERROR_CALLBACK, the callback function or object/method
     * tuple.
     *
     * @param string $userinfo (optional) additional user/debug info
     *
     * @access public
     *
     */
    function __construct($message = 'unknown error', $code = null,
                        $mode = null, $options = null, $userinfo = null)
    {
        if ($mode === null) {
            $mode = PEAR_ERROR_RETURN;
        }
        $this->message   = $message;
        $this->code      = $code;
        $this->mode      = $mode;
        $this->userinfo  = $userinfo;

        $skiptrace = PEAR::getStaticProperty('PEAR_Error', 'skiptrace');

        if (!$skiptrace) {
            $this->backtrace = debug_backtrace();
            if (isset($this->backtrace[0]) && isset($this->backtrace[0]['object'])) {
                unset($this->backtrace[0]['object']);
            }
        }

        if ($mode & PEAR_ERROR_CALLBACK) {
            $this->level = E_USER_NOTICE;
            $this->callback = $options;
        } else {
            if ($options === null) {
                $options = E_USER_NOTICE;
            }

            $this->level = $options;
            $this->callback = null;
        }

        if ($this->mode & PEAR_ERROR_PRINT) {
            if (is_null($options) || is_int($options)) {
                $format = "%s";
            } else {
                $format = $options;
            }

            printf($format, $this->getMessage());
        }

        if ($this->mode & PEAR_ERROR_TRIGGER) {
            trigger_error($this->getMessage(), $this->level);
        }

        if ($this->mode & PEAR_ERROR_DIE) {
            $msg = $this->getMessage();
            if (is_null($options) || is_int($options)) {
                $format = "%s";
                if (substr($msg, -1) != "\n") {
                    $msg .= "\n";
                }
            } else {
                $format = $options;
            }
            die(sprintf($format, $msg));
        }

        if ($this->mode & PEAR_ERROR_CALLBACK && is_callable($this->callback)) {
            call_user_func($this->callback, $this);
        }

        if ($this->mode & PEAR_ERROR_EXCEPTION) {
            trigger_error("PEAR_ERROR_EXCEPTION is obsolete, use class PEAR_Exception for exceptions", E_USER_WARNING);
            eval('$e = new Exception($this->message, $this->code);throw($e);');
        }
    }

    /**
     * Only here for backwards compatibility.
     *
     * Class "Cache_Error" still uses it, among others.
     *
     * @param string $message  Message
     * @param int    $code     Error code
     * @param int    $mode     Error mode
     * @param mixed  $options  See __construct()
     * @param string $userinfo Additional user/debug info
     */
    public function PEAR_Error(
        $message = 'unknown error', $code = null, $mode = null,
        $options = null, $userinfo = null
    ) {
        self::__construct($message, $code, $mode, $options, $userinfo);
    }

    /**
     * Get the error mode from an error object.
     *
     * @return int error mode
     * @access public
     */
    function getMode()
    {
        return $this->mode;
    }

    /**
     * Get the callback function/method from an error object.
     *
     * @return mixed callback function or object/method array
     * @access public
     */
    function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get the error message from an error object.
     *
     * @return  string  full error message
     * @access public
     */
    function getMessage()
    {
        return ($this->error_message_prefix . $this->message);
    }

    /**
     * Get error code from an error object
     *
     * @return int error code
     * @access public
     */
     function getCode()
     {
        return $this->code;
     }

    /**
     * Get the name of this error/exception.
     *
     * @return string error/exception name (type)
     * @access public
     */
    function getType()
    {
        return get_class($this);
    }

    /**
     * Get additional user-supplied information.
     *
     * @return string user-supplied information
     * @access public
     */
    function getUserInfo()
    {
        return $this->userinfo;
    }

    /**
     * Get additional debug information supplied by the application.
     *
     * @return string debug information
     * @access public
     */
    function getDebugInfo()
    {
        return $this->getUserInfo();
    }

    /**
     * Get the call backtrace from where the error was generated.
     * Supported with PHP 4.3.0 or newer.
     *
     * @param int $frame (optional) what frame to fetch
     * @return array Backtrace, or NULL if not available.
     * @access public
     */
    function getBacktrace($frame = null)
    {
        if (defined('PEAR_IGNORE_BACKTRACE')) {
            return null;
        }
        if ($frame === null) {
            return $this->backtrace;
        }
        return $this->backtrace[$frame];
    }

    function addUserInfo($info)
    {
        if (empty($this->userinfo)) {
            $this->userinfo = $info;
        } else {
            $this->userinfo .= " ** $info";
        }
    }

    function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Make a string representation of this object.
     *
     * @return string a string with an object summary
     * @access public
     */
    function toString()
    {
        $modes = array();
        $levels = array(E_USER_NOTICE  => 'notice',
                        E_USER_WARNING => 'warning',
                        E_USER_ERROR   => 'error');
        if ($this->mode & PEAR_ERROR_CALLBACK) {
            if (is_array($this->callback)) {
                $callback = (is_object($this->callback[0]) ?
                    strtolower(get_class($this->callback[0])) :
                    $this->callback[0]) . '::' .
                    $this->callback[1];
            } else {
                $callback = $this->callback;
            }
            return sprintf('[%s: message="%s" code=%d mode=callback '.
                           'callback=%s prefix="%s" info="%s"]',
                           strtolower(get_class($this)), $this->message, $this->code,
                           $callback, $this->error_message_prefix,
                           $this->userinfo);
        }
        if ($this->mode & PEAR_ERROR_PRINT) {
            $modes[] = 'print';
        }
        if ($this->mode & PEAR_ERROR_TRIGGER) {
            $modes[] = 'trigger';
        }
        if ($this->mode & PEAR_ERROR_DIE) {
            $modes[] = 'die';
        }
        if ($this->mode & PEAR_ERROR_RETURN) {
            $modes[] = 'return';
        }
        return sprintf('[%s: message="%s" code=%d mode=%s level=%s '.
                       'prefix="%s" info="%s"]',
                       strtolower(get_class($this)), $this->message, $this->code,
                       implode("|", $modes), $levels[$this->level],
                       $this->error_message_prefix,
                       $this->userinfo);
    }
}

/*
 * Local Variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
<?php
/**
 * PEAR_ChannelFile, the channel handling class
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 1.4.0a1
 */

/**
 * Needed for error handling
 */
require_once 'phar://go-pear.phar/' . 'PEAR/ErrorStack.php';
require_once 'phar://go-pear.phar/' . 'PEAR/XMLParser.php';
require_once 'phar://go-pear.phar/' . 'PEAR/Common.php';

/**
 * Error code if the channel.xml <channel> tag does not contain a valid version
 */
define('PEAR_CHANNELFILE_ERROR_NO_VERSION', 1);
/**
 * Error code if the channel.xml <channel> tag version is not supported (version 1.0 is the only supported version,
 * currently
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_VERSION', 2);

/**
 * Error code if parsing is attempted with no xml extension
 */
define('PEAR_CHANNELFILE_ERROR_NO_XML_EXT', 3);

/**
 * Error code if creating the xml parser resource fails
 */
define('PEAR_CHANNELFILE_ERROR_CANT_MAKE_PARSER', 4);

/**
 * Error code used for all sax xml parsing errors
 */
define('PEAR_CHANNELFILE_ERROR_PARSER_ERROR', 5);

/**#@+
 * Validation errors
 */
/**
 * Error code when channel name is missing
 */
define('PEAR_CHANNELFILE_ERROR_NO_NAME', 6);
/**
 * Error code when channel name is invalid
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_NAME', 7);
/**
 * Error code when channel summary is missing
 */
define('PEAR_CHANNELFILE_ERROR_NO_SUMMARY', 8);
/**
 * Error code when channel summary is multi-line
 */
define('PEAR_CHANNELFILE_ERROR_MULTILINE_SUMMARY', 9);
/**
 * Error code when channel server is missing for protocol
 */
define('PEAR_CHANNELFILE_ERROR_NO_HOST', 10);
/**
 * Error code when channel server is invalid for protocol
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_HOST', 11);
/**
 * Error code when a mirror name is invalid
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_MIRROR', 21);
/**
 * Error code when a mirror type is invalid
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_MIRRORTYPE', 22);
/**
 * Error code when an attempt is made to generate xml, but the parsed content is invalid
 */
define('PEAR_CHANNELFILE_ERROR_INVALID', 23);
/**
 * Error code when an empty package name validate regex is passed in
 */
define('PEAR_CHANNELFILE_ERROR_EMPTY_REGEX', 24);
/**
 * Error code when a <function> tag has no version
 */
define('PEAR_CHANNELFILE_ERROR_NO_FUNCTIONVERSION', 25);
/**
 * Error code when a <function> tag has no name
 */
define('PEAR_CHANNELFILE_ERROR_NO_FUNCTIONNAME', 26);
/**
 * Error code when a <validatepackage> tag has no name
 */
define('PEAR_CHANNELFILE_ERROR_NOVALIDATE_NAME', 27);
/**
 * Error code when a <validatepackage> tag has no version attribute
 */
define('PEAR_CHANNELFILE_ERROR_NOVALIDATE_VERSION', 28);
/**
 * Error code when a mirror does not exist but is called for in one of the set*
 * methods.
 */
define('PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND', 32);
/**
 * Error code when a server port is not numeric
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_PORT', 33);
/**
 * Error code when <static> contains no version attribute
 */
define('PEAR_CHANNELFILE_ERROR_NO_STATICVERSION', 34);
/**
 * Error code when <baseurl> contains no type attribute in a <rest> protocol definition
 */
define('PEAR_CHANNELFILE_ERROR_NOBASEURLTYPE', 35);
/**
 * Error code when a mirror is defined and the channel.xml represents the __uri pseudo-channel
 */
define('PEAR_CHANNELFILE_URI_CANT_MIRROR', 36);
/**
 * Error code when ssl attribute is present and is not "yes"
 */
define('PEAR_CHANNELFILE_ERROR_INVALID_SSL', 37);
/**#@-*/

/**
 * Mirror types allowed.  Currently only internet servers are recognized.
 */
$GLOBALS['_PEAR_CHANNELS_MIRROR_TYPES'] =  array('server');


/**
 * The Channel handling class
 *
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a1
 */
class PEAR_ChannelFile
{
    /**
     * @access private
     * @var PEAR_ErrorStack
     * @access private
     */
    var $_stack;

    /**
     * Supported channel.xml versions, for parsing
     * @var array
     * @access private
     */
    var $_supportedVersions = array('1.0');

    /**
     * Parsed channel information
     * @var array
     * @access private
     */
    var $_channelInfo;

    /**
     * index into the subchannels array, used for parsing xml
     * @var int
     * @access private
     */
    var $_subchannelIndex;

    /**
     * index into the mirrors array, used for parsing xml
     * @var int
     * @access private
     */
    var $_mirrorIndex;

    /**
     * Flag used to determine the validity of parsed content
     * @var boolean
     * @access private
     */
    var $_isValid = false;

    function __construct()
    {
        $this->_stack = new PEAR_ErrorStack('PEAR_ChannelFile');
        $this->_stack->setErrorMessageTemplate($this->_getErrorMessage());
        $this->_isValid = false;
    }

    /**
     * @return array
     * @access protected
     */
    function _getErrorMessage()
    {
        return
            array(
                PEAR_CHANNELFILE_ERROR_INVALID_VERSION =>
                    'While parsing channel.xml, an invalid version number "%version% was passed in, expecting one of %versions%',
                PEAR_CHANNELFILE_ERROR_NO_VERSION =>
                    'No version number found in <channel> tag',
                PEAR_CHANNELFILE_ERROR_NO_XML_EXT =>
                    '%error%',
                PEAR_CHANNELFILE_ERROR_CANT_MAKE_PARSER =>
                    'Unable to create XML parser',
                PEAR_CHANNELFILE_ERROR_PARSER_ERROR =>
                    '%error%',
                PEAR_CHANNELFILE_ERROR_NO_NAME =>
                    'Missing channel name',
                PEAR_CHANNELFILE_ERROR_INVALID_NAME =>
                    'Invalid channel %tag% "%name%"',
                PEAR_CHANNELFILE_ERROR_NO_SUMMARY =>
                    'Missing channel summary',
                PEAR_CHANNELFILE_ERROR_MULTILINE_SUMMARY =>
                    'Channel summary should be on one line, but is multi-line',
                PEAR_CHANNELFILE_ERROR_NO_HOST =>
                    'Missing channel server for %type% server',
                PEAR_CHANNELFILE_ERROR_INVALID_HOST =>
                    'Server name "%server%" is invalid for %type% server',
                PEAR_CHANNELFILE_ERROR_INVALID_MIRROR =>
                    'Invalid mirror name "%name%", mirror type %type%',
                PEAR_CHANNELFILE_ERROR_INVALID_MIRRORTYPE =>
                    'Invalid mirror type "%type%"',
                PEAR_CHANNELFILE_ERROR_INVALID =>
                    'Cannot generate xml, contents are invalid',
                PEAR_CHANNELFILE_ERROR_EMPTY_REGEX =>
                    'packagenameregex cannot be empty',
                PEAR_CHANNELFILE_ERROR_NO_FUNCTIONVERSION =>
                    '%parent% %protocol% function has no version',
                PEAR_CHANNELFILE_ERROR_NO_FUNCTIONNAME =>
                    '%parent% %protocol% function has no name',
                PEAR_CHANNELFILE_ERROR_NOBASEURLTYPE =>
                    '%parent% rest baseurl has no type',
                PEAR_CHANNELFILE_ERROR_NOVALIDATE_NAME =>
                    'Validation package has no name in <validatepackage> tag',
                PEAR_CHANNELFILE_ERROR_NOVALIDATE_VERSION =>
                    'Validation package "%package%" has no version',
                PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND =>
                    'Mirror "%mirror%" does not exist',
                PEAR_CHANNELFILE_ERROR_INVALID_PORT =>
                    'Port "%port%" must be numeric',
                PEAR_CHANNELFILE_ERROR_NO_STATICVERSION =>
                    '<static> tag must contain version attribute',
                PEAR_CHANNELFILE_URI_CANT_MIRROR =>
                    'The __uri pseudo-channel cannot have mirrors',
                PEAR_CHANNELFILE_ERROR_INVALID_SSL =>
                    '%server% has invalid ssl attribute "%ssl%" can only be yes or not present',
            );
    }

    /**
     * @param string contents of package.xml file
     * @return bool success of parsing
     */
    function fromXmlString($data)
    {
        if (preg_match('/<channel\s+version="([0-9]+\.[0-9]+)"/', $data, $channelversion)) {
            if (!in_array($channelversion[1], $this->_supportedVersions)) {
                $this->_stack->push(PEAR_CHANNELFILE_ERROR_INVALID_VERSION, 'error',
                    array('version' => $channelversion[1]));
                return false;
            }
            $parser = new PEAR_XMLParser;
            $result = $parser->parse($data);
            if ($result !== true) {
                if ($result->getCode() == 1) {
                    $this->_stack->push(PEAR_CHANNELFILE_ERROR_NO_XML_EXT, 'error',
                        array('error' => $result->getMessage()));
                } else {
                    $this->_stack->push(PEAR_CHANNELFILE_ERROR_CANT_MAKE_PARSER, 'error');
                }
                return false;
            }
            $this->_channelInfo = $parser->getData();
            return true;
        } else {
            $this->_stack->push(PEAR_CHANNELFILE_ERROR_NO_VERSION, 'error', array('xml' => $data));
            return false;
        }
    }

    /**
     * @return array
     */
    function toArray()
    {
        if (!$this->_isValid && !$this->validate()) {
            return false;
        }
        return $this->_channelInfo;
    }

    /**
     * @param array
     *
     * @return PEAR_ChannelFile|false false if invalid
     */
    public static function &fromArray(
        $data, $compatibility = false, $stackClass = 'PEAR_ErrorStack'
    ) {
        $a = new PEAR_ChannelFile($compatibility, $stackClass);
        $a->_fromArray($data);
        if (!$a->validate()) {
            $a = false;
            return $a;
        }
        return $a;
    }

    /**
     * Unlike {@link fromArray()} this does not do any validation
     *
     * @param array
     *
     * @return PEAR_ChannelFile
     */
    public static function &fromArrayWithErrors(
        $data, $compatibility = false, $stackClass = 'PEAR_ErrorStack'
    ) {
        $a = new PEAR_ChannelFile($compatibility, $stackClass);
        $a->_fromArray($data);
        return $a;
    }

    /**
     * @param array
     * @access private
     */
    function _fromArray($data)
    {
        $this->_channelInfo = $data;
    }

    /**
     * Wrapper to {@link PEAR_ErrorStack::getErrors()}
     * @param boolean determines whether to purge the error stack after retrieving
     * @return array
     */
    function getErrors($purge = false)
    {
        return $this->_stack->getErrors($purge);
    }

    /**
     * Unindent given string (?)
     *
     * @param string $str The string that has to be unindented.
     * @return string
     * @access private
     */
    function _unIndent($str)
    {
        // remove leading newlines
        $str = preg_replace('/^[\r\n]+/', '', $str);
        // find whitespace at the beginning of the first line
        $indent_len = strspn($str, " \t");
        $indent = substr($str, 0, $indent_len);
        $data = '';
        // remove the same amount of whitespace from following lines
        foreach (explode("\n", $str) as $line) {
            if (substr($line, 0, $indent_len) == $indent) {
                $data .= substr($line, $indent_len) . "\n";
            }
        }
        return $data;
    }

    /**
     * Parse a channel.xml file.  Expects the name of
     * a channel xml file as input.
     *
     * @param string  $descfile  name of channel xml file
     * @return bool success of parsing
     */
    function fromXmlFile($descfile)
    {
        if (!file_exists($descfile) || !is_file($descfile) || !is_readable($descfile) ||
             (!$fp = fopen($descfile, 'r'))) {
            require_once 'phar://go-pear.phar/' . 'PEAR.php';
            return PEAR::raiseError("Unable to open $descfile");
        }

        // read the whole thing so we only get one cdata callback
        // for each block of cdata
        fclose($fp);
        $data = file_get_contents($descfile);
        return $this->fromXmlString($data);
    }

    /**
     * Parse channel information from different sources
     *
     * This method is able to extract information about a channel
     * from an .xml file or a string
     *
     * @access public
     * @param  string Filename of the source or the source itself
     * @return bool
     */
    function fromAny($info)
    {
        if (is_string($info) && file_exists($info) && strlen($info) < 255) {
            $tmp = substr($info, -4);
            if ($tmp == '.xml') {
                $info = $this->fromXmlFile($info);
            } else {
                $fp = fopen($info, "r");
                $test = fread($fp, 5);
                fclose($fp);
                if ($test == "<?xml") {
                    $info = $this->fromXmlFile($info);
                }
            }
            if (PEAR::isError($info)) {
                require_once 'phar://go-pear.phar/' . 'PEAR.php';
                return PEAR::raiseError($info);
            }
        }
        if (is_string($info)) {
            $info = $this->fromXmlString($info);
        }
        return $info;
    }

    /**
     * Return an XML document based on previous parsing and modifications
     *
     * @return string XML data
     *
     * @access public
     */
    function toXml()
    {
        if (!$this->_isValid && !$this->validate()) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID);
            return false;
        }
        if (!isset($this->_channelInfo['attribs']['version'])) {
            $this->_channelInfo['attribs']['version'] = '1.0';
        }
        $channelInfo = $this->_channelInfo;
        $ret = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
        $ret .= "<channel version=\"" .
            $channelInfo['attribs']['version'] . "\" xmlns=\"http://pear.php.net/channel-1.0\"
  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
  xsi:schemaLocation=\"http://pear.php.net/dtd/channel-"
            . $channelInfo['attribs']['version'] . " http://pear.php.net/dtd/channel-" .
            $channelInfo['attribs']['version'] . ".xsd\">
 <name>$channelInfo[name]</name>
 <summary>" . htmlspecialchars($channelInfo['summary'])."</summary>
";
        if (isset($channelInfo['suggestedalias'])) {
            $ret .= ' <suggestedalias>' . $channelInfo['suggestedalias'] . "</suggestedalias>\n";
        }
        if (isset($channelInfo['validatepackage'])) {
            $ret .= ' <validatepackage version="' .
                $channelInfo['validatepackage']['attribs']['version']. '">' .
                htmlspecialchars($channelInfo['validatepackage']['_content']) .
                "</validatepackage>\n";
        }
        $ret .= " <servers>\n";
        $ret .= '  <primary';
        if (isset($channelInfo['servers']['primary']['attribs']['ssl'])) {
            $ret .= ' ssl="' . $channelInfo['servers']['primary']['attribs']['ssl'] . '"';
        }
        if (isset($channelInfo['servers']['primary']['attribs']['port'])) {
            $ret .= ' port="' . $channelInfo['servers']['primary']['attribs']['port'] . '"';
        }
        $ret .= ">\n";
        if (isset($channelInfo['servers']['primary']['rest'])) {
            $ret .= $this->_makeRestXml($channelInfo['servers']['primary']['rest'], '   ');
        }
        $ret .= "  </primary>\n";
        if (isset($channelInfo['servers']['mirror'])) {
            $ret .= $this->_makeMirrorsXml($channelInfo);
        }
        $ret .= " </servers>\n";
        $ret .= "</channel>";
        return str_replace("\r", "\n", str_replace("\r\n", "\n", $ret));
    }

    /**
     * Generate the <rest> tag
     * @access private
     */
    function _makeRestXml($info, $indent)
    {
        $ret = $indent . "<rest>\n";
        if (isset($info['baseurl']) && !isset($info['baseurl'][0])) {
            $info['baseurl'] = array($info['baseurl']);
        }

        if (isset($info['baseurl'])) {
            foreach ($info['baseurl'] as $url) {
                $ret .= "$indent <baseurl type=\"" . $url['attribs']['type'] . "\"";
                $ret .= ">" . $url['_content'] . "</baseurl>\n";
            }
        }
        $ret .= $indent . "</rest>\n";
        return $ret;
    }

    /**
     * Generate the <mirrors> tag
     * @access private
     */
    function _makeMirrorsXml($channelInfo)
    {
        $ret = "";
        if (!isset($channelInfo['servers']['mirror'][0])) {
            $channelInfo['servers']['mirror'] = array($channelInfo['servers']['mirror']);
        }
        foreach ($channelInfo['servers']['mirror'] as $mirror) {
            $ret .= '  <mirror host="' . $mirror['attribs']['host'] . '"';
            if (isset($mirror['attribs']['port'])) {
                $ret .= ' port="' . $mirror['attribs']['port'] . '"';
            }
            if (isset($mirror['attribs']['ssl'])) {
                $ret .= ' ssl="' . $mirror['attribs']['ssl'] . '"';
            }
            $ret .= ">\n";
            if (isset($mirror['rest'])) {
                if (isset($mirror['rest'])) {
                    $ret .= $this->_makeRestXml($mirror['rest'], '   ');
                }
                $ret .= "  </mirror>\n";
            } else {
                $ret .= "/>\n";
            }
        }
        return $ret;
    }

    /**
     * Generate the <functions> tag
     * @access private
     */
    function _makeFunctionsXml($functions, $indent, $rest = false)
    {
        $ret = '';
        if (!isset($functions[0])) {
            $functions = array($functions);
        }
        foreach ($functions as $function) {
            $ret .= "$indent<function version=\"" . $function['attribs']['version'] . "\"";
            if ($rest) {
                $ret .= ' uri="' . $function['attribs']['uri'] . '"';
            }
            $ret .= ">" . $function['_content'] . "</function>\n";
        }
        return $ret;
    }

    /**
     * Validation error.  Also marks the object contents as invalid
     * @param error code
     * @param array error information
     * @access private
     */
    function _validateError($code, $params = array())
    {
        $this->_stack->push($code, 'error', $params);
        $this->_isValid = false;
    }

    /**
     * Validation warning.  Does not mark the object contents invalid.
     * @param error code
     * @param array error information
     * @access private
     */
    function _validateWarning($code, $params = array())
    {
        $this->_stack->push($code, 'warning', $params);
    }

    /**
     * Validate parsed file.
     *
     * @access public
     * @return boolean
     */
    function validate()
    {
        $this->_isValid = true;
        $info = $this->_channelInfo;
        if (empty($info['name'])) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_NAME);
        } elseif (!$this->validChannelServer($info['name'])) {
            if ($info['name'] != '__uri') {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_NAME, array('tag' => 'name',
                    'name' => $info['name']));
            }
        }
        if (empty($info['summary'])) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_SUMMARY);
        } elseif (strpos(trim($info['summary']), "\n") !== false) {
            $this->_validateWarning(PEAR_CHANNELFILE_ERROR_MULTILINE_SUMMARY,
                array('summary' => $info['summary']));
        }
        if (isset($info['suggestedalias'])) {
            if (!$this->validChannelServer($info['suggestedalias'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_NAME,
                    array('tag' => 'suggestedalias', 'name' =>$info['suggestedalias']));
            }
        }
        if (isset($info['localalias'])) {
            if (!$this->validChannelServer($info['localalias'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_NAME,
                    array('tag' => 'localalias', 'name' =>$info['localalias']));
            }
        }
        if (isset($info['validatepackage'])) {
            if (!isset($info['validatepackage']['_content'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_NOVALIDATE_NAME);
            }
            if (!isset($info['validatepackage']['attribs']['version'])) {
                $content = isset($info['validatepackage']['_content']) ?
                    $info['validatepackage']['_content'] :
                    null;
                $this->_validateError(PEAR_CHANNELFILE_ERROR_NOVALIDATE_VERSION,
                    array('package' => $content));
            }
        }

        if (isset($info['servers']['primary']['attribs'], $info['servers']['primary']['attribs']['port']) &&
              !is_numeric($info['servers']['primary']['attribs']['port'])) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_PORT,
                array('port' => $info['servers']['primary']['attribs']['port']));
        }

        if (isset($info['servers']['primary']['attribs'], $info['servers']['primary']['attribs']['ssl']) &&
              $info['servers']['primary']['attribs']['ssl'] != 'yes') {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_SSL,
                array('ssl' => $info['servers']['primary']['attribs']['ssl'],
                    'server' => $info['name']));
        }

        if (isset($info['servers']['primary']['rest']) &&
              isset($info['servers']['primary']['rest']['baseurl'])) {
            $this->_validateFunctions('rest', $info['servers']['primary']['rest']['baseurl']);
        }
        if (isset($info['servers']['mirror'])) {
            if ($this->_channelInfo['name'] == '__uri') {
                $this->_validateError(PEAR_CHANNELFILE_URI_CANT_MIRROR);
            }
            if (!isset($info['servers']['mirror'][0])) {
                $info['servers']['mirror'] = array($info['servers']['mirror']);
            }
            foreach ($info['servers']['mirror'] as $mirror) {
                if (!isset($mirror['attribs']['host'])) {
                    $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_HOST,
                      array('type' => 'mirror'));
                } elseif (!$this->validChannelServer($mirror['attribs']['host'])) {
                    $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_HOST,
                        array('server' => $mirror['attribs']['host'], 'type' => 'mirror'));
                }
                if (isset($mirror['attribs']['ssl']) && $mirror['attribs']['ssl'] != 'yes') {
                    $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_SSL,
                        array('ssl' => $info['ssl'], 'server' => $mirror['attribs']['host']));
                }
                if (isset($mirror['rest'])) {
                    $this->_validateFunctions('rest', $mirror['rest']['baseurl'],
                        $mirror['attribs']['host']);
                }
            }
        }
        return $this->_isValid;
    }

    /**
     * @param string  rest - protocol name this function applies to
     * @param array the functions
     * @param string the name of the parent element (mirror name, for instance)
     */
    function _validateFunctions($protocol, $functions, $parent = '')
    {
        if (!isset($functions[0])) {
            $functions = array($functions);
        }

        foreach ($functions as $function) {
            if (!isset($function['_content']) || empty($function['_content'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_FUNCTIONNAME,
                    array('parent' => $parent, 'protocol' => $protocol));
            }

            if ($protocol == 'rest') {
                if (!isset($function['attribs']['type']) ||
                      empty($function['attribs']['type'])) {
                    $this->_validateError(PEAR_CHANNELFILE_ERROR_NOBASEURLTYPE,
                        array('parent' => $parent, 'protocol' => $protocol));
                }
            } else {
                if (!isset($function['attribs']['version']) ||
                      empty($function['attribs']['version'])) {
                    $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_FUNCTIONVERSION,
                        array('parent' => $parent, 'protocol' => $protocol));
                }
            }
        }
    }

    /**
     * Test whether a string contains a valid channel server.
     * @param string $ver the package version to test
     * @return bool
     */
    function validChannelServer($server)
    {
        if ($server == '__uri') {
            return true;
        }
        return (bool) preg_match(PEAR_CHANNELS_SERVER_PREG, $server);
    }

    /**
     * @return string|false
     */
    function getName()
    {
        if (isset($this->_channelInfo['name'])) {
            return $this->_channelInfo['name'];
        }

        return false;
    }

    /**
     * @return string|false
     */
    function getServer()
    {
        if (isset($this->_channelInfo['name'])) {
            return $this->_channelInfo['name'];
        }

        return false;
    }

    /**
     * @return int|80 port number to connect to
     */
    function getPort($mirror = false)
    {
        if ($mirror) {
            if ($mir = $this->getMirror($mirror)) {
                if (isset($mir['attribs']['port'])) {
                    return $mir['attribs']['port'];
                }

                if ($this->getSSL($mirror)) {
                    return 443;
                }

                return 80;
            }

            return false;
        }

        if (isset($this->_channelInfo['servers']['primary']['attribs']['port'])) {
            return $this->_channelInfo['servers']['primary']['attribs']['port'];
        }

        if ($this->getSSL()) {
            return 443;
        }

        return 80;
    }

    /**
     * @return bool Determines whether secure sockets layer (SSL) is used to connect to this channel
     */
    function getSSL($mirror = false)
    {
        if ($mirror) {
            if ($mir = $this->getMirror($mirror)) {
                if (isset($mir['attribs']['ssl'])) {
                    return true;
                }

                return false;
            }

            return false;
        }

        if (isset($this->_channelInfo['servers']['primary']['attribs']['ssl'])) {
            return true;
        }

        return false;
    }

    /**
     * @return string|false
     */
    function getSummary()
    {
        if (isset($this->_channelInfo['summary'])) {
            return $this->_channelInfo['summary'];
        }

        return false;
    }

    /**
     * @param string protocol type
     * @param string Mirror name
     * @return array|false
     */
    function getFunctions($protocol, $mirror = false)
    {
        if ($this->getName() == '__uri') {
            return false;
        }

        $function = $protocol == 'rest' ? 'baseurl' : 'function';
        if ($mirror) {
            if ($mir = $this->getMirror($mirror)) {
                if (isset($mir[$protocol][$function])) {
                    return $mir[$protocol][$function];
                }
            }

            return false;
        }

        if (isset($this->_channelInfo['servers']['primary'][$protocol][$function])) {
            return $this->_channelInfo['servers']['primary'][$protocol][$function];
        }

        return false;
    }

    /**
     * @param string Protocol type
     * @param string Function name (null to return the
     *               first protocol of the type requested)
     * @param string Mirror name, if any
     * @return array
     */
     function getFunction($type, $name = null, $mirror = false)
     {
        $protocols = $this->getFunctions($type, $mirror);
        if (!$protocols) {
            return false;
        }

        foreach ($protocols as $protocol) {
            if ($name === null) {
                return $protocol;
            }

            if ($protocol['_content'] != $name) {
                continue;
            }

            return $protocol;
        }

        return false;
     }

    /**
     * @param string protocol type
     * @param string protocol name
     * @param string version
     * @param string mirror name
     * @return boolean
     */
    function supports($type, $name = null, $mirror = false, $version = '1.0')
    {
        $protocols = $this->getFunctions($type, $mirror);
        if (!$protocols) {
            return false;
        }

        foreach ($protocols as $protocol) {
            if ($protocol['attribs']['version'] != $version) {
                continue;
            }

            if ($name === null) {
                return true;
            }

            if ($protocol['_content'] != $name) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Determines whether a channel supports Representational State Transfer (REST) protocols
     * for retrieving channel information
     * @param string
     * @return bool
     */
    function supportsREST($mirror = false)
    {
        if ($mirror == $this->_channelInfo['name']) {
            $mirror = false;
        }

        if ($mirror) {
            if ($mir = $this->getMirror($mirror)) {
                return isset($mir['rest']);
            }

            return false;
        }

        return isset($this->_channelInfo['servers']['primary']['rest']);
    }

    /**
     * Get the URL to access a base resource.
     *
     * Hyperlinks in the returned xml will be used to retrieve the proper information
     * needed.  This allows extreme extensibility and flexibility in implementation
     * @param string Resource Type to retrieve
     */
    function getBaseURL($resourceType, $mirror = false)
    {
        if ($mirror == $this->_channelInfo['name']) {
            $mirror = false;
        }

        if ($mirror) {
            $mir = $this->getMirror($mirror);
            if (!$mir) {
                return false;
            }

            $rest = $mir['rest'];
        } else {
            $rest = $this->_channelInfo['servers']['primary']['rest'];
        }

        if (!isset($rest['baseurl'][0])) {
            $rest['baseurl'] = array($rest['baseurl']);
        }

        foreach ($rest['baseurl'] as $baseurl) {
            if (strtolower($baseurl['attribs']['type']) == strtolower($resourceType)) {
                return $baseurl['_content'];
            }
        }

        return false;
    }

    /**
     * Since REST does not implement RPC, provide this as a logical wrapper around
     * resetFunctions for REST
     * @param string|false mirror name, if any
     */
    function resetREST($mirror = false)
    {
        return $this->resetFunctions('rest', $mirror);
    }

    /**
     * Empty all protocol definitions
     * @param string protocol type
     * @param string|false mirror name, if any
     */
    function resetFunctions($type, $mirror = false)
    {
        if ($mirror) {
            if (isset($this->_channelInfo['servers']['mirror'])) {
                $mirrors = $this->_channelInfo['servers']['mirror'];
                if (!isset($mirrors[0])) {
                    $mirrors = array($mirrors);
                }

                foreach ($mirrors as $i => $mir) {
                    if ($mir['attribs']['host'] == $mirror) {
                        if (isset($this->_channelInfo['servers']['mirror'][$i][$type])) {
                            unset($this->_channelInfo['servers']['mirror'][$i][$type]);
                        }

                        return true;
                    }
                }

                return false;
            }

            return false;
        }

        if (isset($this->_channelInfo['servers']['primary'][$type])) {
            unset($this->_channelInfo['servers']['primary'][$type]);
        }

        return true;
    }

    /**
     * Set a channel's protocols to the protocols supported by pearweb
     */
    function setDefaultPEARProtocols($version = '1.0', $mirror = false)
    {
        switch ($version) {
            case '1.0' :
                $this->resetREST($mirror);

                if (!isset($this->_channelInfo['servers'])) {
                    $this->_channelInfo['servers'] = array('primary' =>
                        array('rest' => array()));
                } elseif (!isset($this->_channelInfo['servers']['primary'])) {
                    $this->_channelInfo['servers']['primary'] = array('rest' => array());
                }

                return true;
            break;
            default :
                return false;
            break;
        }
    }

    /**
     * @return array
     */
    function getMirrors()
    {
        if (isset($this->_channelInfo['servers']['mirror'])) {
            $mirrors = $this->_channelInfo['servers']['mirror'];
            if (!isset($mirrors[0])) {
                $mirrors = array($mirrors);
            }

            return $mirrors;
        }

        return array();
    }

    /**
     * Get the unserialized XML representing a mirror
     * @return array|false
     */
    function getMirror($server)
    {
        foreach ($this->getMirrors() as $mirror) {
            if ($mirror['attribs']['host'] == $server) {
                return $mirror;
            }
        }

        return false;
    }

    /**
     * @param string
     * @return string|false
     * @error PEAR_CHANNELFILE_ERROR_NO_NAME
     * @error PEAR_CHANNELFILE_ERROR_INVALID_NAME
     */
    function setName($name)
    {
        return $this->setServer($name);
    }

    /**
     * Set the socket number (port) that is used to connect to this channel
     * @param integer
     * @param string|false name of the mirror server, or false for the primary
     */
    function setPort($port, $mirror = false)
    {
        if ($mirror) {
            if (!isset($this->_channelInfo['servers']['mirror'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                    array('mirror' => $mirror));
                return false;
            }

            if (isset($this->_channelInfo['servers']['mirror'][0])) {
                foreach ($this->_channelInfo['servers']['mirror'] as $i => $mir) {
                    if ($mirror == $mir['attribs']['host']) {
                        $this->_channelInfo['servers']['mirror'][$i]['attribs']['port'] = $port;
                        return true;
                    }
                }

                return false;
            } elseif ($this->_channelInfo['servers']['mirror']['attribs']['host'] == $mirror) {
                $this->_channelInfo['servers']['mirror']['attribs']['port'] = $port;
                $this->_isValid = false;
                return true;
            }
        }

        $this->_channelInfo['servers']['primary']['attribs']['port'] = $port;
        $this->_isValid = false;
        return true;
    }

    /**
     * Set the socket number (port) that is used to connect to this channel
     * @param bool Determines whether to turn on SSL support or turn it off
     * @param string|false name of the mirror server, or false for the primary
     */
    function setSSL($ssl = true, $mirror = false)
    {
        if ($mirror) {
            if (!isset($this->_channelInfo['servers']['mirror'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                    array('mirror' => $mirror));
                return false;
            }

            if (isset($this->_channelInfo['servers']['mirror'][0])) {
                foreach ($this->_channelInfo['servers']['mirror'] as $i => $mir) {
                    if ($mirror == $mir['attribs']['host']) {
                        if (!$ssl) {
                            if (isset($this->_channelInfo['servers']['mirror'][$i]
                                  ['attribs']['ssl'])) {
                                unset($this->_channelInfo['servers']['mirror'][$i]['attribs']['ssl']);
                            }
                        } else {
                            $this->_channelInfo['servers']['mirror'][$i]['attribs']['ssl'] = 'yes';
                        }

                        return true;
                    }
                }

                return false;
            } elseif ($this->_channelInfo['servers']['mirror']['attribs']['host'] == $mirror) {
                if (!$ssl) {
                    if (isset($this->_channelInfo['servers']['mirror']['attribs']['ssl'])) {
                        unset($this->_channelInfo['servers']['mirror']['attribs']['ssl']);
                    }
                } else {
                    $this->_channelInfo['servers']['mirror']['attribs']['ssl'] = 'yes';
                }

                $this->_isValid = false;
                return true;
            }
        }

        if ($ssl) {
            $this->_channelInfo['servers']['primary']['attribs']['ssl'] = 'yes';
        } else {
            if (isset($this->_channelInfo['servers']['primary']['attribs']['ssl'])) {
                unset($this->_channelInfo['servers']['primary']['attribs']['ssl']);
            }
        }

        $this->_isValid = false;
        return true;
    }

    /**
     * @param string
     * @return string|false
     * @error PEAR_CHANNELFILE_ERROR_NO_SERVER
     * @error PEAR_CHANNELFILE_ERROR_INVALID_SERVER
     */
    function setServer($server, $mirror = false)
    {
        if (empty($server)) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_SERVER);
            return false;
        } elseif (!$this->validChannelServer($server)) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_NAME,
                array('tag' => 'name', 'name' => $server));
            return false;
        }

        if ($mirror) {
            $found = false;
            foreach ($this->_channelInfo['servers']['mirror'] as $i => $mir) {
                if ($mirror == $mir['attribs']['host']) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                    array('mirror' => $mirror));
                return false;
            }

            $this->_channelInfo['mirror'][$i]['attribs']['host'] = $server;
            return true;
        }

        $this->_channelInfo['name'] = $server;
        return true;
    }

    /**
     * @param string
     * @return boolean success
     * @error PEAR_CHANNELFILE_ERROR_NO_SUMMARY
     * @warning PEAR_CHANNELFILE_ERROR_MULTILINE_SUMMARY
     */
    function setSummary($summary)
    {
        if (empty($summary)) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_NO_SUMMARY);
            return false;
        } elseif (strpos(trim($summary), "\n") !== false) {
            $this->_validateWarning(PEAR_CHANNELFILE_ERROR_MULTILINE_SUMMARY,
                array('summary' => $summary));
        }

        $this->_channelInfo['summary'] = $summary;
        return true;
    }

    /**
     * @param string
     * @param boolean determines whether the alias is in channel.xml or local
     * @return boolean success
     */
    function setAlias($alias, $local = false)
    {
        if (!$this->validChannelServer($alias)) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_INVALID_NAME,
                array('tag' => 'suggestedalias', 'name' => $alias));
            return false;
        }

        if ($local) {
            $this->_channelInfo['localalias'] = $alias;
        } else {
            $this->_channelInfo['suggestedalias'] = $alias;
        }

        return true;
    }

    /**
     * @return string
     */
    function getAlias()
    {
        if (isset($this->_channelInfo['localalias'])) {
            return $this->_channelInfo['localalias'];
        }
        if (isset($this->_channelInfo['suggestedalias'])) {
            return $this->_channelInfo['suggestedalias'];
        }
        if (isset($this->_channelInfo['name'])) {
            return $this->_channelInfo['name'];
        }
        return '';
    }

    /**
     * Set the package validation object if it differs from PEAR's default
     * The class must be includeable via changing _ in the classname to path separator,
     * but no checking of this is made.
     * @param string|false pass in false to reset to the default packagename regex
     * @return boolean success
     */
    function setValidationPackage($validateclass, $version)
    {
        if (empty($validateclass)) {
            unset($this->_channelInfo['validatepackage']);
        }
        $this->_channelInfo['validatepackage'] = array('_content' => $validateclass);
        $this->_channelInfo['validatepackage']['attribs'] = array('version' => $version);
    }

    /**
     * Add a protocol to the provides section
     * @param string protocol type
     * @param string protocol version
     * @param string protocol name, if any
     * @param string mirror name, if this is a mirror's protocol
     * @return bool
     */
    function addFunction($type, $version, $name = '', $mirror = false)
    {
        if ($mirror) {
            return $this->addMirrorFunction($mirror, $type, $version, $name);
        }

        $set = array('attribs' => array('version' => $version), '_content' => $name);
        if (!isset($this->_channelInfo['servers']['primary'][$type]['function'])) {
            if (!isset($this->_channelInfo['servers'])) {
                $this->_channelInfo['servers'] = array('primary' =>
                    array($type => array()));
            } elseif (!isset($this->_channelInfo['servers']['primary'])) {
                $this->_channelInfo['servers']['primary'] = array($type => array());
            }

            $this->_channelInfo['servers']['primary'][$type]['function'] = $set;
            $this->_isValid = false;
            return true;
        } elseif (!isset($this->_channelInfo['servers']['primary'][$type]['function'][0])) {
            $this->_channelInfo['servers']['primary'][$type]['function'] = array(
                $this->_channelInfo['servers']['primary'][$type]['function']);
        }

        $this->_channelInfo['servers']['primary'][$type]['function'][] = $set;
        return true;
    }
    /**
     * Add a protocol to a mirror's provides section
     * @param string mirror name (server)
     * @param string protocol type
     * @param string protocol version
     * @param string protocol name, if any
     */
    function addMirrorFunction($mirror, $type, $version, $name = '')
    {
        if (!isset($this->_channelInfo['servers']['mirror'])) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                array('mirror' => $mirror));
            return false;
        }

        $setmirror = false;
        if (isset($this->_channelInfo['servers']['mirror'][0])) {
            foreach ($this->_channelInfo['servers']['mirror'] as $i => $mir) {
                if ($mirror == $mir['attribs']['host']) {
                    $setmirror = &$this->_channelInfo['servers']['mirror'][$i];
                    break;
                }
            }
        } else {
            if ($this->_channelInfo['servers']['mirror']['attribs']['host'] == $mirror) {
                $setmirror = &$this->_channelInfo['servers']['mirror'];
            }
        }

        if (!$setmirror) {
            $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                array('mirror' => $mirror));
            return false;
        }

        $set = array('attribs' => array('version' => $version), '_content' => $name);
        if (!isset($setmirror[$type]['function'])) {
            $setmirror[$type]['function'] = $set;
            $this->_isValid = false;
            return true;
        } elseif (!isset($setmirror[$type]['function'][0])) {
            $setmirror[$type]['function'] = array($setmirror[$type]['function']);
        }

        $setmirror[$type]['function'][] = $set;
        $this->_isValid = false;
        return true;
    }

    /**
     * @param string Resource Type this url links to
     * @param string URL
     * @param string|false mirror name, if this is not a primary server REST base URL
     */
    function setBaseURL($resourceType, $url, $mirror = false)
    {
        if ($mirror) {
            if (!isset($this->_channelInfo['servers']['mirror'])) {
                $this->_validateError(PEAR_CHANNELFILE_ERROR_MIRROR_NOT_FOUND,
                    array('mirror' => $mirror));
                return false;
            }

            $setmirror = false;
            if (isset($this->_channelInfo['servers']['mirror'][0])) {
                foreach ($this->_channelInfo['servers']['mirror'] as $i => $mir) {
                    if ($mirror == $mir['attribs']['host']) {
                        $setmirror = &$this->_channelInfo['servers']['mirror'][$i];
                        break;
                    }
                }
            } else {
                if ($this->_channelInfo['servers']['mirror']['attribs']['host'] == $mirror) {
                    $setmirror = &$this->_channelInfo['servers']['mirror'];
                }
            }
        } else {
            $setmirror = &$this->_channelInfo['servers']['primary'];
        }

        $set = array('attribs' => array('type' => $resourceType), '_content' => $url);
        if (!isset($setmirror['rest'])) {
            $setmirror['rest'] = array();
        }

        if (!isset($setmirror['rest']['baseurl'])) {
            $setmirror['rest']['baseurl'] = $set;
            $this->_isValid = false;
            return true;
        } elseif (!isset($setmirror['rest']['baseurl'][0])) {
            $setmirror['rest']['baseurl'] = array($setmirror['rest']['baseurl']);
        }

        foreach ($setmirror['rest']['baseurl'] as $i => $url) {
            if ($url['attribs']['type'] == $resourceType) {
                $this->_isValid = false;
                $setmirror['rest']['baseurl'][$i] = $set;
                return true;
            }
        }

        $setmirror['rest']['baseurl'][] = $set;
        $this->_isValid = false;
        return true;
    }

    /**
     * @param string mirror server
     * @param int mirror http port
     * @return boolean
     */
    function addMirror($server, $port = null)
    {
        if ($this->_channelInfo['name'] == '__uri') {
            return false; // the __uri channel cannot have mirrors by definition
        }

        $set = array('attribs' => array('host' => $server));
        if (is_numeric($port)) {
            $set['attribs']['port'] = $port;
        }

        if (!isset($this->_channelInfo['servers']['mirror'])) {
            $this->_channelInfo['servers']['mirror'] = $set;
            return true;
        }

        if (!isset($this->_channelInfo['servers']['mirror'][0])) {
            $this->_channelInfo['servers']['mirror'] =
                array($this->_channelInfo['servers']['mirror']);
        }

        $this->_channelInfo['servers']['mirror'][] = $set;
        return true;
    }

    /**
     * Retrieve the name of the validation package for this channel
     * @return string|false
     */
    function getValidationPackage()
    {
        if (!$this->_isValid && !$this->validate()) {
            return false;
        }

        if (!isset($this->_channelInfo['validatepackage'])) {
            return array('attribs' => array('version' => 'default'),
                '_content' => 'PEAR_Validate');
        }

        return $this->_channelInfo['validatepackage'];
    }

    /**
     * Retrieve the object that can be used for custom validation
     * @param string|false the name of the package to validate.  If the package is
     *                     the channel validation package, PEAR_Validate is returned
     * @return PEAR_Validate|false false is returned if the validation package
     *         cannot be located
     */
    function &getValidationObject($package = false)
    {
        if (!class_exists('PEAR_Validate')) {
            require_once 'phar://go-pear.phar/' . 'PEAR/Validate.php';
        }

        if (!$this->_isValid) {
            if (!$this->validate()) {
                $a = false;
                return $a;
            }
        }

        if (isset($this->_channelInfo['validatepackage'])) {
            if ($package == $this->_channelInfo['validatepackage']) {
                // channel validation packages are always validated by PEAR_Validate
                $val = new PEAR_Validate;
                return $val;
            }

            if (!class_exists(str_replace('.', '_',
                  $this->_channelInfo['validatepackage']['_content']))) {
                if ($this->isIncludeable(str_replace('_', '/',
                      $this->_channelInfo['validatepackage']['_content']) . '.php')) {
                    include_once 'phar://go-pear.phar/' . str_replace('_', '/',
                        $this->_channelInfo['validatepackage']['_content']) . '.php';
                    $vclass = str_replace('.', '_',
                        $this->_channelInfo['validatepackage']['_content']);
                    $val = new $vclass;
                } else {
                    $a = false;
                    return $a;
                }
            } else {
                $vclass = str_replace('.', '_',
                    $this->_channelInfo['validatepackage']['_content']);
                $val = new $vclass;
            }
        } else {
            $val = new PEAR_Validate;
        }

        return $val;
    }

    function isIncludeable($path)
    {
        $possibilities = explode(PATH_SEPARATOR, ini_get('include_path'));
        foreach ($possibilities as $dir) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . $path)
                  && is_readable($dir . DIRECTORY_SEPARATOR . $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This function is used by the channel updater and retrieves a value set by
     * the registry, or the current time if it has not been set
     * @return string
     */
    function lastModified()
    {
        if (isset($this->_channelInfo['_lastmodified'])) {
            return $this->_channelInfo['_lastmodified'];
        }

        return time();
    }
}
<?php
/**
 * PEAR_ChannelFile_Parser for parsing channel.xml
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 1.4.0a1
 */

/**
 * base xml parser class
 */
require_once 'phar://go-pear.phar/' . 'PEAR/XMLParser.php';
require_once 'phar://go-pear.phar/' . 'PEAR/ChannelFile.php';
/**
 * Parser for channel.xml
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a1
 */
class PEAR_ChannelFile_Parser extends PEAR_XMLParser
{
    var $_config;
    var $_logger;
    var $_registry;

    function setConfig(&$c)
    {
        $this->_config = &$c;
        $this->_registry = &$c->getRegistry();
    }

    function setLogger(&$l)
    {
        $this->_logger = &$l;
    }

    function parse($data, $file)
    {
        if (PEAR::isError($err = parent::parse($data, $file))) {
            return $err;
        }

        $ret = new PEAR_ChannelFile;
        $ret->setConfig($this->_config);
        if (isset($this->_logger)) {
            $ret->setLogger($this->_logger);
        }

        $ret->fromArray($this->_unserializedData);
        // make sure the filelist is in the easy to read format needed
        $ret->flattenFilelist();
        $ret->setPackagefile($file, $archive);
        return $ret;
    }
}<?php
/**
 * PEAR_Command, command pattern class
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**
 * Needed for error handling
 */
require_once 'phar://go-pear.phar/' . 'PEAR.php';
require_once 'phar://go-pear.phar/' . 'PEAR/Frontend.php';
require_once 'phar://go-pear.phar/' . 'PEAR/XMLParser.php';

/**
 * List of commands and what classes they are implemented in.
 * @var array command => implementing class
 */
$GLOBALS['_PEAR_Command_commandlist'] = array();

/**
 * List of commands and their descriptions
 * @var array command => description
 */
$GLOBALS['_PEAR_Command_commanddesc'] = array();

/**
 * List of shortcuts to common commands.
 * @var array shortcut => command
 */
$GLOBALS['_PEAR_Command_shortcuts'] = array();

/**
 * Array of command objects
 * @var array class => object
 */
$GLOBALS['_PEAR_Command_objects'] = array();

/**
 * PEAR command class, a simple factory class for administrative
 * commands.
 *
 * How to implement command classes:
 *
 * - The class must be called PEAR_Command_Nnn, installed in the
 *   "PEAR/Common" subdir, with a method called getCommands() that
 *   returns an array of the commands implemented by the class (see
 *   PEAR/Command/Install.php for an example).
 *
 * - The class must implement a run() function that is called with three
 *   params:
 *
 *    (string) command name
 *    (array)  assoc array with options, freely defined by each
 *             command, for example:
 *             array('force' => true)
 *    (array)  list of the other parameters
 *
 *   The run() function returns a PEAR_CommandResponse object.  Use
 *   these methods to get information:
 *
 *    int getStatus()   Returns PEAR_COMMAND_(SUCCESS|FAILURE|PARTIAL)
 *                      *_PARTIAL means that you need to issue at least
 *                      one more command to complete the operation
 *                      (used for example for validation steps).
 *
 *    string getMessage()  Returns a message for the user.  Remember,
 *                         no HTML or other interface-specific markup.
 *
 *   If something unexpected happens, run() returns a PEAR error.
 *
 * - DON'T OUTPUT ANYTHING! Return text for output instead.
 *
 * - DON'T USE HTML! The text you return will be used from both Gtk,
 *   web and command-line interfaces, so for now, keep everything to
 *   plain text.
 *
 * - DON'T USE EXIT OR DIE! Always use pear errors.  From static
 *   classes do PEAR::raiseError(), from other classes do
 *   $this->raiseError().
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class PEAR_Command
{
    // {{{ factory()

    /**
     * Get the right object for executing a command.
     *
     * @param string $command The name of the command
     * @param object $config  Instance of PEAR_Config object
     *
     * @return object the command object or a PEAR error
     */
    public static function &factory($command, &$config)
    {
        if (empty($GLOBALS['_PEAR_Command_commandlist'])) {
            PEAR_Command::registerCommands();
        }
        if (isset($GLOBALS['_PEAR_Command_shortcuts'][$command])) {
            $command = $GLOBALS['_PEAR_Command_shortcuts'][$command];
        }
        if (!isset($GLOBALS['_PEAR_Command_commandlist'][$command])) {
            $a = PEAR::raiseError("unknown command `$command'");
            return $a;
        }
        $class = $GLOBALS['_PEAR_Command_commandlist'][$command];
        if (!class_exists($class)) {
            require_once $GLOBALS['_PEAR_Command_objects'][$class];
        }
        if (!class_exists($class)) {
            $a = PEAR::raiseError("unknown command `$command'");
            return $a;
        }
        $ui =& PEAR_Command::getFrontendObject();
        $obj = new $class($ui, $config);
        return $obj;
    }

    // }}}
    // {{{ & getObject()
    public static function &getObject($command)
    {
        $class = $GLOBALS['_PEAR_Command_commandlist'][$command];
        if (!class_exists($class)) {
            require_once $GLOBALS['_PEAR_Command_objects'][$class];
        }
        if (!class_exists($class)) {
            return PEAR::raiseError("unknown command `$command'");
        }
        $ui =& PEAR_Command::getFrontendObject();
        $config = &PEAR_Config::singleton();
        $obj = new $class($ui, $config);
        return $obj;
    }

    // }}}
    // {{{ & getFrontendObject()

    /**
     * Get instance of frontend object.
     *
     * @return object|PEAR_Error
     */
    public static function &getFrontendObject()
    {
        $a = &PEAR_Frontend::singleton();
        return $a;
    }

    // }}}
    // {{{ & setFrontendClass()

    /**
     * Load current frontend class.
     *
     * @param string $uiclass Name of class implementing the frontend
     *
     * @return object the frontend object, or a PEAR error
     */
    public static function &setFrontendClass($uiclass)
    {
        $a = &PEAR_Frontend::setFrontendClass($uiclass);
        return $a;
    }

    // }}}
    // {{{ setFrontendType()

    /**
     * Set current frontend.
     *
     * @param string $uitype Name of the frontend type (for example "CLI")
     *
     * @return object the frontend object, or a PEAR error
     */
    public static function setFrontendType($uitype)
    {
        $uiclass = 'PEAR_Frontend_' . $uitype;
        return PEAR_Command::setFrontendClass($uiclass);
    }

    // }}}
    // {{{ registerCommands()

    /**
     * Scan through the Command directory looking for classes
     * and see what commands they implement.
     *
     * @param bool   (optional) if FALSE (default), the new list of
     *               commands should replace the current one.  If TRUE,
     *               new entries will be merged with old.
     *
     * @param string (optional) where (what directory) to look for
     *               classes, defaults to the Command subdirectory of
     *               the directory from where this file (__FILE__) is
     *               included.
     *
     * @return bool TRUE on success, a PEAR error on failure
     */
    public static function registerCommands($merge = false, $dir = null)
    {
        $parser = new PEAR_XMLParser;
        if ($dir === null) {
            $dir = dirname(__FILE__) . '/Command';
        }
        if (!is_dir($dir)) {
            return PEAR::raiseError("registerCommands: opendir($dir) '$dir' does not exist or is not a directory");
        }
        $dp = @opendir($dir);
        if (empty($dp)) {
            return PEAR::raiseError("registerCommands: opendir($dir) failed");
        }
        if (!$merge) {
            $GLOBALS['_PEAR_Command_commandlist'] = array();
        }

        while ($file = readdir($dp)) {
            if ($file{0} == '.' || substr($file, -4) != '.xml') {
                continue;
            }

            $f = substr($file, 0, -4);
            $class = "PEAR_Command_" . $f;
            // List of commands
            if (empty($GLOBALS['_PEAR_Command_objects'][$class])) {
                $GLOBALS['_PEAR_Command_objects'][$class] = "$dir/" . $f . '.php';
            }

            $parser->parse(file_get_contents("$dir/$file"));
            $implements = $parser->getData();
            foreach ($implements as $command => $desc) {
                if ($command == 'attribs') {
                    continue;
                }

                if (isset($GLOBALS['_PEAR_Command_commandlist'][$command])) {
                    return PEAR::raiseError('Command "' . $command . '" already registered in ' .
                        'class "' . $GLOBALS['_PEAR_Command_commandlist'][$command] . '"');
                }

                $GLOBALS['_PEAR_Command_commandlist'][$command] = $class;
                $GLOBALS['_PEAR_Command_commanddesc'][$command] = $desc['summary'];
                if (isset($desc['shortcut'])) {
                    $shortcut = $desc['shortcut'];
                    if (isset($GLOBALS['_PEAR_Command_shortcuts'][$shortcut])) {
                        return PEAR::raiseError('Command shortcut "' . $shortcut . '" already ' .
                            'registered to command "' . $command . '" in class "' .
                            $GLOBALS['_PEAR_Command_commandlist'][$command] . '"');
                    }
                    $GLOBALS['_PEAR_Command_shortcuts'][$shortcut] = $command;
                }

                if (isset($desc['options']) && $desc['options']) {
                    foreach ($desc['options'] as $oname => $option) {
                        if (isset($option['shortopt']) && strlen($option['shortopt']) > 1) {
                            return PEAR::raiseError('Option "' . $oname . '" short option "' .
                                $option['shortopt'] . '" must be ' .
                                'only 1 character in Command "' . $command . '" in class "' .
                                $class . '"');
                        }
                    }
                }
            }
        }

        ksort($GLOBALS['_PEAR_Command_shortcuts']);
        ksort($GLOBALS['_PEAR_Command_commandlist']);
        @closedir($dp);
        return true;
    }

    // }}}
    // {{{ getCommands()

    /**
     * Get the list of currently supported commands, and what
     * classes implement them.
     *
     * @return array command => implementing class
     */
    public static function getCommands()
    {
        if (empty($GLOBALS['_PEAR_Command_commandlist'])) {
            PEAR_Command::registerCommands();
        }
        return $GLOBALS['_PEAR_Command_commandlist'];
    }

    // }}}
    // {{{ getShortcuts()

    /**
     * Get the list of command shortcuts.
     *
     * @return array shortcut => command
     */
    public static function getShortcuts()
    {
        if (empty($GLOBALS['_PEAR_Command_shortcuts'])) {
            PEAR_Command::registerCommands();
        }
        return $GLOBALS['_PEAR_Command_shortcuts'];
    }

    // }}}
    // {{{ getGetoptArgs()

    /**
     * Compiles arguments for getopt.
     *
     * @param string $command     command to get optstring for
     * @param string $short_args  (reference) short getopt format
     * @param array  $long_args   (reference) long getopt format
     *
     * @return void
     */
    public static function getGetoptArgs($command, &$short_args, &$long_args)
    {
        if (empty($GLOBALS['_PEAR_Command_commandlist'])) {
            PEAR_Command::registerCommands();
        }
        if (isset($GLOBALS['_PEAR_Command_shortcuts'][$command])) {
            $command = $GLOBALS['_PEAR_Command_shortcuts'][$command];
        }
        if (!isset($GLOBALS['_PEAR_Command_commandlist'][$command])) {
            return null;
        }
        $obj = &PEAR_Command::getObject($command);
        return $obj->getGetoptArgs($command, $short_args, $long_args);
    }

    // }}}
    // {{{ getDescription()

    /**
     * Get description for a command.
     *
     * @param  string $command Name of the command
     *
     * @return string command description
     */
    public static function getDescription($command)
    {
        if (!isset($GLOBALS['_PEAR_Command_commanddesc'][$command])) {
            return null;
        }
        return $GLOBALS['_PEAR_Command_commanddesc'][$command];
    }

    // }}}
    // {{{ getHelp()

    /**
     * Get help for command.
     *
     * @param string $command Name of the command to return help for
     */
    public static function getHelp($command)
    {
        $cmds = PEAR_Command::getCommands();
        if (isset($GLOBALS['_PEAR_Command_shortcuts'][$command])) {
            $command = $GLOBALS['_PEAR_Command_shortcuts'][$command];
        }
        if (isset($cmds[$command])) {
            $obj = &PEAR_Command::getObject($command);
            return $obj->getHelp($command);
        }
        return false;
    }
    // }}}
}<?php
/**
 * PEAR_Command_Common base class
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**
 * base class
 */
require_once 'phar://go-pear.phar/' . 'PEAR.php';

/**
 * PEAR commands base class
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class PEAR_Command_Common extends PEAR
{
    /**
     * PEAR_Config object used to pass user system and configuration
     * on when executing commands
     *
     * @var PEAR_Config
     */
    var $config;
    /**
     * @var PEAR_Registry
     * @access protected
     */
    var $_registry;

    /**
     * User Interface object, for all interaction with the user.
     * @var object
     */
    var $ui;

    var $_deps_rel_trans = array(
                                 'lt' => '<',
                                 'le' => '<=',
                                 'eq' => '=',
                                 'ne' => '!=',
                                 'gt' => '>',
                                 'ge' => '>=',
                                 'has' => '=='
                                 );

    var $_deps_type_trans = array(
                                  'pkg' => 'package',
                                  'ext' => 'extension',
                                  'php' => 'PHP',
                                  'prog' => 'external program',
                                  'ldlib' => 'external library for linking',
                                  'rtlib' => 'external runtime library',
                                  'os' => 'operating system',
                                  'websrv' => 'web server',
                                  'sapi' => 'SAPI backend'
                                  );

    /**
     * PEAR_Command_Common constructor.
     *
     * @access public
     */
    function __construct(&$ui, &$config)
    {
        parent::__construct();
        $this->config = &$config;
        $this->ui = &$ui;
    }

    /**
     * Return a list of all the commands defined by this class.
     * @return array list of commands
     * @access public
     */
    function getCommands()
    {
        $ret = array();
        foreach (array_keys($this->commands) as $command) {
            $ret[$command] = $this->commands[$command]['summary'];
        }

        return $ret;
    }

    /**
     * Return a list of all the command shortcuts defined by this class.
     * @return array shortcut => command
     * @access public
     */
    function getShortcuts()
    {
        $ret = array();
        foreach (array_keys($this->commands) as $command) {
            if (isset($this->commands[$command]['shortcut'])) {
                $ret[$this->commands[$command]['shortcut']] = $command;
            }
        }

        return $ret;
    }

    function getOptions($command)
    {
        $shortcuts = $this->getShortcuts();
        if (isset($shortcuts[$command])) {
            $command = $shortcuts[$command];
        }

        if (isset($this->commands[$command]) &&
              isset($this->commands[$command]['options'])) {
            return $this->commands[$command]['options'];
        }

        return null;
    }

    function getGetoptArgs($command, &$short_args, &$long_args)
    {
        $short_args = '';
        $long_args = array();
        if (empty($this->commands[$command]) || empty($this->commands[$command]['options'])) {
            return;
        }

        reset($this->commands[$command]['options']);
        while (list($option, $info) = each($this->commands[$command]['options'])) {
            $larg = $sarg = '';
            if (isset($info['arg'])) {
                if ($info['arg']{0} == '(') {
                    $larg = '==';
                    $sarg = '::';
                    $arg = substr($info['arg'], 1, -1);
                } else {
                    $larg = '=';
                    $sarg = ':';
                    $arg = $info['arg'];
                }
            }

            if (isset($info['shortopt'])) {
                $short_args .= $info['shortopt'] . $sarg;
            }

            $long_args[] = $option . $larg;
        }
    }

    /**
    * Returns the help message for the given command
    *
    * @param string $command The command
    * @return mixed A fail string if the command does not have help or
    *               a two elements array containing [0]=>help string,
    *               [1]=> help string for the accepted cmd args
    */
    function getHelp($command)
    {
        $config = &PEAR_Config::singleton();
        if (!isset($this->commands[$command])) {
            return "No such command \"$command\"";
        }

        $help = null;
        if (isset($this->commands[$command]['doc'])) {
            $help = $this->commands[$command]['doc'];
        }

        if (empty($help)) {
            // XXX (cox) Fallback to summary if there is no doc (show both?)
            if (!isset($this->commands[$command]['summary'])) {
                return "No help for command \"$command\"";
            }
            $help = $this->commands[$command]['summary'];
        }

        if (preg_match_all('/{config\s+([^\}]+)}/e', $help, $matches)) {
            foreach($matches[0] as $k => $v) {
                $help = preg_replace("/$v/", $config->get($matches[1][$k]), $help);
            }
        }

        return array($help, $this->getHelpArgs($command));
    }

    /**
     * Returns the help for the accepted arguments of a command
     *
     * @param  string $command
     * @return string The help string
     */
    function getHelpArgs($command)
    {
        if (isset($this->commands[$command]['options']) &&
            count($this->commands[$command]['options']))
        {
            $help = "Options:\n";
            foreach ($this->commands[$command]['options'] as $k => $v) {
                if (isset($v['arg'])) {
                    if ($v['arg'][0] == '(') {
                        $arg = substr($v['arg'], 1, -1);
                        $sapp = " [$arg]";
                        $lapp = "[=$arg]";
                    } else {
                        $sapp = " $v[arg]";
                        $lapp = "=$v[arg]";
                    }
                } else {
                    $sapp = $lapp = "";
                }

                if (isset($v['shortopt'])) {
                    $s = $v['shortopt'];
                    $help .= "  -$s$sapp, --$k$lapp\n";
                } else {
                    $help .= "  --$k$lapp\n";
                }

                $p = "        ";
                $doc = rtrim(str_replace("\n", "\n$p", $v['doc']));
                $help .= "        $doc\n";
            }

            return $help;
        }

        return null;
    }

    function run($command, $options, $params)
    {
        if (empty($this->commands[$command]['function'])) {
            // look for shortcuts
            foreach (array_keys($this->commands) as $cmd) {
                if (isset($this->commands[$cmd]['shortcut']) && $this->commands[$cmd]['shortcut'] == $command) {
                    if (empty($this->commands[$cmd]['function'])) {
                        return $this->raiseError("unknown command `$command'");
                    } else {
                        $func = $this->commands[$cmd]['function'];
                    }
                    $command = $cmd;

                    //$command = $this->commands[$cmd]['function'];
                    break;
                }
            }
        } else {
            $func = $this->commands[$command]['function'];
        }

        return $this->$func($command, $options, $params);
    }
}
<?php
/**
 * PEAR_Command_Install (install, upgrade, upgrade-all, uninstall, bundle, run-scripts commands)
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**
 * base class
 */
require_once 'phar://go-pear.phar/' . 'PEAR/Command/Common.php';

/**
 * PEAR commands for installation or deinstallation/upgrading of
 * packages.
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class PEAR_Command_Install extends PEAR_Command_Common
{
    // {{{ properties

    var $commands = array(
        'install' => array(
            'summary' => 'Install Package',
            'function' => 'doInstall',
            'shortcut' => 'i',
            'options' => array(
                'force' => array(
                    'shortopt' => 'f',
                    'doc' => 'will overwrite newer installed packages',
                    ),
                'loose' => array(
                    'shortopt' => 'l',
                    'doc' => 'do not check for recommended dependency version',
                    ),
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, install anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as installed',
                    ),
                'soft' => array(
                    'shortopt' => 's',
                    'doc' => 'soft install, fail silently, or upgrade if already installed',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT), use packagingroot for RPM',
                    ),
                'packagingroot' => array(
                    'shortopt' => 'P',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when packaging files, like RPM packaging',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'alldeps' => array(
                    'shortopt' => 'a',
                    'doc' => 'install all required and optional dependencies',
                    ),
                'onlyreqdeps' => array(
                    'shortopt' => 'o',
                    'doc' => 'install all required dependencies',
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to download any urls or contact channels',
                    ),
                'pretend' => array(
                    'shortopt' => 'p',
                    'doc' => 'Only list the packages that would be downloaded',
                    ),
                ),
            'doc' => '[channel/]<package> ...
Installs one or more PEAR packages.  You can specify a package to
install in four ways:

"Package-1.0.tgz" : installs from a local file

"http://example.com/Package-1.0.tgz" : installs from
anywhere on the net.

"package.xml" : installs the package described in
package.xml.  Useful for testing, or for wrapping a PEAR package in
another package manager such as RPM.

"Package[-version/state][.tar]" : queries your default channel\'s server
({config master_server}) and downloads the newest package with
the preferred quality/state ({config preferred_state}).

To retrieve Package version 1.1, use "Package-1.1," to retrieve
Package state beta, use "Package-beta."  To retrieve an uncompressed
file, append .tar (make sure there is no file by the same name first)

To download a package from another channel, prefix with the channel name like
"channel/Package"

More than one package may be specified at once.  It is ok to mix these
four ways of specifying packages.
'),
        'upgrade' => array(
            'summary' => 'Upgrade Package',
            'function' => 'doInstall',
            'shortcut' => 'up',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'upgrade packages from a specific channel',
                    'arg' => 'CHAN',
                    ),
                'force' => array(
                    'shortopt' => 'f',
                    'doc' => 'overwrite newer installed packages',
                    ),
                'loose' => array(
                    'shortopt' => 'l',
                    'doc' => 'do not check for recommended dependency version',
                    ),
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, upgrade anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as upgraded',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'alldeps' => array(
                    'shortopt' => 'a',
                    'doc' => 'install all required and optional dependencies',
                    ),
                'onlyreqdeps' => array(
                    'shortopt' => 'o',
                    'doc' => 'install all required dependencies',
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to download any urls or contact channels',
                    ),
                'pretend' => array(
                    'shortopt' => 'p',
                    'doc' => 'Only list the packages that would be downloaded',
                    ),
                ),
            'doc' => '<package> ...
Upgrades one or more PEAR packages.  See documentation for the
"install" command for ways to specify a package.

When upgrading, your package will be updated if the provided new
package has a higher version number (use the -f option if you need to
upgrade anyway).

More than one package may be specified at once.
'),
        'upgrade-all' => array(
            'summary' => 'Upgrade All Packages [Deprecated in favor of calling upgrade with no parameters]',
            'function' => 'doUpgradeAll',
            'shortcut' => 'ua',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'upgrade packages from a specific channel',
                    'arg' => 'CHAN',
                    ),
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, upgrade anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as upgraded',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT), use packagingroot for RPM',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'loose' => array(
                    'doc' => 'do not check for recommended dependency version',
                    ),
                ),
            'doc' => '
WARNING: This function is deprecated in favor of using the upgrade command with no params

Upgrades all packages that have a newer release available.  Upgrades are
done only if there is a release available of the state specified in
"preferred_state" (currently {config preferred_state}), or a state considered
more stable.
'),
        'uninstall' => array(
            'summary' => 'Un-install Package',
            'function' => 'doUninstall',
            'shortcut' => 'un',
            'options' => array(
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, uninstall anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not remove files, only register the packages as not installed',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to uninstall remotely',
                    ),
                ),
            'doc' => '[channel/]<package> ...
Uninstalls one or more PEAR packages.  More than one package may be
specified at once.  Prefix with channel name to uninstall from a
channel not in your default channel ({config default_channel})
'),
        'bundle' => array(
            'summary' => 'Unpacks a Pecl Package',
            'function' => 'doBundle',
            'shortcut' => 'bun',
            'options' => array(
                'destination' => array(
                   'shortopt' => 'd',
                    'arg' => 'DIR',
                    'doc' => 'Optional destination directory for unpacking (defaults to current path or "ext" if exists)',
                    ),
                'force' => array(
                    'shortopt' => 'f',
                    'doc' => 'Force the unpacking even if there were errors in the package',
                ),
            ),
            'doc' => '<package>
Unpacks a Pecl Package into the selected location. It will download the
package if needed.
'),
        'run-scripts' => array(
            'summary' => 'Run Post-Install Scripts bundled with a package',
            'function' => 'doRunScripts',
            'shortcut' => 'rs',
            'options' => array(
            ),
            'doc' => '<package>
Run post-installation scripts in package <package>, if any exist.
'),
    );

    // }}}
    // {{{ constructor

    /**
     * PEAR_Command_Install constructor.
     *
     * @access public
     */
    function __construct(&$ui, &$config)
    {
        parent::__construct($ui, $config);
    }

    // }}}

    /**
     * For unit testing purposes
     */
    function &getDownloader(&$ui, $options, &$config)
    {
        if (!class_exists('PEAR_Downloader')) {
            require_once 'phar://go-pear.phar/' . 'PEAR/Downloader.php';
        }
        $a = new PEAR_Downloader($ui, $options, $config);
        return $a;
    }

    /**
     * For unit testing purposes
     */
    function &getInstaller(&$ui)
    {
        if (!class_exists('PEAR_Installer')) {
            require_once 'phar://go-pear.phar/' . 'PEAR/Installer.php';
        }
        $a = new PEAR_Installer($ui);
        return $a;
    }

    function enableExtension($binaries, $type)
    {
        if (!($phpini = $this->config->get('php_ini', null, 'pear.php.net'))) {
            return PEAR::raiseError('configuration option "php_ini" is not set to php.ini location');
        }
        $ini = $this->_parseIni($phpini);
        if (PEAR::isError($ini)) {
            return $ini;
        }
        $line = 0;
        if ($type == 'extsrc' || $type == 'extbin') {
            $search = 'extensions';
            $enable = 'extension';
        } else {
            $search = 'zend_extensions';
            ob_start();
            phpinfo(INFO_GENERAL);
            $info = ob_get_contents();
            ob_end_clean();
            $debug = function_exists('leak') ? '_debug' : '';
            $ts = preg_match('/Thread Safety.+enabled/', $info) ? '_ts' : '';
            $enable = 'zend_extension' . $debug . $ts;
        }
        foreach ($ini[$search] as $line => $extension) {
            if (in_array($extension, $binaries, true) || in_array(
                  $ini['extension_dir'] . DIRECTORY_SEPARATOR . $extension, $binaries, true)) {
                // already enabled - assume if one is, all are
                return true;
            }
        }
        if ($line) {
            $newini = array_slice($ini['all'], 0, $line);
        } else {
            $newini = array();
        }
        foreach ($binaries as $binary) {
            if ($ini['extension_dir']) {
                $binary = basename($binary);
            }
            $newini[] = $enable . '="' . $binary . '"' . (OS_UNIX ? "\n" : "\r\n");
        }
        $newini = array_merge($newini, array_slice($ini['all'], $line));
        $fp = @fopen($phpini, 'wb');
        if (!$fp) {
            return PEAR::raiseError('cannot open php.ini "' . $phpini . '" for writing');
        }
        foreach ($newini as $line) {
            fwrite($fp, $line);
        }
        fclose($fp);
        return true;
    }

    function disableExtension($binaries, $type)
    {
        if (!($phpini = $this->config->get('php_ini', null, 'pear.php.net'))) {
            return PEAR::raiseError('configuration option "php_ini" is not set to php.ini location');
        }
        $ini = $this->_parseIni($phpini);
        if (PEAR::isError($ini)) {
            return $ini;
        }
        $line = 0;
        if ($type == 'extsrc' || $type == 'extbin') {
            $search = 'extensions';
            $enable = 'extension';
        } else {
            $search = 'zend_extensions';
            ob_start();
            phpinfo(INFO_GENERAL);
            $info = ob_get_contents();
            ob_end_clean();
            $debug = function_exists('leak') ? '_debug' : '';
            $ts = preg_match('/Thread Safety.+enabled/', $info) ? '_ts' : '';
            $enable = 'zend_extension' . $debug . $ts;
        }
        $found = false;
        foreach ($ini[$search] as $line => $extension) {
            if (in_array($extension, $binaries, true) || in_array(
                  $ini['extension_dir'] . DIRECTORY_SEPARATOR . $extension, $binaries, true)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            // not enabled
            return true;
        }
        $fp = @fopen($phpini, 'wb');
        if (!$fp) {
            return PEAR::raiseError('cannot open php.ini "' . $phpini . '" for writing');
        }
        if ($line) {
            $newini = array_slice($ini['all'], 0, $line);
            // delete the enable line
            $newini = array_merge($newini, array_slice($ini['all'], $line + 1));
        } else {
            $newini = array_slice($ini['all'], 1);
        }
        foreach ($newini as $line) {
            fwrite($fp, $line);
        }
        fclose($fp);
        return true;
    }

    function _parseIni($filename)
    {
        if (!file_exists($filename)) {
            return PEAR::raiseError('php.ini "' . $filename . '" does not exist');
        }

        if (filesize($filename) > 300000) {
            return PEAR::raiseError('php.ini "' . $filename . '" is too large, aborting');
        }

        ob_start();
        phpinfo(INFO_GENERAL);
        $info = ob_get_contents();
        ob_end_clean();
        $debug = function_exists('leak') ? '_debug' : '';
        $ts = preg_match('/Thread Safety.+enabled/', $info) ? '_ts' : '';
        $zend_extension_line = 'zend_extension' . $debug . $ts;
        $all = @file($filename);
        if ($all === false) {
            return PEAR::raiseError('php.ini "' . $filename .'" could not be read');
        }
        $zend_extensions = $extensions = array();
        // assume this is right, but pull from the php.ini if it is found
        $extension_dir = ini_get('extension_dir');
        foreach ($all as $linenum => $line) {
            $line = trim($line);
            if (!$line) {
                continue;
            }
            if ($line[0] == ';') {
                continue;
            }
            if (strtolower(substr($line, 0, 13)) == 'extension_dir') {
                $line = trim(substr($line, 13));
                if ($line[0] == '=') {
                    $x = trim(substr($line, 1));
                    $x = explode(';', $x);
                    $extension_dir = str_replace('"', '', array_shift($x));
                    continue;
                }
            }
            if (strtolower(substr($line, 0, 9)) == 'extension') {
                $line = trim(substr($line, 9));
                if ($line[0] == '=') {
                    $x = trim(substr($line, 1));
                    $x = explode(';', $x);
                    $extensions[$linenum] = str_replace('"', '', array_shift($x));
                    continue;
                }
            }
            if (strtolower(substr($line, 0, strlen($zend_extension_line))) ==
                  $zend_extension_line) {
                $line = trim(substr($line, strlen($zend_extension_line)));
                if ($line[0] == '=') {
                    $x = trim(substr($line, 1));
                    $x = explode(';', $x);
                    $zend_extensions[$linenum] = str_replace('"', '', array_shift($x));
                    continue;
                }
            }
        }
        return array(
            'extensions' => $extensions,
            'zend_extensions' => $zend_extensions,
            'extension_dir' => $extension_dir,
            'all' => $all,
        );
    }

    // {{{ doInstall()

    function doInstall($command, $options, $params)
    {
        if (!class_exists('PEAR_PackageFile')) {
            require_once 'phar://go-pear.phar/' . 'PEAR/PackageFile.php';
        }

        if (isset($options['installroot']) && isset($options['packagingroot'])) {
            return $this->raiseError('ERROR: cannot use both --installroot and --packagingroot');
        }

        $reg = &$this->config->getRegistry();
        $channel = isset($options['channel']) ? $options['channel'] : $this->config->get('default_channel');
        if (!$reg->channelExists($channel)) {
            return $this->raiseError('Channel "' . $channel . '" does not exist');
        }

        if (empty($this->installer)) {
            $this->installer = &$this->getInstaller($this->ui);
        }

        if ($command == 'upgrade' || $command == 'upgrade-all') {
            // If people run the upgrade command but pass nothing, emulate a upgrade-all
            if ($command == 'upgrade' && empty($params)) {
                return $this->doUpgradeAll($command, $options, $params);
            }
            $options['upgrade'] = true;
        } else {
            $packages = $params;
        }

        $instreg = &$reg; // instreg used to check if package is installed
        if (isset($options['packagingroot']) && !isset($options['upgrade'])) {
            $packrootphp_dir = $this->installer->_prependPath(
                $this->config->get('php_dir', null, 'pear.php.net'),
                $options['packagingroot']);
            $metadata_dir = $this->config->get('metadata_dir', null, 'pear.php.net');
            if ($metadata_dir) {
                $metadata_dir = $this->installer->_prependPath(
                    $metadata_dir,
                    $options['packagingroot']);
            }
            $instreg = new PEAR_Registry($packrootphp_dir, false, false, $metadata_dir); // other instreg!

            if ($this->config->get('verbose') > 2) {
                $this->ui->outputData('using package root: ' . $options['packagingroot']);
            }
        }

        $abstractpackages = $otherpackages = array();
        // parse params
        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);

        foreach ($params as $param) {
            if (strpos($param, 'http://') === 0) {
                $otherpackages[] = $param;
                continue;
            }

            if (strpos($param, 'channel://') === false && @file_exists($param)) {
                if (isset($options['force'])) {
                    $otherpackages[] = $param;
                    continue;
                }

                $pkg = new PEAR_PackageFile($this->config);
                $pf  = $pkg->fromAnyFile($param, PEAR_VALIDATE_DOWNLOADING);
                if (PEAR::isError($pf)) {
                    $otherpackages[] = $param;
                    continue;
                }

                $exists   = $reg->packageExists($pf->getPackage(), $pf->getChannel());
                $pversion = $reg->packageInfo($pf->getPackage(), 'version', $pf->getChannel());
                $version_compare = version_compare($pf->getVersion(), $pversion, '<=');
                if ($exists && $version_compare) {
                    if ($this->config->get('verbose')) {
                        $this->ui->outputData('Ignoring installed package ' .
                            $reg->parsedPackageNameToString(
                            array('package' => $pf->getPackage(),
                                  'channel' => $pf->getChannel()), true));
                    }
                    continue;
                }
                $otherpackages[] = $param;
                continue;
            }

            $e = $reg->parsePackageName($param, $channel);
            if (PEAR::isError($e)) {
                $otherpackages[] = $param;
            } else {
                $abstractpackages[] = $e;
            }
        }
        PEAR::staticPopErrorHandling();

        // if there are any local package .tgz or remote static url, we can't
        // filter.  The filter only works for abstract packages
        if (count($abstractpackages) && !isset($options['force'])) {
            // when not being forced, only do necessary upgrades/installs
            if (isset($options['upgrade'])) {
                $abstractpackages = $this->_filterUptodatePackages($abstractpackages, $command);
            } else {
                $count = count($abstractpackages);
                foreach ($abstractpackages as $i => $package) {
                    if (isset($package['group'])) {
                        // do not filter out install groups
                        continue;
                    }

                    if ($instreg->packageExists($package['package'], $package['channel'])) {
                        if ($count > 1) {
                            if ($this->config->get('verbose')) {
                                $this->ui->outputData('Ignoring installed package ' .
                                    $reg->parsedPackageNameToString($package, true));
                            }
                            unset($abstractpackages[$i]);
                        } elseif ($count === 1) {
                            // Lets try to upgrade it since