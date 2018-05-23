# Slooce Transport plugin for Mautic 2.14

Plugin to provide Slooce transport to Mautic.

## FYI

### Maximum length

Maximum length of message is 160 characters in ISO-8859-2. Message content is encoded from UTF-8.

### Supported Characters
 * Generally speaking, only a subset of the standard ASCII character set is supported for content being
 * delivered to the user via SMS. The list of supported characters are A-Z, a-z, 0-9 and the following:
 * @$_/.,"():;-=+*&%#!?<>' plus space and newline "\n".
 * Most special characters are not supported and will cause messages to be rejected by the wireless
 * operators. In particular, accented characters and the following are NOT supported: tab [ ] ~ { } ^ | € \
 * When authoring content for delivery via SMS, it is also important to use the simple ASCII characters
 * for the apostrophe, the ellipsis, and single and double quotes:
 * use ' instead of  <`> and <’>
 * use " instead of  <“> and <”>
 * use ... instead of ...   (Note: that's three separate periods instead of the single ellipsis character)

## Compatibility

This package requires at least Mautic 2.14 in order to function. Mautic 2.13 introduced SMS transport chain and Mautic 2.14 changes method argument to Lead i.o. number.

## How to install

### Preparations

Ccreate a custom field for Mautic Lead to hold the short-code associated with given user.

### Install using composer

In the root of the project run ```composer.phar require galvani/mautic-slooce-transport-bundle:1.0```

### Install as drop-in

 * Download requested version as zip file from this github repository.
 * Clone the repository into plugins directory:
 
``` git clone https://github.com/galvani/mautic-slooce-plugin.git plugins/MauticSlooceTransportBundle```

 * Navigate to Mautic's plugin administration. 
 * Click **Install/upgrade plugins** button.
 * Enable and configure plugin.
 * You are done, Slooce can be selected as transport in Message Configuration section of Mautic's configuration.
 
 This package is ditributed under the [MIT license](https://opensource.org/licenses/MIT)
