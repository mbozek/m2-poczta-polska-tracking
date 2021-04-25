<?php

namespace PocztaPolska\Tracking\Model;

class Tracking implements TrackingInterface
{
    const WSDL = 'https://tt.poczta-polska.pl/Sledzenie/services/Sledzenie?wsdl';

    const WSS_NS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    const USERNAME = 'sledzeniepp';

    const PASSWORD = 'PPSA';

    private $soapClient;

    public function __construct()
    {
        $this->initSoap();
    }

    public function getTrackByPackageId(string $packageId): \stdClass
    {
        return $this->soapClient->sprawdzPrzesylke([
            'numer' => $packageId
        ]);
    }

    private function initSoap()
    {
        $auth = new \stdClass();
        $auth->Username = new \SoapVar(self::USERNAME, XSD_STRING, NULL, self::WSS_NS, NULL, self::WSS_NS);
        $auth->Password = new \SoapVar(self::PASSWORD, XSD_STRING, NULL, self::WSS_NS, NULL, self::WSS_NS);

        $username_token = new \stdClass();
        $username_token->UsernameToken = new \SoapVar($auth, SOAP_ENC_OBJECT, NULL, self::WSS_NS, 'UsernameToken', self::WSS_NS);

        $security_sv = new \SoapVar(
            new \SoapVar($username_token, SOAP_ENC_OBJECT, NULL, self::WSS_NS, 'UsernameToken', self::WSS_NS),
            SOAP_ENC_OBJECT, NULL, self::WSS_NS, 'Security', self::WSS_NS);
        $header = new \SoapHeader(self::WSS_NS, 'Security', $security_sv, true);

        $this->soapClient = new \SoapClient(self::WSDL, []);
        $this->soapClient->__setSoapHeaders($header);
    }
}
