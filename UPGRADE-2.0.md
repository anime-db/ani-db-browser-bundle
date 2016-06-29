UPGRADE FROM 1.x to 2.0
=======================

### Browser

 * The `Browser::get()` method has been deprecated since 2.0 and will be removed in future.

    Before:

    ```php
    $crawler = $this->get('anime_db.ani_db.browser')->get('anime', ['aid' => 1]);
    ```

    After:

    ```php
    $crawler = $this->get('anime_db.ani_db.browser')->getCrawler('anime', ['aid' => 1]);
    ```

 * The `Browser::get()` method now is not support force request and ignore response cache. Earlier always used response
 caching. Now you can disable the cache for all requests or execute the request directly from the client.

    Before:

    ```php
    $crawler = $this->get('anime_db.ani_db.browser')->get('anime', ['aid' => 1], true);
    ```

    After:

    ```php
    use Symfony\Component\DomCrawler\Crawler;

    $content = $this->get('anime_db.ani_db.browser.client.guzzle')->get('anime', ['aid' => 1]);
    $crawler = new Crawler($content)
    ```

    For disable all cache configure bundle:

    ```yml
    # app/config/config.yml

    anime_db_ani_db_browser:
        client: 'guzzle'
    ```
