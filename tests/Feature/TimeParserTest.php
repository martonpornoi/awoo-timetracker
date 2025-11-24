<?php

namespace Tests\Feature;

use App\Support\TimeParser;
use Tests\TestCase;

class TimeParserTest extends TestCase
{
    public function test_parses_hm_format(): void
    {
        $this->assertSame(135, TimeParser::parse('2h15m')['rounded_minutes']);
    }

    public function test_rounds_up_to_next_quarter(): void
    {
        $this->assertSame(135, TimeParser::parse('2h01m')['rounded_minutes']);
    }

    public function test_parses_colon_format(): void
    {
        $this->assertSame(90, TimeParser::parse('1:30')['rounded_minutes']);
    }

    public function test_parses_decimal_hours(): void
    {
        $this->assertSame(135, TimeParser::parse('2.25')['rounded_minutes']);
    }

    public function test_parses_decimal_comma_hours(): void
    {
        $this->assertSame(135, TimeParser::parse('2,25')['rounded_minutes']);
    }

    /**
     * Parser must throw when the user submits an empty string.
     */
    public function test_rejects_empty_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TimeParser::parse('');
    }

    /**
     * H:MM inputs with minutes >= 60 should be rejected immediately.
     */
    public function test_rejects_invalid_hhmm_minutes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TimeParser::parse('1:75');
    }
}
