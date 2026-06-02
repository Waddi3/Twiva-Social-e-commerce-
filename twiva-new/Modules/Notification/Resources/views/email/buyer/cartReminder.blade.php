<div style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 20px; margin: 0; color: #333;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">

        <div style="background-color: #8a0e49; padding: 20px; text-align: center; color: #ffffff;">
            <h1 style="font-size: 24px; margin: 0;">We Miss You!</h1>
            <p style="font-size: 16px; margin-top: 4px;">Complete your purchase before it's too late!</p>
        </div>

        <div style="padding: 20px; text-align: center;">
            <p style="font-size: 18px; margin-bottom: 10px;">Hi {{ $buyer->name }}</p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                You have items waiting in your cart! Complete your purchase now to ensure you don't miss out.
            </p>

            <a href="https://twiva.com"
               style="
                    display: inline-block;
                    margin-top: 20px;
                    padding: 12px 24px;
                    background-color: #8a0e49;
                    color: #ffffff;
                    text-decoration: none;
                    font-size: 16px;
                    border-radius: 6px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                View Your Cart
            </a>

        </div>

        <div style="background-color: #f3f4f6; padding: 10px; text-align: center; color: #555555;">
            <p style="margin: 0; font-size: 12px;">If you have any questions, feel free to <a href="mailto:support@twiva.com" style="color: #2563eb;">contact us</a>.</p>
            <p style="font-size: 12px;">© {{ date('Y') }} Twiva. All rights reserved.</p>
        </div>

    </div>
</div>
