<?php

namespace App;

class ApacheParser
{
    private static $commentRegex = '/#.*/i';
    private static $directiveRegex = '/([^\\s]+)\\s*(.+)/i';
    private static $sectionOpenRegex = '/<([^\\/\\s>]+)\\s*([^>]+)?>/i';
    private static $sectionCloseRegex = '/<\\/([^\\s>]+)\\s*>/i';

    public function parse($confPath)
    {
        if (empty($confPath)) {
            throw new \Exception("Configuration path cannot be null or empty.");
        }

        if (!file_exists($confPath)) {
            throw new \Exception("Configuration file not found: " . $confPath);
        }

        $currentNode = ConfigNode::createRootNode();

        try {
            $lines = file($confPath); // Don't add flags to file() function, this will broke line numbers (Line numbers are important for debugging)

            $i = 0;
            foreach ($lines as $line) {

                $i++;

                if (preg_match(self::$commentRegex, $line)) {
                    continue;
                }

                if (preg_match(self::$sectionOpenRegex, $line, $sectionOpenMatch)) {
                    $name = $sectionOpenMatch[1];
                    $content = isset($sectionOpenMatch[2]) ? $sectionOpenMatch[2] : '';
                    $sectionNode = ConfigNode::createChildNode($name, $content, $currentNode, $i);
                    $currentNode = $sectionNode;
                } elseif (preg_match(self::$sectionCloseRegex, $line, $sectionCloseMatch)) {
                    $currentNode->endLine = $i;
                    $currentNode = $currentNode->getParent();
                } elseif (preg_match(self::$directiveRegex, $line, $directiveMatch)) {
                    $name = $directiveMatch[1];
                    $content = $directiveMatch[2];
                    ConfigNode::createChildNode($name, $content, $currentNode, $i);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("An error occurred while reading the configuration file.", 0, $e);
        }

        return $currentNode;
    }
}


class ConfigNode
{
    private $name;
    private $content;
    private $parent;
    private $children;

    public $startLine;

    public $endLine;

    private function __construct($name, $content, $parent, $line = null)
    {
        $this->name = $name;
        $this->content = $content;
        $this->parent = $parent;
        $this->children = [];
        $this->startLine = $line;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function getEndLine()
    {
        return $this->endLine;
    }

    public static function createRootNode()
    {
        return new ConfigNode('root', null, null, null);
    }

    public static function createChildNode($name, $content, $parent, $line = null)
    {
        $node = new ConfigNode($name, $content, $parent, $line);
        if ($parent !== null) {
            $parent->children[] = $node;
        }
        return $node;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function __toString()
    {
        return trim($this->name . ' ' . $this->content);
    }
}
