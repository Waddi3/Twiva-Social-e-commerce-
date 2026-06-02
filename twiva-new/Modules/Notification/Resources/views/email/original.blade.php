<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        body {
            margin: 0;
            mso-line-height-rule: exactly;
            padding: 0;
            min-width: 100%;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        td {
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body style="margin: 0;min-width: 100%;background-color: #fff">
<div class="wrapper"
     style="display: table;table-layout: fixed;width: 100%;min-width: 600px;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;background-color: #fff; text-align: center;">
    <table cellpadding="0" cellspacing="0" class="header centered"
           style="border-collapse: collapse;border-spacing: 0;Margin-left: auto;Margin-right: auto;width: 600px">
        <tbody>
        <tr>
            <td class="logo" style="padding: 32px 0; vertical-align: top;">
                <div class="logo-center" style="text-align: center" align="center" id="email-header"><a
                            href="https://www.twiva.co.ke" target="_blank">
                        <img style="width:100px;border: 0;display: block;Margin-left: auto;Margin-right: auto;"
                             src="{{asset('shop/images/logos/Logo.png')}}" alt=""></a>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="centered" style="border-collapse: collapse;border-spacing: 0;Margin-left: auto;Margin-right: auto">
        <tbody>
        <tr>
            <td class="border" style="padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;width: 1px">​</td>
            <td style="padding: 0;vertical-align: top">
                <table class="one-col"
                       style="border-collapse: collapse;border-spacing: 0;Margin-left: auto;Margin-right: auto;width: 600px;background-color: #ffffff;font-size: 14px;table-layout: fixed">
                    <tbody>
                    <tr>
                        <td class="column" style="padding: 0;vertical-align: top;text-align: left">
                            <table border="0" cellpadding="0" cellspacing="0" class="contents"
                                   style="border-collapse: collapse;border-spacing: 0; border:1px solid #ededed; table-layout: fixed;width: 100%">
                                <tbody>
                                <tr>
                                    <td
                                            style="padding: 0 32px;vertical-align: top;word-break: break-word;word-wrap: break-word">

                                        <p style="color: #565656;font-family: sans-serif;font-size: 16px;line-height: 24px;Margin-bottom: 24px; Margin-top: 24px;">
                                            Hi <b>{{ $details['name'] }} , </b></p>
                                        <p style="color: #565656;font-family: sans-serif;font-size: 16px;line-height: 24px;Margin-bottom: 24px; Margin-top: 24px; ">
                                            {{ $details['title'] }}</p>
                                        <p style="color: #565656;font-family: sans-serif;font-size: 16px;line-height: 24px;Margin-bottom: 24px; Margin-top: 24px; ">
                                            {{ $details['email_message'] }} <br> If you
                                            have any questions, send us an email at <a href="mailto:info@twiva.co.ke">info@twiva.co.ke</a>
                                        </p>
                                        <p style="padding-top:10px;color:#565656;font-family: sans-serif;font-size: 16px;line-height: 20px;">
                                            We are glad you're here!
                                        </p>
                                        <p style="color:#565656;font-family: sans-serif;font-size: 16px;padding-bottom: 30px;">
                                            <strong>Twiva team</strong>
                                        </p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif;font-size: 11px; color:#565656; padding-top:20px; text-align: center;">
                            This is an automatically generated email, please do not reply.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif;font-size: 11px; color:#565656; padding-top:10px; text-align: center;">
                            © Twiva 2022, All Rights Reserved.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
