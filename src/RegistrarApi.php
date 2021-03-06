<?php
/** 
 * RegistrarApi help functions
 *
 * PHP version 7
 *
 * @category Library
 * @package  registrarapi
 * @author   orehid <orehid@example.com>
 * @license  MIT
 * @version  1.0
 */


namespace orehid\registrarapi;

/**
 *   RegistrarApi
 */

class RegistrarApi
{

    /**
     *  constants
     */

    protected const LOG_FILES = [];
    protected const LOG_STYLE = 2;		// 1:verbose 2:simple
    protected const EOL = "\n";


    /**
     * helper functions
     */

    /**
     * filterDomain
     */
    public static function filterDomain( string $domain )
    {
        $original = $domain;
        if ($domain==="example.com") {
            $domain=false;
        }
        $domain=filter_var($domain, FILTER_VALIDATE_DOMAIN);
        if (!$domain) {
            throw new \Exception('wrong domain name - '.$original);
        }
        return $domain;
    }

    /**
     * getEnv
     *
     * @return mixed
     */
    public static function getEnv( string $label )
    {
        $value = '';
        $value = (defined('static::'.$label)       ? constant('static::'.$label)       : $value);
        $value = (defined(static::NAME.'_'.$label) ? constant(static::NAME.'_'.$label) : $value);
        return $value;
    }


    /**
     * log
     */
    public static function log($val, string $label="", int $style=0) : void
    {
        $time  = (defined('REQUEST_TIME') ? REQUEST_TIME : time());
        $files = self::getEnv('LOG_FILES');
        if ($files) {
            if ($val) {
                $buf = [];
                $prefix = date("Y-m-d H:i:s", $time)." ";
                if (!is_string($val)) {
                    $val = self::vardump($val, $label, $style);
                }
                foreach ( mb_split('\\r?\\n', $val) as $line) {
                    $buf[] = $prefix.$line;
                }
                if ($buf) {
                    foreach ($files as $file) {
                        file_put_contents($file, implode(static::EOL, $buf).static::EOL, FILE_APPEND);
                    }
                }
            } else {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        touch($file);
                    }
                }
            }
        }
    }

    /**
     * vardump
     *
     * @return string
     */
    public static function vardump($arg, string $label="", int $style=0) : string
    {
		if ($style===0) {
			$style = self::getEnv('LOG_STYLE');
		}
        $dump = function ($arg, $label="", int $indent=0, int $style=1) use ( &$dump ) {
            $type = gettype($arg);
            $buf = str_repeat("  ", $indent). (($label!=="")?(is_string($label)?"'{$label}'":$label)." => ":"");
            switch( $type ) {
            case 'boolean' :
                if ($style===1) {
                    $buf .= "b(".($arg?'TRUE':'FALSE').")".static::EOL;
                } else {
                    $buf .= ($arg?'TRUE':'FALSE').static::EOL;
                }
                break;
            case 'integer' :
                if ($style===1) {
                    $buf .= "i(".$arg.")".static::EOL;
                } else {
                    $buf .= $arg.static::EOL;
                }
                break;
            case 'double' :
                if ($style===1) {
                    $buf .= "f(".$arg.")".static::EOL;
                } else {
                    $buf .= $arg.static::EOL;
                }
                break;
            case 'string' :
                if ($style===1) {
                    $buf .= "s(".strlen($arg).") '".$arg."'".static::EOL;
                } else {
                    $buf .= '"'.$arg.'"'.static::EOL;
                }
                break;
            case 'resource' :
                $buf .= "r(".get_resource_type($arg).")".static::EOL;
                break;
            case 'unknown type' :
            case 'NULL' :
            case 'null' :
                $buf .= $type.static::EOL;
                break;
            case 'object' :
                if (method_exists($arg, '__toString')) {
                    $buf .= "'".$arg."'".static::EOL;
                    break;
                }
                if (isset($arg->typename)) {
                    $buf .= "object(".$arg->typename.")".static::EOL;
                    break;
                }
                $t_buf = "";
                foreach ($arg as $l=>$v) {
                    $t_buf .= $dump($v, $l, $indent+1, $style);
                }
                if (strlen($t_buf)) {
                    if ($style===1) {
                        $buf .= "o(".get_class($arg).") [".static::EOL;
                    } else {
                        $buf .= "[".static::EOL;
                    }
                    $buf .= $t_buf;
                    $buf .= str_repeat("  ", $indent)."]".static::EOL;
                } else {
                    if ($style===1) {
                        $buf .= "o(".get_class($arg).") []".static::EOL;
                    } else {
                        $buf .= "[]".static::EOL;
                    }
                }
                break;
            case 'array' :
                $c = count($arg);
                if ($c===0) {
                    if ($style===1) {
                        $buf .= "a({$c}) []".static::EOL;
                    } else {
                        $buf .= "[]".static::EOL;
                    }
                    break;
                }
                if ($style===1) {
                    $buf .= "a({$c}) [".static::EOL;
                } else {
                    $buf .= "[".static::EOL;
                }
                foreach ($arg as $l=>$v) {
                    $buf .= $dump($v, $l, $indent+1, $style);
                }
                $buf .= str_repeat("  ", $indent)."]".static::EOL;
                break;
            }
            return $buf;
        };
        return $dump($arg, $label, 0, $style);
    }
}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */