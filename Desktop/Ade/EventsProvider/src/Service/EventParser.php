<?php
namespace App\Service;

class EventParser
{
    public function parseResources(array $resources): array
    {
        $allEvents = [];

        foreach ($resources as $resource) {
            $base64Content = $resource->getContent();
            if (!$base64Content) {
                continue;
            }
            $decodedContent = base64_decode($base64Content);
            if (!$decodedContent) {
                continue;
            }
            $lines = explode("\n", $decodedContent);
            $events = [];
            $currentEvent = null;
            $lastKey = null;

            foreach ($lines as $line) {
                $line = rtrim($line);

                if (strpos($line, 'BEGIN:VEVENT') === 0) {
                    $currentEvent = [];
                    $lastKey = null;
                } elseif (strpos($line, 'END:VEVENT') === 0) {
                    if ($currentEvent) {
                        $events[] = [
                            "title" => $currentEvent['SUMMARY'] ?? '',
                            "start" => isset($currentEvent['DTSTART']) ? date(DATE_ATOM, strtotime($currentEvent['DTSTART'])) : null,
                            "end" => isset($currentEvent['DTEND']) ? date(DATE_ATOM, strtotime($currentEvent['DTEND'])) : null,
                            "location" => $currentEvent['LOCATION'] ?? '',
                            "description" => $currentEvent['DESCRIPTION'] ?? '',
                            "uid" => $currentEvent['UID'] ?? ''
                        ];
                        $currentEvent = null;
                    }
                } elseif ($currentEvent !== null) {
                    if ($lastKey && (strpos($line, ' ') === 0 || strpos($line, "\t") === 0)) {
                        $currentEvent[$lastKey] .= " " . ltrim($line);
                    } else {
                        $parts = explode(':', $line, 2);
                        if (count($parts) === 2) {
                            [$key, $value] = array_map('trim', $parts);
                            $currentEvent[$key] = $value;
                            $lastKey = $key;
                        }
                    }
                }
            }

            $allEvents = array_merge($allEvents, $events);
        }

        return $allEvents;
    }
}
