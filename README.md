# Slooce Transport plugin for Mautic 2.14

Plugin to provide Slooce transport to Mautic.

## Requirements

1. Mautic 2.14.0
2. PHP 7+
3. https://github.com/mautic-inc/plugin-mautic-integrations


## How to install

### Preparations

In Mautic, create a contact custom field to hold the Slooce keyword associated with Slooce user.

### Installation (do not use composer at this time)

1. Download https://github.com/mautic-inc/plugin-slooce/archive/master.zip
2. Extract it to plugins/MauticSlooceTransportBundle
3. Delete `app/cache/prod`
3. Run `php app/console mautic:plugins:install`
4. Go to Plugins in Mautic's admin menu (/s/plugins)
5. Click on Slooce, publish, and configure it with the requested information including selecting the custom field created above
6. Go to Mautic's Configuration (/s/config/edit), click on the Text Message Settings, then choose Slooce as the default transport.

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


This package is distributed under the [MIT license](https://opensource.org/licenses/MIT)