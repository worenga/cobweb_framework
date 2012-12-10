<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_NodeBuilder extends CobWeb_Builder {
	private $element = null;
	private $nodes = array ();
	private $attributes = array ();
	private $classes = array ();
	
	public function __construct($element, $id = null) {
		$this->element = strtolower ( $element );
		if($id != null)
			$this->setId ( $id );
	}
	
	public function setId($id) {
		$this->setAttrib ( 'id', $id );
	}
	public function getId() {
		$this->getAttrib ( 'id' );
	}
	public function setAttrib($attrib, $value) {
		if ($attrib == 'class') {
			CobWeb::o ( 'Console' )->warning ( 'Cannot set class by setAttrib, use addClass instead' );
			return false;
		} else {
			$this->attributes [strtolower ( $attrib )] = $value;
			return true;
		}
	}
	public function getAttrib($attrib) {
		return $this->attributes [strtolower ( $attrib )] || null;
	}
	public function getElement() {
		return $this->element;
	}
	public function removeAttrib($attrib){
		if ($this->attributes[$attrib]) {
			unset($this->attributes[$attrib]);
			return true;
		}else{
			return false;
		}
	}
	public function addChild(CobWeb_Builder &$child) {
		$this->nodes [] = &$child;
		return true;
	}
	public function addContent($value, $escape = true) {
		$this->addChild ( new CobWeb_DataBuilder ( $value, $escape ) );
		return true;
	}
	public function addClass($className) {
		$this->classes [] = $className;
		return true;
	}
	public function removeClass($className) {
		$key = array_search ( $className, $this->classes );
		if ($this->classes [$key]) {
			unset ( $this->classes [$key] );
			return true;
		} else {
			return false;
		}
	}
	
	public function hasChilds() {
		if (count ( $this->nodes ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function hasAttribs() {
		if (count ( $this->attributes ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function hasClasses() {
		if (count ( $this->classes ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function render() {
		$return = '<';
		$return .= $this->element;
		if ($this->hasClasses()) {
			$return .= ' class="';
			$classes = null;
			foreach ( array_reverse ( $this->classes ) as $class ) {
				$classes .= $class . ' ';
			}
			$return .= substr($classes,0,-1);
			
			$return .= '"';
		}
		
		if ($this->hasAttribs ()) {
			ksort($this->attributes);
			foreach ($this->attributes as $attrib => $value ) {
				$return .= ' ' . $attrib . '="' . $value . '"';
			}
		}
		
		if ($this->hasChilds () || ($this->element != 'script' || $this->element != 'textarea')) {
			$return .= '>';
			foreach ( $this->nodes as $node ) {
				$return .= $node->render ();
			}
			$return .= '</' . $this->element . '>'.cw_system_crlf;
		} else {
			$return .= '/>';
		}
		return $return;
	}
}
?>