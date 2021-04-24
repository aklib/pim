<?php

/**
 * $Id:$
 * $HeadURL:$
 * $Date:$
 * $Author:$
 * $Revision: $
 *
 * Output.php
 *
 * @since 08.07.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Utils;

abstract class OutputUtils {

    /**
     * Normalizes a name ('ich will etwas')=>'IchWillEtwas')
     *  (e.g. Property|Filename part|Class name etc)
     * @param  string $name to normalize
     * @return string  normalized name
     */
    public static function normalizeName($name, $separator = '', $allowedChars = '') {
        return preg_replace('/\s+/', $separator, trim(ucwords(preg_replace('/[^a-zA-Z0-9' . $allowedChars . ']+/', ' ', $name))));
    }

    public static function truncate($text, $length = 50) {
        $dots = strlen($text) > $length ? '...' : '';
        return substr($text, 0, $length) . $dots;
    }

    public static function isLonger($text, $length) {
        return (strlen($text) > $length);
    }

    public static function convertTextFromDB($inputText) {
        return nl2br($inputText);
    }

    public static function xmlpp($xml, $html_output = false) {
        if (is_null($xml)) {
            return '';
        }

        if (!is_string($xml)) {
            $xml_obj = new \SimpleXMLElement($xml);
            $xml = $xml_obj->asXML();
        }

        $level = 4;
        $indent = 0; // current indentation level
        $pretty = [];

        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml));

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level;  // closing tag, decrease indent
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }
        $xml = implode("\n", $pretty);
        return ($html_output) ? htmlentities($xml) : $xml;
    }

    public static function jsonpp($json, $html = false, $tabspaces = null) {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;

        if ($html) {
            $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
            $newline = "<br/>";
        } else {
            $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
            $newline = "\n";
        }

        for ($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];

            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch ($char) {
                    case ':':
                        $result .= $char . (!$inquote ? " " : "");
                        break;
                    case '{':
                        if (!$inquote) {
                            $tabcount++;
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        } else {
                            $result .= $char;
                        }
                        break;
                    case '}':
                        if (!$inquote) {
                            $tabcount--;
                            $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        } else {
                            $result .= $char;
                        }
                        break;
                    case ',':
                        if (!$inquote) {
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        } else {
                            $result .= $char;
                        }
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote)
                            $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }
        return $result;
    }

}
