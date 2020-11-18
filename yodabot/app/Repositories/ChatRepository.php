<?php

namespace App\Repositories;

use App\Models\InbentaConfigModel;
use GuzzleHttp\Client;
use Carbon\Carbon;

class ChatRepository implements RepositoryInterface {

    public $inbenta_url = "https://api-gce3.inbenta.io/prod/chatbot";

    /**
     * Pre: --
     * Post: Retorna un json amb tota la informació.
     */
    public static function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Pre: La data ha d'estar disponible per fer un createAll()
     * Post: Es crea un nou registre. Es retorna la informació creada amb el nou id.
     */
    public static function create(array $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * Pre: $id existeix a la taula sobre la qual s'estigui operant.
     * Post: s'actualitza la línia $id amb $data. Retorna l'objecte actualitzat. Si no el troba, no retorna res.
     */
    public static function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    /**
     * Pre: $id existeix a la taula sobre la qual s'estigui operant.
     * Post: s'elimina o s'invalida el registre corresponent a id==$id. No retorna res.
     */
    public static function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Pre: $id existeix. Sinó no retornarà res.
     * Post: Retorna el json amb id==$id.
     */
    public static function find($id)
    {
        // TODO: Implement find() method.
    }

    public static function open() {
        self::getAccessToken();
        return self::initConversation();

        return ["status" => "Ok"];
    }

    public static function getAccessToken() {

        $expiration = InbentaConfigRepository::findByName('INBENTA_TOKEN_EXPIRATION_DATE');

        if($expiration) {
            $now = Carbon::now();
            $e = Carbon::createFromFormat('Y-m-d H:m:i',$expiration->value);
            if ($now->greaterThan($e)) $needAccessToken = true;
            else $needAccessToken = false;
        } else $needAccessToken = true;

        if($needAccessToken) {
            $client = new Client();
            $headers = [
                'x-inbenta-key' => env('INBENTA_API_KEY'),
                'Content-Type' => 'application/json'
            ];
            $body = [
                'secret' => env('INBENTA_SECRET')
            ];
            $res = $client->request('POST', env('INBENTA_URL') . '/auth', [
                'headers' => $headers,
                'json' => $body,
            ]);

            $last = InbentaConfigRepository::findByName('INBENTA_TOKEN');
            $res = json_decode($res->getBody()->getContents());
            $accessToken = $res->accessToken;
            $expires_seconds = $res->expires_in;

            if($last) {
                InbentaConfigRepository::update(["value" => $accessToken],$last->id);
                InbentaConfigRepository::update(
                    [
                        "value" => Carbon::now()->addSeconds($expires_seconds)->format('Y-m-d H:m:i')
                    ],
                    InbentaConfigRepository::findByName('INBENTA_TOKEN_EXPIRATION_DATE')->id
                );
            } else {
                InbentaConfigRepository::create([
                    "name" => "INBENTA_TOKEN",
                    "value" => $accessToken
                ]);
                InbentaConfigRepository::create([
                    "name" => "INBENTA_TOKEN_EXPIRATION_DATE",
                    "value" => Carbon::now()->addSeconds($expires_seconds)->format('Y-m-d H:m:i')
                ]);
            }

            self::getChatBotUrl();
        }

    }

    public static function getChatBotUrl() {
        $client = new Client();
        $headers = [
            'x-inbenta-key' => env('INBENTA_API_KEY'),
            'Authorization' => 'Bearer '.InbentaConfigRepository::findByName('INBENTA_TOKEN')->value
        ];
        $res = $client->request('GET', env('INBENTA_URL') . '/apis', [
            'headers' => $headers
        ]);

        InbentaConfigRepository::create([
            "name" => "INBENTA_CHAT_URL",
            "value" => json_decode($res->getBody()->getContents())->apis->chatbot
        ]);
    }

    public static function initConversation() {
        $client = new Client();
        $headers = [
            'x-inbenta-key' => env('INBENTA_API_KEY'),
            'Authorization' => 'Bearer '.InbentaConfigRepository::findByName('INBENTA_TOKEN')->value
        ];

        $res = $client->request('POST', env('INBENTA_CHAT_URL') . '/v1/conversation' , [
            'headers' => $headers,
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public static function chat($message, $sessionToken) {
        $client = new Client();
        $headers = [
            'x-inbenta-key' => env('INBENTA_API_KEY'),
            'Authorization' => 'Bearer '.InbentaConfigRepository::findByName('INBENTA_TOKEN')->value,
            'x-inbenta-session' => 'Bearer ' . $sessionToken,
            'Content-Type' => 'application/json',
        ];
        $body = [
            'message' => $message
        ];

        $res = $client->request('POST', env('INBENTA_CHAT_URL') . '/v1/conversation/message',
            [
                'headers' => $headers,
                'json' => $body
            ]
        );

        $res = json_decode($res->getBody()->getContents());

        if(isset($res->error)) {
            self::getAccessToken();
            return self::chat($message,self::initConversation()->sessionToken);
        }

        return $res->answers[0];
    }

    public static function history($sessionToken) {
        $client = new Client();
        $headers = [
            'x-inbenta-key' => env('INBENTA_API_KEY'),
            'Authorization' => 'Bearer '.InbentaConfigRepository::findByName('INBENTA_TOKEN')->value,
            'x-inbenta-session' => 'Bearer ' . $sessionToken,
            'Content-Type' => 'application/json',
        ];
        $res = $client->request('GET', env('INBENTA_CHAT_URL') . '/v1/conversation/history', [
            'headers' => $headers
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public static function variables($sessionToken) {
        $client = new Client();
        $headers = [
            'x-inbenta-key' => env('INBENTA_API_KEY'),
            'Authorization' => 'Bearer '.InbentaConfigRepository::findByName('INBENTA_TOKEN')->value,
            'x-inbenta-session' => 'Bearer ' . $sessionToken,
            'Content-Type' => 'application/json',
        ];
        $res = $client->request('GET', env('INBENTA_CHAT_URL') . '/v1/conversation/variables', [
            'headers' => $headers
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public static function people() {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $res = $client->request('GET', 'https://swapi.dev/api/people', [
            'headers' => $headers
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public static function films() {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $res = $client->request('GET', 'https://swapi.dev/api/films', [
            'headers' => $headers
        ]);

        return json_decode($res->getBody()->getContents());
    }
}
