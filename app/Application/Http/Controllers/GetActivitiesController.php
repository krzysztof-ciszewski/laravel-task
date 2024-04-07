<?php

namespace App\Application\Http\Controllers;

use App\Domain\Repository\ActivityRepository;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GetActivitiesController extends BaseController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
    }
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['nullable', 'max:3', Rule::enum(ActivityType::class)],
            'occurred_at_from' => 'nullable|date|date_format:d-m-Y',
            'occurred_at_to' => 'nullable|date|date_format:d-m-Y',
            'location' => 'nullable|max:3|regex:/^[A-Z]{3}$/',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return response()->json($this->activityRepository->findBy(
            $request->get('occurred_at_from') ? \DateTimeImmutable::createFromFormat('d-m-Y', $request->get('occurred_at_from')) : null,
            $request->get('occurred_at_to') ? \DateTimeImmutable::createFromFormat('d-m-Y', $request->get('occurred_at_to')) : null,
            $request->get('location') ? new Airport($request->get('location')) : null,
            $request->get('type') ? ActivityType::tryFrom($request->get('type')) : null,
        ));
    }
}
