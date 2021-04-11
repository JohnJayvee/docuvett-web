<?php

namespace App\Libraries\GSuite\Services\Calendar;

use App\Libraries\GSuite\GSuiteClass;
use App\Libraries\GSuite\Traits\SendsParameters;
use Google_Service_Calendar;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Calendar
{

    use SendsParameters;

    public $client;

    public $service;

    /**
     * Optional parameter for getting single and multiple emails
     *
     * @var array
     */
    protected $params = [];

    /**
     * Message constructor.
     *
     * @param GSuiteClass $client
     */
    public function __construct( GSuiteClass $client )
    {
        $this->client = $client;
        $this->service = new Google_Service_Calendar( $client );
    }

    public function all()
    {
        $calendarsResponse = $this->service->calendarList->listCalendarList();
        return $this->prepareListResponse((array)$calendarsResponse->getItems());
    }

    public function store(array $params) {
        $calendar = new \Google_Service_Calendar_Calendar();
        $calendar->setSummary($params['summary']);
        if (isset($params['description'])) {
            $calendar->setDescription($params['description']);
        }

        $this->client->setUseBatch(false);
        $createdCalendar = $this->service->calendars->insert($calendar);
        return $this->prepareSingleResponse($createdCalendar);
    }

    public function update($calendarId, array $params) {
        $this->client->setUseBatch(false);
        $calendar = $this->service->calendars->get($calendarId);
        if ($calendar) {
            if (isset($params['colorId'])) {
                $calendarListEntry = $this->service->calendarList->get($calendarId);
                $calendarListEntry->setColorId($params['colorId']);
                $this->service->calendarList->update($calendarListEntry->getId(), $calendarListEntry);
            }
            if (!isset($params['accessRole']) || $params['accessRole'] == 'owner') {
                $calendar->setSummary($params['summary']);
                if (isset($params['description'])) {
                    $calendar->setDescription($params['description']);
                }
                $updatedCalendar = $this->service->calendars->update($calendarId, $calendar);
                return $this->prepareSingleResponse($updatedCalendar);
            } else {
                return $this->prepareSingleResponse($calendar);
            }
        } else {
            throw new HttpException('404', 'Calendar not found');
        }
    }

    public function destroy($calendarId) {
        $this->client->setUseBatch(false);
        return $this->service->calendars->delete($calendarId);
    }

    public function colors()
    {
        $colors = $this->service->colors->get();
        $eColors = $colors->getEvent();
        foreach ($eColors as $id => $color) {
            $eColors[$id]->id = $id;
        }
        $cColors = $colors->getCalendar();
        foreach ($cColors as $id => $color) {
            $cColors[$id]->id = $id;
        }
        $colors = [
            'event' => array_values((array)$eColors),
            'calendar' => array_values((array)$cColors),
        ];
        return $colors;
    }

    private function prepareListResponse (array $response)
    {
        $calendars = [];
        foreach ($response as $key => $calendar) {
            $calendars[] = $this->prepareSingleResponse($calendar);
        }
        return $calendars;
    }

    private function prepareSingleResponse ($calendar)
    {
//        $internal = isset($this->labels[$calendar->getId()]) ? $this->labels[$calendar->getId()] : false;
//
//        $data = [
//            'id' => $calendar->getId(),
//            'name' => $internal ? $internal['name'] : $calendar->getName(),
//            'order' => $internal ? $internal['order'] : null,
//            'always_hidden' => $internal ? $internal['always_hidden'] : false,
//            'icon_class' => $internal ? $internal['icon_class'] : 'el-icon-caret-right',
//            'color' => $calendar->getColor(),
//            'labelListVisibility' => $calendar->getLabelListVisibility(),
//            'messageListVisibility' => $calendar->getMessageListVisibility(),
//            'messagesTotal' => $calendar->getMessagesTotal(),
//            'messagesUnread' => $calendar->getMessagesUnread(),
//            'threadsTotal' => $calendar->getThreadsTotal(),
//            'threadsUnread' => $calendar->getThreadsUnread(),
//            'type' => $calendar->getType(),
//            'parent_ids' => [],
//        ];

        return $calendar;
    }
}