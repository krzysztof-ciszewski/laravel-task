<?php

namespace Tests\Unit\Domain\Service;
use App\Domain\Service\ActivityParser;
use App\Domain\Service\ActivityParserInterface;
use App\Domain\ValueObject\ReportFormat;
use PHPUnit\Framework\TestCase;

class ActivityParserTest extends TestCase
{
    private ActivityParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ActivityParser([]);
    }

    public function testDoesNotSupport(): void
    {
        $parser = new ActivityParser([]);

        self::assertFalse($parser->supports(ReportFormat::HTML));
    }

    public function testSupoprts(): void
    {
        $mock = $this->createMock(ActivityParserInterface::class);
        $mock->expects(self::once())->method('supports')->with(ReportFormat::HTML)->willReturn(true);

        $parser = new ActivityParser([$mock]);

        self::assertTrue($parser->supports(ReportFormat::HTML));
    }

    public function testParse(): void
    {
        $data = '';

        $mock = $this->createMock(ActivityParserInterface::class);
        $mock->expects(self::once())->method('supports')->with(ReportFormat::HTML)->willReturn(true);
        $mock->expects(self::once())->method('parse')->with($data, ReportFormat::HTML)->willReturn([]);

        $parser = new ActivityParser([$mock]);

        self::assertEmpty($parser->parse($data, ReportFormat::HTML));
    }

    public function testParseThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $parser = new ActivityParser([]);

       $parser->parse('', ReportFormat::HTML);
    }
}
