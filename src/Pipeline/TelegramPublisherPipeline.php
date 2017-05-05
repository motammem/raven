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
        new Telegram(getenv('TELEGRAM_API_KEY'), getenv('TELEGRAM_BOT_USERNAME'));
    }

    /**
     * @param \Raven\Content\Article\Article $payload
     *
     * @return mixed|void
     */
    public function __invoke($payload)
    {
        if ($payload->mainMedia()) {
            $id = Request::sendPhoto([
                'chat_id' => '@khabar_3anieh',
                'caption' => $payload->title ."\n".$payload->url,
            ], $payload->mainMedia()->path)->getResult()->message_id;
        }
        $data = [
            'chat_id' => '@khabar_3anieh',
            'text' => $payload->lead,
        ];
        if (isset($id)) {
            $data['reply_to_message_id'] = $id;
        }
        Request::sendMessage($data);
    }
}
