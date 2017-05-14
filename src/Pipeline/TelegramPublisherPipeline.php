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

use League\Pipeline\StageInterface;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramPublisherPipeline implements StageInterface
{

    public function __construct()
    {
        new Telegram(
          getenv('TELEGRAM_API_KEY'), getenv('TELEGRAM_BOT_USERNAME')
        );
    }

    /**
     * @param \Raven\Content\Article\Article $article
     *
     * @return mixed|void
     */
    public function __invoke($article)
    {
        if ($article->mainMedia()) {
            $id = Request::sendPhoto(
              [
                'chat_id' => '@khabar_3anieh',
                'caption' => $article->title."\n".$article->url,
              ],
              $article->mainMedia()->path
            )->getResult()->message_id;
        }
        $data = [
          'chat_id' => '@khabar_3anieh',
          'text' => $article->lead . "\n". implode(', ' ,array_column($article->tags->toArray(),'name')),
        ];
        if (isset($id)) {
            $data['reply_to_message_id'] = $id;
        }
        Request::sendMessage($data);
    }
}
