<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Notification\Entities\NotificationTitle;

class NotificationTitlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $notifications_titles = [
            [
                'slug' => 'influencer_applied_for_campaign_notification_title',
                'title' => 'An influencer has applied to your campaign invitation.',
                'description' => 'When influencer has applied to your campaign invitation (email title)',
            ],
            [
                'slug' => 'influencer_applied_for_campaign_notification_fcm_title',
                'title' => 'Invitation Applied.',
                'description' => 'When influencer has applied to your campaign invitation (fcm title)',
            ],
            [
                'slug' => 'influencer_rejected_for_campaign_notification_title',
                'title' => 'An influencer has rejected to your campaign invitation.',
                'description' => 'When influencer has rejected to your campaign invitation (email title)',
            ],
            [
                'slug' => 'influencer_rejected_for_campaign_notification_fcm_title',
                'title' => 'Invitation rejected',
                'description' => 'When influencer has rejected to your campaign invitation (fcm title)',
            ],
            [
                'slug' => 'new_order_received_notification_title',
                'title' => 'You have received a new order.',
                'description' => 'Notify business after buyer(customer) finish the order (email title)',
            ],
            [
                'slug' => 'new_order_received_notification_fcm_title',
                'title' => 'new order received',
                'description' => 'Notify business after buyer(customer) finish the order (fcm title)',
            ],
            [
                'slug' => 'product_added_to_influencer_shop_title',
                'title' => 'One of your products has been added to an influencer\'s e-shop.',
                'description' => 'Notify business when influencer add new product (email title)',
            ],
            [
                'slug' => 'product_added_to_influencer_shop_fcm_title',
                'title' => 'Product added to influencer shop',
                'description' => 'Notify business when influencer add new product (fcm title)',
            ],
            [
                'slug' => 'welcome_to_twiva_notification_title',
                'title' => 'Welcome To Twiva!',
                'description' => 'Send welcome message to new user(influencer/business) (email title)',
            ],
            [
                'slug' => 'order_has_been_canceled_notification_title',
                'title' => 'Your order has been cancelled by the seller.',
                'description' => 'Notify buyer to canceled his order (email title)',
            ],
            [
                'slug' => 'order_has_been_delivered_notification_title',
                'title' => 'Your order has been delivered.',
                'description' => 'Notify buyer to delivered his order (email title)',
            ],
            [
                'slug' => 'order_has_been_dispatched_notification_title',
                'title' => 'Your order has been dispatched.',
                'description' => 'Notify buyer to dispatched his order (email title)',
            ],
            [
                'slug' => 'order_has_been_placed_notification_title',
                'title' => 'Your orders has been placed.',
                'description' => 'Notify buyer to placed his order (email title)',
            ],
            [
                'slug' => 'order_is_being_processed_notification_title',
                'title' => 'Your order is being processed.',
                'description' => 'Notify buyer to processed his order (email title)',
            ],
            [
                'slug' => 'campaign_application_sent_to_business_notification_title',
                'title' => 'Your campaign application has been sent to the business.',
                'description' => 'Notify influencer has invitation submit it sent (email title)',
            ],
            [
                'slug' => 'campaign_application_sent_to_business_notification_fcm_title',
                'title' => 'Campaign application has been sent !',
                'description' => 'Notify influencer has invitation submit it sent (fcm title)',
            ],
            [
                'slug' => 'hired_for_campaign_notification_title',
                'title' => 'Congratulations! You have been hired for a campaign you applied for',
                'description' => 'Notify influencer has been hired (email title)',
            ],
            [
                'slug' => 'hired_for_campaign_notification_fcm_title',
                'title' => 'Congratulations!',
                'description' => 'Notify influencer has been hired (fcm title)',
            ],
            [
                'slug' => 'invitation_to_new_campaign_notification_title',
                'title' => 'You have been invited to a new campaign, apply now',
                'description' => 'Notify influencer has been invited to a new campaign (email title)',
            ],
            [
                'slug' => 'invitation_to_new_campaign_notification_fcm_title',
                'title' => 'Apply now!',
                'description' => 'Notify influencer has been invited to a new campaign (fcm title)',
            ],
            [
                'slug' => 'post_is_scheduled_notification_title',
                'title' => 'Your post is scheduled.',
                'description' => 'Notify influencer his schedule\'s post has posted (email title)',
            ],
            [
                'slug' => 'post_is_scheduled_notification_fcm_title',
                'title' => 'Scheduled post',
                'description' => 'Notify influencer his schedule\'s post has posted (fcm title)',
            ],
            [
                'slug' => 'purchase_has_been_made_from_eshop_notification_title',
                'title' => 'A Purchase has been made from your e-shop, You will receive your commission once the product is successfully delivered',
                'description' => 'Notify influencer when new order is paid (email title)',
            ],
            [
                'slug' => 'purchase_has_been_made_from_eshop_notification_fcm_title',
                'title' => 'New order',
                'description' => 'Notify influencer when new order is paid (fcm title)',
            ],
//            [
//                'slug' => 'payment_processed_notification_title',
//                'title' => 'Twiva has processed your payment!',
//                'description' => '',
//            ],
            [
                'slug' => 'registration_with_user_referral_code_notification_title',
                'title' => 'A user has registered with your referral code.',
                'description' => 'Notify influencer when users use your referral code to register in Twiva (email title)',
            ],
            [
                'slug' => 'registration_with_user_referral_code_notification_fcm_title',
                'title' => 'referral code registration',
                'description' => 'Notify influencer when users use your referral code to register in Twiva (fcm title)',
            ],
            [
                'slug' => 'send_otp_via_email_notification_title',
                'title' => 'OTP code request',
                'description' => 'Notify user to delivery opt code (email title)',
            ],
            [
                'slug' => 'winning_payout_notification_success_title',
                'title' => 'Your Profit Payment Successfully Processed - Twiva',
                'description' => 'When automatic payment withdraw for influencer was succeed (email title)',
            ],
            [
                'slug' => 'winning_payout_notification_fail_title',
                'title' => 'Apology for Unsuccessful Payment - Please Resubmit Your Request - Twiva',
                'description' => 'When automatic payment withdraw for influencer was failed (email title)',
            ],
            [
                'slug' => 'winning_payout_notification_success_fcm_title',
                'title' => 'Your Profit Payment Successfully Processed - Twiva',
                'description' => 'When automatic payment withdraw for influencer was succeed (fcm title)',
            ],
            [
                'slug' => 'winning_payout_notification_fail_fcm_title',
                'title' => 'Apology for Unsuccessful Payment - Please Resubmit Your Request - Twiva',
                'description' => 'When automatic payment withdraw for influencer was failed (fcm title)',
            ],
        ];
        foreach ($notifications_titles as $notifications_title) {
            NotificationTitle::create([
                'slug' => $notifications_title['slug'],
                'title' => $notifications_title['title'],
                'description' => $notifications_title['description'],
            ]);
        }
    }
}
