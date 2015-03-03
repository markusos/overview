#OverView 

[![Code Climate](https://codeclimate.com/github/EngageDC/overview/badges/gpa.svg)](https://codeclimate.com/github/EngageDC/overview)

OverView, a project management tool for Basecamp. OverView provides a quick and clear summary of all active tasks for each team member across all Basecamp projects.

The interface is simple and clean; it is designed to make it easy for project managers to quickly organize team members and arrange them in an order that is most beneficial to them.

![OverView in acction](/docs/demo.gif?raw=true)

OverView makes it possible to efficiently see upcoming deadlines and makes it easy to follow up on a project’s progress. Under each team member, tasks can be sorted by project name or by date. 

![OverView on desktop](/docs/screenshot_1.png?raw=true)

##Setup

Clone the repository:
`` $ git clone git@github.com:EngageDC/overview.git``

Open the project directory and run composer install:
`` $ cd overview && composer install ``

If you have Vagrant installed, you can run ``vagrant up`` to instantiate a virtuall machine to try out OverView.

To setup your own version of OverView you need to register an app at [integrate.37signals.com](https://integrate.37signals.com) and update the file ``app/config/basecamp.php`` with your client id and client secret.

```php
 return array(
    'clientId'      => 'YOUR_CLIENT_ID_HERE',
    'clientSecret'  => 'YOUR_CLIENT_SECRET_HERE',
    'userAgent'     => 'Overview (your_email_here@company.com)',
);
```

OverView does not store any information, it is just processing the to-do lists from the Basecamp API and presenting them to the user.
