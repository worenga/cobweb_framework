<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
} 
abstract class CobWeb_Builder {
	abstract public function render();
}
?>