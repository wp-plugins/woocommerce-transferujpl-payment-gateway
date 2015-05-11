=== WooCommerce Payment Gateway - Transferuj.pl  ===
Contributors: transferuj.pl
Donate link: http://transferuj.pl/
Tags: woocommerce, transferuj, payment, polish gateway, polska brama płatności, bramka płatności, płatności internetowe
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 1.1.3
License: GPLv2 
Accept payments from all major polish banks directly on your WooCommerce site via transferuj.pl polish payment gateway system.

== Description ==

Brama płatności dla pluginu Woocommerce.

Transferuj.pl to system szybkich płatności online należący do spółki Krajowy Integrator Płatności SA. Misją przedsiębiorstwa jest wprowadzanie oraz propagowanie nowatorskich metod płatności i rozwiązań płatniczych zapewniających maksymalną szybkość i bezpieczeństwo dokonywanych transakcji.

Jako lider technologiczny, Transferuj.pl oferują największą liczbę metod płatności na rynku. W ofercie ponad 50 sposobów zapłaty znajdą Państwo m.in. największy wybór e-transferów, Zintegrowaną Bramkę Płatności Kartami, mobilną galerię handlową RockPay oraz narzędzie do zbiórek pieniężnych w sieci – serwis eHat.me. Dodatkowe funkcjonalności systemu obejmują pełen design w RWD, przelewy masowe oraz udostępnione biblioteki mobilne i dodatki do przeglądarek automatyzujące przelewy. Transferuj.pl oferuje również płatności odroczone, raty online Premium SMS oraz płatność za pomocą kodu QR.

Transferuj.pl zapewnia najwyższy poziom bezpieczeństwa potwierdzony certyfikatem PCI DSS Level 1. System gwarantuje wygodę oraz możliwość natychmiastowej realizacji zamówienia. Oferta handlowa Transferuj.pl jest dokładnie dopasowana do Twoich potrzeb.

Transferuj.pl Online Payment System belongs to Krajowy Integrator Płatności Inc. The company’s mission is to introduce and promote innovative payment methods and solutions ensuring maximum speed and safety of online transactions.

As technological leader, Transferuj.pl offers the largest number of payment methods on market. Among over 50 ways of finalizing transactions you will find the widest choice of direct online payments, Integrated Card Payment Gate, mobile shopping center – RockPay and group payments tool – eHat.me. Additional features include: RWD design, mass pay-outs, mobile libraries and payment automation application. You can also pay using postponed payment, online installments, Premium SMS and QR code payment.

The highest level of security of payments processed by Transferuj.pl is verified by PCI DSS Level 1 certificate. System guarantees convenience and instant order execution. Our business offer is flexible and prepared according to your needs.


== Installation ==

= WYMAGANIA =

Aby korzystać z płatności Transferuj.pl w platformie Woocommerce niezbędne jest:

a)	Posiadanie konta w systemie Transferuj.pl
b)	Aktywna wtyczka WooCommerce dla Wordpressa. 
c)	Pobranie plików instalacyjnych modułu z katalogu wtyczek Wordpress:



= INSTALACJA MODUŁU =

Instalacja autmatyczna 
a)	Przejdź do menu „Wtyczki” następnie „Dodaj nową” i w miejscu „Szukaj wtyczek”  wyszukaj „Transferuj”
b)	W „Wynikach wyszukiwania” pojawi się moduł płatności Transferuj, który należy zainstalować. 
 

Instalacja ręczna 
a)	Rozpakuj zawartość archiwum na dysk. Po rozpakowaniu powinien powstać folder „woocommerce_transferuj”.
b)	Wyślij cały folder  do katalogu wp-content/plugins znajdującego się w Twojej instalacji Wordpress.

1.	Przejdź do panelu administracyjnego i otwórz zakładkę „Wtyczki”. Kliknij „Włącz” przy pozycji „Transferuj.pl”.
2.	Przejdź do WooCommerce ->Ustawienia i wybierz  zakładkę „Zamówienia ” po czym z listy dostępnych metod płatności  wybierz Transferuj.pl. 
3.	Teraz należy dokonać odpowiednich ustawień dla modułu płatności Transferuj:
	a.	Włącz/Wyłącz – należy pozostawić zaznaczone, aby klienci mogli dokonywać płatności przez Transferuj.
	b.	Nazwa – nazwa płatności 
	c.	Opis - opis bramki płatności Transferuj, który widzi użytkownik przy tworzeniu zamówienia
	d.	ID sprzedawcy – pole obowiązkowe, Twój ID otrzymany podczas zakładania konta  Transferuj.pl
	e.	Kod bezpieczeństwa  – należy wpisać kod  ustawiony w Panelu Odbiorcy Płatności  w Transferuj.pl. Menu -> Ustawienia -> Powiadomienia -> Kod 		Bezpieczeństwa. 
	f.	Dopłata doliczana za korzystanie z Transferuj – opcja ta pozwala doliczyć do kwoty zamówienia, opłatę  za korzystanie płatności Transferuj. Domyślnie 		wybrana jest opcja NIE pozostałe opcje:
			PLN – należy podać kwotę jaka ma zostać doliczona do zamówienia
			% - należy podać jaki procent z danego zamówienia zostanie doliczony do całkowitej kwoty do zapłaty.
	g.	Kwota dopłaty – dla wybranej w poprzednim punkcie opcji:
			PLN- kwota doliczana do zamówienia, liczby dziesiętne należy podać po kropce np.  3.50
			% - procent jaki ma zostać doliczony z danego zamówienia do całkowitej kwoty zamówienia, liczby dziesiętne należy podać po kropce np. 2.75 
	h.	Włącz wybór banku na stronie sklepu– dostępne opcje:
			TAK – klient będzie dokonywał wyboru kanału płatności na stronie sklepu.
			NIE – klient dokona wyboru kanału płatności po przejściu do Panelu Transakcyjnego Transferuj.
	i.	Widok listy kanałów- pozwala wybrać na jakiej zasadzie mają być wyświetlane kanały płatności na stronie sprzedawcy:
			Lista – rozwijana lista zawierająca kanały płatności.
			Kafelki – kanały płatności wyświetlane w formie ikon z logami banków.
		Opcja brana pod uwagę tylko z aktywną opcją h.
4.	Następnie należy kliknąć „Zapisz zmiany”.


= Testy =

Moduł był testowany na systemie zbudowanym z wersji Woocommerce 2.2.1 i Wordpress 4.1.


= KONTAKT =

W razie potrzeby odpowiedzi na pytania powstałe podczas lektury lub szczegółowe wyjaśnienie kwestii technicznych prosimy o kontakt poprzez formularz znajdujący się w Panelu Odbiorcy lub na adres e-mail: pt@transferuj.pl 
