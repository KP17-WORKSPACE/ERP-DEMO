<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
    <meta http-equiv=Content-Type content=text/html; charset=iso-8859-1 />
    <title>VENUS-HRMS</title>
    <style>
        body {
            margin: 0px;
            padding: 0px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }
        
        a {
            color: #005f37;
            text-decoration: none;
        }
        
        .table {
            color: #003300;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            background-color: #ffffff;
            border: solid 1px #005f37;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <table width=700 border=0 cellspacing=0 cellpadding=0 align=center bgcolor=#eaeaea style=border:1px solid #eaeaea;>
        <tr>
            <td style="text-align: center;">
                <img src=https://crm.venushrms.com/public/backEnd/img/venuserp.png alt=VENUS-ERP border=0 height=25 style="padding:15px 20px" />
            </td>
        </tr>
        <tr>
            <td>
                <table width=100% border=0 cellspacing=10 cellpadding=10 bgcolor=#FFFFFF>
                    <tr>
                        <td style='line-height:25px;'><br />
                            <b>Dear {!! $name !!},</b><br />
                            {!! $body !!}<br />
                            <br /><br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor=#eaeaea height=30 align=center style=color:#000000; font-family:Arial, Helvetica, sans-serif; font-size:12px;>VENUS-CRM</td>
        </tr>
    </table>
</body>

</html>