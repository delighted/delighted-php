[![Build Status](https://travis-ci.org/delighted/delighted-php.svg?branch=master)](https://travis-ci.org/delighted/delighted-php)

# Delighted PHP API Client

Official PHP client for the [Delighted API](https://delighted.com/docs/api).

## Requirements

- PHP 5.5 or greater
- The [Composer](http://getcomposer.org/) package manager
- A [Delighted API](https://delighted.com/docs/api) key

## Installation

Install via [Composer](http://getcomposer.org/) by adding this to your `composer.json`:

```
{
  "require": {
    "delighted/delighted": "2.*"
  }
}
```

Then install via:

```
composer install
```

This will also install the [Guzzle](https://github.com/guzzle/guzzle) HTTP request library that the Delighted PHP API Client depends upon.

## Configuration

To get started, you need to configure the client with your secret API key. At some point in your application's initialization, before you call any other Delighted PHP API client methods, do this (replacing `YOUR_API_KEY` with your actual API key, of course):

```
Delighted\Client::setApiKey('YOUR_API_KEY');
```

**Note:** Your API key is secret, and you should treat it like a password. You can find your API key in your Delighted account, under *Settings* > *API*.

## Usage

Adding/updating people and scheduling surveys:

```php
// Add a new person, and schedule a survey immediately
$person1 = \Delighted\Person::create(['email' => 'ellie@icloud.com']);

// Add a new person, and schedule a survey after 1 minute (60 seconds)
$person2 = \Delighted\Person::create(['email' => 'richard.nguyen@aol.com', 'delay' => 60]);

// Add a new person, but do not schedule a survey
$person3 = \Delighted\Person::create(['email' => 'gvargas@gmail.com', 'send' => false]);

// Add a new person with full set of attributes, including a custom question
// product name, and schedule a survey with a 30 second delay
$props = ['customer_id' => 123, 'country' => 'USA', 'question_product_name' => 'The London Trench'];
$person4 = \Delighted\Person::create([
                                        'email' => 'alexis_burke@austinstephens.com',
                                        'name' => 'Alexis Burke',
                                        'properties' => $props,
                                        'delay' => 30
                                    ]);

// Update an existing person (identified by email), adding a name, without
// scheduling a survey
$updated_person1 = \Delighted\Person::create([
                                                'email' => 'ellie@icloud.com',
                                                'name' => 'Ellie Newman',
                                                'send' => false
                                            ]);
```

Unsubscribing people:

```php
// Unsubscribe an existing person
\Delighted\Unsubscribe::create(['person_email' => 'ellie@icloud.com'])
```

Listing unsubscribed people:

```php
// List all unsubscribed people, 20 per page, first 2 pages
$unsubscribes = \Delighted\Unsubscribe::all()
$unsubscribes_p2 = \Delighted\Unsubscribe::all(['page' => 2]);
```

Listing bounced people:

```php
// List all bounced people, 20 per page, first 2 pages
$bounces = \Delighted\Bounce::all()
$bounces_p2 = \Delighted\Bounce::all(['page' => 2]);
```

Deleting pending survey requests

```php
// Delete all pending (scheduled but unsent) survey requests for a person,
// by email.
\Delighted\SurveyRequest::deletePending(['person_email' => 'ellie@icloud.com']);
```

Adding survey responses:

```php
// Add a survey response, score only
$survey_response1 = \Delighted\SurveyResponse::create(['person' => $person1->id, 'score' => 10]);

// Add *another* survey response (for the same person), score and comment
$survey_response2 = \Delighted\SurveyResponse::create([
                                                         'person' => $person1->id,
                                                         'score' => 5,
                                                         'comment' => 'Really nice.'
                                                      ]);
```

Retrieving a survey response:

```php
// Retrieve an existing survey response
$survey_response3 = \Delighted\SurveyResponse::retrieve('123');
```

Updating survey responses:

```php
// Update a survey response score
$survey_response4 = \Delighted\SurveyResponse::retrieve('234');
$survey_response4->score = 10;
$survey_response4->save();

// Update (or add) survey response properties
$survey_response4->person_properties = ['segment' => 'Online'];
$survey_response4->save();

// Update person who recorded the survey response
$survey_response4->person = '321';
$survey_response4->save();
```

Listing survey responses:

```php
// List all survey responses, 20 per page, first 2 pages
$responses_p1 = \Delighted\SurveyResponse::all()
$responses_p2 = \Delighted\SurveyResponse::all(['page' => 2]);

// List all survey responses, 20 per page, expanding person object
$responses_p1_expand = \Delighted\SurveyResponse::all(['expand' => ['person']]);
// The person property is a \Delighted\Person object now
print $responses_p1_expand[0]->person->name;

// List all survey responses, 20 per page, for a specific trend (ID: 123)
$responses_p1_trend = \Delighted\SurveyResponse::all(['trend' => '123']);

// List all survey responses, 20 per page, in reverse chronological order
// (newest first)
$responses_p1_desc = \Delighted\SurveyResponse::all(['order' => 'desc']);

// List all survey responses, 100 per page, page 5, with a time range
$filtered_survey_responses = \Delighted\SurveyResponse::all([
                                                               'page' => 5,
                                                               'per_page' => 100, 
                                                               'since' => gmmktime(0, 0, 0, 10, 1, 2013),
                                                               'until' => gmmktime(0, 0, 0, 11, 1, 2013)
                                                            ]);
```

Retrieving metrics:

```php
// Get current metrics, 30-day simple moving average, from most recent response
$metrics = \Delighted\Metrics::retrieve()

// Get current metrics, 30-day simple moving average, from most recent response
// for a specific trend (ID: 123)
$metrics = \Delighted\Metrics::retrieve(['trend' => '123']);

// Get metrics, for given range
$metrics = \Delighted\Metrics::retrieve([
                                           'since' => gmmktime(0, 0, 0, 10, 1, 2013), 
                                           'until' => gmmktime(0, 0, 0, 11, 1, 2013)
                                        ]);
```

## Rate limits

If a request is rate limited, a `\Delighted\RequestException` exception is raised. You can rescue that exception to implement exponential backoff or retry strategies. The exception provides a `getRetryAfter()` method to tell you how many seconds you should wait before retrying. For example:

```php
try {
    $metrics = \Delighted\Metrics::retrieve();
} catch (Delighted\RequestException $e) {
    $errorCode = $e->getCode();

    if ($errorCode == 429) { // rate limited
        $retryAfterSeconds = e->getRetryAfter();
        // wait for $retryAfterSeconds before retrying
        // add your retry strategy here ...
    } else {
        // some other error
    }
}
```

## Advanced Configuration and Testing

The various Delighted resource methods use a shared client object to make the HTTP requests to the Delighted server. To change how that shared client object works, you can pass an array of options to the `\Delighted\Client::getInstance()` method (before you call any resource methods) that control its behavior.

The chief option you may want to change is `baseUrl`, which defaults to `https://api.delighted.com/v1/`. If you want to send Delighted API requests to a different URL (for example, a local mock server for testing), pass that URL as the value for the `baseURL` array key in the options passed to `\Delighted\Client::getInstance()`. For example:

```php
$myUrl = 'http://localhost/delighted-mock/';
\Delighted\Client::getInstance(['baseUrl' => $myUrl]);
```

You can also easily mock Delighted API requests and responses by following the pattern that the API client's test cases use:

- Use the `\Delighted\TestClient` class instead of `Delighted\Client`
- Create a `\GuzzleHttp\Handler\MockHandler` to mock the requests. Because the `$client` is a shared instance, you'll want to use a shared `$mock_handler`, too.
- Create `\GuzzleHttp\HandlerStack` and pass to the client.
- Make assertions about the request and response as desired.

For example:

```php
$mock_response = new \GuzzleHttp\Psr7\Response(200, [], ['nps' => 10]);
$mock_handler = new \GuzzleHttp\Handler\MockHandler([$mock_response]);
$handler_stack = \GuzzleHttp\HandlerStack::create($mock_handler);        
$client = \Delighted\TestClient::getInstance(['apiKey' => 'xyzzy', 'handler' => $handler_stack]);
$metrics = Delighted\Metrics::retrieve(['client' => $client]);

// This prints 10 -- the value comes from the mock response
print $metrics->nps;
```

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Run the tests (`php -f run-tests.php`)
4. Commit your changes (`git commit -am 'Add some feature'`)
5. Push to the branch (`git push origin my-new-feature`)
6. Create new Pull Request

## Releasing

1. Bump the version in `lib/Delighted/Version.php`.
2. Update the README and CHANGELOG as needed.
3. Tag the commit for release.
4. Push (Packagist will pick up the release from the tag).
