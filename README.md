# μFRAME #

Micro PHP-framework for SPA web and console applications

* contains no router: `URL paths` are routed to controllers and actions directly by `kebab-case` to `camelCase` or `PascalCase` convertion
* supports `GZIP`, `ZLIB`, and `BZIP2` formats for compression of ingress (to be used **only with trusted clients**) and egress data
* contains Telegram class (with non-blocking `Telegram::sendNonBlocking` method) to enable basic monitoring and notification functionality quickly

### How do I get set up? ###

* Clone this repository
* Make `public/index.php` an entrypoint in your web-server config
* You've got yourself a working SPA site!