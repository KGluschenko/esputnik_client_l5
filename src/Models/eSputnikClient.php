<? namespace Vis\eSputnikClient;

use Illuminate\Support\Facades\Config;
use Vis\CurlClient\CurlClient;

/**
 * API coverage 3/43 methods
 *@link Methods names are identical to WADL file https://esputnik.com/api/application.wadl
 */

class eSputnikClient
{
    private $apiUrl;
    private $curl;

    public function __construct()
    {
        $this->apiUrl = "https://esputnik.com/api/v1/";

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
            $segment = str_replace("{id}", $replacement, $segment);
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
     * @param array $array
     * @return array
     */
    private function prepareParams(array $array)
    {
        $params = [];

        foreach($array as $key => $value){
            $params[] = [
                'key'   =>   $key,
                'value' =>   $value ?: ""
            ];
        }

        return $params;
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/version-GET
     * @return mixed
     */
    public function getVersion()
    {
        $url = $this->getFullUrl("version");

        return $this->useCurl('GET', $url);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/{id}/send-POST
     * @param $template
     * @param array $recipients
     * @param boolean $email
     * @param $params
     * @return mixed
     */
    public function sendPreparedMessage($template, array $recipients = [], $params = [], $email = true )
    {
        $template = $this->getConfigValue('prepared_message_templates')[$template] ?: 0;

        if(!$template  || !count($recipients)){
            return false;
        }

        $url = $this->getFullUrl("message/{id}/send", $template);

        $preparedParams = [
            'email'      => $email,
            'recipients' => $recipients,
            'params'     => $this->prepareParams($params)
        ];

        return $this->useCurl('POST', $url, [], $preparedParams);
    }

    /**
     * @link https://esputnik.com/api/methods.html#/v1/message/email/status-GET
     * @param $id
     * @return mixed
     */

    public function getInstantEmailStatus($id)
    {
        $url = $this->getFullUrl("message/email/status");
        $preparedParams = ['ids' => $id];

        return $this->useCurl('GET', $url, $preparedParams);
    }

}


