# Chronos

[![Build Status](https://travis-ci.org/maherio/chronos.svg?branch=master)](https://travis-ci.org/maherio/chronos)

Scheduler API for managing work shifts. This is a coding exercise from When I Work.

----

# Instructions

To run this project, follow these steps:

- Grab a copy of the repository and navigate to it
```bash
git clone https://github.com/maherio/chronos.git
cd chronos
```
- Locally install the project dependencies
```bash
composer install
```
- Run a shell instance of the project
```bash
chmod 755 ./bin/serve
./bin/serve
```
- Go to your browser and enter `localhost:8000` into the URL bar

# Implementation Notes

- I chose to try out Equip. I'd never used any of the 3 suggested frameworks before so I figured I might as well try
out the When I Work in-house one.
- I feel like I need to clarify how I interpreted the prompt, specifically these parts:
 - The prompt says to build a RESTful API, but that it is not intended to be just a CRUD application. Those are slightly
 contradictory statements (a restful api IS just a crud service for resource representations). Therefore, I interpreted
 the application and API are separate projects and separate concerns - in fact one of the biggest benefits of a RESTful
 API is that it can power multiple clients who each have their own user stories. So with that in mind, I interpreted the
 included user stories as **application features** which I am not building, but which can be powered by this **RESTful
 API** which I am building. This is why the endpoints I've created are simply resource representations for User and
 Shift rather than application-specific feature endpoints.
 - I wasn't sure what the field 'break' in the format of a float might refer to... I was expecting it to be a datetime
 to indicate when their break started (assuming all breaks are the same length of time), or an integer indicating how
 many minutes it would last (assuming the time of break doesn't matter), but I really don't know what a float is supposed
 to indicate. I decided it doesn't really matter to the API as long as the client application is aware of what it means.
- If I were taking more time to make this closer to a production API, here are some things I'd do:
 - Add authentication.
 - Add hypermedia controls rather than rely on the clients to know the full url scheme.
 - Separate DB Entity (Spot ORM) from Domain Entity (the API resources should not be the same thing as db entities).
 - Improve input validation (making sure request params are valid) as well as entity validation (things like making
 sure end_date is after start_date).
 - Add paging and smarter response functionality (like not showing past shifts by default, enable looking up hours by a specific week).
 - Correct any PUT calls.
  - A PUT request should take in the full resource, not just specific fields.
  - I also couldn't get Equip to accept request body parameters, so I left them as query parameters for now. I'd want to
  correct that to be just like POST params and pass data through the request body instead of the url.
 - Improve graceful error handling and informative responses.
 - Flesh out some more necessary functionality like creating users.
 - Add Docker to improve consistency among environments and improve process.
 - Use a more scalable db backend, like externally hosted mysql.
 - Add a caching layer to improve performance.
 - Add api documentation using something like Swagger.

# Thoughts on Equip as a framework

I'm a little confused about ADR and Equip's implementation of it (I wasn't familiar with either before this).

- I'm confused why Equip doesn't work out of the box, but instead requires the user to install a 3rd party library (zend
diactoros). I think a framework should work as shipped, and **allow** the user to make use of different implementations
rather than **require** them to install additional libraries.
- It seems like **Equip confuses Action with Domain.** The ADR docs state that the Domain replaces the Model from MVC
and is simply the functionality of domain objects (models, mappers, factories, etc.). Likewise, Action replaces most of
the Controller logic (that is, to use the request to interact with the Domain and pass the necessary Domain data to a
Responder). However, it seems that Equip relegates this functionality to the Domain rather than the Action. One example
of this is that the Domain receives the request input. The Action should encapsulate request input and appropriately
interact with the Domain, which will in turn have no idea about request input. The ADR documentation that brought about
my confusion on these differences is shown here: https://github.com/pmjones/adr#adr-revision
- Also, it seems that **Responder logic has leaked into the Domain.** Apparently in Equip, the Domain has control over
the response (both status and body), violating another part of ADR which is that the Responder has complete control over
the response. The ADR documentation detailing this is shown here: https://github.com/pmjones/adr#components

# User Stories

Note that I seeded the table with 2 managers (ids 1 and 2), 2 employees (ids 3 and 4), and 4 shifts (ids 1-4).

- As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me.
```
GET /users/4
```
- As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me.
```
GET /shifts?include_employee&starts_before=2016-06-13+16%3A00%3A50.000000&ends_after=2016-06-13+15%3A00%3A50.000000
```
- As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.
```
GET /users/3/hours
```
- As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts.
```
GET /shifts/3?include_manager
```
- As a manager, I want to schedule my employees, by creating shifts for any employee.
```
POST /shifts
{
'id' => 3,
'manager_id' => 2,
'employee_id' => 3,
'break' => 1.5,
'start_time' => 2016-07-20 00:00:00.000000,
'end_time' => 2016-07-20 08:00:00.000000,
}
```
- As a manager, I want to see the schedule, by listing shifts within a specific time period.
```
GET /shifts?starts_before=2016-06-13+16%3A00%3A50.000000&ends_after=2016-06-13+15%3A00%3A50.000000
```
- As a manager, I want to be able to change a shift, by updating the time details.
```
PUT /shifts/1?start_time=Mon%2C%2025%20Aug%202005%2015%3A35%3A00%20%2B0000
```
- As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.
```
PUT /shifts/3?employee_id=4
```
- As a manager, I want to contact an employee, by seeing employee details.
```
GET /users
```

----

# REST Scheduler API

While we will accept a framework of your choice, and a language of your choice, we _highly_ encourage using **PHP*** and one of the following frameworks: [Equip](https://github.com/equip/framework), [Radar](https://github.com/radarphp/Radar.Project), or [Proton](https://github.com/alexbilbie/proton).

If you choose to use another language, please be prepared to show fluency in multiple languages (including at least one [C-family language](https://en.wikipedia.org/wiki/List_of_C-family_programming_languages)), and knowledge of multiple design principles ([DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself), [Open/Closed](https://en.wikipedia.org/wiki/Open/closed_principle), etc) and patterns ([Factory](https://en.wikipedia.org/wiki/Factory_method_pattern), [DataMapper](https://en.wikipedia.org/wiki/Data_mapper_pattern), etc).

<sub><sup>* Our core application is written almost entirely in PHP and JS.</sup></sub>

## Requirements

The API must follow REST specification:

- POST should be used to create
- GET should be used to read
- PUT should be used to update (and optionally to create)
- DELETE should be used to delete

Additional methods can be used for expanded functionality.

The API should include the following roles:

- employee (read)
- manager (write)

The `employee` will have much more limited access than a `manager`. The specifics of what each role should be able to do is listed below in [User Stories](#user-stories).

## Data Types

All data structures use the following types:

| type   | description |
| ------ | ----------- |
| int    | a integer number |
| float  | a floating point number |
| string | a string |
| bool   | a boolean |
| id     | a unique identifier |
| fk     | a reference to another id |
| date   | an RFC 2822 formatted date string |

## Data Structures

### User

| field       | type |
| ----------- | ---- |
| id          | id |
| name        | string |
| role        | string |
| email       | string |
| phone       | string |
| created_at  | date |
| updated_at  | date |

The `role` must be either `employee` or `manager`. At least one of `phone` or
`email` must be defined.

### Shift

| field       | type |
| ----------- | ---- |
| id          | id |
| manager_id  | fk |
| employee_id | fk |
| break       | float |
| start_time  | date |
| end_time    | date |
| created_at  | date |
| updated_at  | date |

Both `start_time` and `end_time` are required. Unless defined, the `manager_id`
should always default to the manager that created the shift. Any shift without
an `employee_id` will be visible to all employees.

## User stories

**Please note that this not intended to be a CRUD application.** Only the functionality described by the user stories should be exposed via the API.

- [ ] As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me.
- [ ] As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me.
- [ ] As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.
- [ ] As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts.

- [ ] As a manager, I want to schedule my employees, by creating shifts for any employee.
- [ ] As a manager, I want to see the schedule, by listing shifts within a specific time period.
- [ ] As a manager, I want to be able to change a shift, by updating the time details.
- [ ] As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.
- [ ] As a manager, I want to contact an employee, by seeing employee details.
