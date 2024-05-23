<?php

declare(strict_types=1);

namespace Shimmie2;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
* CAPTCHA abstraction                                                       *
\* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function captcha_get_html(bool $anon_only): string
{
    global $config, $user;

    if (DEBUG && ip_in_range(get_real_ip(), "127.0.0.0/8")) {
        return "";
    }

    $captcha = "";
    if (!$anon_only || $user->is_anonymous()) {
        $r_publickey = $config->get_string("api_recaptcha_pubkey");
        if (!empty($r_publickey)) {
            $captcha = "
				<div id=\"captcha\" style=\"width:300px;height:74px;background:var(--input-submit);
				border:1px solid var(--input-submit-border);\" onclick=\"loadHCaptcha()\">
					<noscript>Captcha requires JavaScript.</noscript>
					<div style=\"display:flex;justify-content:center;align-items:center;height:100%;\">
						Click to load HCaptcha
					</div>
					<script>
						function loadHCaptcha() {
							const divEle = document.getElementById(\"captcha\");
							divEle.innerHTML = ``;
							const newDiv = document.createElement(\"div\");
							newDiv.innerHTML = `
								<div class=\"h-captcha\" data-sitekey=\"c4b4f6a7-c1e3-40d3-af6c-f950bda8d788\"></div>
							`;
							divEle.appendChild(newDiv);
							const newScript = document.createElement(\"script\");
							newScript.setAttribute(\"src\", \"https://js.hcaptcha.com/1/api.js?recaptchacompat=off\");
							divEle.appendChild(newScript);
						}
					</script>
				</div>";
        } /*else {
            session_start();
            $captcha = \Securimage::getCaptchaHtml(['securimage_path' => './vendor/dapphp/securimage/']);
        }*/
    }
    return $captcha;
}

function captcha_check(bool $anon_only): bool
{
    global $config, $user;

    if (DEBUG && ip_in_range(get_real_ip(), "127.0.0.0/8")) {
        return true;
    }

    if ($anon_only && !$user->is_anonymous()) {
        return true;
    }
    $r_privatekey = $config->get_string('api_recaptcha_privkey');
    if (!empty($r_privatekey)) {
        if (!empty($_POST['h-captcha-response'])) {
            $data = [
                'secret' => $r_privatekey,
                'response' => $_POST['h-captcha-response']
            ];
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);

            if (is_string($response)) {
                $responseData = json_decode($response);
                if ($responseData->success) {
                    return true;
                }
            }
            return false;
        }
    } /*else {
        session_start();
        $securimg = new \Securimage();
        if ($securimg->check($_POST['captcha_code']) === false) {
            log_info("core", "Captcha failed (Securimage)");
            return false;
        }
    }*/

    return true;
}
