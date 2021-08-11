<?php

class ParserBranch
{
    private const START_SYMBOL = '{{';
    private const END_SYMBOL = '}}';
    private const START_SYMBOL_DECODE = '{';
    private const END_SYMBOL_DECODE = '}';

    private const TEMPLATE_TEXT = 'Hello, my name is {{ name }}.';

    public function benchRegExp()
    {
        $text = self::TEMPLATE_TEXT;
        $matches = [];
        preg_match_all('/{{(.*?)}}/gm', $text, $matches);

        return $matches;
    }

    public function benchWithString()
    {
        $result = [];
        $template1 = self::TEMPLATE_TEXT;

        $current_position = $end = 0;
        $stop = true;

        // разбираем шаблон на элементы
        while ($stop) {
            $without_decode = strpos($template1, self::START_SYMBOL, $current_position);
            $with_decode = strpos($template1, self::START_SYMBOL_DECODE, $current_position);

            $decode         = $without_decode && $without_decode <= $with_decode;
            $start          = $decode   ? $without_decode     : $with_decode;
            $start_symbol   = $decode   ? self::START_SYMBOL  : self::START_SYMBOL_DECODE;
            $end_symbol     = $decode   ? self::END_SYMBOL    : self::END_SYMBOL_DECODE;

            if ($start !== false) {
                $end = strpos($template1, $end_symbol, $start);

                if ($end === false) {
                    throw new \Exception('Invalid template.');
                }

                $result[] = substr($template1, $current_position, $start - $current_position);

                $result[][trim(substr($template1, $start + strlen($start_symbol),
                    $end - $start - strlen($end_symbol)))] = ['', 'option' => ['decode' => $decode]];

                $current_position = $end + strlen($end_symbol);
            } else {
                if ($finish_string = substr($template1, $current_position)) {
                    $result[] = $finish_string;
                }
                $stop = false;
            }
        }

        return $result;
    }
}
