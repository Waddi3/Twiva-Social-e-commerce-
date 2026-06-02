<?php

namespace Modules\Front\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller;
use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;

class FrontController extends Controller
{


    public function home()
    {
        $categoryRepository = new ECommerceCategoryRepository();
        $categories = $categoryRepository->getSubCategories([]);
        return view('front::e-commerce.pages.home', [
            'categories' => $categories,
        ]);
        //return view('front::home', ['feeds' => []]);
    }

    public function services()
    {
        return view('front::services');
    }

    public function influencerMarketing()
    {
        return view('front::influencer-marketing');

    }

    public function socialCommerce()
    {
        return view('front::social-commerce');

    }

    public function faq()
    {
        return view('front::faq');
    }

    public function about()
    {
        return view('front::about');

    }

    public function getThreeFeeds()
    {
        $client = new Client();
        $feeds = [];
        try
        {
            $request = $client->request('GET', 'https://blog.twiva.co.ke/wp-json/wp/v2/posts?context=embed&per_page=3&order=desc');
            foreach (json_decode($request->getBody()->getContents()) as $key => $feed)
            {
                $feeds[$key]['link'] = $feed->link;
                $feeds[$key]['title'] = $feed->title->rendered;
                $feeds[$key]['image'] = $feed->fimg_url;
                $feeds[$key]['excerpt'] = strip_tags($feed->excerpt->rendered);
            }
        }
        catch (GuzzleException)
        {
        }

        return response()->json(['view' => view('front::partial._blogs', ['feeds' => $feeds])->render()]);
    }

    public function policy()
    {
        return view('front::landing.policy');
    }

    public function termsConditions()
    {
        return view('front::landing.terms-conditions');
    }

    public function banned()
    {
        return view('front::landing.banned');
    }






}
