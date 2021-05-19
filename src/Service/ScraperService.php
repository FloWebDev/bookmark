<?php

namespace App\Service;

class ScraperService
{
    /**
     * Méthode permettant d'obtenir le titre d'une page à partir d'un lien externe
     *
     * @link https://www.kodingmadesimple.com/2016/06/get-webpage-title-meta-description-from-url-php.html#:~:text=PHP%20Code%20to%20Get%20Webpage%20Title%20from%20URL%3A&text=php%20%2F%2F%20function%20to%20get,title%20echo%20'Title%3A%20'%20.
     *
     * @param string $url URL externe
     * @return string|null
     */
    public function getTitleFromUrl(string $url): ?string
    {
        $content  = @file_get_contents($url);
        $title    = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $content, $match) ? $match[1] : null;

        return html_entity_decode($title, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
