<?php

namespace App\Domain\Service;

use App\Domain\Entity\Activity;
use App\Domain\ValueObject\ReportFormat;

class ActivityParser implements ActivityParserInterface
{
    /**
     * @var ActivityParserInterface[] $parsers
     */
    public function __construct(private readonly array $parsers)
    {
    }

    /**
     * @return Activity[]
     */
    public function parse(string $data, ReportFormat $format): array
    {
        $parser = $this->getParser($format);

        return $parser->parse($data, $format);
    }

    public function supports(ReportFormat $format): bool
    {
        try {
            $this->getParser($format);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getParser(ReportFormat $format): ActivityParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($format)) {
                return $parser;
            }
        }

        throw new \InvalidArgumentException(sprintf("Parser for format %s not found", $format->value));
    }
}
