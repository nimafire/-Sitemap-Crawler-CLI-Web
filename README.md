# PHP Sitemap Crawler

A **standalone PHP script** to crawl a website and generate a `sitemap.xml` file. Works via both **CLI** and **Web interface**, with live log output and configurable crawl depth.
ive use this crawler for my WHMCS system and HTML site, you can run it as a cronjob.


## Features

- Works on **CLI** and **Web**  
- Configurable **start URL** and **crawl depth**  
- **Live log** output to see which URLs are being crawled  
- Avoids duplicate URLs and skips non-HTML resources (images, CSS, JS, etc.)  
- Excludes specific paths (e.g., `/blog`, `/tag`)  
- Generates standard `sitemap.xml` compatible with Google Sitemap protocol  

## Requirements

- PHP 8+  
- `allow_url_fopen` enabled  
- No external libraries required  

## Usage

### CLI

```bash
php sitemap.php

### WEB
Simply place sitemap.php in your web server root and open it in a browser.
