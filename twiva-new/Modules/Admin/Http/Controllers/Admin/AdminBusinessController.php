<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Events\NewUserRegistered;
use Modules\Admin\Http\Requests\AdminBulkProductUpload;
use Modules\Admin\Http\Requests\AdminCreateBusinessRequest;
use Modules\Admin\Http\Requests\AdminUpdateBusinessRequest;
use Modules\Admin\Imports\BulkProductUploadImport;
use Modules\Admin\Repositories\BulkProductUploadRepository;
use Modules\Business\Entities\Company;
use Modules\Business\Repository\BusinessRepository;
use Modules\Home\Entities\City;
use Modules\Home\Entities\CountryCode;
use Modules\Home\Entities\State;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;
use ZipArchive;

class AdminBusinessController extends Controller
{
    private BulkProductUploadRepository $bulkUploadRepository;
    public function __construct()
    {
        $this->bulkUploadRepository = new BulkProductUploadRepository();
    }

    public function index()
    {
        $users = User::withTrashed()->with([
            'businessDetails', 'products', 'businessTotalSales'
        ])
            ->withCount('influencerShopProducts')
            ->where('type', User::BusinessType)
            ->orderBy('id', 'desc')
            ->paginate(10);
        $data = [
            'users' => $users,
        ];

        $users->each(function ($user) {
            $user->businessTotalSales = User::calculateBusinessEarning($user);
        });

        return view('admin::business.index', $data);
    }

    public function products($id)
    {
        $products = Product::with(['productReviewsAndRatings', 'eShopConnected'])
            ->orderBy('id', 'desc')
            ->where('user_id', $id)->paginate(10);
        $company = Company::where('user_id', $id)->first();

        return view('admin::business.products', compact('products', 'company'));
    }

    public function eShopsConnected($id)
    {
        $product = Product::with('company', 'eshopConnected.influencerShop.influencer.influencerDetails')->find($id);
        return view('admin::business.eshopsConnected', compact('product'));
    }

    public function create()
    {
        $countries = CountryCode::all();
        return view('admin::business.create', compact('countries'));
    }

    public function store(AdminCreateBusinessRequest $request)
    {
        $data = $request->validated();
        $business = BusinessRepository::createBusiness($data);
        event(new NewUserRegistered($business, $data['password']));
        $business->update([
            'is_profile_completed' => 1,
            'status' => true,
            'is_verified' => 1,
            'mobile_verified_at' => now(),
        ]);
        return redirect()->route('admin.business.index')->with('message', 'Business Created Successfully');
    }

    public function show($business)
    {
        $company = Company::with('products', 'countryDetails', 'stateDetails', 'cityDetails')->where('user_id', $business)->first();
        $user = User::with('products')->withTrashed()->where('id', $business)->first();
        return view('admin::business.show', compact('company', 'user'));
    }

    public function edit($business)
    {
        $states = '';
        $cities = '';
        $business = User::findOrFail($business);
        $company = Company::with('countryDetails', 'stateDetails', 'cityDetails')->where('user_id', $business->id)->first();
        $countries = CountryCode::all();
        if ($company && $company->country) {
            $states = State::where('country_id', $company->country)->get();
        }
        if ($company && $company->state) {
            $cities = City::where('state_id', $company->state)->get();
        }

        $data = [
            'id' => $business->id,
            'business' => $business,
            'company' => $company,
            'countries' => $countries,
            'cities' => $cities,
            'states' => $states,
        ];
        return view('admin::business.edit', $data);
    }

    public function update(AdminUpdateBusinessRequest $request, $id)
    {
        $data = $request->validated();

        $business = User::findOrFail($id);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'phone_code' => $data['phone_code'],
            'description' => $data['description'],
        ];
        if (array_key_exists('logo', $data) && isset($data['logo'])) {
            $data['logo'] = $data['logo']->store(Company::IMAGE_PATH, 'public');
            $data['logo'] = str_replace(User::IMAGE_PATH, '', $data['logo']);
            $userData['profile_image'] = $data['logo'];
        }

        unset($data['phone_code'], $data['phone_number'], $data['name'], $data['description'], $data['logo'], $data['image'], $data['email']);
        $business->update($userData);
        $business->businessDetails()->update($data);

        return redirect()->route('admin.business.index')->with('message', 'Business Updated Successfully');
    }

    public function destroy($id)
    {
        //
    }


    public function changeStatus(Request $request)
    {
        $user = User::withTrashed()->find(request('user_id'));
        $user->trashed() ? $user->restore() : $user->delete();
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function influencersConnected($id)
    {
        // TODO::H to check product added column in admin index
        /*
          'influencerShopProducts' => function ($influencerShopProducts) use ($products) {
            $influencerShopProducts->whereIn('product_id', $products);
        }
         */
        $products = Product::where('user_id', $id)->pluck('id');
        $users = User::withTrashed()->with('influencerDetails', 'shop', 'influencerShopProducts', 'businessTotalSales')
            ->whereIn('id', function ($query) use ($products) {
                $query->select('influencer_id')->from('influencer_shop_product')
                    ->whereIn('product_id', $products);
            })->paginate(10);

        return view('admin::business.influencers', compact('users'));
    }

    public function exportProductCsv(Request $request, $id)
    {
        return Excel::download(new BusinessProductsExport($id), 'businessProducts.csv');
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'business.csv';
        $users = User::with([
            'businessDetails', 'userSubscription', 'userSubscription.subscription', 'products', 'businessTotalSales'
        ])->where('type', '1')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['USER ID', 'BUSINESS NAME', 'CONTACT PERSON', 'EMAIL', 'CONTACT NO.', 'JOINED', 'SUBSCRIPTION', 'PRODUCTS', 'TOTAL SALES'];

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['USER ID'] = $user->id;
                $row['BUSINESS NAME'] = $user->businessDetails["busi_name"] ?? "--";
                $row['CONTACT PERSON'] = $user->businessDetails["contact_name"] ?? "--";
                $row['EMAIL'] = $user->businessDetails["comp_email"] ?? "--";
                $row['CONTACT NO.'] = $user->businessDetails["busi_number"] ?? "--";
                // $row['JOINED']  = $user->created_at->format('d/m/Y') ?? "--";
                $row['JOINED'] = date('d/m/y', strtotime($user->created_at)) ?? "--";
                $row['SUBSCRIPTION'] = $user->userSubscription["subscription"]["subscription_title"] ?? '';
                $row['PRODUCTS'] = count($user->products);
                $row['TOTAL SALES'] = count($user->businessTotalSales) . ' (KSH' . $user->businessTotalSales->sum('amount') . ')';

                fputcsv($file, [$row['USER ID'], $row['BUSINESS NAME'], $row['CONTACT PERSON'], $row['EMAIL'], $row['CONTACT NO.'], $row['JOINED'], $row['SUBSCRIPTION'], $row['PRODUCTS'], $row['TOTAL SALES']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function productStatus(Request $request)
    {
        if ($request->status == 0 || $request->status == 1) {
            $product = Product::find($request->product_id);
            $product->update(['status' => $request->status]);
            InfluencerShopProducts::where('product_id', $request->product_id)->update(['status' => $request->status]);
        }
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function toggleProductTrending(Request $request)
    {
        if ($request->trending == 0 || $request->trending == 1) {
            $product = Product::find($request->product_id);
            $product->update(['is_trending' => $request->trending]);
        }
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function updateWaivePrompt()
    {
        $user = User::find(request('user_id'));
        $result = $user->update([
            'waive_prompt' => request('status')
        ]);
        if ($result) {
            return response()->json(['success' => 'Status change successfully.']);
        }
        return response()->json(['error' => 'please check your data'], 422);
    }

    public function bulkProductUpload(AdminBulkProductUpload $request)
    {
        $data = $request->validated();
        $user = User::find($data['business-id']);
        if( !$user ) {
            return redirect()->route('admin.business.index')->with('message', 'The Business not found');
        }

        $file = $request->file('file');
        $path = $file->getPathName();
        $destinationPath = Storage::disk('local')->path('tmp_bulk_products/'.$data['business-id'].'/');

        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            $zip->extractTo($destinationPath);
            $zip->close();
        } else {
            return redirect()->route('admin.business.index')->with('message', 'Failed to open the file');
        }

        $fileName = $this->bulkUploadRepository->getProductListFileName($destinationPath);
        if( is_null($fileName) || $fileName == '' ) {
            return redirect()->route('admin.business.index')->with('message', 'The product list file not exists');
        }
        if( !File::exists($destinationPath.$fileName) ) {
            return redirect()->route('admin.business.index')->with('message', 'Invalid file format');
        }

        // Import
        $bulkProductUploadImport = new BulkProductUploadImport($data['business-id'], $destinationPath);
        try {
            Excel::import($bulkProductUploadImport, $destinationPath.$fileName);

            // Remove tmp files
            File::deleteDirectory($destinationPath);

            return redirect()->route('admin.business.index')->with('message', 'The products has been imported successfully');
        }
        catch(Exception $exception) {
            return redirect()->route('admin.business.index')->with('message', 'Error: '.$exception->getMessage());
        }
    }

    public function updateAccessingEarningsPriorToDelivery()
    {
        $user = User::find(request('user_id'));
        $result = $user->update([
            'accessing_earnings_prior_to_delivery' => request('status')
        ]);
        if ($result) {
            return response()->json(['success' => 'Status change successfully.']);
        }
        return response()->json(['error' => 'please check your data'], 422);
    }
}
