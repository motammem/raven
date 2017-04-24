<?php

/*
 * This file is part of the Raven project.
 *
 * (c) Amin Alizade <motammem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Raven\Pipeline;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use League\Pipeline\StageInterface;

class TelegramPublisherPipeline implements StageInterface
{
    public function __construct()
    {
        $telegram = new Telegram('360054263:AAGnDHlLehdNk7_ooqaA7mQsbztXVKGFm1c', 'SaniehBot');
    }

    /**
     * @param \Raven\Content\Article\Article $payload
     *
     * @return mixed|void
     */
    public function __invoke($payload)
    {
        if ($payload->getImage()) {
            $content = file_get_contents($payload->getImage()->getOriginalUrl());
            touch(__DIR__.'/image.jpg');
            file_put_contents(__DIR__.'/image.jpg', $content);
            $id = Request::sendPhoto([
                'chat_id' => '@khabar_3anieh',
            ], __DIR__.'/image.jpg');
        }
        $data = [
            'chat_id' => '@khabar_3anieh',
            'text' => $payload->getBody(),
        ];
        if (isset($id)) {
            $data['reply_to_message_id'] = $id;
        }
        Request::sendMessage($data);
    }
}
