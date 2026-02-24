# WP Starter Plugin
A clean starter plugin you can copy/paste and reuse. It includes the usual stuff you end up needing: safe bootstrapping, constants, autoload-ish include pattern, activation/deactivation hooks, an admin page, assets enqueue, and a couple of helper functions.

[![version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://semver.org)
[![PHP Version](https://img.shields.io/badge/PHP-%3E=7.2-green.svg)](https://www.php.net/)

## Plugin folder structure

```bash
my-custom-plugin/
  my-custom-plugin.php
  uninstall.php
  readme.txt   (optional)
  includes/
    class-plugin.php
    functions.php
  admin/
    class-admin.php
  assets/
    admin.css
    admin.js
```

## Usage

1. Copy the `my-custom-plugin` folder to your WordPress `wp-content/plugins` directory.
2. Activate the plugin from the WordPress admin dashboard.
3. Customize the plugin by editing the files in the `includes` and `admin` directories as needed.

### Reuse Checklist (What You Rename Every Time)

  * Class prefix MCP_ → your plugin prefix (e.g., ACME_)
  * Text domain my-custom-plugin
  * Option keys mcp_*
  * Menu slug mcp-settings
  * Constants MCP_*


## Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

### [1.0.0] - 2026-02-24

#### Added
- Initial release with basic plugin structure and functionality.


## Copyright and License

This plugin is released under the [GPL2 or later](https://www.gnu.org/licenses/gpl-2.0.html) license.

## Versioning

We use [Semantic Versioning (SemVer)](https://semver.org/) for versioning.