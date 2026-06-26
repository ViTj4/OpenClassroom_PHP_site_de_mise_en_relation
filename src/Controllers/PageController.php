<?php

class PageController extends AbstractController
{
    public function __construct(
        private readonly BookManager $bookManager
    ) {
    }

    public function home(): void
    {
        $latestBooks = $this->bookManager->findLatest();

        $this->render('views/home/index.php', 'Tom Troc', 'home', [
            'latestBooks' => $latestBooks ?: array_slice($this->getFallbackBooks(), 0, 4),
        ]);
    }

    public function books(): void
    {
        $books = $this->bookManager->findAll();

        $this->render('views/book/index.php', 'Nos livres à l\'échange - Tom Troc', 'books', [
            'books' => $books ?: $this->getFallbackBooks(),
        ]);
    }

    public function book(): void
    {
        $uuid = $_GET['uuid'] ?? null;
        $book = is_string($uuid) ? $this->bookManager->findByUuid($uuid) : null;

        if ($book === null) {
            $book = $this->findFallbackBook($uuid) ?? $this->getFallbackBooks()[1];
        }

        $this->render('views/book/show.php', $book->getTitle() . ' - Tom Troc', 'book', [
            'book' => $book,
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->render('views/errors/404.php', 'Page introuvable - Tom Troc', '404');
    }

    /**
     * @return Book[]
     */
    private function getFallbackBooks(): array
    {
        $description = "J'ai récemment plongé dans les pages de ce livre et j'ai été enchanté par cette œuvre captivante.\n\nLes photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.\n\nChaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.";

        $books = [
            [
                'uuid' => '00000000-0000-4000-8000-000000000001',
                'title' => 'Esther',
                'author' => 'Alabaster',
                'image' => 'assets/images/Esther_Alabaster.png',
                'owner_pseudo' => 'CamilleClubLit',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000002',
                'title' => 'The Kinfolk Table',
                'author' => 'Nathan Williams',
                'image' => 'assets/images/Nathan_Williams.png',
                'owner_pseudo' => 'Alexlecture',
                'description' => "J'ai récemment plongé dans les pages de 'The Kinfolk Table' et j'ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d'une simple collection de recettes ; il célèbre l'art de partager des moments authentiques autour de la table.\n\nLes photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité.\n\nChaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers.\n\n'The Kinfolk Table' incarne parfaitement l'esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.",
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000003',
                'title' => 'Wabi Sabi',
                'author' => 'Beth Kempton',
                'image' => 'assets/images/Wabi_Sabi.png',
                'owner_pseudo' => 'Alexlecture',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000004',
                'title' => 'Milk & honey',
                'author' => 'Rupi Kaur',
                'image' => 'assets/images/Milk_and_Honey.png',
                'owner_pseudo' => 'Hugo1990_12',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000005',
                'title' => 'The Creative Act',
                'author' => 'Rick Rubin',
                'image' => 'assets/images/Esther_Alabaster.png',
                'owner_pseudo' => 'Nathalire',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000006',
                'title' => 'Design as Art',
                'author' => 'Bruno Munari',
                'image' => 'assets/images/Nathan_Williams.png',
                'owner_pseudo' => 'CamilleClubLit',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000007',
                'title' => 'Minimalist Graphics',
                'author' => 'Gestalten',
                'image' => 'assets/images/Wabi_Sabi.png',
                'owner_pseudo' => 'Alexlecture',
            ],
            [
                'uuid' => '00000000-0000-4000-8000-000000000008',
                'title' => 'Hygge',
                'author' => 'Meik Wiking',
                'image' => 'assets/images/Milk_and_Honey.png',
                'owner_pseudo' => 'Hugo1990_12',
            ],
        ];

        return array_map(
            static fn (array $book): Book => Book::fromArray([
                'description' => $description,
                'owner_uuid' => '00000000-0000-4000-8000-000000000000',
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'exchanged_at' => null,
                'owner_profile_picture' => UserManager::DEFAULT_PROFILE_PICTURE,
                ...$book,
            ]),
            $books
        );
    }

    private function findFallbackBook(?string $uuid): ?Book
    {
        if ($uuid === null) {
            return null;
        }

        foreach ($this->getFallbackBooks() as $book) {
            if ($book->getUuid() === $uuid) {
                return $book;
            }
        }

        return null;
    }
}
