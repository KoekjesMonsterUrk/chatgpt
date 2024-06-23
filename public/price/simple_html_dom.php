<?php
/**
 * simple_html_dom.php
 * A simple HTML DOM parser
 * 
 * Original library by S.C. Chen (http://simplehtmldom.sourceforge.net/)
 * Modified by John Schlick (https://github.com/sunra/php-simple-html-dom-parser)
 */

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_TYPE_ROOT',    5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_QUOTE',   2);
define('HDOM_INFO_SPACE',   3);
define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);
define('HDOM_INFO_ENDSPACE',7);

class simple_html_dom {
    public $root = null;
    public $nodes = array();
    public $callback = null;
    public $lowercase = false;
    protected $pos;
    protected $doc;
    protected $char;
    protected $size;
    protected $cursor;
    protected $parent;
    protected $noise = array();
    protected $token_blank = " \t\r\n";
    protected $token_equal = ' =/>';
    protected $token_slash = " />\r\n\t";
    protected $token_attr = ' >';
    // Note that this is referenced by a child node, and so it needs to be public for that node to see this information.
    public $_charset = '';
    public $_target_charset = '';
    public $default_br_text = "";
    public $default_span_text = "";

    /**
     * simple_html_dom constructor.
     * @param string $str
     * @param bool $lowercase
     * @param bool $forceTagsClosed
     * @param string $target_charset
     * @param bool $stripRN
     * @param string $defaultBRText
     * @param string $defaultSpanText
     */
    function __construct($str = null, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "", $defaultSpanText = "")
    {
        if ($str) {
            if (preg_match("/^http:\/\//i",$str) || is_file($str)) {
                $this->load_file($str);
            } else {
                $this->load($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
            }
        }
        // Set the target charset.
        if (!empty($target_charset)) {
            $this->_target_charset = $target_charset;
        }
    }

    /**
     * @param string $str
     * @param bool $lowercase
     * @param bool $stripRN
     * @param string $defaultBRText
     * @param string $defaultSpanText
     */
    function load($str, $lowercase=true, $stripRN=true, $defaultBRText="", $defaultSpanText="")
    {
        global $debugObject;

        // prepare
        $this->prepare($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
        // strip out comments
        $this->remove_noise("'<!--(.*?)-->'is", true);
        // strip out <style> tags
        $this->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is");
        $this->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is");
        // strip out <script> tags
        $this->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is");
        $this->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is");
        // strip out preformatted tags
        $this->remove_noise("'<\s*pre[^>]*[^/]>(.*?)<\s*/\s*pre\s*>'is");
        $this->remove_noise("'<\s*pre\s*>(.*?)<\s*/\s*pre\s*>'is");
        // strip out <code> tags
        $this->remove_noise("'<\s*code[^>]*[^/]>(.*?)<\s*/\s*code\s*>'is");
        $this->remove_noise("'<\s*code\s*>(.*?)<\s*/\s*code\s*>'is");
        // strip out <![CDATA[ ... ]]> sections
        $this->remove_noise("'<!\[CDATA\[(.*?)\]\]>'is", true);

        // remove White Space at the beginning of the doc
        $this->remove_noise("'^\s*'");

        // pre check
        $this->remove_noise("'^[\s]+<[\s]+'", true);

        // strip out some strange symbols that appear in documents
        $this->remove_noise("'<(?(?=\\?)|[\\?])[^>]+>'is");

        // parse the document
        while ($this->parse())
        {
            ;
        }
        // check
        $this->root->_[HDOM_INFO_END] = $this->cursor;
    }

    // load html from file
    function load_file()
    {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

    /**
     * @param bool $echo
     * @return string
     */
    function save($echo=false)
    {
        $ret = $this->root->innertext();
        if ($echo) echo $ret;
        return $ret;
    }

    // prepare HTML data and init everything
    protected function prepare($str, $lowercase=true, $stripRN=true, $defaultBRText="", $defaultSpanText="")
    {
        $this->clear();
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        // set the length of content
        $this->size = strlen($str);
        // set the content
        $this->doc = $str;
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->root = new simple_html_dom_node($this);
        $this->root->tag = 'root';
        $this->root->_[HDOM_INFO_BEGIN] = -1;
        $this->root->_[HDOM_INFO_END] = $this->size;
        $this->parent = $this->root;
        if ($stripRN) {
            $this->doc = str_replace("\r", " ", $this->doc);
        }
        $this->char = $this->doc[0];
    }

    // parse html content
    protected function parse()
    {
        // set end position if no end tag
        if (($this->char === null) && ($this->parent->tag !== 'root')) {
            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            return false;
        }

        $s = $this->copy_until_char('<');
        $parent = $this->parent;

        // if there is no start tag, must be text content
        if ($s === '') {
            $this->char = $this->doc[++$this->pos];
            return true;
        }

        // no need to check pre or code tags (script is removed already)
        if ($parent->tag !== 'pre' && $parent->tag !== 'code') {
            // check for white space
            $s = $this->remove_noise($s, false);
            if ($s === '') {
                $this->char = $this->doc[++$this->pos];
                return true;
            }

            $s = $this->restore_noise($s);
        }

        // check for white space
        if ($s !== '') {
            $node = new simple_html_dom_node($this);
            $this->nodes[] = $node;
            $node->_[HDOM_INFO_BEGIN] = $this->cursor;
            $node->tag = 'text';
            $node->nodetype = HDOM_TYPE_TEXT;
            $node->_[HDOM_INFO_TEXT] = $s;
            $this->cursor += strlen($s);
            $parent->nodes[] = $node;
        }

        if ($this->char === '<') {
            // check for end tag
            if ($this->read_tag()) {
                return true;
            }
        }

        $this->char = $this->doc[++$this->pos];
        return true;
    }

    // read tag info
    protected function read_tag()
    {
        // set end position if no end tag
        if (($this->char === null) && ($this->parent->tag !== 'root')) {
            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            return false;
        }

        if ($this->char === '<') {
            $this->char = $this->doc[++$this->pos];
        }

        // check for comment
        if ($this->char === '!') {
            $node = new simple_html_dom_node($this);
            $this->nodes[] = $node;
            $node->_[HDOM_INFO_BEGIN] = $this->cursor;
            $node->tag = 'comment';
            $node->nodetype = HDOM_TYPE_COMMENT;
            $node->_[HDOM_INFO_TEXT] = $this->copy_until_char('>');
            $this->cursor += strlen($node->_[HDOM_INFO_TEXT]) + 2;
            $this->char = $this->doc[++$this->pos];
            $this->parent->nodes[] = $node;
            return true;
        }

        // check for text
        if ($this->char === '/') {
            $this->char = $this->doc[++$this->pos];
            $node = new simple_html_dom_node($this);
            $this->nodes[] = $node;
            $node->_[HDOM_INFO_BEGIN] = $this->cursor;
            $node->tag = $this->copy_until_char('>');
            $this->char = $this->doc[++$this->pos];
            // set end node info
            $node->_[HDOM_INFO_END] = $this->cursor;
            $this->cursor++;
            $node->nodetype = HDOM_TYPE_ENDTAG;
            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            $this->parent = $this->parent->parent;
            return true;
        }

        $node = new simple_html_dom_node($this);
        $this->nodes[] = $node;
        $node->_[HDOM_INFO_BEGIN] = $this->cursor;
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $node->tag = $this->copy_until_char(' >');
        // read tag
        while ($this->char !== '>' && $this->char !== '/') {
            $name = $this->copy_until_char('=');
            if ($this->char === '=') {
                $this->char = $this->doc[++$this->pos];
                if ($this->char === '"') {
                    $quote_type = HDOM_QUOTE_DOUBLE;
                } else {
                    if ($this->char === "'") {
                        $quote_type = HDOM_QUOTE_SINGLE;
                    } else {
                        $quote_type = HDOM_QUOTE_NO;
                    }
                }
                if ($quote_type === HDOM_QUOTE_DOUBLE) {
                    $this->char = $this->doc[++$this->pos];
                    $value = $this->copy_until_char('"');
                    $this->char = $this->doc[++$this->pos];
                } else {
                    if ($quote_type === HDOM_QUOTE_SINGLE) {
                        $this->char = $this->doc[++$this->pos];
                        $value = $this->copy_until_char("'");
                        $this->char = $this->doc[++$this->pos];
                    } else {
                        $value = $this->copy_until_char($this->token_attr);
                    }
                }
                if (!empty($name)) {
                    $node->_[HDOM_INFO_SPACE][] = ' ';
                }
                $node->_[HDOM_INFO_SPACE][] = $name;
                $node->_[HDOM_INFO_SPACE][] = $value;
                $node->_[HDOM_INFO_SPACE][] = $quote_type;
                $node->_[HDOM_INFO_SPACE][] = ' ';
            }
            $this->char = $this->doc[++$this->pos];
        }
        if ($this->char === '/') {
            $node->_[HDOM_INFO_ENDSPACE] = ' ';
            $node->nodetype = HDOM_TYPE_ELEMENT | HDOM_TYPE_ENDTAG;
        }
        $this->char = $this->doc[++$this->pos];
        $node->_[HDOM_INFO_END] = $this->cursor++;
        $this->parent->nodes[] = $node;
        if ($node->nodetype !== (HDOM_TYPE_ELEMENT | HDOM_TYPE_ENDTAG)) {
            $this->parent = $node;
        }
        return true;
    }

    // copy until the first occurrence of character from the string
    protected function copy_until_char($char)
    {
        $pos = strpos($this->doc, $char, $this->pos);
        if ($pos === false) {
            return '';
        }
        $ret = substr($this->doc, $this->pos, $pos - $this->pos);
        $this->pos = $pos;
        $this->char = $this->doc[$this->pos];
        return $ret;
    }

    // remove noise from HTML content
    protected function remove_noise($pattern, $remove_tag=false)
    {
        preg_match_all($pattern, $this->doc, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($matches as $match) {
            $key = '___noise___'.sprintf('% 3d', count($this->noise)+100);
            $this->noise[$key] = $match[0][0];
            $this->doc = substr_replace($this->doc, $key, $match[0][1], strlen($match[0][0]));
            if ($remove_tag) {
                $this->remove_noise($pattern, true);
            }
        }
        $this->size = strlen($this->doc);
    }

    // restore noise to HTML content
    protected function restore_noise($text)
    {
        while (($pos = strpos($text, '___noise___'))!==false) {
            $key = substr($text, $pos, 11);
            if (isset($this->noise[$key])) {
                $text = substr_replace($text, $this->noise[$key], $pos, 11);
            }
        }
        return $text;
    }

    // clear
    function clear()
    {
        foreach ($this->nodes as $node) {
            $node->clear();
            $node = null;
        }
        if (isset($this->doc)) {
            $this->doc = null;
        }
        $this->pos = null;
        $this->size = null;
        $this->char = null;
        $this->cursor = null;
        $this->noise = null;
    }

    // destructor
    function __destruct()
    {
        $this->clear();
    }
}

// simple html dom node
class simple_html_dom_node {
    public $nodetype = HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent = null;
    public $info = array();
    public $doc = null;
    public $_ = array();

    function __construct($dom)
    {
        $this->dom = $dom;
    }

    function __destruct()
    {
        $this->clear();
    }

    // clear
    function clear()
    {
        $this->nodetype = HDOM_TYPE_TEXT;
        $this->tag = 'text';
        $this->attr = array();
        $this->children = array();
        $this->nodes = array();
        $this->parent = null;
        $this->info = array();
        $this->doc = null;
        $this->_ = array();
    }

    // get the node's inner html
    function innertext()
    {
        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->_[HDOM_INFO_TEXT];
        }
        if ($this->nodetype == HDOM_TYPE_COMMENT) {
            return $this->_[HDOM_INFO_TEXT];
        }
        $ret = '';
        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }
        return $ret;
    }

    // get the node's outer html
    function outertext()
    {
        if ($this->tag == 'text') {
            return $this->_[HDOM_INFO_TEXT];
        }
        if ($this->nodetype == HDOM_TYPE_COMMENT) {
            return $this->_[HDOM_INFO_TEXT];
        }
        $ret = '<'.$this->tag;
        foreach ($this->attr as $key => $val) {
            if ($val === null || $val === '') {
                $ret .= ' '.$key;
            } else {
                $ret .= ' '.$key.'="'.htmlspecialchars($val, ENT_QUOTES).'"';
            }
        }
        if ($this->nodetype == (HDOM_TYPE_ELEMENT | HDOM_TYPE_ENDTAG)) {
            $ret .= ' />';
        } else {
            $ret .= '>';
            if ($this->nodes) {
                foreach ($this->nodes as $n) {
                    $ret .= $n->outertext();
                }
            }
            $ret .= '</'.$this->tag.'>';
        }
        return $ret;
    }
}

// get html dom from file
function file_get_html()
{
    $args = func_get_args();
    $str = call_user_func_array('file_get_contents', $args);
    if ($str === false) {
        return false;
    }
    $dom = new simple_html_dom();
    $dom->load($str);
    return $dom;
}

// get html dom from string
function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset='UTF-8', $stripRN=true, $defaultBRText="", $defaultSpanText="")
{
    $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
    if (empty($str) || strlen($str) > 600000) { // Won't catch error, will ignore silently.
        $dom->clear();
        return false;
    }
    $dom->load($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
    return $dom;
}
?>
