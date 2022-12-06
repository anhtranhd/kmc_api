<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=320, initial-scale=1"/>
    <title>Quên mật khẩu</title>
    <style type="text/css">

        /* ----- Client Fixes ----- */

        /* Force Outlook to provide a "view in browser" message */
        #outlook a {
            padding: 0;
        }

        /* Force Hotmail to display emails at full width */
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        /* Force Hotmail to display normal line spacing */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }


        /* Prevent WebKit and Windows mobile changing default text sizes */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Remove spacing between tables in Outlook 2007 and up */
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Allow smoother rendering of resized image in Internet Explorer */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* ----- Reset ----- */

        html,
        body,
        .body-wrap,
        .body-wrap-cell {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #464646;
            text-align: left;
        }

        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        td, th {
            text-align: left;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #464646;
            line-height: 1.5em;
        }

        b a,
        .footer a {
            text-decoration: none;
            color: #464646;
        }

        a.blue-link {
            color: blue;
            text-decoration: underline;
        }

        /* ----- General ----- */

        td.center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .body-padding {
            padding: 24px 40px 40px;
        }

        .border-bottom {
            border-bottom: 1px solid #D8D8D8;
        }

        table.full-width-gmail-android {
            width: 100% !important;
        }

        .stars {
            width: 55px;
            height: 55px;
        }

        .rating {
            width: 140px;
            font-weight: bold;
        }

        /* ----- Header ----- */
        .header {
            font-weight: bold;
            font-size: 16px;
            line-height: 16px;
            height: 16px;
            padding-top: 19px;
            padding-bottom: 7px;
        }

        .header a {
            color: #464646;
            text-decoration: none;
        }

        /* ----- Footer ----- */

        .footer a {
            font-size: 12px;
        }
    </style>

    <style type="text/css" media="only screen and (max-width: 650px)">
        @media only screen and (max-width: 650px) {
            * {
                font-size: 16px !important;
            }


            table[class*="w320"] {
                width: 320px !important;
            }

            td[class="mobile-center"],
            div[class="mobile-center"] {
                text-align: center !important;
            }

            td[class*="body-padding"] {
                padding: 20px !important;
            }

            td[class="mobile"] {
                text-align: right;
                vertical-align: top;
            }
        }
    </style>

</head>
<body style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top" align="left" width="100%"
            style="background:repeat-x url(https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2) #f9f8f8;">
            <center>

                <table class="w320 full-width-gmail-android" bgcolor="#f9f8f8"
                       background="https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2"
                       style="background-color:transparent" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td width="100%" height="48" valign="top">
                            <!--[if gte mso 9]>
                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false"
                                    style="mso-width-percent:1000;height:49px;">
                                <v:fill type="tile" src="https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2"
                                        color="#ffffff"/>
                                <v:textbox inset="0,0,0,0">
                            <![endif]-->
                            <table class="full-width-gmail-android" cellspacing="0" cellpadding="0" border="0"
                                   width="100%">
                                <tr>
                                    <td class="header center" width="100%">
                                        <a href="#">
                                            museums
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <!--[if gte mso 9]>
                            </v:textbox>
                            </v:rect>
                            <![endif]-->
                        </td>
                    </tr>
                </table>

                <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff">
                    <tr>
                        <td align="center">
                            <center>
                                <table class="w320" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                        <td class="body-padding mobile-padding">
                                            <table cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td style="text-align:center; font-size:30px; padding-bottom:20px;">
                                                        Tài khoản AR mall
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Chào bạn {{ $account->full_name }}, <br>
                                                        <br>
                                                        Mật khẩu của bạn đã được reset bởi quản trị viên vào {{ $account->updated_at }}<br>
                                                        <br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: bold">
                                                        Thông tin tài khoản:
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-style: italic">
                                                        Tên đăng nhập: <span style="color: #1c72c5; margin-right: 5px">{{ $account->email }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-style: italic">
                                                        Mật khẩu: <span style="margin-right: 5px">{{ $newPassword }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Link đăng nhập lại hệ thống qua website <a href="{{ env('WEB_URL') }}">{{ env('WEB_URL') }}</a>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td class="left" style="text-align:left;">
                                                        <br/>
                                                        <br/>
                                                        Trân trọng!
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
</body>
</html>
