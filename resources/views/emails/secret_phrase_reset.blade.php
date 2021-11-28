<!DOCTYPE html>
<html>

<style>
    body {
        background: #fcfcfc;
    }

    table {
        mso-margin-top-alt: 0px;
        mso-margin-bottom-alt: 0px;
        mso-padding-alt: 0px 0px 0px 0px;
        border-collapse: collapse;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }
</style>

<table style="background: #fcfcfc; width: 100%;" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="50%" valign="top"><img src="" style="opacity: 0;" width="100%" alt="" /></td>
        <td width="600" valign="top" style="min-width: 600px;">
            <br>

            <table style="width: 100%; background: #ffffff; border: 0;" width="100%" cellspacing="0" cellpadding="0"
                   border="0">
                <tbody>
                <tr>
                    <td
                        style="font-family: Arial; text-align: center; vertical-align: top; background: #ffffff; padding-top: 16px; padding-bottom: 16px; border-top: #f3f3f3 solid 1px; border-bottom: 1px solid rgba(79,79,79, 0.15); border-right: #f3f3f3 solid 1px; border-left: #f3f3f3 solid 1px;">
                        <a href="{{ config('app.url') }}" target="blank"
                           style="display: inline-block; border:none; text-decoration: none; display: block; margin: auto; border: 0; cursor: pointer;"
                           title="2local logo" border="0">
                            <img src="{{ asset('assets/2local-logo.png') }}" alt="2local logo" title="2local logo">
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%; background: #ffffff; border: 0;" cellpadding="0" border="0" cellspacing="0"
                   width="100%">
                <tr>
                    <td style="font-family: Arial; text-align: center; padding: 24px; border-left: #f3f3f3 solid 1px; border-right: #f3f3f3 solid 1px; text-align: center;"
                        valign=" top">
                        <h3
                            style="font-size: 33px; font-weight: 400; margin-top: 0; margin-bottom: 20px; line-height: 39.6px; color: #4F4F4F;">
                            Hello {{ $username }}!
                        </h3>
                        <p
                            style="font-size: 19px; font-weight: 400; margin-top: 0; margin-bottom: 20px; line-height: 28px; color: #787878;">
                            Your secret phrase has been successfully changed. This is the new secret phrase:
                        </p>
                        <p
                            style="font-size: 14px; font-weight: 700; letter-spacing: 0.1px; margin-top: 0; margin-bottom: 4px; line-height: 28px; color: #787878;">
                            {{ $secretPhrase }}
                        </p>
                    </td>
                </tr>
            </table>


            <table style="width: 100%; border: 0;" cellpadding="0" border="0" cellspacing="0" width="100%">
                <tr>
                    <td style="vertical-align: top;" colspan="2" valign="top">
                        <img src="{{ asset('assets/shade.png') }}" style=" margin-left: 1px; display: block; border: 0;" alt="">
                    </td>
                </tr>
            </table>
            <br>
        </td>
        <td width="50%" valign="top"><img src="" style="opacity: 0;" width="100%" alt="" /></td>
    </tr>
</table>
</html>
