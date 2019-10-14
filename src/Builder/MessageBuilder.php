<?php

namespace App\Builder;

use App\Entity\Message;
use App\Builder\TypeBuilder;
use Symfony\Component\HttpFoundation\Request;
use App\Builder\UserBuilder;
use App\Finder\UserFinder;
use App\Finder\ResidenceFinder;
use App\Finder\MessageFinder;

class MessageBuilder
{
    protected $userBuilder;
    protected $userFinder;
    protected $residenceFinder;
    protected $messageFinder;

    public function __construct(UserBuilder $userBuilder, UserFinder $userFinder, ResidenceFinder $residenceFinder, MessageFinder $messageFinder)
    {
        $this->userBuilder = $userBuilder;
        $this->userFinder = $userFinder;
        $this->residenceFinder = $residenceFinder;
        $this->messageFinder = $messageFinder;
    }

    /**
     * @param Message[] $message
     * @return array
     */
    public function buildList(array $messages)
    {
        $dataResponse = array();
        foreach ($messages as $message) {
            $dataResponse[] = $this->build($message);
        }
        return $dataResponse;
    }

    /**
     * @param Message $message
     * @return array
     */
    public function build($message)
    {
        return array(
            'id' => $message->getId(),
            //'subject' => $message->getSubject()?: '',
            'content' => $message->getContent(),
            'sender_id' => $message->getSender()->getID(),
            'recipient_id' => $message->getRecipient()->getID(),
            'creation_date' => $message->getCreationDate() ? $message->getCreationDate()->format('d-m-Y H:i:s') : null,
            'notification_counter' => $message->getNotificationCounter(),
            'status' => $message->getStatus() ?: 0
        );
    }

    /**
     * @return array
     */
    public function buildFreshMessage(Request $request)
    {
        $message = new Message();
        $message->setStatus(0);
        return $this->buildMessage($message, $request);
    }

    /**
     * @param Message $message
     * @return array
     */
    public function buildMessage(Message $message, Request $request)
    {
        $message->setSubject($request->get('subject') ?: $message->getSubject());
        $message->setContent($request->get('content') ?: $message->getContent());

        if ($request->get('sender_id')) {
            $sender = $this->userFinder->findUser($request->get('sender_id'));
            $message->setSender($sender);
        }
        if ($request->get('receiver_id')) {
            $receiver = $this->userFinder->findUser($request->get('receiver_id'));
            $message->setRecipient($receiver);
        }
        if ($request->get('residence_id')) {
            $residence = $this->residenceFinder->findResidence($request->get('residence_id'));
            $message->setResidence($residence);
        }
        
        return $message;
    }
}
