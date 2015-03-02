# Delighted PHP API Client

Official PHP client for the [Delighted API](https://delighted.com/docs/api).

## Requirements

- PHP 5.3.3 or greater
- The [Composer](http://getcomposer.org/) package manager
- A [Delighted API](https://delighted.com/docs/api) key

## Installation

Install via [Composer](http://getcomposer.org/) by adding this to your `composer.json`:

```
{
  "require": {
    "delighted/delighted-php": "1.*"
  }
}
```

Then install via:

```
composer install
```

This will also install the [Guzzle](https://github.com/guzzle/guzzle3) HTTP request library that the Delighted PHP API Client depends upon.

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
$person1 = \Delighted\Person::create(array('email' => "foo+test1@delighted.com"));

// Add a new person, and schedule a survey after 1 minute (60 seconds)
$person2 = \Delighted\Person::create(array('email' => "foo+test2@delighted.com",
  'delay' => 60));

// Add a new person, but do not schedule a survey
$person3 = \Delighted\Person::create(array('email' => "foo+test3@delighted.com",
  'send' => false));

// Add a new person with full set of attributes, including a custom question
// product name, and schedule a survey with a 30 second delay
$person4 = \Delighted\Person::create(array('email' => "foo+test4@delighted.com",
  'name' => "Joe Bloggs", 'properties' => array('customer_id' => 123, 'country' => "USA",
  'question_product_name' => "Apple Genius Bar" ), 'delay' => 30));

// Update an existing person (identified by email), adding a name, without
// scheduling a survey
$updated_person1 = \Delighted\Person::create(array('email' => "foo+test1@delighted.com",
  'name' => "James Scott", 'send' => false));
```

Unsubscribing people:

```php
// Unsubscribe an existing person
\Delighted\Unsubscribe::create(array('person_email' => "foo+test1@delighted.com"))
```

Deleting pending survey requests

```php
// Delete all pending (scheduled but unsent) survey requests for a person, by email.
\Delighted\SurveyRequest::delete_pending(array('person_email' => "foo+test1@delighted.com"));
```

Adding survey responses:

```php
// Add a survey response, score only
$survey_response1 = \Delighted\SurveyResponse::create(array('person' => $person1->id,
  'score' => 10));

// Add *another* survey response (for the same person), score and comment
$survey_response2 = \Delighted\SurveyResponse::create(array('person' => $person1->id,
  'score => 5, 'comment' => "Really nice."));
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
$survey_response4->person_properties = array('segment' => "Online" );
$survey_response4->save();

// Update person who recorded the survey response
$survey_response4->person = '321';
$survey_response4->save();
```

Listing survey responses:

```php
// List all survey responses, 20 per page, first 2 pages
$survey_responses_page1 = \Delighted\SurveyResponse::all()
$survey_responses_page2 = \Delighted\SurveyResponse::all(array('page' => 2));

// List all survey responses, 20 per page, expanding person object
$survey_responses_page1_expanded = \Delighted\SurveyResponse::all(array('expand' => array('person')));
// The person property is a \Delighted\Person object now
print $survey_responses_page1_expanded[0]->person->name;

// List all survey responses, 20 per page, for a specific trend (ID: 123)
$survey_responses_page1_trend = \Delighted\SurveyResponse::all(array('trend' => "123"));

// List all survey responses, 20 per page, in reverse chronological order (newest first)
$survey_responses_page1_desc = \Delighted\SurveyResponse::all(array('order' => 'desc'));

// List all survey responses, 100 per page, page 5, with a time range
$filtered_survey_responses = \Delighted\SurveyResponse::all(array('page' => 5,
  'per_page' => 100, 'since' => gmmktime(0,0,0,10,1,2013),
  'until' => gmmktime(0,0,0,11,1,2013)));
```

Retrieving metrics:

```php
// Get current metrics, 30-day simple moving average, from most recent response
$metrics = \Delighted\Metrics::retrieve()

// Get current metrics, 30-day simple moving average, from most recent response,
// for a specific trend (ID: 123)
$metrics = \Delighted\Metrics::retrieve(array('trend' => "123"));

// Get metrics, for given range
$metrics = \Delighted\Metrics::retrieve(array('since' => gmmktime(0,0,0,10,1,2013),
  'until' => gmmktime(0,0,0,11,1,2013)));
```
