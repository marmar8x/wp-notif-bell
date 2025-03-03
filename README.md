# Wordpress Notif Bell - **WPNB**

> The plugin's tools and settings are not yet complete and are in **Beta**.

A plugin for implementing user notifications on a WordPress site! A completely flexible plugin with the possibility of arbitrary implementation and completely light and clean coding.

In terms of implementation, this plugin needs to be implemented by the programmer and you cannot use the plugin just by installing this plugin unless it has already been implemented by another theme or plugin.

#### Send

In the first step, you need to send the notification. The notification information will be fully stored in the database along with its recipients and will be available for display later.

#### Collect

In the next stage, you need to target the sent notifications based on the user or any other criteria and display them. Notifications can be managed, paginated, or filtered and shown to users either in full or partially.

#### Update & Remove

Notifications will have the ability to be updated and deleted as usual, and for this purpose, they can be selected as a target with the plugin itself.


### Ts & Sass

TypeScript and Sass are an old tragedy for this plugin. Because I started the plugin a long time ago while learning these two, and they were a playground for me. Now, considering that they don't have any specific issues, they haven't been edited.

### Main components

#### Sender

The `Sender` works individually and can send and save notifications each time by receiving complete information about the notification and its recipients.

```php
wpnb_sender() | new Sender;
```

#### Collector

The recipient of the target collects saved notifications based on the targets and received information. Like a basket, you can gather notifications. Usually, the collection is done based on user information and created or custom commands.

```php
wpnb_collector() | new Collector;
```

Receiver `modules` operate directly and will affect the receiver process:

##### - Pagination

For pagination of group notifications to display page by page when needed.

##### - Observer

To view and filter notifications based on the user with the option to filter notifications by seen or unseen.

#### Updater

As usual, the sent notifications can be edited. This is completely understandable and does not require explanation.

```php
wpnb_updater() | new Updater;
```

#### Remover

The remover can delete notifications individually or in groups, just like the updater.

```php
wpnb_remover() | new Remover;
```

> The sender, collector, updater, and deleter represent the 4 database operations which include INSERT, SELECT, UPDATE, DELETE.

> Executing updates and deletions without rules and conditions can be destructive. If an update is executed without a condition or **WHERE** clause, it will edit all notifications. Similarly, a deletion executed without rules will delete all notifications.

```php
// !! This will change all notifications title to Hey.
// UPDATE `table` SET `title` = 'Hey';
wpnb_updater()
    ->set_title('Hey')
    ->run();

// !! It deletes all notifications !!
// DELETE FROM `table`;
wpnb_remover()
    ->run();
```

### Notification itself

Each notification includes complete information such as name, content, date of sending, recipients, content format, tags, and input data.

#### Text Magic

The `TextMagic` is a simple module that can create dynamic texts and values within notification text. With magical values, you will be able to send notifications collectively instead of using the names of users and sending a notification to each user individually, and utilize the information of each one specifically.

- `[var-name]` Displaying the value of a defined variable.
- `[user:key]` Retrieving current user information from **WP_User**.
- `[user-meta:name]` Displaying information from the current user's meta list. (user meta)
- `[opt:name]` Displaying information from WordPress options.
- `[date:format|now]` Display date and time.
- `[date-i18n:format|now]` Display the received **I18n** date and time.
- `[data:key]` Receiving information from notification data.

Like:
```
Hello [user:display_name], I hope you are doing well. We have added a new discount code for this season.

Thanks
[opt:blogname]
```

#### Render & Format

The notification text will be rendered based on your selected format. Efforts will be made to ensure the output text is in **HTML**, and if other inputs are detected, they will be converted.

### Eye
This module is designed to save the status of notifications from the user's perspective. It changes for each user which notifications they have seen and can update the status of notifications based on their ID. Additionally, this module adds the capabilities of seen-all and unseen-all.

```php
// Irmmr\WpNotifBell\User\Eye
// $eye = new Eye( WP_User, Options )
$eye = wpnb_user_eye();

// set seen [notif->id]
$eye->set_seen(35, 2, 891);

// set seen all
$eye->set_seen_all();

// check
echo $eye->get_status(152) ? 'seen' : 'unseen';
```

### How to use?

To do this, you need to add some HTML and CSS to the discussion.
Call one of the main functions and display the values to the user with a little UI.

### Living environment

The environment for receiving and displaying notifications is live. This means that if a new user registers on your site and you do not impose restrictions on notifications based on date and time, previous public messages will also be displayed to the user. These aspects have advantages such as creating default welcome messages, but to prevent this phenomenon, you can use `Collector::user` to display user messages after the registration date.


