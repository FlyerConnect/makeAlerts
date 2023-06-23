<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client as TwilioClient;
use Exception;

class MakeTest extends Controller
{
    function getAllScenariosFunction()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', env('MAKE_URL') . '/scenarios', [
                'headers' => [
                    'Authorization' => env('MAKE_API'),
                ],
                'query' => [
                    'teamId' => env('MAKE_TEAM')
                ]
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException $ex) {
            if ($ex->hasResponse()) {
                return $ex->getResponse()->getBody()->getContents();
            } else {
                return $ex->getMessage();
            }
        }
    }
    function getAllHooksFunction()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', env('MAKE_URL') . '/hooks', [
                'headers' => [
                    'Authorization' => env('MAKE_API'),
                ],
                'query' => [
                    'teamId' => env('MAKE_TEAM'),

                ]
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException $ex) {
            if ($ex->hasResponse()) {
                return $ex->getResponse()->getBody()->getContents();
            } else {
                return $ex->getMessage();
            }
        }
    }
    public function getAllScenarios()
    {
        $allScenarios = $this->getAllScenariosFunction();
        echo $allScenarios;
    }


    public function pingScenarios()
    {
        $receiversEmail = ['info@reachwellapp.com','support@reachwellapp.com','benjaminzavala74@gmail.com'];
        $receiversSms = ['720-336-9663','+529614482428'];
        $allScenarios = $this->getAllScenariosFunction();
        $jsonString = json_decode($allScenarios);
        $responses = [];
        foreach ($jsonString->scenarios as $key => $scenario) {
            $id = $jsonString->scenarios[$key]->id;
            $name = $jsonString->scenarios[$key]->name;
            $isLinked = $jsonString->scenarios[$key]->islinked;
            if (!$isLinked) {
                foreach ($receiversEmail as $email) {
                    try {
                        $this->sendMakeEmail($name, $id, 'false', $email);
                        $responses[$id.'-email-'.$email] = "Mail sent successfully for scenario ID: {$id} to {$email}";
                    } catch (Exception $e) {
                        echo "Error sending mail for scenario ID: {$id}. Error: {$e->getMessage()}" . PHP_EOL;
                    }
                }
                foreach ($receiversSms as $smsnumber) {
                    try {
                        $sms = $this->sendMakeSMS($name,'false',$smsnumber);
                        $responses[$id.'-sms-'.$smsnumber] = $sms;
                    } catch (Exception $e) {
                        echo "Error sending mail for scenario ID: {$id}. Error: {$e->getMessage()}" . PHP_EOL;
                    }
                }
                
                
            }
        }
        return response()->json($responses);
    }

    public function sendMakeEmail($scenarioname, $id, $linkstatus, $receiveremail)
    {
        $mail = "Hello, this message is to let you know that Make Scenario {$scenarioname} with the id {$id} has run into troubles and its linked status is: {$linkstatus} and it is disconnected. Please review within Make platform any issues that may have happened.";

        Mail::raw($mail, function (Message $message) use ($receiveremail, $scenarioname) {
            $message->to($receiveremail);
            $message->subject('Make Scenario Status ' . $scenarioname);
        });
    }
    public function sendMakeSMS($scenarioname,$linkstatus,$smsto)
    {   
        $body = "Scenario {$scenarioname} linked status is: {$linkstatus} and it is disconnected. Please review within Make platform";
        $twilioSid = env('TWILIO_SID');
        $twilioAuthToken = env('TWILIO_AUTH_TOKEN');

        $twilioFromNumber = env('TWILIO_PHONE_NUMBER');

        $ch = curl_init();

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json";
        $data = http_build_query([
            'Body' => $body,
            'From' => $twilioFromNumber,
            'To' => $smsto,
        ]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERPWD, $twilioSid . ':' . $twilioAuthToken);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }
}
