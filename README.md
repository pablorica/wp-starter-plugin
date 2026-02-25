# WP Starter Plugin
A clean starter plugin you can copy/paste and reuse. It includes the usual stuff you end up needing: safe bootstrapping, constants, autoload-ish include pattern, activation/deactivation hooks, an admin page, assets enqueue, and a couple of helper functions.

[![version](https://img.shields.io/badge/version-1.0.1-blue.svg)](https://semver.org)
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

---

### Why use a Singleton in a WordPress plugin?

There is a singleton in `my-custom-plugin.php`, and you might be wondering why. 
The main reason is to ensure that there is only one instance of the plugin class throughout the entire WordPress application. 
This can help prevent conflicts and ensure that all parts of the plugin are working with the same data and state.

Think of the singleton as:

>   The “main controller” of your plugin — the one and only brain coordinating everything.

#### Prevent multiple instances

Without a singleton:

```php
$plugin1 = new My_Plugin();
$plugin2 = new My_Plugin();
```

This means:

- Hooks could be registered twice
- Scripts could be enqueued twice
- Settings could be initialized twice
- You might get duplicate output

With a singleton:

```php
My_Plugin::instance();
```

You can’t accidentally create another instance because:

 - The constructor is private
 - The instance is stored statically
 - It always returns the same object

So you guarantee one central plugin controller.

#### Centralized bootstrapping

In WordPress, everything is hook-driven.

The singleton becomes your:

 - Main hook loader
 - Dependency loader
 - Plugin initializer

Example:

```php
add_action( 'plugins_loaded', array( $this, 'init' ) );
```

This ensures:

 - The plugin loads at the right time
 - Other plugins are already available
 - You don’t accidentally run logic too early

The singleton acts like your plugin’s “engine”.


#### Global access without globals

Instead of doing this:

```php
global $my_plugin;
$my_plugin->do_something();
```

You can do:

```php
My_Plugin::instance()->do_something();
```

This is:

 - Cleaner
 - Safer
 - Avoids polluting global scope
 - More modern OOP

#### Avoid side effects during includes

When WordPress loads a plugin file, it runs immediately.

If you just do:

```php
new My_Plugin();
```

You lose control over:

 - When it loads
 - Whether it loads twice
 - Whether dependencies exist yet

Singleton lets you control instantiation timing.

#### Is it required?

**No.**

For very small plugins, this is perfectly fine:

```php
class My_Plugin {
   public function __construct() {
      add_action( 'init', array( $this, 'setup' ) );
   }
}
new My_Plugin();
```

Singleton becomes useful when:

 - The plugin grows
 - You split into multiple classes
 - You need shared state
 - You want consistent structure across projects


#### When NOT to use it

Singletons are not ideal when:

 - You want dependency injection
 - You want proper unit testing
 - You're building a large-scale, modern architecture

In enterprise setups, devs often prefer:

 - Service containers
 - Bootstrappers
 - Dependency injection

But for typical WordPress plugins?

**Singleton is practical, clean, and very common.**


---

### Why use `final class` instead of just `class`?

```php
final class My_Plugin {
```

`final` means:

> This class cannot be extended (no other class can `extends` it).


#### Prevents Inheritance (On Purpose)

If someone tries:

```php
class My_Custom_Extension extends My_Plugin {}
```

They’ll get a fatal error.

That’s intentional.

Why?

Because your main plugin class is usually:

* The bootstrapper
* The orchestrator
* The lifecycle manager
* The singleton controller

You typically **don’t want other code modifying that behavior via inheritance**.

#### Protects Your Architecture

Your main plugin class is not meant to be:

* Overridden
* Subclassed
* Used as a base framework

It’s meant to:

> Load the plugin and wire everything together.

By marking it `final`, you’re saying:

“This class is complete. It is not a base class.”

#### Prevents Unexpected Behavior

Inheritance allows method overriding:

```php
class My_Extension extends My_Plugin {
    public function init() {
        // completely different logic
    }
}
```

Now your plugin behavior is unpredictable.

Using `final` ensures:

* Hooks are added only once
* Lifecycle methods are not overridden
* Core behavior stays stable

#### It Matches the Singleton Pattern

Most singletons are declared `final` because:

* Singletons are meant to have exactly one instance
* Inheritance can break that guarantee
* Extending a singleton can create subtle bugs

In fact, a “proper” strict singleton usually looks like:

```php
final class My_Plugin {

    private static $instance = null;

    private function __construct() {}

    private function __clone() {}

    private function __wakeup() {}

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

Notice how it locks everything down.

#### Performance? (Minor)

There’s a tiny performance benefit because PHP doesn’t need to check for inheritance.

But honestly — that’s not why we use it.

Architecture clarity is the real reason.

#### When NOT to use `final`

Do NOT use `final` when:

* You are building a framework
* You expect developers to extend your class
* You are designing abstract base classes
* You want customization via inheritance

Example:

```php
abstract class Base_Controller {}
```

That’s meant to be extended — so not `final`.

#### WordPress-Specific Reality

Most modern, well-structured WP plugins:

* Use `final` on the main plugin class
* Use `final` on internal utility classes
* Avoid inheritance as extension mechanism
* Prefer hooks, filters, and composition instead

Because in WordPress:

> Extensibility should happen via hooks, not class inheritance.

#### Simple Rule of Thumb

Use `final` when:

* The class is not meant to be extended
* It controls plugin lifecycle
* It implements singleton
* It’s internal architecture

Use normal `class` when:

* It’s a base class
* It’s meant for extension
* It’s part of a public API


---

### What is the purpose of `uninstall.php`?

>  `uninstall.php` is automatically executed by WordPress — but only when the plugin is deleted, not when it is deactivated.


#### When Is `uninstall.php` Called?

It runs **only when the user clicks:**

**Plugins → Delete**

Not when:

* ❌ Deactivating the plugin
* ❌ Updating the plugin
* ❌ Activating the plugin

Only when the plugin is permanently deleted.

#### How WordPress Knows to Call It

When you delete a plugin, WordPress checks:

1. Does the plugin have an `uninstall.php` file?
2. If yes → it loads that file directly.
3. If not → it checks for a `register_uninstall_hook()`.

So this works automatically:

```bash
my-plugin/
  my-plugin.php
  uninstall.php   ← WordPress detects this
```

You do NOT need to include it manually.

#### What Happens Internally

When deleting a plugin, WordPress does something like:

```php
define( 'WP_UNINSTALL_PLUGIN', true );
include 'uninstall.php';
```

That’s why in `uninstall.php` we always add:

```php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}
```

That prevents someone from directly accessing:

```bash
https://yoursite.com/wp-content/plugins/my-plugin/uninstall.php
```

this is a security best practice 

#### Difference: Deactivation vs Uninstall

##### Deactivation

Triggered by:

```php
register_deactivation_hook()
```

Use this for:

* Flushing rewrite rules
* Clearing scheduled cron jobs
* Temporary cleanup

But the plugin still exists.

##### Uninstall (Delete)

Triggered by:

* `uninstall.php`
  OR
* `register_uninstall_hook()`

Use this for:

* Deleting options
* Deleting custom tables
* Removing CPT data (if appropriate)
* Cleaning transients
* Full cleanup

This is permanent removal.

#### Alternative: `register_uninstall_hook()`

Instead of `uninstall.php`, you can do:

```php
register_uninstall_hook( __FILE__, 'my_plugin_uninstall' );

function my_plugin_uninstall() {
    delete_option( 'my_option' );
}
```

##### Why many devs prefer `uninstall.php`

Because:

* It loads fewer things
* It runs in isolation
* Cleaner separation
* Safer for large plugins

WordPress does NOT load the full plugin when running `uninstall.php`.

That’s important.

#### Important Detail

When `uninstall.php` runs:

* Your main plugin class is NOT loaded
* Constants are NOT defined
* Singleton does NOT exist

So you must write standalone cleanup code.

For example, this will fail:

```php
My_Plugin::instance()->cleanup();
```

Instead, write plain procedural cleanup code.

#### Best Practice for Cleanup

Example:

```php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete options
delete_option( 'mcp_installed_at' );

// If multisite
delete_site_option( 'mcp_installed_at' );
```

If you create custom tables, delete them here.

#### Why WordPress Designed It This Way

Because uninstall is:

* A destructive action
* Should not rely on plugin runtime logic
* Should not depend on classes
* Should be safe and isolated

So WordPress loads a minimal environment.

#### Simple Mental Model

| Action     | What runs                    |
| ---------- | ---------------------------- |
| Activate   | `register_activation_hook`   |
| Deactivate | `register_deactivation_hook` |
| Delete     | `uninstall.php`              |


---


## Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

### [1.0.1] - 2026-02-24

#### Updated

- Updated the README with more detailed explanations about the singleton pattern and its use in WordPress plugins.


### [1.0.0] - 2026-02-24

#### Added
- Initial release with basic plugin structure and functionality.


## Copyright and License

This plugin is released under the [GPL2 or later](https://www.gnu.org/licenses/gpl-2.0.html) license.

## Versioning

We use [Semantic Versioning (SemVer)](https://semver.org/) for versioning.