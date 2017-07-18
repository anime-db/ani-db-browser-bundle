UPGRADE FROM 1.x to 2.0
=======================

Browser
-------

* The `Browser::get()` return content, not a `DomCrawler`.

   Before:

   ```php
   $crawler = $this->get('anime_db.ani_db.browser')->get('anime', ['aid' => 1]);
   ```

   After:

   ```php
   use Symfony\Component\DomCrawler\Crawler;

   $content = $this->get('anime_db.ani_db.browser')->get('anime', ['aid' => 1]);
   $crawler = new Crawler($content);
   ```

* The `Browser::get()` method now is not cache response.
