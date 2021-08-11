<?php

namespace app\ReversTemplate;

use app\ReversTemplate\Exception\InvalidTemplateException;

class Template
{
    // without decode
    private const START_SYMBOL = '{{';
    private const END_SYMBOL = '}}';

    // with decode
    private const START_SYMBOL_DECODE = '{';
    private const END_SYMBOL_DECODE = '}';

    private $_template = '';
    private $_parse_template = [];

    /** @throws \Exception */
    public function __construct(string $template)
    {
        $this->parse($template);
    }

    /**
     * Парсит шаблон, чтобы выделить переменные
     *
     * @param string $template
     * @throws \Exception
     */
    public function parse(string $template): void
    {
        if ($template !== '') {
            $this->_template = $template;
        } else {
            throw new \ErrorException('Template cannot be empty');
        }

        $current_position = 0;
        $in_process = true;

        // разбираем шаблон на элементы
        while ($in_process) {
            $without_decode = strpos($this->_template, self::START_SYMBOL, $current_position);
            $with_decode    = strpos($this->_template, self::START_SYMBOL_DECODE, $current_position);

            // настройки для корректного выделения переменных из шаблона
            {
                $decode         = $without_decode && $without_decode <= $with_decode;
                $start          = $decode ? $without_decode : $with_decode;
                $start_symbol   = $decode ? self::START_SYMBOL : self::START_SYMBOL_DECODE;
                $end_symbol     = $decode ? self::END_SYMBOL : self::END_SYMBOL_DECODE;
            }

            if ($start !== false) {
                // определяет конец переменной
                $end = strpos($this->_template, $end_symbol, $start);

                if ($end === false) {
                    throw new InvalidTemplateException('Invalid template.');
                }

                $this->_parse_template[] = substr($this->_template, $current_position, $start - $current_position);

                // определяет наименование переменной в шаблоне
                $variable_key = trim(
                    substr(
                        $this->_template,
                        $start + strlen($start_symbol),
                        $end - $start - strlen($end_symbol)
                    )
                );
                if ($variable_key) {
                    $options = new TemplateOptions();
                    $options->setDecode($decode);
                    $this->_parse_template[][$variable_key] = ['', 'options' => $options];
                }

                $current_position = $end + strlen($end_symbol);
            } else {
                // если после нахождения последней переменной остался текст, то добавляем его
                if ($finish_template = substr($this->_template, $current_position)) {
                    $this->_parse_template[] = $finish_template;
                }
                // останавливаем разбивку шаблона
                $in_process = false;
            }
        }
    }

    public function getParseTemplate(): array
    {
        return $this->_parse_template;
    }

    public function getStringTemplate(): string
    {
        return $this->_template;
    }
}
