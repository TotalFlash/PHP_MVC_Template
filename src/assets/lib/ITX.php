<?php
/**
 * Integrated Template - IT
 *
 * PHP version 4
 *
 * Copyright (c) 1997-2007 Ulf Wendel, Pierre-Alain Joye,
 *                         David Soria Parra
 *
 * This source file is subject to the New BSD license, That is bundled
 * with this package in the file LICENSE, and is available through
 * the world-wide-web at
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the new BSD license and are unable
 * to obtain it through the world-wide-web, please send a note to
 * pajoye@php.net so we can mail you a copy immediately.
 *
 * Author: Ulf Wendel <ulf.wendel@phpdoc.de>
 *         Pierre-Alain Joye <pajoye@php.net>
 *         David Soria Parra <dsp@php.net>
 *
 * @category HTML
 * @package  HTML_Template_IT
 * @author   Ulf Wendel <uw@netuse.de>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @version  CVS: $Id$
 * @link     http://pear.php.net/packages/HTML_Template_IT
 * @access   public
 */

require_once 'assets/lib/Template/IT.php';
require_once 'assets/lib/Template/IT_Error.php';

/**
 * Integrated Template Extension - ITX
 *
 * With this class you get the full power of the phplib template class.
 * You may have one file with blocks in it but you have as well one main file
 * and multiple files one for each block. This is quite useful when you have
 * user configurable websites. Using blocks not in the main template allows
 * you to modify some parts of your layout easily.
 *
 * Note that you can replace an existing block and add new blocks at runtime.
 * Adding new blocks means changing a variable placeholder to a block.
 *
 * @category HTML
 * @package  HTML_Template_IT
 * @author   Ulf Wendel <uw@netuse.de>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @link     http://pear.php.net/packages/HTML_Template_IT
 * @access   public
 */
class HTML_Template_ITX extends HTML_Template_IT
{
  /**
   * Array with all warnings.
   * @var    array
   * @access public
   * @see    $printWarning, $haltOnWarning, warning()
   */
  var $warn = array();

  /**
   * Print warnings?
   * @var    array
   * @access public
   * @see    $haltOnWarning, $warn, warning()
   */
  var $printWarning = false;

  /**
   * Call die() on warning?
   * @var    boolean
   * @access public
   * @see    $warn, $printWarning, warning()
   */
  var $haltOnWarning = false;

  /**
   * RegExp used to test for a valid blockname.
   * @var string
   * @access private
   */
  var $checkblocknameRegExp = '';

  /**
   * Functionnameprefix used when searching function calls in the template.
   * @var string
   * @access public
   */
  var $functionPrefix = 'func_';

  /**
   * Functionname RegExp.
   * @var string
   * @access public
   */
  var $functionnameRegExp = '[_a-zA-Z]+[A-Za-z_0-9]*';

  /**
   * RegExp used to grep function calls in the template.
   *
   * The variable gets set by the constructor.
   *
   * @access private
   * @var string
   * @see HTML_Template_IT()
   */
  var $functionRegExp = '';

  /**
   * List of functions found in the template.
   *
   * @access private
   * @var array
   */
  var $functions = array();

  /**
   * List of callback functions specified by the user.
   *
   * @access private
   * @var array
   */
  var $callback = array();

  /**
   * Builds some complex regexps and calls the constructor
   * of the parent class.
   *
   * Make sure that you call this constructor if you derive your own
   * template class from this one.
   *
   * @param string $root Root node?
   *
   * @access public
   * @see    HTML_Template_IT()
   */
  function __construct($root = '')
  {

    $this->checkblocknameRegExp = '@' . $this->blocknameRegExp . '@';

    $this->functionRegExp = '@' . $this->functionPrefix . '(' .
      $this->functionnameRegExp . ')\s*\(@sm';

    parent::__construct($root);
  } // end func constructor

  /**
   * Clears all datafields of the object and rebuild the internal blocklist
   *
   * LoadTemplatefile() and setTemplate() automatically call this function
   * when a new template is given. Don't use this function
   * unless you know what you're doing.
   *
   * @access private
   * @return null
   */
  function init()
  {
    $this->free();
    $this->buildFunctionlist();
    $this->findBlocks($this->template);

    // we don't need it any more
    $this->template = '';
    $this->buildBlockvariablelist();

  } // end func init

  /**
   * Replaces an existing block with new content.
   *
   * This function will replace a block of the template and all blocks
   * contained in the replaced block and add a new block instead, means
   * you can dynamically change your template.
   *
   * Note that changing the template structure violates one of the IT[X]
   * development goals. I've tried to write a simple to use template engine
   * supporting blocks. In contrast to other systems IT[X] analyses the way
   * you've nested blocks and knows which block belongs into another block.
   * The nesting information helps to make the API short and simple. Replacing
   * blocks does not only mean that IT[X] has to update the nesting
   * information (relatively time consumpting task) but you have to make sure
   * that you do not get confused due to the template change itself.
   *
   * @param string  $block        Blockname
   * @param string  $template     Blockcontent
   * @param boolean $keep_content true if the new block inherits the content
   *                              of the old block
   *
   * @return   boolean
   * @throws   IT_Error
   * @see      replaceBlockfile(), addBlock(), addBlockfile()
   * @access   public
   */
  function replaceBlock($block, $template, $keep_content = false)
  {
    if (!isset($this->blocklist[$block])) {
      return new IT_Error("The block "."'$block'".
        " does not exist in the template and thus it can't be replaced.",
        __FILE__, __LINE__);
    }

    if ($template == '') {
      return new IT_Error('No block content given.', __FILE__, __LINE__);
    }

    if ($keep_content) {
      $blockdata = $this->blockdata[$block];
    }

    // remove all kinds of links to the block / data of the block
    $this->removeBlockData($block);

    $template = "<!-- BEGIN $block -->" . $template . "<!-- END $block -->";
    $parents  = $this->blockparents[$block];

    $this->findBlocks($template);
    $this->blockparents[$block] = $parents;

    // KLUDGE: rebuild the list for all block - could be done faster
    $this->buildBlockvariablelist();

    if ($keep_content) {
      $this->blockdata[$block] = $blockdata;
    }

    // old TODO - I'm not sure if we need this
    // update caches

    return true;
  } // end func replaceBlock

  /**
   * Replaces an existing block with new content from a file.
   *
   * @param string  $block        Blockname
   * @param string  $filename     Name of the file that contains the blockcontent
   * @param boolean $keep_content true if the new block inherits the content of
   *                              the old block
   *
   * @brother replaceBlock()
   * @access  public
   * @return null
   */
  function replaceBlockfile($block, $filename, $keep_content = false)
  {
    return $this->replaceBlock($block, $this->getFile($filename), $keep_content);
  } // end func replaceBlockfile

  /**
   * Adds a block to the template changing a variable placeholder
   * to a block placeholder.
   *
   * Add means "replace a variable placeholder by a new block".
   * This is different to PHPLibs templates. The function loads a
   * block, creates a handle for it and assigns it to a certain
   * variable placeholder. To to the same with PHPLibs templates you would
   * call set_file() to create the handle and parse() to assign the
   * parsed block to a variable. By this PHPLibs templates assume
   * that you tend to assign a block to more than one one placeholder.
   * To assign a parsed block to more than only the placeholder you specify
   * in this function you have to use a combination of getBlock()
   * and setVariable().
   *
   * As no updates to cached data is necessary addBlock() and addBlockfile()
   * are rather "cheap" meaning quick operations.
   *
   * The block content must not start with <!-- BEGIN blockname -->
   * and end with <!-- END blockname --> this would cause overhead and
   * produce an error.
   *
   * @param string $placeholder Name of the variable placeholder, the name
   *                            must be unique within the template.
   * @param string $blockname   Name of the block to be added
   * @param string $template    Content of the block
   *
   * @return   boolean
   * @throws   IT_Error
   * @see      addBlockfile()
   * @access   public
   */
  function addBlock($placeholder, $blockname, $template)
  {
    // Don't trust any user even if it's a programmer or yourself...
    if ($placeholder == '') {
      return new IT_Error('No variable placeholder given.',
        __FILE__, __LINE__);
    } elseif ($blockname == '' ||
      !preg_match($this->checkblocknameRegExp, $blockname)
    ) {
      return new IT_Error("No or invalid blockname '$blockname' given.",
        __FILE__, __LINE__);
    } elseif ($template == '') {
      return new IT_Error('No block content given.', __FILE__, __LINE__);
    } elseif (isset($this->blocklist[$blockname])) {
      return new IT_Error('The block already exists.',
        __FILE__, __LINE__);
    }

    // find out where to insert the new block
    $parents = $this->findPlaceholderBlocks($placeholder);
    if (count($parents) == 0) {

      return new IT_Error("The variable placeholder".
        " '$placeholder' was not found in the template.",
        __FILE__, __LINE__);

    } elseif (count($parents) > 1) {

      reset($parents);
      foreach ($parents as $k => $parent) {
        $msg .= "$parent, ";
      }
      $msg = substr($parent, -2);

      return new IT_Error("The variable placeholder "."'$placeholder'".
        " must be unique, found in multiple blocks '$msg'.",
        __FILE__, __LINE__);
    }

    $template = "<!-- BEGIN $blockname -->"
      . $template
      . "<!-- END $blockname -->";
    $this->findBlocks($template);
    if ($this->flagBlocktrouble) {
      return false;    // findBlocks() already throws an exception
    }

    $this->blockinner[$parents[0]][] = $blockname;

    $escblockname = '__' . $blockname . '__';

    $this->blocklist[$parents[0]] = preg_replace(
      '@' . $this->openingDelimiter . $placeholder .
      $this->closingDelimiter . '@',
      $this->openingDelimiter . $escblockname . $this->closingDelimiter,
      $this->blocklist[$parents[0]]
    );

    $this->deleteFromBlockvariablelist($parents[0], $placeholder);
    $this->updateBlockvariablelist($blockname);

    return true;
  } // end func addBlock

  /**
   * Adds a block taken from a file to the template changing a variable
   * placeholder to a block placeholder.
   *
   * @param string $placeholder Name of the variable placeholder to be converted
   * @param string $blockname   Name of the block to be added
   * @param string $filename    File that contains the block
   *
   * @brother    addBlock()
   * @access     public
   * @return     null
   */
  function addBlockfile($placeholder, $blockname, $filename)
  {
    return $this->addBlock($placeholder, $blockname, $this->getFile($filename));
  } // end func addBlockfile

  /**
   * Returns the name of the (first) block that contains
   * the specified placeholder.
   *
   * @param string $placeholder Name of the placeholder you're searching
   * @param string $block       Name of the block to scan. If left out (default)
   *                            all blocks are scanned.
   *
   * @return   string  Name of the (first) block that contains
   *                   the specified placeholder.
   *                   If the placeholder was not found or an error occurred
   *                   an empty string is returned.
   * @throws   IT_Error
   * @access   public
   */
  function placeholderExists($placeholder, $block = '')
  {
    if ($placeholder == '') {
      new IT_Error('No placeholder name given.', __FILE__, __LINE__);
      return '';
    }

    if ($block != '' && !isset($this->blocklist[$block])) {
      new IT_Error("Unknown block '$block'.", __FILE__, __LINE__);
      return '';
    }

    // name of the block where the given placeholder was found
    $found = '';

    if ($block != '') {
      if (is_array($variables = $this->blockvariables[$block])) {
        // search the value in the list of blockvariables
        reset($variables);
        foreach ($variables as $k => $variable) {
          if ($k == $placeholder) {
            $found = $block;
            break;
          }
        }
      }
    } else {

      // search all blocks and return the name of the first block that
      // contains the placeholder
      reset($this->blockvariables);
      foreach ($this->blockvariables as $blockname => $variables) {
        if (is_array($variables) && isset($variables[$placeholder])) {
          $found = $blockname;
          break;
        }
      }
    }

    return $found;
  } // end func placeholderExists

  /**
   * Checks the list of function calls in the template and
   * calls their callback function.
   *
   * @access  public
   * @return  null
   */
  function performCallback()
  {
    reset($this->functions);
    foreach ($this->functions as $func_id => $function) {
      if (isset($this->callback[$function['name']])) {
        if ($this->callback[$function['name']]['expandParameters']) {
          $callFunction = 'call_user_func_array';
        } else {
          $callFunction = 'call_user_func';
        }

        if ($this->callback[$function['name']]['object'] != '') {
          $call = $callFunction(
            array(
              &$GLOBALS[$this->callback[$function['name']]['object']],
              $this->callback[$function['name']]['function']),
            $function['args']);

        } else {
          $call = $callFunction(
            $this->callback[$function['name']]['function'],
            $function['args']);
        }
        $this->variableCache['__function' . $func_id . '__'] = $call;
      }
    }

  } // end func performCallback

  /**
   * Returns a list of all function calls in the current template.
   *
   * @return   array
   * @access   public
   */
  function getFunctioncalls()
  {
    return $this->functions;
  } // end func getFunctioncalls

  /**
   * Replaces a function call with the given replacement.
   *
   * @param int    $functionID  Function ID
   * @param string $replacement Replacement
   *
   * @access   public
   * @deprecated
   * @return null
   */
  function setFunctioncontent($functionID, $replacement)
  {
    $this->variableCache['__function' . $functionID . '__'] = $replacement;
  } // end func setFunctioncontent

  /**
   * Sets a callback function.
   *
   * IT[X] templates (note the X) can contain simple function calls.
   * "function call" means that the editor of the template can add
   * special placeholder to the template like 'func_h1("embedded in h1")'.
   * IT[X] will grab this function calls and allow you to define a callback
   * function for them.
   *
   * This is an absolutely evil feature. If your application makes heavy
   * use of such callbacks and you're even implementing if-then etc. on
   * the level of a template engine you're reinventing the wheel... - that's
   * actually how PHP came into life. Anyway, sometimes it's handy.
   *
   * Consider also using XML/XSLT or native PHP. And please do not push
   * IT[X] any further into this direction of adding logics to the template
   * engine.
   *
   * For those of you ready for the X in IT[X]:
   *
   * <?php
   * ...
   * function h_one($args) {
   *    return sprintf('<h1>%s</h1>', $args[0]);
   * }
   *
   * ...
   * $itx = new HTML_Template_ITX(...);
   * ...
   * $itx->setCallbackFunction('h1', 'h_one');
   * $itx->performCallback();
   * ?>
   *
   * template:
   * func_h1('H1 Headline');
   *
   * @param string  $tplfunction              Function name in the template
   * @param string  $callbackfunction         Name of the callback function
   * @param string  $callbackobject           Name of the callback object
   * @param boolean $expandCallbackParameters If the callback is called with
   *                                          a list of parameters or with an
   *                                          array holding the parameters
   *
   * @return     boolean   False on failure.
   * @throws     IT_Error
   * @access     public
   * @deprecated The $callbackobject parameter is deprecated since
   *             version 1.2 and might be dropped in further versions.
   */
  function setCallbackFunction($tplfunction, $callbackfunction,
                               $callbackobject = '',
                               $expandCallbackParameters = false) {
    if ($tplfunction == '' || $callbackfunction == '') {
      return new IT_Error("No template function "."('$tplfunction')".
        " and/or no callback function ('$callbackfunction') given.",
        __FILE__, __LINE__);
    }
    $this->callback[$tplfunction] = array(
      'function' => $callbackfunction,
      'object'   => $callbackobject,
      'expandParameters' => (boolean)
      $expandCallbackParameters);

    return true;
  } // end func setCallbackFunction

  /**
   * Sets the Callback function lookup table
   *
   * @param array $functions function table
   *                           array[templatefunction] =
   *                               array(
   *                                   "function" => userfunction,
   *                                   "object" => userobject
   *                               )
   *
   * @access    public
   * @return null
   */
  function setCallbackFuntiontable($functions)
  {
    $this->callback = $functions;
  } // end func setCallbackFunctiontable

  /**
   * Recursively removes all data associated with a block, including
   * all inner blocks
   *
   * @param string $block block to be removed
   *
   * @return null
   * @access   private
   */
  function removeBlockData($block)
  {
    if (isset($this->blockinner[$block])) {
      foreach ($this->blockinner[$block] as $k => $inner) {
        $this->removeBlockData($inner);
      }

      unset($this->blockinner[$block]);
    }

    unset($this->blocklist[$block]);
    unset($this->blockdata[$block]);
    unset($this->blockvariables[$block]);
    unset($this->touchedBlocks[$block]);

  } // end func removeBlockinner

  /**
   * Returns a list of blocknames in the template.
   *
   * @return    array    [blockname => blockname]
   * @access    public
   * @see       blockExists()
   */
  function getBlocklist()
  {
    $blocklist = array();
    foreach ($this->blocklist as $block => $content) {
      $blocklist[$block] = $block;
    }

    return $blocklist;
  } // end func getBlocklist

  /**
   * Checks whether a block exists.
   *
   * @param string $blockname Blockname
   *
   * @return   boolean
   * @access   public
   * @see      getBlocklist()
   */
  function blockExists($blockname)
  {
    return isset($this->blocklist[$blockname]);
  } // end func blockExists

  /**
   * Returns a list of variables of a block.
   *
   * @param string $block Blockname
   *
   * @return   array    [varname => varname]
   * @access   public
   * @see      BlockvariableExists()
   */
  function getBlockvariables($block)
  {
    if (!isset($this->blockvariables[$block])) {
      return array();
    }

    $variables = array();
    foreach ($this->blockvariables[$block] as $variable => $v) {
      $variables[$variable] = $variable;
    }

    return $variables;
  } // end func getBlockvariables

  /**
   * Checks whether a block variable exists.
   *
   * @param string $block    Blockname
   * @param string $variable Variablename
   *
   * @return   boolean
   * @access   public
   * @see      getBlockvariables()
   */
  function BlockvariableExists($block, $variable)
  {
    return isset($this->blockvariables[$block][$variable]);
  } // end func BlockvariableExists

  /**
   * Builds a functionlist from the template.
   *
   * @access private
   * @return null
   */
  function buildFunctionlist()
  {
    $this->functions = array();

    $template = $this->template;

    $num = 0;

    while (preg_match($this->functionRegExp, $template, $regs)) {

      $pos = strpos($template, $regs[0]);

      $template = substr($template, $pos + strlen($regs[0]));

      $head = $this->getValue($template, ')');
      $args = array();

      $search = $regs[0] . $head . ')';

      $replace = $this->openingDelimiter .
        '__function' . $num . '__' .
        $this->closingDelimiter;

      $this->template = str_replace($search, $replace, $this->template);
      $template       = str_replace($search, $replace, $template);

      while ($head != '' && $args2 = $this->getValue($head, ',')) {
        $arg2 = trim($args2);

        $args[] = ('"' == $arg2[0] || "'" == $arg2[0]) ?
          substr($arg2, 1, -1) : $arg2;

        if ($arg2 == $head) {
          break;
        }
        $head = substr($head, strlen($arg2) + 1);
      }

      $this->functions[$num++] = array('name'    => $regs[1],
        'args'    => $args);
    }

  } // end func buildFunctionlist

  /**
   * Truncates the given code from the first occurrence of
   * $delimiter but ignores $delimiter enclosed by " or '.
   *
   * @param string $code      The code which should be parsed
   * @param string $delimiter The delimiter char
   *
   * @access private
   * @return string
   * @see    buildFunctionList()
   */
  function getValue($code, $delimiter)
  {
    if ($code == '') {
      return '';
    }

    if (!is_array($delimiter)) {
      $delimiter = array($delimiter => true);
    }

    $len         = strlen($code);
    $enclosed    = false;
    $enclosed_by = '';

    if (isset($delimiter[$code[0]])) {
      $i = 1;
    } else {
      for ($i = 0; $i < $len; ++$i) {
        $char = $code[$i];

        if (($char == '"' || $char == "'")
          && ($char == $enclosed_by || '' == $enclosed_by)
          && (0 == $i || ($i > 0 && '\\' != $code[$i - 1]))
        ) {

          if (!$enclosed) {
            $enclosed_by = $char;
          } else {
            $enclosed_by = "";
          }
          $enclosed = !$enclosed;

        }

        if (!$enclosed && isset($delimiter[$char])) {
          break;
        }
      }
    }

    return substr($code, 0, $i);
  } // end func getValue

  /**
   * Deletes one or many variables from the block variable list.
   *
   * @param string $block     Blockname
   * @param mixed  $variables Name of one variable or array of variables
   *                          (array (name => true ) ) to be stripped.
   *
   * @access   private
   * @return null
   */
  function deleteFromBlockvariablelist($block, $variables)
  {
    if (!is_array($variables)) {
      $variables = array($variables => true);
    }

    reset($this->blockvariables[$block]);
    foreach ($this->blockvariables[$block] as $varname => $val) {
      if (isset($variables[$varname])) {
        unset($this->blockvariables[$block][$varname]);
      }
    }
  } // end deleteFromBlockvariablelist

  /**
   * Updates the variable list of a block.
   *
   * @param string $block Blockname
   *
   * @access   private
   * @return null
   */
  function updateBlockvariablelist($block)
  {
    preg_match_all(
      $this->variablesRegExp,
      $this->blocklist[$block], $regs
    );

    if (count($regs[1]) != 0) {
      foreach ($regs[1] as $k => $var) {
        $this->blockvariables[$block][$var] = true;
      }
    } else {
      $this->blockvariables[$block] = array();
    }

    // check if any inner blocks were found
    if (isset($this->blockinner[$block])
      && is_array($this->blockinner[$block])
      && count($this->blockinner[$block]) > 0
    ) {
      /*
       * loop through inner blocks, registering the variable
       * placeholders in each
       */
      foreach ($this->blockinner[$block] as $childBlock) {
        $this->updateBlockvariablelist($childBlock);
      }
    }
  } // end func updateBlockvariablelist

  /**
   * Returns an array of blocknames where the given variable
   * placeholder is used.
   *
   * @param string $variable Variable placeholder
   *
   * @return   array     $parents parents[0..n] = blockname
   * @access   public
   */
  function findPlaceholderBlocks($variable)
  {
    $parents = array();
    reset($this->blocklist);
    foreach ($this->blocklist as $blockname => $content) {
      reset($this->blockvariables[$blockname]);

      foreach ($this->blockvariables[$blockname] as $varname => $val) {
        if ($variable == $varname) {
          $parents[] = $blockname;
        }
      }
    }

    return $parents;
  } // end func findPlaceholderBlocks

  /**
   * Handles warnings, saves them to $warn and prints them or
   * calls die() depending on the flags
   *
   * @param string $message Warning
   * @param string $file    File where the warning occurred
   * @param int    $line    Linenumber where the warning occurred
   *
   * @see      $warn, $printWarning, $haltOnWarning
   * @access   private
   * @return null
   */
  function warning($message, $file = '', $line = 0)
  {
    $message = sprintf(
      'HTML_Template_ITX Warning: %s [File: %s, Line: %d]',
      $message,
      $file,
      $line
    );

    $this->warn[] = $message;

    if ($this->printWarning) {
      print $message;
    }

    if ($this->haltOnWarning) {
      die($message);
    }
  } // end func warning

} // end class HTML_Template_ITX
?>