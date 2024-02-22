<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/figuren-theater/ft-performance">
    <img src="https://raw.githubusercontent.com/figuren-theater/logos/main/favicon.png" alt="figuren.theater Logo" width="100" height="100">
  </a>

  <h1 align="center">figuren.theater | Performance</h1>

  <p align="center">
    Fast websites are more accessible, more sustainable and are giving a better UX. This is the code which accelerates <a href="https://figuren.theater">figuren.theater</a> and its WordPress Multisite Network.
    <br /><br /><br />
    <a href="https://meta.figuren.theater/blog"><strong>Read our blog</strong></a>
    <br />
    <br />
    <a href="https://figuren.theater">See the network in action</a>
    •
    <a href="https://mein.figuren.theater">Join the network</a>
    •
    <a href="https://websites.fuer.figuren.theater">Create your own network</a>
  </p>
</div>

## About

![](https://raw.githubusercontent.com/figuren-theater/.github/main/assets/pagespeed-figuren.theater.svg)


* [x] *list closed tracking-issues or `docs` files here*
* [ ] Do you have any [ideas](https://github.com/figuren-theater/ft-performance/issues/new) ?

## Background & Motivation

...

## Install

1. Install via command line
	```sh
	composer require figuren-theater/ft-performance
	```

## Usage

### API

```php
Figuren_Theater::API\get_...()
```

### Plugins included

This package contains the following plugins.
Thoose are completely managed by code and lack of their typical UI.

* [Cache-Control](https://github.com/carstingaxion/wordpress-cache-control)
* [Cache Enabler](https://wordpress.org/plugins/cache-enabler/#developers)
* [Fast404](https://wordpress.org/plugins/fast404/#developers)
* [PWA](https://wordpress.org/plugins/pwa/#developers)
* [Quicklink for WordPress](https://wordpress.org/plugins/quicklink/#developers)
* [SQLite Object Cache](https://wordpress.org/plugins/sqlite-object-cache/#developers)
* [WP-Super-Preload](https://github.com/carstingaxion/WP-Super-Preload)
    This plugin helps to keep whole pages of your site always being cached in the fresh based on the sitemap.xml and your own settings.


## Built with & uses

  - [dependabot](/.github/dependabot.yml)
  - [figuren.theater pagespeed-insights data workflow ](https://github.com/figuren-theater/.github/actions/workflows/pagespeed-insights.yml)
  - [code-quality](https://github.com/figuren-theater/code-quality/)
     A set of status checks to ensure high and consitent code-quality for the figuren.theater platform.

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## Versioning

We use [Semantic Versioning](http://semver.org/) for versioning. For the versions
available, see the [tags on this repository](https://github.com/figuren-theater/ft-performance/tags).

## Authors

  - **Carsten Bach** - *Provided idea & code* - [figuren.theater/crew](https://figuren.theater/crew/)

See also the list of [contributors](https://github.com/figuren-theater/ft-performance/contributors)
who participated in this project.

## License

This project is licensed under the **GPL-3.0-or-later**, see the [LICENSE](/LICENSE) file for details

## Acknowledgments

  - [altis](https://github.com/search?q=org%3Ahumanmade+altis) by humanmade, as our digital role model and inspiration
  - [@roborourke](https://github.com/roborourke) for his clear & understandable [coding guidelines](https://docs.altis-dxp.com/guides/code-review/standards/)
  - [python-project-template](https://github.com/rochacbruno/python-project-template) for their nice template->repo renaming workflow
