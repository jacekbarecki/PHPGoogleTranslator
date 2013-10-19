<?php

/**
 * Class GoogleTranslator
 *
 * A simple wrapper over Google Translate API methods.
 *
 * @version 0.1
 * @author Jacek Barecki
 * @link https://github.com/jacek-b/PHPGoogleTranslator
 */
class GoogleTranslator {

    /**
     * Paste your API key here.
     *
     * @var string
     */
    private static $_apiKey = '';

    /**
     * Test mode on/off.
     *
     * If it's enabled, no requests are made to the API but
     * translate() method still returns some result.
     * The other methods - detect() and languages() - return null.
     *
     * @var bool
     */
    private static $_testMode = true;

    /**
     * URL for sending requests in translate() method.
     *
     * @var string
     */
    private static $_translateUrl = 'https://www.googleapis.com/language/translate/v2';

    /**
     * URL for sending requests in languages() method.
     *
     * @var string
     */
    private static $_languagesUrl = 'https://www.googleapis.com/language/translate/v2/languages';

    /**
     * URL for sending requests in detect() method.
     *
     * @var string
     */
    private static $_detectUrl = 'https://www.googleapis.com/language/translate/v2/detect';

    /**
     * Translate the given text to another language.
     *
     * @param string $targetLanguage Target language code (e.g. 'en').
     * @param string $text Source text
     * @param strinf $sourceLanguage (optional) Source language code - if null, then Google Translate API will
     *                                try to detect it by itself.
     * @param string $prettyprint (optional, default 'true') 'true'|'false' If prettyprint=true, the results
     *                            returned by the server will be human readable (pretty printed)
     * @param string $format (optional, default 'html') 'html'|'text'  Indicate that the text to be translated is either plain-text or HTML
     * @return string The translated text
     * @throws Exception
     */
    static function translate($targetLanguage, $text, $sourceLanguage = null, $prettyprint = 'true', $format = 'html') {
        if(self::$_testMode) {
            return '(' . $sourceLanguage . ' -> ' . $targetLanguage . ') ' . $text;
        }

        $params = array(
            'q' => $text,
            'target' => $targetLanguage
        );

        if(!empty($sourceLanguage)) {
            $params['source'] = $sourceLanguage;
        }

        if(!empty($prettyprint)) {
            $params['prettyprint'] = $prettyprint;
        }

        if(!empty($format)) {
            $params['format'] = $format;
        }

        $url = self::$_translateUrl . '?key=' . rawurlencode(self::$_apiKey);
        foreach($params as $key => $value) {
            $url .= "&$key=" . rawurlencode($value);
        }

        $result = self::_sendRequest($url);
        if(empty($result['data']['translations'][0]['translatedText'])) {
            throw new Exception('The response doesn\'t contain a valid translation.');
        }

        return $result['data']['translations'][0]['translatedText'];
    }

    /**
     * Detects the language of a text string.
     *
     * @param string $text Source text
     * @return string Language code of given text
     * @throws Exception
     */
    static function detect($text) {
        if(self::$_testMode) {
            return null;
        }

        $url = self::$_detectUrl . '?key=' . rawurlencode(self::$_apiKey) . '&q=' . rawurlencode($text);
        $result = self::_sendRequest($url);

        if(empty($result['data']['detections'][0][0]['language'])) {
            throw new Exception('The server didn\'t return a valid response.');
        }

        return $result['data']['detections'][0][0]['language'];
    }

    /**
     * Returns a list of language codes supported by translate API.
     *
     * @return array
     */
    static function languages() {
        if(self::$_testMode) {
            return null;
        }

        $url = self::$_languagesUrl . '?key=' . rawurlencode(self::$_apiKey);
        return self::_sendRequest($url);
    }

    /**
     * Sends a request to given url and decodes the response.
     *
     * @param string $url Request URL
     * @return array An array containing json decoded response
     * @throws Exception When response code is different from 200, the response is empty or is not a json encoded string
     */
    private static function _sendRequest($url) {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        $responseDecoded = json_decode($response, true);

        if($responseCode != 200) {
            $msg = 'The server responded with the code ' . $responseCode . '. ';
            if(!empty($responseDecoded['error']['errors'])) {
                foreach($responseDecoded['error']['errors'] as $value) {
                    $msg .= $value['message'];
                }
            }
            throw new Exception($msg);
        }

        if(empty($responseDecoded)) {
            throw new Exception('The server response is empty or is not valid.');
        }

        return $responseDecoded;
    }
}