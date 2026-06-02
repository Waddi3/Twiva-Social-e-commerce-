<?php

namespace Modules\Chat\Http\Repositories\Api\V1;

use Modules\Chat\Entities\Report;
use Modules\Chat\Entities\ReportReason;


class ApiTimelineReportRepository
{
    public function __construct() {}


    public function reasons()
    {
        $report_reasons = ReportReason::query()
            ->latest();

        return $report_reasons->get();
    }

    public function report($data, $timeline)
    {

        $data['user_id'] = auth('api')->id();
        $data['timeline_id'] = $timeline;
        $report = Report::create($data);
        return $report;
    }

    public function reports($filters)
    {

        $reports_query = Report::query()
            ->with(['user:id,name,profile_image', 'timeline' => function ($timeline_query) {
                return $timeline_query->withTrashed();
            }])
            ->latest();

        if (array_key_exists('report_id', $filters) && $filters['report_id']) {
            $reports_query->where('id', $filters['report_id']);
        }
        if (array_key_exists('query', $filters) && $filters['query']) {
            $reports_query->where(function ($where) use ($filters) {
                return $where->where('content', 'like', "%{$filters['query']}%")
                    ->orWhere('user_id',  $filters['query'])
                    ->orWhereHas('user', function ($where_has)  use ($filters) {
                        return $where_has->where('name', 'like',  "%{$filters['query']}%")
                            ->orWhere('email', 'like',  "%{$filters['query']}%");
                    })
                    ->orWhereHas('timeline', function ($where_has)  use ($filters) {
                        return $where_has->withTrashed()->where('content', 'like',  "%{$filters['query']}%");
                    })
                ;
            });
        }
        return $reports_query->paginate($filters['per_page']);
    }

    public function reportStatus($data)
    {
        $result = Report::query()
            ->where('id', $data['report_id'])
            ->first()->update([
                'status' => $data['status']
            ]);

        return $result;
    }
}
