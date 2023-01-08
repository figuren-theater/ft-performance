# figuren.theater | Performance

Fast websites are more accessible, more sustainable and are giving a better UX. This is the code which accelerates [figuren.theater](https://figuren.theater) and its WordPress Multisite Network.

---

## Plugins included

This package contains the following plugins. 
Thoose are completely managed by code and lack of their typical UI.

* [Cache-Control](https://wordpress.org/plugins/cache-control/#developers)
* [Cache Enabler](https://wordpress.org/plugins/cache-enabler/#developers)
* [Native Gettext](https://wordpress.org/plugins/native-gettext/#developers)
* [PWA](https://wordpress.org/plugins/pwa/#developers)
* [Quicklink for WordPress](https://wordpress.org/plugins/quicklink/#developers)
* [SQLite Object Cache](https://wordpress.org/plugins/sqlite-object-cache/#developers)
* [WP-Super-Preload](https://github.com/carstingaxion/WP-Super-Preload)
    This plugin helps to keep whole pages of your site always being cached in the fresh based on the sitemap.xml and your own settings.

## What does this package do in addition?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

- ...

Add the following to your composer project:

```
"extra": {
    "dropin-paths": {
        "htdocs/wp-content/": [
            "package:figuren-theater/ft-performance:templates/htdocs/wp-content/object-cache.php"
        ]
    }
}
```
