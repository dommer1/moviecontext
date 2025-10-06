<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleMetadata;
use App\Models\Author;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing authors and tags
        $authors = Author::all();
        $tags = Tag::all();

        // Create sample articles
        $articles = [
            [
                'title' => 'Oppenheimer: Christopher Nolan vytvoril majstrovské dielo o jadrovej ére',
                'slug' => 'oppenheimer-christopher-nolan-vytvoril-majstrovské-dielo-o-jadrovej-ére',
                'content' => "Christopher Nolan sa opäť prekonáva vo svojom najnovšom filme Oppenheimer. Tento biografický thriller o 'otcovi atómovej bomby' prináša fascinujúci pohľad na vedeckú revolúciu 20. storočia a jej dôsledky.\n\nFilm zachytáva život Roberta Oppenheimera, vedca ktorý viedol Manhattan Project - tajný projekt na vývoj atómovej bomby počas druhej svetovej vojny. Nolanova réžia je ako vždy brilantná, kombinujúc komplexné vedecké témy s napínavým príbehom.\n\nHlavnú rolu stvárňuje Cillian Murphy, ktorý podáva excelentný výkon ako geniálny ale rozporuplný vedec. Film obsahuje aj hviezdne obsadenie vrátane Emily Blunt, Matt Damon a Robert Downey Jr.\n\nOppenheimer je vizuálne ohromujúci film s nádhernými zábermi a pozoruhodnou prácou s časom. Nolan používa svoje charakteristické techniky, ako paralelné strihy a komplexné flashbacky, aby rozvinul príbeh.\n\nTento film nie je len o vede - je to hlboký pohľad na morálne dilemy, ktoré prináša vedecký pokrok. Oppenheimerova postava je fascinujúca štúdia človeka, ktorý vytvoril niečo, čo zmenilo svet navždy.\n\nFilm získal veľkú pozornosť na tohtoročných Oscaroch a je jasným kandidátom na mnohé ceny. Nolan dokazuje, že stále patrí medzi najtalentovanejších režisérov súčasnosti.",
                'excerpt' => 'Christopher Nolan sa opäť prekonáva vo svojom najnovšom filme Oppenheimer. Tento biografický thriller o "otcovi atómovej bomby" prináša fascinujúci pohľad na vedeckú revolúciu 20. storočia.',
                'seo_title' => 'Oppenheimer: Christopher Nolan vytvoril majstrovské dielo o jadrovej ére',
                'seo_description' => 'Recenzia filmu Oppenheimer od Christophera Nolana. Biografický thriller o Robertovi Oppenheimerovi a vývoji atómovej bomby.',
                'published_at' => now()->subDays(2),
                'view_count' => 2450,
                'author_id' => $authors->where('specialization', 'professional_critic')->first()->id ?? 1,
                'tags' => ['dráma', 'biografia', 'Christopher Nolan', 'atómová bomba'],
                'metadata' => [
                    'imdb_id' => 'tt15398776',
                    'csfd_url' => 'https://www.csfd.cz/film/1189434-oppenheimer/',
                    'czech_release_date' => '2023-07-20',
                    'streaming_platforms' => ['Netflix', 'HBO Max'],
                    'fun_fact' => 'Film Oppenheimer sa nakrúcal na 70 rôznych miestach po celom svete, vrátane skutočných lokalít spojených s Manhattan Project.',
                ],
            ],
            [
                'title' => 'Barbie movie prekonáva očakávania s rekordnými tržbami',
                'slug' => 'barbie-movie-prekonava-ocakavania-s-rekordnymi-trzbami',
                'content' => "Film Barbie od režisérky Grety Gerwigovej pokračuje v ohromujúcom úspechu a stáva sa jedným z najväčších blockbustrov tohto roka.\n\nTento ružový blockbuster, ktorý kombinuje fantasy, komédiu a sociálny komentár, zarobil už viac ako 1,4 miliardy dolárov po celom svete. V Českej republike sa stal najnavštevovanejším filmom leta.\n\nMargot Robbie a Ryan Gosling stvárňujú ikonické postavy Barbie a Kena s brilantným humorom a charizmou. Film obsahuje hviezdne obsadenie vrátane Will Ferrell, Issa Rae a Michaela Cerry.\n\nGreta Gerwigová vytvorila niečo výnimočné - film, ktorý je zároveň zábavný a hlboký. Barbie nie je len o hračkách, ale o identite, feminizme a spoločenských očakávaniach.\n\nHudba od skladateľa Marka Ronsona a Lizzo je ďalším plusom filmu. Soundtrack sa stal hitom a piesne ako 'Angel' a 'Dance The Night' bodujú v hitparádach.\n\nBarbie dokazuje, že mainstreamové filmy môžu byť zároveň komerčne úspešné a intelektuálne podnetné. Je to dokonalý letný blockbuster.",
                'excerpt' => 'Film Barbie od režisérky Grety Gerwigovej pokračuje v ohromujúcom úspechu a stáva sa jedným z najväčších blockbustrov tohto roka.',
                'seo_title' => 'Barbie movie prekonáva očakávania s rekordnými tržbami',
                'seo_description' => 'Barbie sa stáva najväčším blockbustrom roka. Recenzia filmu s Margot Robbie a Ryanom Goslingom.',
                'published_at' => now()->subDays(5),
                'view_count' => 1890,
                'author_id' => $authors->where('specialization', 'enthusiastic_blogger')->first()->id ?? 2,
                'tags' => ['komédia', 'fantasy', 'Greta Gerwig', 'blockbuster'],
                'metadata' => [
                    'imdb_id' => 'tt1517268',
                    'csfd_url' => 'https://www.csfd.cz/film/1189435-barbie/',
                    'czech_release_date' => '2023-07-20',
                    'streaming_platforms' => ['HBO Max', 'Disney+'],
                    'fun_fact' => 'Film Barbie obsahuje viac ako 200 ružových odtieňov a bol nakrúcaný v Warner Bros. štúdiách, kde boli vytvorené celé Barbie Land.',
                ],
            ],
            [
                'title' => 'Killers of the Flower Moon: Scorseseho majstrovské dielo o osmanských vraždách',
                'slug' => 'killers-of-the-flower-moon-scorseseho-majstrovské-dielo-o-osmanských-vraždach',
                'content' => "Martin Scorsese sa vracia s ďalším filmovým skvostom - Killers of the Flower Moon. Tento epický western o osmanských vraždách na začiatku 20. storočia je ďalším majstrovským dielom legendárneho režiséra.\n\nFilm rozpráva skutočný príbeh o sérii vražd členov kmena Osage v Oklahome v 20. rokoch minulého storočia. Ernest Burkhart (Leonardo DiCaprio) sa zamiluje do Mollie Kyleovej (Lily Gladstone) a zapletie sa do brutálnych vražd.\n\nScorseseho réžia je ako vždy precízna a vizuálne ohromujúca. Film má tri a pol hodiny, ale každá minúta je naplnená obsahom. Obsadenie je hviezdne - okrem DiCapria a Gladstoneovej tu nájdeme aj Jesseho Plemonsa, Tany Singer a Johna Lithgowa.\n\nKillers of the Flower Moon je film o chamtivosti, rasizme a korupcii. Scorsese skúma temné stránky americkej histórie s typickou hĺbkou a empatiou.\n\nFilm je adaptáciou knihy Davida Granna a prináša autentický pohľad na tragédiu Osage. Scorsese spolupracoval s Robertom De Nirom na scenári, čo je záruka kvality.\n\nToto je film, ktorý si zaslúži Oscary. DiCaprio, Gladstoneová a celý filmový tím podávajú výkony na úrovni majstrov. Killers of the Flower Moon je dôkazom, že Scorsese stále patrí medzi najväčších režisérov.",
                'excerpt' => 'Martin Scorsese sa vracia s ďalším filmovým skvostom - Killers of the Flower Moon. Tento epický western o osmanských vraždách je ďalším majstrovským dielom.',
                'seo_title' => 'Killers of the Flower Moon: Scorseseho majstrovské dielo o osmanských vraždách',
                'seo_description' => 'Recenzia filmu Killers of the Flower Moon od Martina Scorseseho. Epický western o vraždách v kmene Osage.',
                'published_at' => now()->subDays(1),
                'view_count' => 3200,
                'author_id' => $authors->where('specialization', 'professional_critic')->first()->id ?? 1,
                'tags' => ['western', 'dráma', 'Martin Scorsese', 'historický film'],
                'metadata' => [
                    'imdb_id' => 'tt5537002',
                    'csfd_url' => 'https://www.csfd.cz/film/1189436-killers-of-the-flower-moon/',
                    'czech_release_date' => '2023-10-20',
                    'streaming_platforms' => ['Apple TV+', 'Disney+'],
                    'fun_fact' => 'Film Killers of the Flower Moon sa nakrúcal v Oklahome na pôvodných miestach, kde sa odohrávali skutočné udalosti.',
                ],
            ],
            [
                'title' => 'Najlepšie horory na Netflix: Čo stojí za pozretie?',
                'slug' => 'najlepsie-horory-na-netflix-co-stoji-za-pozretie',
                'content' => "Netflix má v posledných mesiacoch skutočne silnú kolekciu hororov. Pozrel som si všetky novinky a vybral som tie najlepšie kusy, ktoré stojí za to vidieť.\n\nZačneme s The Haunting of Hill House - klasika, ktorá stále desí. Každá epizóda je majstrovsky napísaná a zrežírovaná. Mike Flanagan dokazuje, že vie robiť horory s dušou.\n\nPotom tu máme Midnight Mass - ďalší Flanagan hit. Vampírsky horor s náboženskou tematikou. Hamish Linklater ako kazateľ je geniálny. Film skúma vieru, morálku a spoločnosť.\n\nNemôžem zabudnúť na The Witcher - aj keď nie je čisto horor, má veľa temných prvkov. Henry Cavill ako Geralt je perfektne obsadený. Netflix konečne našiel správny spôsob, ako adaptovať Sapkowského knihy.\n\nSpomedzi novších prírastkov by som odporučil Archive 81 - found footage horor o démonickej entite. Je to strašidelné a zároveň inteligentné. Nechýba tu ani humor a napätie.\n\nAk hľadáte niečo tradičnejšie, pozrite si Evil Dead (1981) - klasika Sama Raimiho. Je to krvavé, zábavné a stále desivé. Dokazuje, že horory nepotrebujú CGI, aby boli strašidelné.\n\nNetflix má skutočne bohatú kolekciu hororov pre každý vkus - od psychologických po slashere. Ak ste fanúšikom žánru, určite si nájdete niečo vhodné.",
                'excerpt' => 'Netflix má v posledných mesiacoch skutočne silnú kolekciu hororov. Pozrel som si všetky novinky a vybral som tie najlepšie kusy.',
                'seo_title' => 'Najlepšie horory na Netflix: Čo stojí za pozretie?',
                'seo_description' => 'Sprievodca najlepšími horormi na Netflix. Od klasík ako The Haunting of Hill House po novinky ako Archive 81.',
                'published_at' => now()->subDays(3),
                'view_count' => 4200,
                'author_id' => $authors->where('specialization', 'skeptical_expert')->first()->id ?? 3,
                'tags' => ['horor', 'Netflix', 'streaming', 'recenzie'],
                'metadata' => [
                    'streaming_platforms' => ['Netflix'],
                    'fun_fact' => 'Netflix produkuje viac hororov ako ktorékoľvek iné štúdio. V roku 2023 vyšlo viac ako 20 pôvodných hororov na platforme.',
                ],
            ],
            [
                'title' => 'Avatar 3: Prvé obrázky z natáčania naznačujú veľké veci',
                'slug' => 'avatar-3-prve-obrazky-z-natacania-naznacuju-velke-veci',
                'content' => "James Cameron konečne odhalil prvé obrázky z natáčania Avatar 3! A vyzerajú fantasticky. Pandora sa vracia vo veľkom štýle.\n\nPrvé fotografie ukazujú postavy v nových Na'vi oblekoch s realistickejšími pohybmi. Technológia motion capture sa výrazne zlepšila od prvého Avatar.\n\nCameron sľubuje, že tretí diel bude ešte väčší a lepší ako predchádzajúce. Pandora bude rozšírenejšie a príbeh sa bude zaoberať globálnejšími témami.\n\nObsadenie zostáva rovnaké - Sam Worthington, Zoe Saldana, Sigourney Weaver a Stephen Lang sa vracajú. Pridajú sa nové hviezdy ako Michelle Yeoh.\n\nNatáčanie prebieha v Novom Zélande pod Cameronovým dohľadom. Režisér používa novú technológiu, ktorá umožňuje lepšie integrovanie CGI s živými hercami.\n\nAvatar 3 by mal vyjsť v roku 2025, čo znamená, že máme ešte dva roky čakať. Ale prvé obrázky naznačujú, že to bude stáť za to.\n\nCameronova vizuálna revolúcia pokračuje. Avatar 3 bude ďalším míľnikom v dejinách filmu.",
                'excerpt' => 'James Cameron konečne odhalil prvé obrázky z natáčania Avatar 3! A vyzerajú fantasticky. Pandora sa vracia vo veľkom štýle.',
                'seo_title' => 'Avatar 3: Prvé obrázky z natáčania naznačujú veľké veci',
                'seo_description' => 'Prvé obrázky z natáčania Avatar 3 od Jamesa Camerona. Pandora sa vracia s novými technológiami a príbehmi.',
                'published_at' => now()->subHours(6),
                'view_count' => 5800,
                'author_id' => $authors->where('specialization', 'enthusiastic_blogger')->first()->id ?? 2,
                'tags' => ['sci-fi', 'Avatar', 'James Cameron', 'novinky'],
                'metadata' => [
                    'imdb_id' => 'tt1757678',
                    'csfd_url' => 'https://www.csfd.cz/film/1189437-avatar-3/',
                    'czech_release_date' => '2025-12-19',
                    'streaming_platforms' => ['Disney+'],
                    'fun_fact' => 'James Cameron osobne strávil 8 rokov vývojom technológií pre Avatar. Filmová séria stála už viac ako 1 miliardu dolárov.',
                ],
            ],
            [
                'title' => 'Najlepšie sci-fi filmy na Disney+: Výber pre fanúšikov',
                'slug' => 'najlepsie-sci-fi-filmy-na-disney-vyber-pre-fanusikov',
                'content' => "Disney+ má skutočne bohatú kolekciu sci-fi filmov. Od klasík po moderné hity, každý si nájde niečo pre seba.\n\nZačneme s Tron: Legacy (2010) - vizuálne ohromujúci film s Garrettom Hedlundom. Jeff Bridges ako Kevin Flynn je legenda. Film má skvelé akčné scény a nádhernú vizuálnu stránku.\n\nPotom tu máme Wall-E (2008) - Pixar klasika o robotovi a poslednom človeku na Zemi. Je to krásny príbeh o láske, ekológii a spotrebe. Animácia je dokonalá.\n\nNemôžem zabudnúť na Arrival (2016) s Amy Adams a Jeremy Renner. Denis Villeneuve vytvoril inteligentný sci-fi film o mimozemšťanoch. Je to viac o filozofii ako o akcií.\n\nAvengers: Endgame (2019) patrí sem kvôli časovým paradoxom a multivesmíru. Marvel konečne uzavrel nekonečnú ságu. Robert Downey Jr. ako Tony Stark je nezabudnuteľný.\n\nStar Wars: The Force Awakens (2015) prináša novú generáciu do galaxie ďaleko, ďaleko. Rey, Finn a Poe začínajú novú kapitolu. J.J. Abrams zachoval ducha originálu.\n\nAndor (séria) je ďalší skvost. Diego Luna ako Cassian Andor je perfektne obsadený. Séria skúma vzburu proti Impériu detailnejšie ako predchádzajúce diely.\n\nDisney+ má sci-fi pre každý vkus - od rodinných príbehov po temné dystopie. Ak ste fanúšikom žánru, určite sa nudit nebudete.",
                'excerpt' => 'Disney+ má skutočne bohatú kolekciu sci-fi filmov. Od klasík po moderné hity, každý si nájde niečo pre seba.',
                'seo_title' => 'Najlepšie sci-fi filmy na Disney+: Výber pre fanúšikov',
                'seo_description' => 'Sprievodca najlepšími sci-fi filmy na Disney+. Od Tron: Legacy po Star Wars a Marvel filmy.',
                'published_at' => now()->subDays(7),
                'view_count' => 2900,
                'author_id' => $authors->where('specialization', 'skeptical_expert')->first()->id ?? 3,
                'tags' => ['sci-fi', 'Disney+', 'streaming', 'sprievodca'],
                'metadata' => [
                    'streaming_platforms' => ['Disney+'],
                    'fun_fact' => 'Disney vlastní viac ako 30 sci-fi filmov a sérií. Ich kolekcia zahŕňa všetko od Star Wars po Marvel Cinematic Universe.',
                ],
            ],
        ];

        foreach ($articles as $articleData) {
            $tagsData = $articleData['tags'] ?? [];
            $metadataData = $articleData['metadata'] ?? [];
            unset($articleData['tags'], $articleData['metadata']);

            $article = Article::create($articleData);

            // Attach tags
            if (! empty($tagsData)) {
                foreach ($tagsData as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['slug' => \Illuminate\Support\Str::slug($tagName), 'type' => 'genre']
                    );
                    $article->tags()->attach($tag);
                }
            }

            // Create metadata
            if (! empty($metadataData)) {
                ArticleMetadata::create(array_merge($metadataData, ['article_id' => $article->id]));
            }
        }
    }
}
