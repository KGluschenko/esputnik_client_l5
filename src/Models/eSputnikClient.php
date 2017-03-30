<? namespace Vis\eSputnikClient;

use Illuminate\Support\Facades\Config;
use Vis\CurlClient\CurlClient;

/**
 * @link Api docs https://esputnik.com/api/
 * @link Api integration docs https://esputnik.com/support/integraciya-s-api
 * @link Methods names are identical to WADL https://esputnik.com/api/application.wadl
 */

class eSputnikClient
{
    private $apiUrl;
    private $curl;

    public function __construct()
    {
        $this->apiUrl = 'https://esputnik.com/api/v1/';

        $this->curl = New CurlClient();
        $this->curl->setRequestCredentials($this->getConfigValue('login'), $this->getConfigValue('password'))
                   ->setRequestHeader([
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json'
               ]);
    }

    /**
     * @param string $value
     * @return mixed
     */
    private function getConfigValue($value)
    {
        return Config::get('esputnik-client.esputnik.'.$value);
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string $segment
     * @param string $replacement
     * @return int
     */
    private function getFullUrl($segment, $replacement = null)
    {
        if ($replacement) {
            $segment = str_replace('{id}', $replacement, $segment);
        }

        return $this->getApiUrl() . $segment;
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed $urlParams (optional) array
     * @param mixed $payload (optional) array
     * @return array
     */
    private function useCurl($method, $url, $urlParams = [], $payload = [])
    {
        return $this->curl->setRequestMethod($method)->setRequestUrl($url,$urlParams)->setRequestPayload($payload, 'json')->doCurlRequest();
    }

    /**
     * should this method be a method ?
     * @param array $params
     * @return array $preparedParams
     */
    private function prepareParams(array $params)
    {
        $preparedParams = [];

        foreach($params as $key => $value){
            $preparedParams[] = [
                'key'   =>   $key,
                'value' =>   $value ?: ''
            ];
        }

        return $preparedParams;
    }

    /**
     * should this method be a method ?
     * @param array $recipients
     * @param array $params
     * @return array $preparedParams
     */
    private function prepareExtendedParams(array $recipients, array $params)
    {
        $count = count($recipients)-1;
        $preparedParams = [];

        for($i = 0; $i <= $count; $i++){
            $preparedParams[$i] =[
                'email'     => $recipients[$i],
                'jsonParam' => json_encode($params[$i])
            ] ;
        }

        return $preparedParams;
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/version-GET
     * @return mixed
     */
    public function getVersion()
    {
        $url = $this->getFullUrl('version');

        return $this->useCurl('GET', $url);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/{id}/send-POST
     * @param string $templateName
     * @param array $recipients
     * @param boolean $email
     * @param array $params
     * @return mixed
     */
    public function sendPreparedMessage($templateName, array $recipients = [], array $params = [], $email = true)
    {
        $templates = $this->getConfigValue('prepared_message_templates');
        $template  = isset($templates[$templateName]) ? $templates[$templateName] : 0;

        if(!$template  || !count($recipients)){
            return false;
        }

        $url = $this->getFullUrl('message/{id}/send', $template);

        $preparedParams = [
            'email'      => $email,
            'recipients' => $recipients,
            'params'     => $this->prepareParams($params)
        ];

        return $this->useCurl('POST', $url, [], $preparedParams);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/{id}/smartsend-POST
     * @param string $templateName
     * @param array $recipients
     * @param boolean $email
     * @param array $params
     * @return mixed
     */
    public function sendExtendedPreparedMessage($templateName, array $recipients = [], array $params = [], $email = true)
    {
        $templates = $this->getConfigValue('prepared_message_templates');
        $template  = isset($templates[$templateName]) ? $templates[$templateName] : 0;

        if(!$template  || !count($recipients) || (count($recipients) != count($params))){
            return false;
        }

        $url = $this->getFullUrl('message/{id}/smartsend', $template);

        $preparedParams = [
            'email'      => $email,
            'recipients' => $this->prepareExtendedParams($recipients,$params),
        ];

        return $this->useCurl('POST', $url, [], $preparedParams);
    }

    /**
     * @link ????
     * @param int $id
     * @return mixed
     */

    public function getInstantMessageStatus($id)
    {
        $url = $this->getFullUrl('message/status');
        $preparedParams = ['ids' => $id];

        return $this->useCurl('GET', $url, $preparedParams);
    }

    /**
     * @link https://esputnik.com/api/example_v1_message_email_POST.html
     * @param string $from
     * @param string $subject
     * @param string $htmltext
     * @param string $plaintext
     * @param array $emails
     * @return mixed
     */
    public function sendEmail($from, $subject, $htmltext, $emails = [], $plaintext = '')
    {
        if(!count($emails)){
            return false;
        }

        $url = $this->getFullUrl('message/email');

        $preparedParams = [
            'from'       => $from,
            'subject'    => $subject,
            'htmlText'   => $htmltext,
            'emails'     => $emails,
            'plaintText' => $plaintext,
        ];

        return $this->useCurl('POST', $url, [], $preparedParams);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/email/status-GET
     * @param string $id
     * @return mixed
     */

    public function getInstantEmailStatus($id)
    {
        $url = $this->getFullUrl('message/email/status');
        $preparedParams = ['ids' => $id];

        return $this->useCurl('GET', $url, $preparedParams);
    }


    /**
     * @link https://esputnik.com/api/example_v1_message_sms_POST.html
     * @param string $from
     * @param string $text
     * @param array $phones
     * @return mixed
     */
    public function sendSMS($from, $text, $phones = [])
    {
        if(!count($phones)){
            return false;
        }

        $url = $this->getFullUrl('message/sms');

        $preparedParams = [
            'from'         => $from,
            'text'         => $text,
            'phoneNumbers' => $phones,
        ];

        return $this->useCurl('POST', $url, [], $preparedParams);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/sms/status-GET
     * @param string $id
     * @return mixed
     */

    public function getInstantSmsStatus($id)
    {
        $url = $this->getFullUrl('message/sms/status');
        $preparedParams = ['ids' => $id];

        return $this->useCurl('GET', $url, $preparedParams);
    }

}


