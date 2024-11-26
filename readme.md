# LLM-Friendly WP

**License**: MIT

**License URL**: https://opensource.org/license/mit

---

## Description

**LLM-Friendly WP** is a plugin designed to make documents on your WordPress site easily discoverable and usable by large language models (LLMs and AI). It achieves this by:
- Generating **Markdown** versions of your selected posts or pages.
- Serving the Markdown version of each URL when it ends with `?md=1`.
- Automatically creating an `llms.txt` file, inspired by the [llms-txt project](https://llmstxt.org/), to help LLMs index your site's Markdown content.

### Key Features:
- Converts selected categories of posts/pages into **Markdown format**.
- Allows easy access to Markdown versions via URLs with a `md=1` parameter.
- Automatically generates an `llms.txt` file containing links to Markdown documents.

---

## Installation

1. **Download** the plugin and upload the `llm-friendly-wp` folder to your WordPress `wp-content/plugins` directory.
2. **Activate** the plugin through the 'Plugins' menu in WordPress.
3. After activation, configure the plugin settings via the **'LLM-Friendly WP' menu** in the admin dashboard.

---

## Features

- **Markdown Conversion**: Converts WordPress posts or pages into Markdown using the [HTML To Markdown for PHP library](https://github.com/thephpleague/html-to-markdown).
- **Automatic Markdown Serving**: Access Markdown versions of posts by appending `?md=1` or `&md=1` to the URL.
- **llms.txt File Generation**: Automatically generates a `llms.txt` file with links to your Markdown documents for LLM discovery.
- **Easy Setup**: Simple configuration to select categories for Markdown conversion.

---

## How to Use

1. Go to the plugin's **settings page** and select the categories of posts or pages you want to convert into Markdown.
2. Visit any post or page with the `?md=1` URL parameter to view its Markdown version.
3. Generate the `llms.txt` file from the plugin settings. The file will include links to all your converted Markdown documents.

---

## Changelog

### 0.5.2
- Initial release.
- Markdown conversion for selected categories of posts/pages.
- Automatic serving of Markdown versions when the URL ends with `md=1`.
- Generates an `llms.txt` file with links to Markdown documents.

---

## Frequently Asked Questions

### How do I convert posts or pages to Markdown?
Select the categories you want to convert through the plugin settings page.

### Can I access the Markdown version of any post?
Yes! Simply add `?md=1` or `&md=1` to the end of a post's URL.

### What is the `llms.txt` file?
The `llms.txt` file contains links to your converted Markdown documents, enabling large language models to discover and index your content.

---

## Resources

- [HTML To Markdown for PHP](https://github.com/thephpleague/html-to-markdown)
- [llms-txt Project](https://llmstxt.org/)

---

## Author

This plugin is developed by **Kiboko Labs**.
