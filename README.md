# TYPO3 Graphql - Ease the process of creating GraphQL APIs in TYPO3

This extension enables you to quickly implement a graphql api TYPO3 extension.

## Webserver configuration - Apache

In order to read Authorization header (Required by JWT authorization), you have to tell apache to allow `Authorization` header.

```apacheconf
# Enbale Authorization header
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```
