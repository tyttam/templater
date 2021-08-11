<?php

namespace test\ReversTemplate;

use app\ReverseTemplate;
use app\ReversTemplate\Exception\{ InvalidTemplateException, ResultTemplateMismatchException};
use PHPUnit\Framework\TestCase;

class ReversTemplateTest extends TestCase
{
    public function testReversResultLite(): void
    {
        $template = 'Hello, my name is {{name}}.';
        $result_text = 'Hello, my name is Juni.';

        $reverse_template = new ReverseTemplate($template, $result_text);
        $this->assertEquals(
            ['name' => 'Juni'],
            $reverse_template->revers()
        );

        $template = 'Hello, my name is {{name}}.';
        $result_text = 'Hello, my name is .';
        $reverse_template->setTemplate($template);
        $reverse_template->setReverseText($result_text);
        $this->assertEquals(
            ['name' => ''],
            $reverse_template->revers()
        );

        $template = 'Hello, my name is {name}.';
        $result_text = 'Hello, my name is <robot>.';
        $reverse_template->setTemplate($template);
        $reverse_template->setReverseText($result_text);
        $this->assertEquals(
            ['name' => '<robot>'],
            $reverse_template->revers()
        );

        $template = 'Hello, my name is {{name}}.';
        $result_text = 'Hello, my name is &lt;robot&gt;.';
        $reverse_template->setTemplate($template);
        $reverse_template->setReverseText($result_text);
        $this->assertEquals(
            ['name' => '<robot>'],
            $reverse_template->revers()
        );
    }

    public function testReversResultHard(): void
    {
        $template = '<td class="text">{{ first_date }} в {{ time }} часов.<br>Срок проведения переторжки: <b>{{ second_date }} </b><br>Срок продления переторжки - 10 мин.<br>Мин. снижение - .500000 % от текущей цены участника</td>';
        $result_text = '<td class="text">05.08.2021 в 11 часов.<br>Срок проведения переторжки: <b>05.08.2021 12:00:00 - 05.08.2021 13:00:00 </b><br>Срок продления переторжки - 10 мин.<br>Мин. снижение - .500000 % от текущей цены участника</td>';

        $reverse_template = new ReverseTemplate($template, $result_text);
        $this->assertEquals(
            [
                'first_date' => '05.08.2021',
                'time' => '11',
                'second_date' => '05.08.2021 12:00:00 - 05.08.2021 13:00:00'
            ],
            $reverse_template->revers()
        );
    }

    public function testResultTemplateMismatchException(): void
    {
        $this->expectException(ResultTemplateMismatchException::class);

        $template = 'Hello, my name is {{name}}.';
        $result_text = 'Hello';

        $reverse_template = new ReverseTemplate($template, $result_text);
        $reverse_template->revers();

        $this->expectException(InvalidTemplateException::class);

        $template = 'Hello, my name is {{name}.';
        $result_text = 'Hello, my name is Juni.';
        $reverse_template->setTemplate($template);
        $reverse_template->setReverseText($result_text);

        $reverse_template->revers();
    }
}
