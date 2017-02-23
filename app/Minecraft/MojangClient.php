<?php
namespace App\Minecraft;

use GuzzleHttp;
use App\Minecraft\MojangAccount;

/**
 * Class MojangClient
 * @package MCAuth\HttpClient
 */
class MojangClient {

    /**
     * User Agent used for requests
     */
    const USER_AGENT = 'Minepic 2.0 (minepic.org)';

    /**
     * HTTP Client for requests
     *
     * @var GuzzleHttp\Client
     */
    private $HttpClient;

    /**
     * Method for Guzzle
     *
     * @var string
     */
    private $method = 'GET';

    /**
     * URL for Guzzle
     *
     * @var string
     */
    private $url = '';

    /**
     * Data array for Guzzle
     *
     * @var array
     */
    private $data = [];

    /**
     * Last API Response
     *
     * @var
     */
    private $lastResponse;

    /**
     * Last Error
     *
     * @var string
     */
    private $lastError = "";

    /**
     * Last error code
     *
     * @var int
     */
    private $lastErrorCode = 0;

    /**
     * Last content Type
     *
     * @var string
     */
    private $lastContentType = '';

    /**
     * MojangClient constructor.
     */
    public function __construct() {
        $this->HttpClient = new GuzzleHttp\Client(
            [
                'headers' => [
                    'User-Agent' => self::USER_AGENT
                ]
            ]
        );
    }

    /**
     * Set HTTP Method
     *
     * @param string $method
     */
    private function setMethod($method = '') {
        $this->method = $method;
    }

    /**
     * Set URL
     *
     * @param string $url
     */
    private function setURL($url = '') {
        $this->url = $url;
    }

    /**
     * Set data
     *
     * @param array $data
     */
    private function setData(array $data) {
        $this->data = $data;
    }

    /**
     * Last response from API
     *
     * @return mixed
     */
    public function getLastResponse() {
        return $this->lastResponse;
    }

    /**
     * Send new request
     *
     * @return bool
     */
    private function sendRequestApi() : bool {
        try {
            $response = $this->HttpClient->request($this->method, $this->url, $this->data);
            $this->lastResponse = GuzzleHttp\json_decode($response->getBody()->getContents(), TRUE);
            $this->lastContentType = $response->getHeader('content-type')[0];
            $this->lastErrorCode = 0;
            $this->lastError = "";
            return true;
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->lastResponse = GuzzleHttp\json_decode($e->getResponse()->getBody()->getContents(), TRUE);
            $this->lastContentType = $e->getResponse()->getHeader('content-type')[0];
            $this->lastErrorCode = $e->getResponse()->getStatusCode();
            $this->lastError = "Error";
            if (isset($this->lastResponse['errorMessage'])) {
                $this->lastError .= ": ".$this->lastResponse['errorMessage'];
            }
            return false;

        }
    }

    /**
     * Generic request
     *
     * @return bool
     */
    private function sendRequest() {
        try {
            $response = $this->HttpClient->request($this->method, $this->url, $this->data);
            $this->lastResponse = $response->getBody()->getContents();
            $this->lastContentType = $response->getHeader('content-type')[0];
            $this->lastErrorCode = 0;
            $this->lastError = "";
            return true;
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->lastResponse = $e->getResponse()->getBody()->getContents();
            $this->lastContentType = $e->getResponse()->getHeader('content-type')[0];
            $this->lastErrorCode = $e->getResponse()->getStatusCode();
            $this->lastError = "Error";
            if (isset($this->lastResponse['errorMessage'])) {
                $this->lastError .= ": ".$this->lastResponse['errorMessage'];
            }
            return false;

        }
    }

    /**
     * Account info from username
     *
     * @param string $username
     * @return MojangAccount
     * @throws \Exception
     */
    public function sendUsernameInfoRequest(string $username) : MojangAccount {
        $this->setMethod('GET');
        $this->setURL(env('MINECRAFT_PROFILE_URL').$username);
        if ($this->sendRequestApi()) {
            return new MojangAccount([
                'username' => $this->lastResponse['name'],
                'uuid' => $this->lastResponse['id'],
            ]);
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }

    /**
     * Account info from UUID
     *
     * @param string $uuid
     * @return MojangAccount
     * @throws \Exception
     */
    public function getUuidInfo(string $uuid) : MojangAccount {
        $this->setMethod('GET');
        $this->setURL(env('MINECRAFT_SESSION_URL').$uuid);
        if ($this->sendRequestApi()) {
            $account = new MojangAccount();
            if ($account->loadFromApiResponse($this->lastResponse)) {
                return $account;
            }
            throw new \Exception("Cannot create data account");
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }

    /**
     * Get Skin
     *
     * @param $skin
     * @throws \Exception
     */
    public function getSkin(string $skin) {
        $this->setMethod('GET');
        $this->setURL(env('MINECRAFT_TEXTURE_URL').$skin);
        if ($this->sendRequest()) {
            if ($this->lastContentType == 'image/png') {
                return $this->lastResponse;
            }
            throw new \Exception("Invalid format: ");
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }
}