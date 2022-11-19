# jela-svijeta
## Backend zadatak

### Sadržaj
1. Opis
    1. Request
    2. Response
2. Lokalno postavljanje
3. Primjer

#

### 1. Opis
Ova aplikacija se sastoji od baze jela, sastojaka, kategorija i tagova. </br>
S obzirom da je aplikacija višejezična, jela, sastojci, kategorije i tagovi imaju
tablice prijevoda. Također postoji i tablica jezika u kojoj se nalaze dostupni jezici. </br>

Tablice je potrebno kreirati korištenjem migracija. </br>
Tablice se trebaju popuniti podacima korištenjem seedera i paketa FakerPHP /
Faker. </br>
Poželjno je koristiti “Dependency Injection”. </br>
Pri rješavanju zadatka trebao bi se pridržavati “SOLID design principles”.

Aplikacija treba imati jedan endpoint na kojem se trebaju izlistavati jela. Koji
podatci se prikazuju i kako, ovisi o parametrima u query-ju.

Pretpostavimo da sva jela imaju unesen isti broj prijevoda koji je identičan broju
jezika u tablici languages. </br>
- Jelo može biti bez kategorije, ili može pripadatati samo jednoj kategoriji </br>
- Jelo mora imati definiran barem jedan tag </br>
- Jelo mora imati definiran barem jedan sastojak

Potrebno je napraviti validaciju svih parametara requesta po kojima ce se filtrirati
rezultati baze.

#### i. Request
Želimo imati kontrolu nad </br>
  - `per_page` - (optional) Broj rezultata po stranici
  - `page` - (optional) broj stranice
  - `category` - (optional) id kategorije po kojoj želimo filtrirati rezultate; osim id,
  ovaj parametar može imati vrijednost `NULL` (gdje ne postoji kategorija) kao i
  vrijednost `!NULL` (gdje postoji kategorija)
  - `tags` - (optional) lista id-jeva po kojima želimo filtrirati rezultate (npr,
  tags=1,2,3). Vratiti samo jela koja imaju sve navedene tagove.
  - `with` - (optional) lista ključnih riječi (ingredients, category, tags) s kojima
  dajemo do znanja koje dodatne podatke očekujemo u responsu
  - `lang` - (required) parametar kojim definiramo jezik
  - `diff_time` - (optional) UNIX Timestamp; kad je ovaj parametar proslijeđen
  tad je potrebno vratiti sve iteme (i one obrisane). Treba vratiti sve ne samo
  izmjenjene nakon datuma proslijeđenog u ovom parametru *
  
*S obzirom na to da nije predviđena kreacija, ažuriranje i brisanje, nije se
potrebno posebno fokusirati na razradu ove funkcionalnosti, ono što je bitno je
sljedeće: kada je u requestu poslan parametar `diff_time` i kada je to pozitivan
cijeli broj veći od 0, tada je pri selektiranju podataka iz baze potrebno uzeti u
obzir sva jela (uključujući i obrisana) koja su kreirana, modificirana ili obrisana
nakon datuma definiranog u tom parametru.

#### ii. Response
  - `id` - id jela iz tablice dish
  - `title` - naziv jela iz tablice prijevoda za jelo ovisno o parametru `lang`
  - `description` - opis jela iz tablice prijevoda za jelo ovisno o parametru `lang`
  - `status` - zadana vrijednost je `created` osim ako je u requestu proslijeđen
  parametar `diff_time`, tada status može biti jedan od `created`, `modified`,
  `deleted` ovisno o tome dali je vraćeno jelo bilo kreirano, modificirano ili
  obrisano nakon vremena definiranog u parametru `diff_time`. Manipulaciju
  status potrebno je izvesti putem time stampa `created_at`, `updated_at` i
  `deleted_at`
  
Ovi gore spomenuti property definiraju osnovnu shemu responsa; međutim
shemu responsa je moguće promijeniti (proširiti) tako da se pošalje jedan ili više
ključnih riječi u parametar `with`, tada se u responsu na svakom objektu još mogu
pojaviti i property `tags`, `category` ili/i `ingredients`.

### 2. Lokalno postavljanje
1. Klonirati repozitorij: `git clone git@github.com:filip-kr/jela-svijeta.git`
2. Instalirati zavisnosti: `composer update`
3. Kreirati bazu i korisnika sa svim ovlastima prema podacima iz `.env` datoteke: </br> `DATABASE_URL="mysql://js-filip-kr:js256@127.0.0.1:3306/jelasvijeta?serverVersion=mariadb-10.4.25&charset=utf8mb4"` </br> ili kreirati vlastitu i prilagoditi postavke. Obratiti pažnju na verziju baze i UTF8 charset radi prikaza japanskih znakova.
4. Izvesti migraciju: `bin/console doctrine:migrations:migrate`
5. Učitati fixtures-e: `bin/console doctrine:fixtures:load`

### 3. Primjer
Request: `http://localhost:8000/api?category=!NULL&diff_time=122590485&with=ingredients,tags,category&per_page=5&page=1&lang=ja` </br>

[Response](https://github.com/filip-kr/jela-svijeta/files/10048086/Response.txt)
