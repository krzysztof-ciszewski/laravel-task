<?php

namespace App\Application\Http\Controllers;

use App\Domain\Repository\ActivityRepository;
use App\Domain\Service\ActivityParserInterface;
use App\Domain\ValueObject\ReportFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class ParseReportController extends BaseController
{
    public function __construct(
        private readonly ActivityParserInterface $activityParser,
        private readonly ActivityRepository $activityRepository
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'report' => 'file|required|mimetypes:text/html|mimes:html|max:12288',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('report');

        try {
            $activities = $this->activityParser->parse($file->getContent(), ReportFormat::HTML);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }

        $this->activityRepository->save($activities);

        return response()->json($activities);
    }
}
