# PHP Sitemap Generator

A simple PHP-based sitemap generator that crawls a given website and produces a sitemap.xml file.  
Supports both **CLI** and **Web-based** usage.

## Requirements
- PHP 7.4 or higher
- cURL extension enabled
- Web server (e.g., Apache, Nginx) if using web mode

## Installation
Clone the repository and place the file on your server or local machine:
git clone https://github.com/your-repo/sitemap-generator.git
cd sitemap-generator

## Usage

### CLI
php sitemap.php
# Follow the prompts to enter your start URL and crawl depth.
# The script will generate a sitemap.xml file in the same directory.

### Web
# Place sitemap.php in your web server root (e.g., /var/www/html).
# Open it in a browser: http://your-server/sitemap.php
# Use the form to set your start URL and crawl depth.
# The sitemap.xml file will be created in the same directory.

## Features
- Crawl any website starting from a given URL
- Set maximum crawl depth
- Exclude external links
- Generates a standard `sitemap.xml` file
- Works via CLI and Web interface

## License
MIT License
