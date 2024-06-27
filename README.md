# GravityForms <-> Fishbowl CRM Integration

Custom plugin that enables standard WordPress GravityForms plugin to integrate with Fishbowl/Delightable Restaurant CRM platform

- Upon successful form submission, lead data will route to CRM platform via their custom back-end API endpoint.
- Includes a built-in status monitor, to ensure that leads are successfully posting to CRM
- Includes a built-in retry function, which attempts to re-send leads to CRM in the event they initially fail due to a timeout error
- Includes custom wp-admin settings panel, to allow non-technical users to tweak configuration options
