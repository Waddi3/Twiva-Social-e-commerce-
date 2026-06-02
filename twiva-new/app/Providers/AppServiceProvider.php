<?php

namespace App\Providers;

use App\View\Components\Partials\CheckoutContainer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Buyer\Entities\Buyer;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;
use Modules\Notification\Notifications\Business\InfluencerAppliedForCampaignNotification;
use Modules\Notification\Notifications\Business\InfluencerRejectedForCampaignNotification;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Business\ProductAddedToInfluencerShop;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedViaMobileNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedNotification;
use Modules\Notification\Notifications\Buyer\OrderIsBeingProcessedViaMobileNotification;
use Modules\Notification\Notifications\Buyer\ResendOrderDeliveryCodeNotification;
use Modules\Notification\Notifications\Buyer\ResetPasswordViaSmsNotification;
use Modules\Notification\Notifications\Buyer\VerifyPhoneNumberNotification;
use Modules\Notification\Notifications\Influencer\CampaignApplicationSentToBusinessNotification;
use Modules\Notification\Notifications\Influencer\HiredForCampaignNotification;
use Modules\Notification\Notifications\Influencer\InvitationToNewCampaignNotification;
use Modules\Notification\Notifications\Influencer\PostIsScheduledNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Notification\Notifications\Influencer\WelcomeToTwivaNotification;
use Modules\Notification\Notifications\Influencer\WinningPayoutNotification;
use Modules\Notification\Notifications\User\RegistrationWithUserReferralCodeNotification;
use Modules\Notification\Notifications\User\TestFCM;
use Modules\Order\Entities\ShippingAddress;
use Modules\Platform\Entities\WhatsAppCategory;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\User\Entities\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('checkout-container', CheckoutContainer::class);

        HasMany::macro('sync', function (array $data, $deleting = true) {
            $changes = [
                'created' => [], 'deleted' => [], 'updated' => [],
            ];

            /** @var HasMany $this */

            // Get the primary key.
            $relatedKeyName = $this->getRelated()->getKeyName();

            // Get the current key values.
            $current = $this->newQuery()->pluck($relatedKeyName)->all();

            // Cast the given key to an integer if it is numeric.
            $castKey = function ($value) {
                if (is_null($value)) {
                    return $value;
                }

                return is_numeric($value) ? (int) $value : (string) $value;
            };

            // Cast the given keys to integers if they are numeric and string otherwise.
            $castKeys = function ($keys) use ($castKey) {
                return (array) array_map(function ($key) use ($castKey) {
                    return $castKey($key);
                }, $keys);
            };

            // Get any non-matching rows.
            $deletedKeys = array_diff(
                $current,
                $castKeys(
                    Arr::pluck($data, $relatedKeyName)
                )
            );

            if ($deleting && count($deletedKeys) > 0) {
                $this->getRelated()->destroy($deletedKeys);
                $changes['deleted'] = $deletedKeys;
            }

            // Separate the submitted data into "update" and "new"
            // We determine "newRows" as those whose $relatedKeyName (usually 'id') is null.
            $newRows = Arr::where($data, function ($row) use ($relatedKeyName) {
                return Arr::get($row, $relatedKeyName) === null;
            });

            // We determine "updateRows" as those whose $relatedKeyName (usually 'id') is set, not null.
            $updatedRows = Arr::where($data, function ($row) use ($relatedKeyName) {
                return Arr::get($row, $relatedKeyName) !== null;
            });

            if (count($newRows) > 0) {
                $newRecords = $this->createMany($newRows);
                $changes['created'] = $castKeys(
                    $newRecords->pluck($relatedKeyName)->toArray()
                );
            }

            foreach ($updatedRows as $row) {
                $this->getRelated()->where($relatedKeyName, $castKey(Arr::get($row, $relatedKeyName)))
                    ->update($row);
            }

            $changes['updated'] = $castKeys(Arr::pluck($updatedRows, $relatedKeyName));

            return $changes;
        });


        Relation::MorphMap([
            'products' => Product::class,
            'influencer_shop_product' => InfluencerShopProducts::class,
            'categories' => Category::class,
            'sub_categories' => SubCategories::class,
            'whats_app_groups' => WhatsAppGroup::class,
            'users' => User::class,
            'whats_app_categories' => WhatsAppCategory::class,
            ProductAddedToInfluencerShop::class => ProductAddedToInfluencerShop::class,
            WelcomeToTwivaNotification::class => WelcomeToTwivaNotification::class,
            PurchaseHasBeenMadeFromEshopNotification::class => PurchaseHasBeenMadeFromEshopNotification::class,
            NewOrderReceivedNotification::class => NewOrderReceivedNotification::class,
            InfluencerAppliedForCampaignNotification::class => InfluencerAppliedForCampaignNotification::class,
            CampaignApplicationSentToBusinessNotification::class => CampaignApplicationSentToBusinessNotification::class,
            PostIsScheduledNotification::class => PostIsScheduledNotification::class,
            RegistrationWithUserReferralCodeNotification::class => RegistrationWithUserReferralCodeNotification::class,
            HiredForCampaignNotification::class => HiredForCampaignNotification::class,
            InvitationToNewCampaignNotification::class => InvitationToNewCampaignNotification::class,
            ResendOrderDeliveryCodeNotification::class => ResendOrderDeliveryCodeNotification::class,
            VerifyPhoneNumberNotification::class => VerifyPhoneNumberNotification::class,
            TestFCM::class => TestFCM::class,
            WinningPayoutNotification::class => WinningPayoutNotification::class,
            OrderIsBeingProcessedViaMobileNotification::class => OrderIsBeingProcessedViaMobileNotification::class,
            OrderHasBeenDispatchedViaMobileNotification::class => OrderHasBeenDispatchedViaMobileNotification::class,
            InfluencerRejectedForCampaignNotification::class => InfluencerRejectedForCampaignNotification::class,
            OrderIsBeingProcessedNotification::class => OrderIsBeingProcessedNotification::class,
            OrderHasBeenDispatchedNotification::class => OrderHasBeenDispatchedNotification::class,
            ResetPasswordViaSmsNotification::class => ResetPasswordViaSmsNotification::class,
            'shipping_address' => ShippingAddress::class,
            'buyers' => Buyer::class,
        ]);
    }
}

/*



*/
