<?php

namespace Modules\Chat\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Modules\Chat\Http\Repositories\Api\V1\ApiTimelineReportRepository;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineReportRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineReportsRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineReportStatusRequest;
use Modules\Chat\Transformers\Api\V1\ApiListReportReasonTimelineResource;
use Modules\Chat\Transformers\Api\V1\ApiListReportsResource;
use Symfony\Component\HttpFoundation\Response;

class ApiTimeLineReportController extends Controller
{

    public function __construct(public ApiTimelineReportRepository $api_timeline_report_repository)
    {
    }

    public function reportReasons()
    {
        $report_reasons = $this->api_timeline_report_repository->reasons();
        return response(ApiListReportReasonTimelineResource::collection($report_reasons), Response::HTTP_OK);
    }

    public function report(ApiTimelineReportRequest $request, $timeline)
    {
        $data = $request->validated();
        if (!$timeline) {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $report = $this->api_timeline_report_repository->report($data, $timeline);
        return response(['data' => ['report' => ['id' => $report->id]]], Response::HTTP_OK);
    }

    public function reports(ApiTimelineReportsRequest $request)
    {
        $filters = $request->validated();
        $reports = $this->api_timeline_report_repository->reports($filters);
        return response(['data' => ApiListReportsResource::collection($reports)->resource], Response::HTTP_OK);
    }

    public function show(ApiTimelineReportsRequest $request, $report)
    {
        $filters = $request->validated();
        $filters['report_id'] = $report;
        $reports = $this->api_timeline_report_repository->reports($filters);
        return response(['data' => ApiListReportsResource::collection($reports)], Response::HTTP_OK);
    }

    public function status(ApiTimelineReportStatusRequest $request, $report)
    {
        $data = $request->validated();
        $data['report_id'] = $report;
        $reports = $this->api_timeline_report_repository->reportStatus($data);
        return response(['data' => ['result' => $reports]], Response::HTTP_OK);
    }
}
