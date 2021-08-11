<?php

namespace app\ReversTemplate;

use app\ReversTemplate\Exception\ResultTemplateMismatchException;

class Reverse
{
    private $_result_parse_template;
    private $_reverse_text;

    public function __construct(Template $parse_template, string $reverse_text)
    {
        if ($reverse_text === '') {
            throw new \ErrorException('Template result cannot be empty.');
        }

        $this->_result_parse_template = $parse_template->getParseTemplate();
        $this->_reverse_text = $reverse_text;
    }

    public function reverseParse(): array
    {
        $result = [];

        foreach ($this->_result_parse_template as $key => $part) {
            if (is_string($part)) {
                $replace_temp = substr_replace($this->_reverse_text, '', strlen($part),
                    strlen($this->_reverse_text) - strlen($part));
                $this->_reverse_text = substr_replace($this->_reverse_text, '', 0, strlen($part));
                if ($replace_temp !== $part) {
                    throw new ResultTemplateMismatchException('Result not matches original template.');
                }
            }

            if (is_array($part)) {
                // бросаем исключение при перезаписи элементов
                if (isset($result[key($part)])) {
                    throw new \Exception('Variable already exists');
                }

                $end_var = 0;
                $skip = false;

                // если есть соседний элемент в шаблоне, то есть переменная не является концом шаблона
                if (isset($this->_result_parse_template[$key + 1])) {
                    if (is_string($this->_result_parse_template[$key + 1])) {
                        $end_var = strpos($this->_reverse_text, $this->_result_parse_template[$key + 1]);
                        $skip = $this->_reverse_text === $this->_result_parse_template[$key + 1];
                    } elseif (is_array($this->_result_parse_template[$key + 1])) {
                        throw new \LogicException('It is impossible to determine the value of a variable when two (or more) variables are located side by side');
                    }
                }

                if ($skip) {
                    $substr = '';
                } else {
                    $substr = $end_var > 0
                        ? substr_replace($this->_reverse_text, '', $end_var, strlen($this->_reverse_text) - $end_var)
                        : substr($this->_reverse_text, $end_var, strlen($this->_reverse_text) - $end_var);
                }

                /** @var TemplateOptions $options */
                $options = $part['options'] ?? false;
                $result[key($part)] = $options && $options->getDecode()
                    ? $substr
                    : html_entity_decode($substr, ENT_COMPAT, "UTF-8");

                $this->_reverse_text = substr_replace($this->_reverse_text, '', 0, $end_var);
            }
        }

        return $result;
    }
}
