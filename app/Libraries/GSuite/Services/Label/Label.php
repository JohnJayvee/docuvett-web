<?php

namespace App\Libraries\GSuite\Services\Label;

use App\Libraries\GSuite\GSuiteClass;
use App\Libraries\GSuite\Traits\SendsParameters;
use Google_Service_Gmail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Label
{
    use SendsParameters;

    public $client;

    public $service;

    public $preload = false;

    /**
     * Optional parameter for getting single and multiple emails
     *
     * @var array
     */
    protected $params = [];


    const MESSAGES_PER_PAGE = 20;
    const TYPE_SYSTEM = 'system';
    const TYPE_USER = 'user';
    const REQUIRED_FIELD_HEADERS = 'headers';
    const REQUIRED_FIELD_BODY = 'body';
    const REQUIRED_FIELD_LABELS = 'labels';
    const REQUIRED_FIELD_THREAD_ID = 'threadID';
    const REQUIRED_FIELD_READ_DATE = 'read_date';
    const REQUIRED_FIELD_PAYLOAD = 'payload';
    const MIME_TYPE_TEXT_PLAIN = 'text/plain';
    const MIME_TYPE_TEXT_HTML = 'text/html';
    const CACHE_TIME = 1440;
    const LABEL_TEXT_COLOR = '#ffffff';

    // List of allowed label's background colors from API docs
    const LABEL_COLORS = [
        '#000000',
        '#434343',
        '#666666',
        '#999999',
        '#cccccc',
        '#efefef',
        '#f3f3f3',
        '#ffffff',
        '#fb4c2f',
        '#ffad47',
        '#fad165',
        '#16a766',
        '#43d692',
        '#4a86e8',
        '#a479e2',
        '#f691b3',
        '#f6c5be',
        '#ffe6c7',
        '#fef1d1',
        '#b9e4d0',
        '#c6f3de',
        '#c9daf8',
        '#e4d7f5',
        '#fcdee8',
        '#efa093',
        '#ffd6a2',
        '#fce8b3',
        '#89d3b2',
        '#a0eac9',
        '#a4c2f4',
        '#d0bcf1',
        '#fbc8d9',
        '#e66550',
        '#ffbc6b',
        '#fcda83',
        '#44b984',
        '#68dfa9',
        '#6d9eeb',
        '#b694e8',
        '#f7a7c0',
        '#cc3a21',
        '#eaa041',
        '#f2c960',
        '#149e60',
        '#3dc789',
        '#3c78d8',
        '#8e63ce',
        '#e07798',
        '#ac2b16',
        '#cf8933',
        '#d5ae49',
        '#0b804b',
        '#2a9c68',
        '#285bac',
        '#653e9b',
        '#b65775',
        '#822111',
        '#a46a21',
        '#aa8831',
        '#076239',
        '#1a764d',
        '#1c4587',
        '#41236d',
        '#83334c',
    ];

    private $labels = [
        'INBOX' => [
            'name' => 'Inbox',
            'icon_class' => 'el-icon-fa-inbox',
            'order' => 1,
            'always_hidden' => false,
            ],
        'STARRED' => [
            'name' => 'Starred',
            'icon_class' => 'el-icon-fa-star',
            'order' => 2,
            'always_hidden' => false,
        ],
        'SNOOZED' => [
            'name' => 'Snoozed',
            'icon_class' => 'el-icon-fa-clock',
            'order' => 3,
            'always_hidden' => true,
        ],
        'IMPORTANT' => [
            'name' => 'Important',
            'icon_class' => 'el-icon-fa-bookmark',
            'order' => 4,
            'always_hidden' => false,
        ],
        'UNREAD' => [
            'name' => 'Unread',
            'icon_class' => 'el-icon-fa-eye',
            'order' => 5,
            'always_hidden' => true,
        ],
        'CHAT' => [
            'name' => 'Chats',
            'icon_class' => 'el-icon-fa-comments',
            'order' => 6,
            'always_hidden' => false,
        ],
        'SENT' => [
            'name' => 'Sent',
            'icon_class' => 'el-icon-fa-paper-plane',
            'order' => 7,
            'always_hidden' => false,
        ],
        'DRAFT' => [
            'name' => 'Drafts',
            'icon_class' => 'el-icon-fa-file',
            'order' => 8,
            'always_hidden' => false,
        ],
        'ALL_MAIL' => [
            'id' => 'ALL_MAIL',
            'name' => 'All Mail',
            'full_name' => 'All Mail',
            'order' => 9,
            'always_hidden' => false,
            'icon_class' => 'el-icon-fa-envelope',
            'labelListVisibility' => 'labelShow',
            'messageListVisibility' => 'hide',
            'messagesTotal' => 0,
            'messagesUnread' => 0,
            'threadsTotal' => 0,
            'threadsUnread' => 0,
            'color' => null,
            'backgroundColor' => '',
            'type' => 'system',
            'parent_ids' => [],
            'parent' => '',
        ],
        'SPAM' => [
            'name' => 'Spam',
            'icon_class' => 'el-icon-fa-exclamation-circle',
            'order' => 10,
            'always_hidden' => false,
        ],
        'TRASH' => [
            'name' => 'Trash',
            'icon_class' => 'el-icon-fa-trash',
            'order' => 11,
            'always_hidden' => false,
        ],
        'CATEGORIES' => [
            'id' => null,
            'name' => 'Categories',
            'full_name' => 'Categories',
            'order' => 20,
            'always_hidden' => false,
            'icon_class' => '',
            'labelListVisibility' => 'labelShow',
            'messageListVisibility' => 'hide',
            'messagesTotal' => 0,
            'messagesUnread' => 0,
            'threadsTotal' => 0,
            'threadsUnread' => 0,
            'color' => null,
            'backgroundColor' => '',
            'type' => 'system',
            'parent_ids' => [],
            'parent' => '',
        ],
        'CATEGORY_PERSONAL' => [
            'name' => 'Categories/Personal',
            'icon_class' => 'el-icon-fa-user',
            'order' => 21,
            'always_hidden' => true,
        ],
        'CATEGORY_SOCIAL' => [
            'name' => 'Categories/Social',
            'icon_class' => 'el-icon-fa-users',
            'order' => 22,
            'always_hidden' => false,
        ],
        'CATEGORY_UPDATES' => [
            'name' => 'Categories/Updates',
            'icon_class' => 'el-icon-fa-info-circle',
            'order' => 23,
            'always_hidden' => false,
        ],
        'CATEGORY_FORUMS' => [
            'name' => 'Categories/Forums',
            'icon_class' => 'el-icon-fa-commenting',
            'order' => 24,
            'always_hidden' => false,
        ],
        'CATEGORY_PROMOTIONS' => [
            'name' => 'Categories/Promotions',
            'icon_class' => 'el-icon-fa-tag',
            'order' => 25,
            'always_hidden' => false,
        ],
    ];

    /**
     * Message constructor.
     *
     * @param GSuiteClass $client
     */
    public function __construct( GSuiteClass $client )
    {
        $this->client = $client;
        $this->service = new Google_Service_Gmail( $client );
    }

    public function all()
    {
        $labelsResponse = $this->service->users_labels->listUsersLabels($this->client->userId);

        $this->client->setUseBatch(true);
        $labels = $labelsResponse->getLabels();
        $batchResponses = [];

        $chunkedLabels = array_chunk($labels,50);
        foreach ($chunkedLabels as $cLabels) {
            $batch = new \Google_Http_Batch($this->client, false, null, 'batch/gmail/v1');
            foreach ($cLabels as $label) {
                $request = $this->service->users_labels->get($this->client->userId, $label->getId());
                $batch->add($request, $label->getId());
            }
            $currentResponse = $batch->execute();
            foreach ($currentResponse as $key => $item) {
                if(!$item instanceof \Google_Service_Gmail_Label) {
                    $index = array_search($key, array_keys($currentResponse));
                    $currentResponse[$key] = $cLabels[$index];
                    if($item instanceof \Google_Service_Exception) {
                        \Log::warning('Label fetch error: ' . $item->getMessage());
                    }
                }
            }
            $batchResponses[] = $currentResponse;
        }
        $batchResponse = array_merge(...$batchResponses);


        $this->client->setUseBatch(false);
        return $this->prepareListResponse($batchResponse);
    }

    public function store(array $params) {
        $label = new \Google_Service_Gmail_Label();
        $name = $params['name'];
        if (!empty($params['parent'])) {
            $name = $params['parent'].'/'.$name;
        }
        $label->setName($name);
        if (!empty($params['backgroundColor'])) {
            $color = new \Google_Service_Gmail_LabelColor();
            $color->setBackgroundColor($params['backgroundColor']);
            $color->setTextColor(self::LABEL_TEXT_COLOR);
            $label->setColor($color);
        }

        $this->client->setUseBatch(false);
        $createdLabel = $this->service->users_labels->create($this->client->userId, $label);
        return $this->prepareSingleResponse($createdLabel);
    }

    public function update($labelId, array $params) {
        $this->client->setUseBatch(false);
        $label = $this->service->users_labels->get($this->client->userId, $labelId);
        if ($label) {
            $name = $params['name'];
            if (!empty($params['parent'])) {
                $name = $params['parent'].'/'.$name;
            }
            $label->setName($name);

            if (!empty($params['backgroundColor'])) {
                $color = new \Google_Service_Gmail_LabelColor();
                $color->setBackgroundColor($params['backgroundColor']);
                $color->setTextColor(self::LABEL_TEXT_COLOR);
                $label->setColor($color);
            }
            $updatedLabel = $this->service->users_labels->update($this->client->userId, $labelId, $label);
            return $this->prepareSingleResponse($updatedLabel);
        } else {
            throw new HttpException('404', 'Label not found');
        }
    }

    public function destroy($labelId) {
        $this->client->setUseBatch(false);
        return $this->service->users_labels->delete($this->client->userId, $labelId);
    }

    private function prepareListResponse (array $response)
    {
        $labels = [];
        foreach ($response as $key => $label) {
            $labels[] = $this->prepareSingleResponse($label);
        }
        $labels = $this->sortLabels($labels);
        return $this->setNestingLevel($labels);
    }

    private function prepareSingleResponse (\Google_Service_Gmail_Label $label)
    {
        $internal = isset($this->labels[$label->getId()]) ? $this->labels[$label->getId()] : false;

        preg_match('/(^.*)\//', $label->getName(), $matches);
        $color = $label->getColor();
        $data = [
            'id' => $label->getId(),
            'full_name' => $internal ? $internal['name'] : $label->getName(),
            'name' => $internal ? $internal['name'] : $label->getName(),
            'order' => $internal ? $internal['order'] : null,
            'always_hidden' => $internal ? $internal['always_hidden'] : false,
            'icon_class' => $internal ? $internal['icon_class'] : '',
            'color' => $color,
            'backgroundColor' => $color ? $color->getBackgroundColor() : '',
            'labelListVisibility' => $label->getLabelListVisibility(),
            'messageListVisibility' => $label->getMessageListVisibility(),
            'messagesTotal' => $label->getMessagesTotal(),
            'messagesUnread' => $label->getMessagesUnread(),
            'threadsTotal' => $label->getThreadsTotal(),
            'threadsUnread' => $label->getThreadsUnread(),
            'type' => $label->getType(),
            'parent_ids' => [],
            'parent' => isset($matches[1]) ? $matches[1] : '',
        ];

        return $data;
    }

    private function sortLabels(array $labels)
    {
        $labels[] = $this->labels['ALL_MAIL'];
        $labels[] = $this->labels['CATEGORIES'];
        usort($labels, function($first, $second) {

            if (is_null($first['order']) && is_null($second['order'])) {
                return strnatcasecmp($first['full_name'], $second['full_name']);
            } elseif (is_null($first['order'])) {
                return 1;
            } elseif (is_null($second['order'])) {
                return -1;
            }
            return ($first['order'] > $second['order']) ? 1 : -1;
        });

        return $labels;
    }

    /**
     * Sets nesting level of each label in array
     * @param array $labels
     * @return array
     */
    private function setNestingLevel(array $labels)
    {
        $parents = [];

        foreach ($labels as $key => $label) {
            // removes inappropriate parents from the end of array
            for ($i = (count($parents) - 1); $i >= 0; $i--) {
                if (mb_strpos($label['full_name'], rtrim($parents[$i]['full_name'], '/') . '/') !== 0) {
                    array_pop($parents);
                }
            }

            // if we have a previous label
            if ($key) {
                // check if name of current label contains prev name + slash
                if (mb_strpos($label['full_name'], rtrim($labels[$key - 1]['full_name'], '/') . '/') === 0) {
                    $parents[] = $labels[$key - 1];

                    // parent label has children
                    $labels[$key - 1]['children'] = true;
                }
            }

            if (count($parents)) {
                foreach ($parents as $parent) {
                    $labels[$key]['parent_ids'][] = $parent['id'];
                }

//                $labels[$key]['level'] = count($parents);

                $trimmedName = mb_substr($label['full_name'], mb_strlen(rtrim($parents[count($parents) - 1]['full_name'], '/') . '/'));

                $labels[$key]['name'] = $trimmedName ? $trimmedName : '/';
            } else {
                $labels[$key]['name'] = $label['name'];
            }
        }

        return $labels;
    }
}