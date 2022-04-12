<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CalendarRepository;
use App\Repository\TargetRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TargetController extends AbstractController {
    public const DAILY = "D";
    public const WEEKLY = "W";
    public const MONTHLY = "M";
    public const YEARLY = "Y";

    #[Route('/target', name: 'target')]
    public function index(TargetRepository $targetRepository): Response {
        $targets = $targetRepository->findAll();

        return $this->render('target/index.html.twig', [
            'targets' => $targets,
        ]);
    }

    #[Route('/target/test', name: 'test')]
    public function getHoursByPeriod(Request $request, ActivityRepository $activityRepository, CalendarRepository $calendarRepository): Response {
        $day = $request->query->get('day');
        $period = $request->query->get('period');

        //Récupérer le contenu de la page Web à partir de l'url.
        $url = "https://connect.garmin.com/modern/proxy/wellness-service/wellness/dailySleepData/3a38753f-5490-4068-9a83-60085bc847e3?date={$day}&nonSleepBufferMinutes=60";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'authority: connect.garmin.com',
                'pragma: no-cache',
                'cache-control: no-cache',
                'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                'accept: application/json, text/plain, */*',
                'nk: NT',
                'x-app-ver: 4.53.2.0',
                'sec-ch-ua-mobile: ?0',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'referer: https://connect.garmin.com/modern/sleep/2022-04-06',
                'accept-language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                'cookie: ' . $this->getCookiesString(),
                'sec-gpc: 1'
            ),
        ));

        $response = curl_exec($curl);
        $response_json = json_decode($response, true);


        curl_close($curl);

//        $calendars = new ArrayCollection();
//
//        switch($period) {
//            case self::DAILY:
//                $activities = $activityRepository->findAll();
//                foreach($activities as $activity) {
//                    $calendars->add($calendarRepository->findBy([
//                        'activity' => $activity,
//                        'startedAt' => null
//                    ]));
//                }
//                break;
//            case self::WEEKLY:
//                break;
//            case self::MONTHLY:
//                break;
//            case self::YEARLY:
//                break;
//            default:
//                break;
//        }

        if(isset($response_json['dailySleepDTO'])) {
            $a = [
                'day' => $day,
                'startedAt' => $response_json['dailySleepDTO']['sleepStartTimestampGMT'],
                'stoppedAt' => $response_json['dailySleepDTO']['sleepEndTimestampGMT']
            ];
        } else {
            $a = $response_json;
        }

        return $this->json($a);
    }

    private function getCookiesString(): string {
        $cookies_string = "";
        try {
            $cookies_json = json_decode(file_get_contents(GarminConnectController::COOKIES_FILE_PATH), true);
            foreach($cookies_json as $cookies) {
                $cookies_string .= $cookies['name'] . "=" . $cookies['value'] . "; ";
            }
        } catch(Exception $e) {
            return "";
        }

        return $cookies_string;
    }

    #[Route('/target/signin', name: 'signin')]
    public function signin(Request $request, ActivityRepository $activityRepository, CalendarRepository $calendarRepository): Response {
        // NE FONCTIONNE PAS
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sso.garmin.com/sso/signin?service=https%253A%252F%252Fconnect.garmin.com%252Fmodern%252F&webhost=https%253A%252F%252Fconnect.garmin.com%252Fmodern%252F&source=https%253A%252F%252Fconnect.garmin.com%252Fsignin%252F&redirectAfterAccountLoginUrl=https%253A%252F%252Fconnect.garmin.com%252Fmodern%252F&redirectAfterAccountCreationUrl=https%253A%252F%252Fconnect.garmin.com%252Fmodern%252F&gauthHost=https%253A%252F%252Fsso.garmin.com%252Fsso&locale=en_US&id=gauth-widget&cssUrl=https%253A%252F%252Fconnect.garmin.com%252Fgauth-custom-v1.2-min.css&privacyStatementUrl=https%253A%252F%252Fwww.garmin.com%252Fen-US%252Fprivacy%252Fconnect%252F&clientId=GarminConnect&rememberMeShown=true&rememberMeChecked=false&createAccountShown=true&openCreateAccount=false&displayNameShown=false&consumeServiceTicket=false&initialFocus=true&embedWidget=false&socialEnabled=false&generateExtraServiceTicket=true&generateTwoExtraServiceTickets=true&generateNoServiceTicket=false&globalOptInShown=true&globalOptInChecked=false&mobile=false&connectLegalTerms=true&showTermsOfUse=false&showPrivacyPolicy=false&showConnectLegalAge=false&locationPromptShown=true&showPassword=true&useCustomHeader=false&mfaRequired=false&performMFACheck=false&rememberMyBrowserShown=true&rememberMyBrowserChecked=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=flo.berson06%40gmail.com&password=blwpgBEqUwjqtn2SdKjP&embed=false&_csrf=20C58F450010DFABA16FD671EC1AAFFE9B59E51A6D41AA21B4264ACD88112C12351B3A12734B4D8EF07EEE5E677645560A25',
            CURLOPT_HTTPHEADER => array(
                'authority: sso.garmin.com',
                'pragma: no-cache',
                'cache-control: no-cache',
                'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'upgrade-insecure-requests: 1',
                'origin: https://sso.garmin.com',
                'content-type: application/x-www-form-urlencoded',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: navigate',
                'sec-fetch-user: ?1',
                'sec-fetch-dest: iframe',
                'referer: https://sso.garmin.com/sso/signin?service=https%3A%2F%2Fconnect.garmin.com%2Fmodern%2F&webhost=https%3A%2F%2Fconnect.garmin.com%2Fmodern%2F&source=https%3A%2F%2Fconnect.garmin.com%2Fsignin%2F&redirectAfterAccountLoginUrl=https%3A%2F%2Fconnect.garmin.com%2Fmodern%2F&redirectAfterAccountCreationUrl=https%3A%2F%2Fconnect.garmin.com%2Fmodern%2F&gauthHost=https%3A%2F%2Fsso.garmin.com%2Fsso&locale=en_US&id=gauth-widget&cssUrl=https%3A%2F%2Fconnect.garmin.com%2Fgauth-custom-v1.2-min.css&privacyStatementUrl=https%3A%2F%2Fwww.garmin.com%2Fen-US%2Fprivacy%2Fconnect%2F&clientId=GarminConnect&rememberMeShown=true&rememberMeChecked=false&createAccountShown=true&openCreateAccount=false&displayNameShown=false&consumeServiceTicket=false&initialFocus=true&embedWidget=false&socialEnabled=false&generateExtraServiceTicket=true&generateTwoExtraServiceTickets=true&generateNoServiceTicket=false&globalOptInShown=true&globalOptInChecked=false&mobile=false&connectLegalTerms=true&showTermsOfUse=false&showPrivacyPolicy=false&showConnectLegalAge=false&locationPromptShown=true&showPassword=true&useCustomHeader=false&mfaRequired=false&performMFACheck=false&rememberMyBrowserShown=true&rememberMyBrowserChecked=false',
                'accept-language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                'cookie: SESSION=02b26ae9-713e-496e-b179-1f846c80044f; notice_preferences=0:; notice_gdpr_prefs=0:; notice_poptime=1619726400000; cmapi_gtm_bl=ga-ms-ua-ta-asp-bzi-sp-awct-cts-csm-img-flc-fls-mpm-mpr-m6d-tc-tdc; cmapi_cookie_privacy=permit 1 required; GarminLocalePref=en_US; GarminUserPrefs=en-US; GarminGlobalStorage=%7B%22global%22%3A%7B%22locale%22%3A%22en-US%22%7D%7D; GarminCartCount=0; notice_behavior=expressed,eu; __VCAP_ID__=df74bd5c-162a-4108-47eb-6987; __cflb=02DiuHkH2SZrbLnjiuY1KAY9ZUtRHBhBpXLkZevuSTNPv; __cfruid=77ca01bbb1989a99f971d34c99dd72080927fbd1-1649323966; org.springframework.web.servlet.i18n.CookieLocaleResolver.LOCALE=en_US; utag_main=v_id:017fb0878ef7000c6bb679c4562005072006b06a00bd0$_sn:12$_ss:0$_st:1649325939034$ses_id:1649324014631%3Bexp-session$_pn:2%3Bexp-session; CONSENTMGR=c1:0%7Cc2:0%7Cc3:0%7Cc4:0%7Cc5:0%7Cc6:0%7Cc7:0%7Cc8:0%7Cc9:0%7Cc10:0%7Cc11:0%7Cc12:0%7Cc13:0%7Cc14:0%7Cc15:0%7Cts:1649324139058%7Cconsent:true; ADRUM=s=1649324147086&r=https%3A%2F%2Fsso.garmin.com%2Fsso%2Fsignin%3Fhash%3D1946154629',
                'sec-gpc: 1'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $this->json([
            'header' => $response
        ]);
    }
}
