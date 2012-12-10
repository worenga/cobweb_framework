<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Translator
{
    private $language = null;
    private $libs = array();
    private $phrases = array();
    function __construct ()
    {
        $this->language = cw_language;
        $this->loadLib('common');
    }
    public function getLanguage ()
    {
        return $this->language;
    }
    public function setLanguage ($language)
    {
        if ($language != $this->language) {
            $this->language = $language;
            $this->phases = array();
            $oldLibs = $this->libs;
            $this->libs = array();
            foreach ($oldLibs as $lib) {
                $this->loadLib($lib);
            }
            return true;
        }else{
            return false;
        }
    }
    private function languageDir ()
    {
        return cw_language_dir . strtolower($this->language) . '/';
    }
    public function loadLib ($libName)
    {
        if (in_array($libName, $this->libs)) {
            return true;
        } else {
            $possibleFilename = $this->languageDir() . $this->language . '.' . strtolower($libName) . '.lang.' . cw_phpex;
            if (file_exists($possibleFilename)) {
                require_once $possibleFilename;
                $this->libs[] = $libName;
                $this->phrases = array_merge($this->phrases, $lang);
                return true;
            } else {
                CobWeb::o('Console')->warning('Unable to load translation lib: "' . $libName . '" for language: ' . $this->language);
                return false;
            }
        }
    }
    public function getBit ($key)
    {
        if (! empty($key) && array_key_exists($key, $this->phrases)) {
            return $this->phrases[$key];
        } else {
            CobWeb::o('Console')->notice('CobWeb_Translator##getBit: Translation key '.$key.' was not found in this language('.$this->language.')');
            return false;
        }
    }
    public function __get ($key)
    {
        return $this->getBit($key);
    }
    function __destruct ()
    {}
}
?>