<?php
/**
 * This package provides Phing tasks for using QueryPath from within Phing.
 * 
 * @package Phing_QueryPath
 * @author M Butcher <matt@aleph-null.tv>
 * @copyright Copyright (c) 2010, Matt Butcher
 * @version 1.0
 * @license http://opensource.org/licenses/lgpl-2.1.php The GNU Lesser GPL (LGPL).
 */

require_once 'phing/Task.php';
require_once 'QueryPath/QueryPath.php';

class QueryPathReplacementTask extends Task {

  protected $filesets = array();
  
  /**
   * An associative array of substitutions.
   * The key is a CSS 3 Selector, the value is what should be substituted in.
   */
  protected $rules = array();
  
  protected $isHTML = FALSE;
  
  public function init() {}
  public function main() {
    
    foreach ($this->filesets as $fs) {
      $files  = $fs->getDirectoryScanner($this->getProject())->getIncludedFiles();
      $fullPath = $fs->getDir($this->project)->getPath();
      foreach ($files as $filename) {
        $file = $fullPath . DIRECTORY_SEPARATOR . $filename;
        try {
          // Process each file with QueryPath.
          $this->processWithQueryPath($file);
        } catch (Exception $e) {
          $this->log($e->getMessage(), $this->quiet ? Project::MSG_VERBOSE : Project::MSG_WARN);
        }
      }
    }
    
  }
  
  /**
   * Indicate whether rule values should be decode and treated as HTML/XML markup.
   *
   * @param boolean $boolean
   *   A flag indicating whether rule values should be decoded. TRUE means they should
   *   be decoded, FALSE means they should not.
   */
  public function setDecodeHTML($boolean) {
    $this->isHTML = filter_var($boolean, FILTER_SANITIZE_BOOLEAN);
  }
  
  /**
   * Process a file with QueryPath.
   *
   * @param string $filename
   *   The file to modify.
   */
  protected function processWithQueryPath($file) {
    
    print $file . PHP_EOL;
    $qp = qp($file);
    
    $method = $this->isHTML ? 'xml' : 'text';
    
    // Run all transforms.
    foreach ($this->rules as $rule) {
      
      $selector = $rule->getSelector();
      $value = $rule->getValue();
      
      // If we are allowing HTML input, convert escaped string back to markup.
      if ($this->isHTML) {
        $value = html_entity_decode($value);
      }
      
      $qp->top($selector)->$method($value);
    }
    
    // Write the file.
    $qp->writeXML($file);
    //$qp->writeXML();
  }
  
  /**
   * Create a rule from a rule element.
   */
  public function createRule(){
    $rule = new QueryPathReplacementRule();
    $this->rules[] = $rule;
    return $rule;
  }

  // Inherit docs.
  public function createFileSet() {
    $num = array_push($this->filesets, new FileSet());
    return $this->filesets[$num - 1];
  }
}

/**
 * Replacement rule.
 *
 * Rule elements will be transformed into one of these objects.
 */
class QueryPathReplacementRule {
  
  protected $name = NULL;
  protected $value = NULL;
  
  public function setSelector($name){
    $this->name = $name;
  }
  public function getSelector(){
    return $this->name;
  }
  public function setValue($value){
    $this->value = $value;
  }
  public function getValue(){
    return $this->value;
  }
  public function addText($text){
    $this->value = trim($text);
  }
}