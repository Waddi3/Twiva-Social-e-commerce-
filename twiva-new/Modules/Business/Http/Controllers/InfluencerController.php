<?php

namespace Modules\Business\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Business\Http\Requests\InfluencerCompareRequest;
use Modules\Business\Http\Requests\InfluencerShowRequest;
use Modules\Business\Http\Resources\CompareInfluencersCollection;
use Modules\Business\Http\Resources\IndexInfluencersCollection;
use Modules\Business\Http\Resources\ShowInfluencersCollection;
use Modules\Influencer\Repositories\InfluencerRepository;

class InfluencerController extends Controller
{

    private $influencerRepository;

    public function __construct(InfluencerRepository $influencerRepository)
    {
        $this->influencerRepository = $influencerRepository;
    }

    public function index(Request $request)
    {
        $influencers = $this->influencerRepository->getInfluencers();

        if (\request('searchIndicator')) {
            $influencers = $this->influencerRepository->searchInInfluencers($influencers);
        }
        if (\request()->has('order_by')) {
            $influencers = $this->influencerRepository->orderInfluencerBy($influencers);
        }
        if (request()->has('without_order')) {

        } else {
            $order = \request('order') ?? 5;
            $influencers->orderBy(DB::raw('RAND(' . $order . ')'));
        }
        $influencers = $influencers->paginate(21)->appends(request()->query());
        return response()->json(['data' => IndexInfluencersCollection::collection($influencers)->resource]);
    }

    public function compare(InfluencerCompareRequest $request)
    {
        $influencers = $this->influencerRepository->getInfluencers()->whereIn('id', $request->validated()['ids'])->get();
        return response()->json(['data' => CompareInfluencersCollection::collection($influencers)->resource]);
    }

    public function show(InfluencerShowRequest $request)
    {
        $influencer = $this->influencerRepository->getInfluencers()->where('id', $request->validated()['id'])->first();
        return response()->json(['data' => new ShowInfluencersCollection($influencer)]);
    }


}
