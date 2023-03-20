<?php

namespace App\Services;

use App\Models\Point;
use App\Services\Api\ValamarFiskalizacija;
use DateTime;
use DOMDocument;
use DOMElement;
use Exception;
use Nevs\Config;
use Nevs\Log;
use XMLWriter;

class Fiskal
{
    public static function Fiskal(int $reservation_id, DateTime $time, string $oib, int $sequence, string $location, string $device, string $amount, string $zki, bool $late_delivery = false,Point $point): array
    {
        if (config('valamar.valamar_fiskalizacija_demo_mode')) {
            $oib = config('valamar.valamar_fiskalizacija_demo_oib');
        }else{

        }

        $certificate_data = Fiskal::GetCertificate($point->fiskal_id);

        if ($certificate_data === false) {

            $response = [
                'success' => false,
                'request' => '',
                'response' => '',
                'error' => 'invalid certificate file or password'
            ];

            ValamarFiskalizacija::write_db_log(
                $reservation_id,
                'fiskal',
                json_encode(array('payload' => array())),
                json_encode(array('payload' => $response)),
                '',
                '',
                'error'
            );

            return $response;
        }

        $ns = 'tns';
        $uri_id = uniqid();
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $writer = new XMLWriter();
        $writer->openMemory();

        $writer->setIndent(4);
        $writer->startElementNs($ns, 'PrateciDokumentiZahtjev', 'http://www.apis-it.hr/fin/2012/types/f73');
        {
            #ID Poruke
            $writer->writeAttribute('Id', $uri_id);
            #Zaglavlje
            $writer->startElementNs($ns, 'Zaglavlje', null);
            {
                $writer->writeElementNs($ns, 'IdPoruke', null, $uuid);
                $writer->writeElementNs($ns, 'DatumVrijeme', null, date('d.m.Y\TH:i:s'));
            }
            $writer->endElement();
            #Prateći Dokument
            $writer->startElementNs($ns, 'PrateciDokument', null);
            {
                $writer->writeElementNs($ns, 'Oib', null, $oib);
                $writer->writeElementNs($ns, 'DatVrijeme', null, $time->format('d.m.Y\TH:i:s'));

                $writer->startElementNs($ns, 'BrPratecegDokumenta', null);
                {
                    $writer->writeElementNs($ns, 'BrOznPD', null, $sequence);
                    $writer->writeElementNs($ns, 'OznPosPr', null, $location);
                    $writer->writeElementNs($ns, 'OznNapUr', null, $device);
                }
                $writer->endElement();

                $writer->writeElementNs($ns, 'IznosUkupno', null, $amount);

                $writer->writeElementNs($ns, 'ZastKodPD', null, $zki);
                $writer->writeElementNs($ns, 'NakDost', null, $late_delivery ? 'true' : 'false');
            }
            $writer->endElement();
        }
        $writer->endElement();

        $xml_request = $writer->outputMemory();

        $xml_request_dom_doc = new DOMDocument();
        $xml_request_dom_doc->loadXML($xml_request);

        $canonical = $xml_request_dom_doc->C14N();
        $digest_value = base64_encode(hash('sha1', $canonical, true));

        $root_element = $xml_request_dom_doc->documentElement;

        $signature_node = $root_element->appendChild(new DOMElement('Signature'));
        $signature_node->setAttribute('xmlns','http://www.w3.org/2000/09/xmldsig#');

        $signed_info_node = $signature_node->appendChild(new DOMElement('SignedInfo'));
        $signed_info_node->setAttribute('xmlns','http://www.w3.org/2000/09/xmldsig#');

        $canonicalization_method_node = $signed_info_node->appendChild(new DOMElement('CanonicalizationMethod'));
        $canonicalization_method_node->setAttribute('Algorithm','http://www.w3.org/2001/10/xml-exc-c14n#');

        $signature_method_node = $signed_info_node->appendChild(new DOMElement('SignatureMethod'));
        $signature_method_node->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');

        $reference_node = $signed_info_node->appendChild(new DOMElement('Reference'));
        $reference_node->setAttribute('URI', sprintf('#%s', $uri_id));

        $transforms_node = $reference_node->appendChild(new DOMElement('Transforms'));

        $transform1_node = $transforms_node->appendChild(new DOMElement('Transform'));
        $transform1_node->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');

        $transform2_node = $transforms_node->appendChild(new DOMElement('Transform'));
        $transform2_node->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');

        $digest_method_node = $reference_node->appendChild(new DOMElement('DigestMethod'));
        $digest_method_node->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#sha1');

        $reference_node->appendChild(new DOMElement('DigestValue', $digest_value));

        $signed_info_node = $xml_request_dom_doc->getElementsByTagName('SignedInfo')->item(0);

        $x509_issuer = $certificate_data['public_certificate_data']['issuer'];
        $x509_issuer_name = sprintf('CN=%s,O=%s,C=%s', $x509_issuer['CN'], $x509_issuer['O'], $x509_issuer['C']);
        $x509_issuer_serial = $certificate_data['public_certificate_data']['serialNumber'];
        if (!is_numeric($x509_issuer_serial)) {
            $x509_issuer_serial = Fiskal::bchexdec($certificate_data['public_certificate_data']['serialNumberHex']);
        }

        $public_certificate_pure_string = str_replace('-----BEGIN CERTIFICATE-----', '', $certificate_data['public_certificate']);
        $public_certificate_pure_string = str_replace('-----END CERTIFICATE-----', '', $public_certificate_pure_string);

        $signed_info_signature = null;

        if (!openssl_sign($signed_info_node->C14N(true), $signed_info_signature, $certificate_data['private_key_resource'], OPENSSL_ALGO_SHA1)) {
            throw new Exception('Unable to sign the request');
        }

        $signature_node = $xml_request_dom_doc->getElementsByTagName('Signature')->item(0);
        $signature_value_node = new DOMElement('SignatureValue', base64_encode($signed_info_signature));
        $signature_node->appendChild($signature_value_node);

        $key_info_node = $signature_node->appendChild(new DOMElement('KeyInfo'));

        $x509_data_node = $key_info_node->appendChild(new DOMElement('X509Data'));
        $x509_certificate_node = new DOMElement('X509Certificate', $public_certificate_pure_string);
        $x509_data_node->appendChild($x509_certificate_node);

        $x509_issuer_serial_node = $x509_data_node->appendChild(new DOMElement('X509IssuerSerial'));

        $x509_issuer_name_node = new DOMElement('X509IssuerName',$x509_issuer_name);
        $x509_issuer_serial_node->appendChild($x509_issuer_name_node);

        $x509_serial_number_node = new DOMElement('X509SerialNumber',$x509_issuer_serial);
        $x509_issuer_serial_node->appendChild($x509_serial_number_node);

        $envelope = new DOMDocument();

        $envelope->loadXML('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
            <soapenv:Body></soapenv:Body>
        </soapenv:Envelope>');

        $envelope->encoding = 'UTF-8';
        $envelope->version = '1.0';

        $xml_request_type_node = $xml_request_dom_doc->getElementsByTagName('PrateciDokumentiZahtjev')->item(0);

        $xml_request_type_node = $envelope->importNode($xml_request_type_node, true);

        $envelope->getElementsByTagName('Body')->item(0)->appendChild($xml_request_type_node);

        $payload = $envelope->saveXML();

        $ch = curl_init();

        $options = array(
            CURLOPT_URL => config('valamar.valamar_fiskalizacija_demo_mode') ? 'https://cistest.apis-it.hr:8449/FiskalizacijaServiceTest' : 'https://cis.porezna-uprava.hr:8449/FiskalizacijaService',
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false
        );

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if ($response) {

            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $dom_response = new DOMDocument();
            $dom_response->loadXML($response);


            if ($code === 200) {
                $Jir = $dom_response->getElementsByTagName('Jir')->item(0);
                if ($Jir) {

                    $return =  [
                        'success' => true,
                        'request' => $payload,
                        'response' => $response,
                        'jir' => $Jir->nodeValue,
                        'error' => ''
                    ];

                    ValamarFiskalizacija::write_db_log(
                        $reservation_id,
                        'fiskal',
                        json_encode(array('payload' => $payload)),
                        json_encode(array('payload' => $dom_response->saveXML())),
                        $zki,
                        $return['jir'],
                        'success'
                    );

                    return $return;
                }
            } else {
                $error_code = $dom_response->getElementsByTagName('SifraGreske')->item(0);
                $error_message = $dom_response->getElementsByTagName('PorukaGreske')->item(0);

                if ($error_code && $error_message) {
                    return [
                        'success' => false,
                        'request' => $payload,
                        'response' => $response,
                        'error' => $error_code->nodeValue . ' ' . $error_message->nodeValue
                    ];
                } else {
                    return [
                        'success' => false,
                        'request' => $payload,
                        'response' => $response,
                        'error' => 'HTTP response code '.$code.' not suited for further actions.'
                    ];
                }
            }
        } else {
            $response =  [
                'success' => false,
                'request' => $payload,
                'response' => $response,
                'error' => curl_error($ch)
            ];

            ValamarFiskalizacija::write_db_log(
                $reservation_id,
                'fiskal',
                json_encode(array('payload' => $payload)),
                json_encode(array('payload' => $response)),
                '',
                '',
                'error'
            );

            return $response;
        }

        curl_close($ch);

        $return =  [
            'success' => false,
            'request' => $payload,
            'response' => $response,
            'error' => 'unknown'
        ];

        ValamarFiskalizacija::write_db_log(
            $reservation_id,
            'fiskal',
            json_encode(array('payload' => $payload)),
            json_encode(array('payload' => $response)),
            '',
            '',
            'error'
        );

        return $response;
    }



    private static function bchexdec($hex)
    {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }
        return $dec;
    }

    public static function GenerateZKI(DateTime $time, string $oib, int $sequence, string $location, string $device, float $amount,Point $point): string|null
    {

        $zki_unsigned = $oib
            . $time->format('d.m.Y H:i:s')
            . $sequence
            . $location
            . $device
            . $amount;

        $zki_signature = null;
        $certificate_data = Fiskal::GetCertificate($point->fiskal_id);

        if ($certificate_data === false) {
            return null;
        }

        openssl_sign($zki_unsigned, $zki_signature,$certificate_data['private_key_resource'], OPENSSL_ALGO_SHA1);

        return md5($zki_signature);
    }

    private static function GetCertificate($fiskal_id): array|bool
    {
        $certificate_password = '';
        $certificate_file = '';

        if (config('valamar.valamar_fiskalizacija_demo_mode')) {
            $certificate_password = config('valamar.valamar_fiskalizacija_demo_cert_pw');
            $certificate_file = storage_path().'/certificates/'. config('valamar.valamar_fiskalizacija_demo_cert');

        } else {

            switch ($fiskal_id){
                #Valamar
                case 1:
                    $certificate_password = config('valamar.valamar_fiskalizacija_valamar_pw');
                    $certificate_file = storage_path().'/certificates/'. config('valamar.valamar_fiskalizacija_valamar_cert');
                    break;
                #Imperial Rab
                case 2:
                    $certificate_password = config('valamar.valamar_fiskalizacija_imperial_pw');
                    $certificate_file = storage_path().'/certificates/'. config('valamar.valamar_fiskalizacija_imperial_cert');
            }
        }

        if ($certificate_file != '') {


            $certificate = null;
            openssl_pkcs12_read(file_get_contents($certificate_file), $certificate, $certificate_password);


            if ($certificate == null) {
                return false;
            }

            $public_certificate = $certificate['cert'];
            $private_key = $certificate['pkey'];

            return [
                'private_key_resource' => openssl_pkey_get_private($private_key, $certificate_password),
                'public_certificate' => $public_certificate,
                'public_certificate_data' => openssl_x509_parse($public_certificate)
            ];
        }

        return false;
    }
}
