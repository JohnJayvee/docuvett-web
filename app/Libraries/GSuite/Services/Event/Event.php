<?php

namespace App\Libraries\GSuite\Services\Event;

use App\Libraries\GSuite\GSuiteClass;
use App\Libraries\GSuite\Traits\SendsParameters;
use Google_Service_Calendar;
use Exception;
use Twilio\Exceptions\HttpException;

class Event
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

    public function all(array $calendarIds, array $params)
    {
        $this->client->setUseBatch(true);
        $batch = new \Google_Http_Batch($this->client, false, null, 'batch/calendar/v3');

        foreach ($calendarIds as $calendarId) {
            $request = $this->service->events->listEvents($calendarId, $params);
            $batch->add($request, $calendarId);
        }
        $batchResponse = $batch->execute();

        $this->client->setUseBatch(false);
        return $this->prepareListResponse($batchResponse);
    }

    public function get($calendarId, $eventId) {
        $event = $this->service->events->get($calendarId, $eventId);
        return $this->prepareSingleResponse($event, $calendarId);
    }

    public function store(array $params) {
        $event = new \Google_Service_Calendar_Event();
//        $event->setSummary($params['summary']);
//        if (isset($params['description'])) {
//            $event->setDescription($params['description']);
//        }

        $this->client->setUseBatch(false);
        $createdEvent = $this->service->events->insert($params['calendarId'], $event);
        return $this->prepareSingleResponse($createdEvent, $params['calendarId']);
    }

    public function update($eventId, array $params) {
        $this->client->setUseBatch(false);
        $event = $this->service->events->get($params['calendarId'], $eventId);
        if ($event) {
//            $event->setSummary($params['summary']);
//            if (isset($params['description'])) {
//                $event->setDescription($params['description']);
//            }
            $updatedEvent = $this->service->events->update($params['calendarId'], $eventId, $event);
            return $this->prepareSingleResponse($updatedEvent, $params['calendarId']);
        } else {
            throw new HttpException('404', 'Event not found');
        }
    }

    public function destroy($calendarId, $eventId) {
        $this->client->setUseBatch(false);
        return $this->service->events->delete($calendarId, $eventId);
    }

    private function prepareListResponse(array $response)
    {
        $events = [];
        foreach ($response as $key => $calendar) {
            if ($calendar instanceof \Google_Service_Calendar_Events) {
                foreach ($calendar->getItems() as $event) {
                    $id = preg_replace('/^response-/', '', $key);
                    $events[] = $this->prepareSingleResponse($event, $id);
                }
            }
        }
        return $events;
    }

    private function prepareSingleResponse(\Google_Service_Calendar_Event $event, $calendarId)
    {
        $start = $event->getStart()->date ? $event->getStart()->date : $event->getStart()->dateTime;
        $end = $event->getEnd()->date ? $event->getEnd()->date : $event->getEnd()->dateTime;

        $allDay = (bool)$event->getStart()->date;

        $result = [
            'id' => $event->getId(),
            'title' => $event->getSummary(),
            'location' => $event->getLocation() ? $event->getLocation() : '',
            'description' => $event->getDescription() ? $event->getDescription() : '',
            'allDay' => $allDay,
            'start' => $start,
            'startTime' => $allDay ? '' : date('H:i', strtotime($start)),
            'end' => $end,
            'endTime' => $allDay ? '' : date('H:i', strtotime($end)),
            'colorId' => $event->getColorId() ? $event->getColorId() : '',
            'calendarId' => $calendarId,
            'originalCalendarId' => $calendarId,
            'recurringEventId' => $event->getRecurringEventId(),
            'recurrence' => $event->getRecurrence(),
        ];

        return (object)$result;
    }
}