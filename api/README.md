# Istruzioni

## Installazione
Una volta copiato il codice da github, assicurarsi di trovarsi nella cartella "api" e poi usare il comando `composer install` per installare tutte le dependencies necessarie.
Copiare il file `.env.example` e rinominarlo `.env`. All'interno inserire tutte le informazioni necessarie perché l'applicazione possa connettersi a un database e a un provider per spedire emails.
Usare il comando `php artisan key:generate` per generare una key per l'applicazione.
Usare il comando `php artisan migrate:fresh --seed` per migrare il database e riempirlo di test dati. (Leggere le considerazioni alla fine di questo documento prima di utilizzare questo comando)
Ora che il database e' stato creato e riempito si puo' usare il comando `php artisan serve` per create un link di test a cui potersi connettere per testare l'applicazione.

## Testare l'applicazione
Per effettuare il login utilizzate una delle mail generate dal seeder che potete trovare nella tabella "users" del database e utilizzate la parola `password` come password per ogni user.

Gli endpoint utilizzabili sono:
- api/user/post/login -> Per effetuare il login (Body input richiesti: email, password)
- api/user/post/logout -> Per effetuare il logout
- api/user/post/follow -> Per aggiungere un user alla lista di user seguiti (Body input: user_id)
- api/url/get/all -> Per ottenere tutti gli url presenti dell'applicazione
- api/url/get/creator/{creatorId} -> Per ottenere tutti gli url creati da un'user
- api/url/get/followed -> Per ottenere tutti gli url creati da user seguiti dall'utente "loggato"
- api/url/get/search/{tags} -> Per ottenere tutti gli url che sono associati ad almeno uno dei tag inseriti (Si puo' cercare per molteplici tags separandoli con una virgola e senza spazi fra di loro)
- api/url/post/like -> Per aggiungere un nuovo like a un url (Body input: url_id)
- api/url/post/new -> Per aggiungere un nuovo url (Body input: new_link)


## Considerazioni
Una prima considerazione da fare riguarda il DatabaseSeeder. Ho fatto svariati test e provato svariati metodi per rendere il riempimento del database di dati per il test piu' veloce possibile ma purtroppo risulta ancora un po' lento.
Secondo i miei test allo stato attuale lo script ci mette circa 5 minuti a creare 10.000 users e ad effetuare le varie operazioni richieste per ogni user (creare gli url, aggiungere likes e tags e followers).
Questo vuol dire che per create 100.000 users (e le altre operazioni necessarie) lo script impieghera' all'incirca 50 minuti.
Ovviamente questi tempi sono solo una stima e posso cambiare (specialmente in base alla potenza del pc utilizzato).
Sarei molto interessato a ricevere feedback su come si potrebbe rendere questo processo piu' veloce.
(Alla fine di ogni iterazione ho incluso un "var_dump" dell id dello user, cosi' che ci sia una rappresentazione visiva all'interno del terminale, la trovo una buona pratica nel caso di script che durano piu' di un paio di minuti).

Un altra considerazione da fare per quando riguarda il DatabaseSeeder. Ho provato a creare i 100.000 users tutti in una volta utilizzando le factory ma questo risultava ogni volta in un crash dello script. Credo che questo sia dovuto al fatto che provando ad inserire una collezione di users cosi' grande all'interno di una variable la memoria dedicata a PHP viene esaurita e quindi lo script crasha. Per risolvere questo problema ho deciso di creare "solo" 10.000 users alla volta e avere un loop che esegue la creazione degli users 10 volte (cosi da raggungere la quantita' richiesta di almeno 100.000).
Ovviamente sentitevi liberi di modificare la quantita' di iterazioni cosi da ingrandire o diminuire il numero di users creati (e quindi la durata dello script).

Alcune cosiderazioni generali sul codice dell'applicazione. Mentre trovo che il codice sia generalmente pulito e di buona qualita' prima di poter pubblicare un'applicazione del genere bisognere aggiungere alcune cose, ad esempio avere una validazione migliore degli input (attualmente l'unica valizione fatta e' all'interno della funzione per effetuare il login).
In oltre, nel caso di una vera applicazione che deve essere utilizzata a scopo commerciale, una maggiore separazione fra i vari componenti sarebbe consigliata (in questo campo sono un grande fan della libreria: nwidart/laravel-modules, che permette di "modularizzare" i vari componenti di un'applicazione Laravel).

Per quanto riguarda i commenti e DocBlocks, ritengo che vadano usati solo quando necessari per spiegare una funzione estremamente complessa o nel caso aiuti l'IDE a "comprendere" il codice scritto e quindi aiutare lo sviluppatore ad utilizzare quel codice.
Non essendoci funzioni complesse in questa applicazione ho incluso solo alcuni DocBlocks nei modelli per permettere all'IDE di suggerire i vari campi utlizzabili di ogni modello.

Per quanto riguarda le notifiche, sono a conoscenza che Laravel ha gia' delle funzionalita' al suo interno per gestire notifiche di vario genere, ma non avendo una vera esperienza con questo metodo, ho preferito utilizzare gli Events/Listerners a cui sono molto piu' abituato.

Visto che le mail degli user sono random e non vere email, ho aggiunto un controllo nel file "SendNotificationListener" per mandare le notifiche sempre allo stesso indirizzo (in questo caso la mail di chi deve effettuare il test).

Un'ultima considerazione per quanto riguarda la funzione "getUrlsByTags" che ritorna tutti i url in base a quali tag sia associati a quel url. Utilizzando i modelli di Laravel (in specifico la funzione "whereHas") la ricerca risultava estremamente lenta (circa un minuto per richiesta), per questo motivo ho deciso di utilizzare la classe DB e fare manualmente i vari join e where. Questo ha permesso di rendere le richieste estremamente piu' veloci (circa un secondo a richiesta) ma ora il formato dei dati ritornati non e' piu' consistente con quello presente negli altri routes. Ho provato vari metodi per risolvere questo problema ma non sono riuscito a trovare una soluzione che fosse sia performante che elegante (a livello di codice), quindi per ora ho lasciato la "selection" di tutti i field del url e dello user, sarei molto interessato a sapere come si potrebbe "raggrupare" tutti i dati dello user in un array e inserirlo all'interno dell'array del url (cosi da copiare il formato che gli altri routes hanno).