<?php

class PageController extends AbstractController
{
    public function home(): void
    {
        $this->render('views/home/index.php', 'Tom Troc', 'home');
    }

    public function books(): void
    {
        $this->render('views/book/index.php', 'Nos livres à l\'échange - Tom Troc', 'books');
    }

    public function book(): void
    {
        $this->render('views/book/show.php', 'The Kinkfolk Table - Tom Troc', 'book');
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->render('views/errors/404.php', 'Page introuvable - Tom Troc', '404');
    }
}
