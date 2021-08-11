<?php
namespace app;

use app\ReversTemplate\Reverse;
use app\ReversTemplate\Template;

class ReverseTemplate
{
    private $_reverse_text;

    /** @var Template */
    protected $_template;
    protected $_options = [];

    /**  @throws \Exception */
    public function __construct(string $template, string $reverse_text, array $options = []) {
        $this->setReverseText($reverse_text);
        $this->setOptions($options);
        $this->setTemplate($template);
    }

    /** @throws \Exception */
    public function setTemplate(string $template): void
    {
        $this->_template = new Template($template);
    }

    public function getTemplate(): Template
    {
        return $this->_template;
    }

    public function setReverseText(string $reverse_text): void
    {
        $this->_reverse_text = $reverse_text;
    }

    public function setOptions(array $options): void
    {
        $this->_options = $options;
    }

    /** @throws \Exception */
    public function revers(): array
    {
        $reverse = new Reverse($this->getTemplate(), $this->_reverse_text);

        return $reverse->reverseParse();
    }
}
