<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $config;
    protected $apiUrl;

    public function __construct()
    {
        $this->config = config('services.asist_sms');
        $this->apiUrl = str_replace('?WSDL', '', $this->config['wsdl_url']);
    }

    public function sendSms(string $phoneNumber, string $message): bool
    {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        if (!$formattedNumber) {
            return false;
        }

        $username = $this->config['username'];
        $password = $this->config['password'];
        $usercode = $this->config['usercode'];
        $accountId = $this->config['account_id_otp'];
        $originator = $this->config['originator'];
        
        // ===================================================================
        // ===       EKSİK 3 PARAMETRE BURAYA EKLENDİ (SON DÜZELTME)       ===
        // ===================================================================
        $innerXml = "<SendSms>
                        <Username>{$username}</Username>
                        <Password>{$password}</Password>
                        <UserCode>{$usercode}</UserCode>
                        <AccountId>{$accountId}</AccountId>
                        <Originator>{$originator}</Originator>
                        <SendDate></SendDate>
                        <ValidityPeriod>60</ValidityPeriod>
                        <MessageText>{$message}</MessageText>
                        <IsCheckBlackList>0</IsCheckBlackList>
                        <ReceiverList><Receiver>{$formattedNumber}</Receiver></ReceiverList>
                    </SendSms>";
        // ===================================================================

        $soapEnvelope = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                          <soap:Body>
                            <sendSms xmlns="https://webservice.asistiletisim.com.tr/SmsProxy">
                              <requestXml><![CDATA[' . $innerXml . ']]></requestXml>
                            </sendSms>
                          </soap:Body>
                        </soap:Envelope>';

        try {
            Log::info('SMS API Istegi Gonderiliyor', ['xml' => $soapEnvelope]);

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'https://webservice.asistiletisim.com.tr/SmsProxy/sendSms',
            ])->withBody($soapEnvelope, 'text/xml')
              ->post($this->apiUrl);

            Log::info('SMS API Cevabi Alindi', ['status' => $response->status(), 'body' => $response->body()]);

            if (!$response->successful()) {
                return false;
            }

            $responseBody = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response->body());
            $xml = new \SimpleXMLElement($responseBody);
            $errorCode = (int)$xml->soapBody->sendSmsResponse->sendSmsResult->ErrorCode;

            if ($errorCode === 0) {
                return true;
            } else {
                Log::error("SMS gonderim hatasi: API ErrorCode: {$errorCode}");
                return false;
            }

        } catch (Exception $e) {
            Log::error('SMS gonderim sirasinda Exception olustu: ' . $e->getMessage());
            return false;
        }
    }
    
    private function formatPhoneNumber(string $phoneNumber)
    {
        $cleanedNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strlen($cleanedNumber) == 10) return '90' . $cleanedNumber;
        if (strlen($cleanedNumber) == 11 && substr($cleanedNumber, 0, 1) === '0') return '90' . substr($cleanedNumber, 1);
        if (strlen($cleanedNumber) == 12 && substr($cleanedNumber, 0, 2) === '90') return $cleanedNumber;
        Log::error("Gecersiz telefon numarasi formati: {$phoneNumber}");
        return false;
    }
}