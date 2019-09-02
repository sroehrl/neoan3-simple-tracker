# Simple tracker

Simple PHP analytics

Cherishing privacy is an ideal that contrasts the requirement to adapt to user-behavior, understand action items for marketing, and react on once audience.
This is why simple tracker focuses on the absolute minimum.

- no data-handoff to external sources
- no identity threatening data collection

Simple tracker can answer the following questions:

- Which pages have been visited?
- How many times?
- When?
- From which link did the visit come from

Identifiers (e.g. userIds, sessions, jwt, ...) can be added to 

- identify unique visits
- create an understanding of click-paths

## What simple tracker is not
Simple tracker does not come with a UI. It is a data-collection tool storing visitor data in [Filebase](https://github.com/tmarois/Filebase) to establish a layer
for UI development according to your needs.

# Installation
`composer require neoan3-apps/simple-tracker`

# Usage

Please refer to [Filebase documentation](https://github.com/tmarois/Filebase) in order to query/process data.

## data format
```PHP
$visits = [
    [
        'date'       => (string) $date, // format Y-m-d H:i:s
        'endpoint'   => (string) $endpoint, // fully qualified
        'referrer'   => (string) $from, // if known, referrer (defaults to NULL)
        'identifier' => (string) $custom_identifier // however you want to track a user/session 
    ], ...
]

```

## track
`Neoan3\Apps\SimpleTracker::track(string $identifier)`

Using track() should be done as early as possible (In neoan3 in your frame, without a framework in your index.php),
but after potential identifiers.
An identifier can be whatever you want (e.g. user-id, PHP-session) and is optional. 
Without an identifier each visit is captured as if an individual user made a request.

## endpointData
`Neoan3\Apps\SimpleTracker::endpointData(string $endpoint)`

Example:

```PHP
$page = 'https://mysite.com/about-me/';

// return Filebase Document
$data = Neoan3\Apps\SimpleTracker::endpointData($page)->toArray();

$totalVisits = count($data['visits']);

```

## identifierData
`Neoan3\Apps\SimpleTracker::identifierData(string $identifier)`

Example:

```PHP
$user =  Neoan3\Apps\SimpleTracker::identifierData(Neoan3\Apps\Session::user_id());

// get recent activity of current user

$recent = $user->filter('visits', date('Y-m-d H:i:s',strtotime('last monday')), function($item, $date){
            return ($item['date'] == $date ? $item : false);
        })
```
