<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - Movie Context</title>
    <meta name="description" content="{{ $title }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .single-post-content a { color: #00C6D3; text-decoration: underline; font-weight: 700; }
        .single-post-content h1 { font-size: 2.25rem; line-height: 2.5rem; font-weight: 700; margin-bottom: 2rem; }
        .single-post-content h2 { font-size: 1.5rem; line-height: 2rem; font-weight: 700; margin: 2rem 0 1rem 0; }
        .single-post-content p { margin-bottom: 1rem; line-height: 1.7; }
        .single-post-content ul { margin-bottom: 1rem; padding-left: 1.5rem; }
        .single-post-content li { margin-bottom: 0.5rem; line-height: 1.6; }
    </style>
</head>
<body class="bg-white text-gray-900">
    <!-- Navigation -->
    <header class="fixed top-0 inset-x-0 z-30">
        <div class="relative bg-white z-20">
            <div class="max-w-7xl w-full px-6 mx-auto">
                <div class="flex justify-between items-center gap-x-6 h-14 sm:h-18">
                    <a href="{{ route('home') }}" class="shrink-0">
                        <div class="font-bold text-xl">Movie Context</div>
                    </a>

                    <ul class="hidden items-center gap-x-8 xl:flex">
                        <li>
                            <a href="{{ route('home') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('home') ? 'text-green-400' : 'hover:text-green-400' }}">Domov</a>
                        </li>
                        <li>
                            <a href="{{ route('tag.show', 'recenzie') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('tag.show') && request()->route('slug') === 'recenzie' ? 'text-green-400' : 'hover:text-green-400' }}">Recenzie</a>
                        </li>
                        <li>
                            <a href="{{ route('tag.show', 'novinky') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('tag.show') && request()->route('slug') === 'novinky' ? 'text-green-400' : 'hover:text-green-400' }}">Novinky</a>
                        </li>
                        <li>
                            <a href="#" class="font-bold whitespace-nowrap transition-colors duration-150 hover:text-green-400">Autori</a>
                        </li>
                    </ul>

                    <div class="flex items-center gap-x-6">
                        <div class="hidden xl:flex items-center gap-x-4">
                            <button type="button" class="flex justify-center items-center size-10 border border-blue transition-colors duration-150 hover:text-white hover:bg-blue">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <button type="button" class="flex justify-center items-center size-10 border border-blue transition-colors duration-150 hover:text-white hover:bg-blue">
                                <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </button>
                            <a href="#" class="flex items-center h-8 font-bold text-white bg-green-400 px-4 border border-green-400 transition-colors duration-150 hover:text-green-400 hover:bg-white sm:h-10 sm:px-5">
                                Predplatné
                            </a>
                        </div>

                        <button type="button" class="flex justify-center items-center size-6 text-blue transition-colors duration-150 hover:text-green-400 xl:hidden">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-12 mt-14 sm:mt-18 lg:py-20">
        <div class="max-w-6xl w-full px-6 mx-auto">
            <div class="single-post-content">
                <h1 class="wp-block-heading">Poučenie o spracúvaní súborov cookies</h1>

                <style>.single-post-content a{color:#00C6D3; text-decoration:underline;font-weight:700;}</style>

                <p>Vypracované spoločnosťou Kontext Media s. r. o., IČO: 56 589 271, so sídlom: Digital Park I, Einsteinova 21, 851 01 Bratislava („Kontext Media" alebo „my" v príslušnom tvare) ako prevádzkovateľom spracúvania osobných údajov, získaných prostredníctvom webu www.magazinkontext.sk („web"), pri ktorého prevádzke sú využívané súbory cookies.</p>

                <p>Keďže niektoré súbory cookies, využívané pri prevádzke webu, môžu v rámci svojej funkcionality získavať aj osobné údaje návštevníkov webu, radi by sme Vám ako jednému z jeho návštevníkov, poskytli stručné, transparentné, zrozumiteľné a ľahko dostupné informácie ohľadne spracúvania súborov cookies, ešte pred tým, ako s ich využívaním udelíte svoj súhlas (v rozsahu v akom sa vyžaduje).</p>

                <p>Rovnako Vám poskytneme informácie, ako môžete ukladaniu súborov cookies predísť, čo však môže mať nepriaznivý vplyv na riadne fungovanie niektorých funkcií webu.</p>

                <p>V prípade akýchkoľvek otázok týkajúcich sa súborov cookies využívaných webom alebo spracúvaním Vašich osobných údajov prípadne uplatnením Vašich práv v zmysle platnej právnej úpravy, nás môžete kedykoľvek kontaktovať:<br>
                listinne na adrese: Kontext Media s. r. o., Digital Park I, Einsteinova 21, 851 01 Bratislava, alebo elektronicky na e-mailovej adrese: ochrana@magazinkontext.sk.</p>

                <h2 class="wp-block-heading">1. Čo sú to súbory cookies?</h2>

                <p>Súbory cookies sú malé súbory, ktoré pri návšteve webu ukladá webový prehliadač vo Vašom počítači alebo inom zariadení ako mobilný telefón či tablet. Súbory cookies umožňujú okrem iného webu rozpoznať Vaše zariadenie a zapamätať si určité informácie o Vašich reláciách počas pripojenia. Súbory cookies dovoľujú webu napríklad určiť, či ste niekedy v minulosti web už navštívili, prípadne prispôsobiť nastavenia webu Vašim individuálnym potrebám. Súbory cookies si totiž pamätajú typ používaného webového prehliadača alebo Vami zvolené nastavenia, ktoré zostávajú predvolenými nastaveniami pri opakovanej návšteve webu, čo má za následok zvýšenie Vášho komfortu pri jeho užívaní.</p>

                <p>Súbory cookies používame v rámci webu predovšetkým za účelom zabezpečenia jeho bezproblémovej funkčnosti a za účelom zlepšenia Vášho zážitku pri využívaní webu. Tieto účely sa napĺňajú prostredníctvom zbierania súhrnných štatistických údajov o počte návštevníkov webu a získavania údajov o tom, ako títo web využívajú, pričom na tieto účely môžeme používať aj súbory cookies tretích strán.</p>

                <p>Pri prevádzke webu teda využívame viacero druhov súborov cookies s rôznymi funkcionalitami, na dosiahnutie rôznych účelov. V zásade na webe využívame dočasné a permanentné súbory cookies.</p>

                <p>Dočasné cookies sú tie, ktoré si web pamätá počas toho ako si ho prehliadate. Po zatvorení webového prehliadača alebo opustení webu sa automaticky vymažú.</p>

                <p>Permanentné cookies na rozdiel od dočasných zostávajú uchované vo Vašom zariadení, pričom na ich uloženie (uchovanie) nemá vplyv opustenie webu, zatvorenie webového prehliadača prípadne vypnutie Vášho zariadenia.</p>

                <p>V rámci prevádzky webu využívame tieto druhy súborov cookies, na nižšie popísané účely:</p>

                <p>Základné (funkčné, nutné) cookies:</p>

                <p>Sú nevyhnutné pre správne fungovanie webu. Umožňujú Vám prezeranie webu a využívanie jeho kľúčových funkcií a zabezpečujú napríklad prístup k zabezpečeným oblastiam webu. Na používanie týchto cookies sa Váš súhlas nevyžaduje. Ak nechcete aby boli vo Vašom prípade tieto cookies používané, je potrebné aby ste web neotvárali, resp. nevyužívali.</p>

                <p>Reklamné (marketingové) cookies:</p>

                <p>Prostredníctvom týchto cookies súborov Vám zobrazujeme cielenú reklamu na webe alebo iných webových stránkach, čo znamená, že sa Vám zobrazuje cielená reklama na základe Vašej aktivity na webe. Tieto súbory sú anonymizované, teda priamo Vás neidentifikujú, avšak vieme Vás rozpoznať ako návštevníka a prispôsobiť pre Vás reklamu. Na používanie tohto typu súborov sa vyžaduje Váš súhlas. V prípade ak ho neudelíte alebo odvoláte, nebude sa Vám zobrazovať cielená (individualizovaná reklama).</p>

                <p>Štatistické/analytické cookies:</p>

                <p>Táto kategória cookies nám pomáha zvýšiť užívateľské pohodlie webu tým, že nám umožňujú porozumieť ako web využívate a tiež nám pomáhajú analyzovať výkon rôznych predajných kanálov. Napríklad nám pomáhajú získavať údaje týkajúce sa najmä návštev, pôvodu návštev a výkonnosti webu a pod. Štatistické súbory cookies identifikujú opakovanú návštevu webu z rovnakého webového prehliadača na rovnakom zariadení a dokážu sledovať Vašu aktivitu pri návšteve webu. Rovnako nám pomáhajú rozpoznať technické problémy, ktoré sa môžu na webe vyskytnúť a taktiež sledujú aj efektivitu jednotlivých súčastí webu, na základe čoho dokážeme zdokonaliť navigáciu na webe. Na používanie tohto typu súborov sa vyžaduje Váš súhlas. V prípade ak ho neudelíte alebo odvoláte, nebudú sa zbierať analytické údaje o Vašej návšteve webu.</p>

                <h2 class="wp-block-heading">2. Aktuálny zoznam súborov cookies, ktoré sú zbierané prostredníctvom tohto webu</h2>

                <p>Pokiaľ v rámci špecifikácie cookies súborov používaných na webe uvádzame, že informácie v cookies súbore zdieľame s poskytovateľom cookies, znamená to, že cookies súbory nie sú vytvorené alebo spravované nami, ale treťou nezávislou stranou (napr. obchodný partner, poskytovateľ služieb, atď). Prevádzkovateľom cookies tretích strán je tretia nezávislá strana, ktorá ukladá tieto súbory cookies do Vášho zariadenia pri Vašej návšteve webu, avšak informácie získané prostredníctvom týchto cookies má k dispozícii tretia strana, ktorá ich následne s nami zdieľa. Cookies tretích strán sa používajú za rôznymi účelmi (napr. analytické alebo reklamné).</p>

                <p><a href="#" target="_blank" rel="noreferrer noopener"><strong>Aktuálny zoznam cookies</strong></a></p>

                <h2 class="wp-block-heading">3. Vaše práva</h2>

                <p>V súvislosti so spracúvaním osobných údajov v rámci využívania cookies súborov na webe, Vám, ako dotknutej osobe, vznikajú nižšie uvedené práva, ktoré si môžete u nás kedykoľvek uplatniť vo forme žiadosti. V takomto prípade sme povinný poskytnúť Vám informácie o opatreniach, ktoré sme prijali na základe Vašej žiadosti a to bez zbytočného odkladu, najneskôr však do 1 mesiaca. Túto lehotu môžeme predĺžiť o ďalšie 2 mesiace, pričom v takomto prípade Vás informujeme o každom takomto predĺžení do 1 mesiaca od doručenia žiadosti spolu s dôvodmi zmeškania lehoty.</p>

                <ul class="wp-block-list">
                <li>Právo na prístup
                <ul class="wp-block-list">
                <li>máte právo získať potvrdenie, či spracúvame Vaše osobné údaje, pričom v prípade, ak áno, tak máte právo získať k týmto osobným údajom prístup. Máte zároveň právo na poskytnutie všetkých informácii v rámci tohto oznámenia, pričom toto oznámenie pravidelne aktualizujeme.</li>
                </ul>
                </li>
                </ul>

                <h2 class="wp-block-heading">4. Tretie strany, prenos a doba uchovávania dát.</h2>

                <ul class="wp-block-list">
                <li>Niektoré súbory cookies spravujeme priamo my, zatiaľ čo iné sú spravované našimi partnermi – tretími stranami</li>
                <li>Google LLC (Google Analytics, Google Ads) – analýza návštevnosti a reklamné služby</li>
                <li>Microsoft Corporation (Clarity) – analýza používateľského správania</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-white bg-blue-900">
        <div class="max-w-7xl w-full px-6 mx-auto">
            <div class="py-12 border-b border-gray-600 lg:py-20">
                <div class="flex flex-col gap-12 lg:flex-row lg:justify-between lg:gap-28">
                    <ul class="grid gap-8 lg:grid-cols-4 lg:gap-12">
                        <li>
                            <div class="font-bold text-xl mb-4">Movie Context</div>
                        </li>

                        <li class="lg:hidden">
                            <button type="button" class="flex justify-between items-center w-full">
                                <span class="font-bold lg:text-lg">O nás</span>
                                <svg class="size-4 fill-current transform transition-transform duration-150">
                                    <use href="#chevron-down"></use>
                                </svg>
                            </button>
                        </li>

                        <li class="hidden space-y-6 lg:block">
                            <h3 class="text-lg font-bold">O nás</h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Predplatné</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Pre študentov</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Podpora</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Kontakt</a>
                                </li>
                            </ul>
                        </li>

                        <li class="lg:hidden">
                            <button type="button" class="flex justify-between items-center w-full">
                                <span class="font-bold lg:text-lg">Spolupráca</span>
                                <svg class="size-4 fill-current transform transition-transform duration-150">
                                    <use href="#chevron-down"></use>
                                </svg>
                            </button>
                        </li>

                        <li class="hidden space-y-6 lg:block">
                            <h3 class="text-lg font-bold">Spolupráca</h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Inzercia a spolupráce</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Tiráž</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Copyright a citovanie</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <div class="max-w-full md:max-w-[420px]">
                        <h3 class="text-lg font-bold pb-4">Prihlásiť</h3>
                        <p class="pb-6">Prihláste sa na odber bezplatného týždenného newslettra Movie Context.</p>
                        <div class="flex flex-col w-full space-y-2">
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                                <input type="email" name="newsletter_email" class="w-full text-white bg-transparent p-3 border-[1.5px] border-gray-400 transition-all duration-150 placeholder:text-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-blue-900" placeholder="Zadajte svoj e-mail">
                                <a href="#" class="font-bold whitespace-nowrap px-6 py-3 border-[1.5px] border-gray-400 text-white transition-all duration-150 hover:text-white hover:bg-green-400 hover:border-green-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-blue-900">Prihlásiť sa</a>
                            </div>
                            <p class="text-sm">Prihlásením potvrdzujem, že som sa oboznámil s pravidlami ochrany osobných údajov.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center gap-6 py-8 lg:flex-row lg:justify-between">
                <div class="flex flex-col items-center gap-6 lg:flex-row">
                    <p class="text-sm">© Copyright {{ date('Y') }} | Movie Context.</p>
                    <ul class="flex flex-col items-center gap-2 sm:flex-row sm:gap-6">
                        <li>
                            <a href="{{ route('terms') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Obchodné podmienky</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Ochrana osobných údajov</a>
                        </li>
                        <li>
                            <a href="{{ route('cookies') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Cookies</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
