=== LLM-Friendly WP ===
Contributors: Kiboko Labs
Tags: markdown, large language models, llms.txt, markdown converter, content discovery
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: trunk
License: MIT
License URI: https://mit-license.org/

== Description ==

LLM-Friendly WP is a plugin designed to make the documents written on your WordPress site easily discoverable and usable by large language models (LLMs and AI). This is achieved by generating Markdown versions of your chosen posts or pages and automatically serving the Markdown version of each URL when it ends with "?md=1".

In addition, the plugin generates a `llms.txt` file, inspired by the [llms-txt project](https://llmstxt.org/), which helps LLMs easily access and index your site's Markdown content.

The plugin works by:
- Converting selected categories of posts/pages into Markdown format.
- Allowing these Markdown versions to be easily accessed via URLs with a `md=1` parameter.
- Automatically creating a `llms.txt` file, containing links to your Markdown documents.

== Installation ==

1. Download the plugin and upload the `llm-friendly-wp` folder to your WordPress `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. After activation, you can configure the plugin settings via the 'LLM-Friendly WP' menu in the admin dashboard.

== Features ==

- **Markdown Conversion**: Converts WordPress posts or pages into Markdown using the HTML To Markdown for PHP library.
- **Automatic Markdown Serving**: Access the Markdown version of your posts by appending `?md=1` or `&md=1` to the URL.
- **llms.txt File Generation**: Generates a `llms.txt` file with links to your Markdown documents for LLM discovery.
- **Easy Setup**: Simple setup process with options to choose which categories to convert into Markdown.

== How to Use ==

Once installed and activated, you can:
- Go to the plugin's settings page and select which categories of posts or pages you want to convert into Markdown.
- Whenever you visit a post or page with the `md=1` URL parameter, the plugin will serve the Markdown version.
- You can also generate the `llms.txt` file from the plugin settings, which will contain links to all your converted Markdown documents.

== Changelog ==

= 0.5.2 =
* Initial release.
* Markdown conversion for selected categories of posts/pages.
* Automatic serving of Markdown versions when the URL ends with `md=1`.
* Generates a `llms.txt` file with links to Markdown documents.

== Frequently Asked Questions ==

= How do I convert posts or pages to Markdown? =
You can select the categories of posts/pages you want to convert through the plugin settings page.

= Can I access the Markdown version of any post? =
Yes! Simply add `?md=1` or `&md=1` to the end of any post URL to access its Markdown version.

= What is the `llms.txt` file? =
The `llms.txt` file contains links to your converted Markdown documents and is used to help large language models easily discover and index your content.

== Resources ==

- [HTML To Markdown for PHP](https://github.com/thephpleague/html-to-markdown)
- [llms-txt Project](https://llmstxt.org/)

== Author ==

This plugin is developed by **Kiboko Labs**.

