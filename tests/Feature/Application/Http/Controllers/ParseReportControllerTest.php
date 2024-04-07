<?php

namespace Feature\Application\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ParseReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $invalidReportContent;
    private string $validReportContent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invalidReportContent = file_get_contents(sprintf('%s/tests/Data/invalid_report.html', base_path()));
        $this->validReportContent = file_get_contents(sprintf('%s/tests/Data/valid_report.html', base_path()));
        ;
    }

    public function testMissingReport(): void
    {
        $response = $this->post('/api/report');

        $response->assertStatus(400);
        $response->assertJson(['report' => ['The report field is required.']]);
    }

    public function testReportTooBig(): void
    {
        $report = UploadedFile::fake()->create(
            'report.html',
            100 * 1024,
            'text/html'
        );

        $response = $this->post('/api/report', ['report' => $report]);


        $response->assertStatus(400);
        $response->assertJson(['report' => ['The report field must not be greater than 12288 kilobytes.']]);
    }

    public function testReportInvalidFileType(): void
    {
        $report = UploadedFile::fake()->create(
            'report.json',
            1024,
            'application/json'
        );

        $response = $this->post('/api/report', ['report' => $report]);


        $response->assertStatus(400);
        $response->assertJson(['report' => ['The report field must be a file of type: text/html.', 'The report field must be a file of type: html.']]);
    }

    public function testReportInvalidStructure(): void
    {
        $report = UploadedFile::fake()->createWithContent(
            'report.html',
            $this->invalidReportContent
        );

        $response = $this->post('/api/report', ['report' => $report]);


        $response->assertStatus(400);
        $response->assertJson(['Invalid report']);
    }


    public function testValidReport(): void
    {
        $report = UploadedFile::fake()->createWithContent(
            'report.html',
            $this->validReportContent
        );

        $response = $this->post('/api/report', ['report' => $report]);

        $response->assertStatus(200);
        $response->assertContent('[{"type":"CI","location":{"code":"CPH"},"to":null,"occurred_at":{"date":"2022-01-10 07:45:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":1},{"type":"FLT","location":{"code":"KRP"},"to":{"code":"CPH"},"occurred_at":{"date":"2022-01-10 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":{"date":"2022-01-10 08:45:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_arrival":{"date":"2022-01-10 09:35:00.000000","timezone_type":3,"timezone":"UTC"},"id":2},{"type":"CO","location":{"code":"CPH"},"to":null,"occurred_at":{"date":"2022-01-10 17:55:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":3},{"type":"FLT","location":{"code":"CPH"},"to":{"code":"KRP"},"occurred_at":{"date":"2022-01-10 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":{"date":"2022-01-10 16:45:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_arrival":{"date":"2022-01-10 17:35:00.000000","timezone_type":3,"timezone":"UTC"},"id":4},{"type":"DO","location":{"code":"KRP"},"to":null,"occurred_at":{"date":"2022-01-12 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":5},{"type":"CI","location":{"code":"KRP"},"to":null,"occurred_at":{"date":"2022-01-15 05:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":6},{"type":"CO","location":{"code":"KRP"},"to":null,"occurred_at":{"date":"2022-01-15 17:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":7},{"type":"SBY","location":{"code":"KRP"},"to":null,"occurred_at":{"date":"2022-01-15 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":8},{"type":"CI","location":{"code":"EBJ"},"to":null,"occurred_at":{"date":"2022-01-22 05:55:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":9},{"type":"UNK","location":{"code":"KRP"},"to":null,"occurred_at":{"date":"2022-01-22 00:00:00.000000","timezone_type":3,"timezone":"UTC"},"scheduled_time_departure":null,"scheduled_time_arrival":null,"id":10}]');
    }
}
