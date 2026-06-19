(function () {
  const en = {
    'language.label': 'Language',
    'nav.coverage': 'Coverage',
    'nav.capabilities': 'Capabilities',
    'nav.pricing': 'Pricing',
    'nav.setup': 'Receiver setup',
    'nav.faq': 'FAQ',
    'common.buy': 'Buy now',
    'common.close': 'Close',
    'hero.title': 'TruePoint regional RTK corrections with survey-grade precision.',
    'hero.body': 'Subscribe online to cloud-based, multi-frequency NTRIP corrections backed by BDSTAR technology and distributed by Topomarket IKE across European and Mediterranean territories.',
    'hero.primary': 'Start annual subscription',
    'hero.secondary': 'View coverage',
    'proof.accuracy': 'horizontal accuracy target',
    'proof.countries': 'sales territories',
    'proof.support': 'technical stability',
    'coverage.title': 'Coverage built around regional commercial rights.',
    'coverage.body': 'Topomarket IKE manages exclusive distribution in ten countries and online sales rights in three additional high-growth markets.',
    'coverage.exclusive': 'Exclusive distribution territories',
    'coverage.online': 'Online sales rights territories',
    'capabilities.title': 'RTK capability without hardware lock-in.',
    'capabilities.body': 'The service supports the standard GNSS constellations and NTRIP workflows used by field survey, civil construction, UAV, and precision agriculture teams.',
    'cap.multi.title': 'Multi-frequency corrections',
    'cap.multi.body': 'Supports GPS, GLONASS, BDS, Galileo, and QZSS correction workflows through NTRIP.',
    'cap.accuracy.title': 'Sub-2cm positioning target',
    'cap.accuracy.body': 'Designed for professional RTK field work where fast fixes and stable repeatability matter.',
    'cap.local.title': 'Local coordinate compatibility',
    'cap.local.body': 'Positioned for localized official coordinate-system support across the target countries.',
    'cap.hardware.title': 'Hardware-agnostic access',
    'cap.hardware.body': 'Works with standard GNSS receivers, tablets, controllers, and UAV RTK clients that support NTRIP.',
    'pricing.title': 'Transparent subscription tiers.',
    'pricing.body': 'Choose the subscription window that matches your field campaign. Prices are listed before tax and use EUR as the checkout currency.',
    'pricing.note': 'PayPal checkout supports PayPal balance, debit card, and credit card payments where PayPal makes those methods available.',
    'pricing.best': 'Best value',
    'pricing.beforeTax': 'before tax',
    'pricing.choose': 'Choose plan',
    'pricing.feature.1': 'Multi-frequency RTK corrections',
    'pricing.feature.2': 'NTRIP credentials by email',
    'pricing.feature.3': 'GPS, GLONASS, BDS, Galileo, QZSS',
    'pricing.feature.4': 'TruePoint technical support route',
    'plan.monthly.name': '1-Month RTK Subscription',
    'plan.monthly.duration': '1 month',
    'plan.monthly.persona': 'Short-term contractors, UAV pilots, and temporary projects.',
    'plan.quarterly.name': '3-Month RTK Subscription',
    'plan.quarterly.duration': '3 months',
    'plan.quarterly.persona': 'Seasonal agriculture, infrastructure, and recurring site work.',
    'plan.annual.name': '1-Year RTK Subscription',
    'plan.annual.duration': '1 year',
    'plan.annual.persona': 'Survey firms, engineering teams, and enterprise fleets.',
    'setup.title': 'Configuration guidelines for GNSS receivers.',
    'setup.body': 'After payment, the system sends the NTRIP host, port, mountpoint, username, and password. Use the matching guide below for the receiver family in the field.',
    'guide.leica.steps': ['Open the controller Internet/NTRIP profile.', 'Enter the host, port, username, password, and assigned mountpoint.', 'Select the local coordinate system required for the project.', 'Connect, confirm fixed RTK status, and store the profile.'],
    'guide.trimble.steps': ['Open GNSS contacts or receiver internet settings.', 'Create an NTRIP caster connection with the emailed parameters.', 'Select the mountpoint for the service country.', 'Start the survey and verify fixed RTK before measuring.'],
    'guide.topcon.steps': ['Open the network RTK or NTRIP setup menu.', 'Add a new caster using the host, port, and credentials.', 'Choose the assigned VRS mountpoint.', 'Save the profile and confirm correction age and fixed solution.'],
    'guide.generic.steps': ['Open the NTRIP client in the controller or field app.', 'Enter host, port, mountpoint, username, and password exactly as emailed.', 'Enable the correct GNSS antenna and data-link settings.', 'Connect in open sky and wait for fixed RTK.'],
    'guide.uav.steps': ['Open the drone RTK network service menu.', 'Set custom NTRIP instead of manufacturer network when needed.', 'Paste the caster details and mountpoint.', 'Confirm RTK connected before takeoff and monitor correction age during flight.'],
    'ops.title': 'Automated purchase, logged fulfillment, admin control.',
    'ops.body': 'The checkout flow creates the PayPal order server-side, captures payment, records the transaction, generates a provisioning package, and emails the customer. Administrators can update prices, countries, service parameters, and order status from the admin panel.',
    'ops.payments': 'Server-side PayPal Orders API bridge',
    'ops.security': 'Secrets stored only in PHP configuration',
    'ops.admin': 'Admin panel for catalog and order management',
    'faq.title': 'Frequently asked questions.',
    'faq.what.q': 'What is TruePoint?',
    'faq.what.a': 'TruePoint is a cloud-based CORS network service for real-time GNSS correction data using NTRIP.',
    'faq.hardware.q': 'Do I need a specific receiver brand?',
    'faq.hardware.a': 'No. The service is designed for hardware-agnostic NTRIP access where the receiver or field app supports network RTK.',
    'faq.activation.q': 'When is the subscription activated?',
    'faq.activation.a': 'The system sends connection details after payment capture. Operational checks may require up to 24 hours if TruePoint account verification is needed.',
    'faq.payment.q': 'How are payments processed?',
    'faq.payment.a': 'Payments are processed through PayPal Orders API. PayPal may offer PayPal balance, debit card, and credit card options depending on country and account eligibility.',
    'faq.support.q': 'Who provides technical support?',
    'faq.support.a': 'Technical queries are routed to the TruePoint technical support team in Germany, with English-language support as the baseline.',
    'faq.tax.q': 'Are the prices tax inclusive?',
    'faq.tax.a': 'No. The business-plan prices are retail prices before tax. Local VAT or invoicing rules may apply.',
    'footer.copy': 'TruePoint CORS Network subscriptions distributed by Topomarket IKE.',
    'footer.admin': 'Admin panel',
    'checkout.title': 'Complete your subscription',
    'checkout.country': 'Service country',
    'checkout.name': 'Full name',
    'checkout.email': 'Email',
    'checkout.receiver': 'Receiver model',
    'checkout.notes': 'Project notes',
    'checkout.configure': 'PayPal is not configured yet. Add the PayPal client ID and secret in app/config.php before live checkout.',
    'checkout.required': 'Enter your name, email, and service country before continuing.',
    'checkout.creating': 'Creating PayPal order...',
    'checkout.capturing': 'Capturing payment...',
    'checkout.success': 'Payment complete. Your connection details are shown below and have been emailed.',
    'checkout.error': 'Checkout could not be completed. Please try again or contact support.'
  };

  const localized = {
    el: {
      'language.label': 'Γλώσσα', 'nav.coverage': 'Κάλυψη', 'nav.capabilities': 'Δυνατότητες', 'nav.pricing': 'Τιμές', 'nav.setup': 'Ρύθμιση δέκτη', 'nav.faq': 'Συχνές ερωτήσεις', 'common.buy': 'Αγορά τώρα', 'common.close': 'Κλείσιμο',
      'hero.title': 'Περιφερειακές διορθώσεις RTK TruePoint με τοπογραφική ακρίβεια.', 'hero.body': 'Εγγραφείτε online σε cloud-based, πολυσυχνοτικές διορθώσεις NTRIP με τεχνολογία BDSTAR και διανομή από την Topomarket IKE.', 'hero.primary': 'Ετήσια συνδρομή', 'hero.secondary': 'Προβολή κάλυψης',
      'coverage.title': 'Κάλυψη με βάση τα περιφερειακά εμπορικά δικαιώματα.', 'coverage.exclusive': 'Αποκλειστικές περιοχές διανομής', 'coverage.online': 'Περιοχές online πωλήσεων',
      'pricing.title': 'Διαφανή πακέτα συνδρομής.', 'pricing.choose': 'Επιλογή πακέτου', 'pricing.best': 'Καλύτερη αξία', 'pricing.beforeTax': 'προ φόρων',
      'setup.title': 'Οδηγίες ρύθμισης για δέκτες GNSS.', 'faq.title': 'Συχνές ερωτήσεις.', 'footer.admin': 'Πίνακας διαχείρισης', 'checkout.title': 'Ολοκλήρωση συνδρομής', 'checkout.country': 'Χώρα υπηρεσίας'
    },
    tr: {
      'language.label': 'Dil', 'nav.coverage': 'Kapsama', 'nav.capabilities': 'Yetenekler', 'nav.pricing': 'Fiyatlar', 'nav.setup': 'Alıcı kurulumu', 'nav.faq': 'SSS', 'common.buy': 'Satın al', 'common.close': 'Kapat',
      'hero.title': 'Ölçme hassasiyetinde TruePoint bölgesel RTK düzeltmeleri.', 'hero.body': 'BDSTAR teknolojisiyle desteklenen ve Topomarket IKE tarafından sunulan bulut tabanlı, çok frekanslı NTRIP düzeltmelerine online abone olun.', 'hero.primary': 'Yıllık abonelik başlat', 'hero.secondary': 'Kapsamayı gör',
      'coverage.title': 'Bölgesel ticari haklara göre oluşturulmuş kapsama.', 'coverage.exclusive': 'Münhasır dağıtım bölgeleri', 'coverage.online': 'Online satış hakkı bölgeleri',
      'pricing.title': 'Şeffaf abonelik paketleri.', 'pricing.choose': 'Paketi seç', 'pricing.best': 'En iyi değer', 'pricing.beforeTax': 'vergi hariç',
      'setup.title': 'GNSS alıcıları için kurulum yönergeleri.', 'faq.title': 'Sık sorulan sorular.', 'footer.admin': 'Yönetim paneli', 'checkout.title': 'Aboneliğinizi tamamlayın', 'checkout.country': 'Hizmet ülkesi'
    },
    mk: {
      'language.label': 'Јазик', 'nav.coverage': 'Покриеност', 'nav.capabilities': 'Можности', 'nav.pricing': 'Цени', 'nav.setup': 'Поставување приемник', 'nav.faq': 'ЧПП', 'common.buy': 'Купи сега', 'common.close': 'Затвори',
      'hero.title': 'RTK корекции со висока прецизност за регионалната мрежа TruePoint.', 'hero.primary': 'Започни годишна претплата', 'hero.secondary': 'Види покриеност',
      'coverage.exclusive': 'Ексклузивни територии за дистрибуција', 'coverage.online': 'Територии за онлајн продажба', 'pricing.title': 'Јасни претплатнички пакети.', 'pricing.choose': 'Избери пакет', 'pricing.beforeTax': 'пред данок', 'setup.title': 'Насоки за поставување GNSS приемници.', 'faq.title': 'Чести прашања.', 'footer.admin': 'Админ панел', 'checkout.country': 'Земја на услугата'
    },
    sq: {
      'language.label': 'Gjuha', 'nav.coverage': 'Mbulimi', 'nav.capabilities': 'Aftësitë', 'nav.pricing': 'Çmimet', 'nav.setup': 'Konfigurimi i marrësit', 'nav.faq': 'FAQ', 'common.buy': 'Bli tani', 'common.close': 'Mbyll',
      'hero.title': 'Korrigjime RTK me saktësi të lartë për rrjetin rajonal TruePoint.', 'hero.primary': 'Fillo abonimin vjetor', 'hero.secondary': 'Shiko mbulimin',
      'coverage.exclusive': 'Territore ekskluzive shpërndarjeje', 'coverage.online': 'Territore shitjeje online', 'pricing.title': 'Nivele abonimi transparente.', 'pricing.choose': 'Zgjidh planin', 'pricing.beforeTax': 'para tatimit', 'setup.title': 'Udhëzime konfigurimi për marrës GNSS.', 'faq.title': 'Pyetje të shpeshta.', 'footer.admin': 'Paneli i administratorit', 'checkout.country': 'Shteti i shërbimit'
    },
    sr: {
      'language.label': 'Језик', 'nav.coverage': 'Покривеност', 'nav.capabilities': 'Могућности', 'nav.pricing': 'Цене', 'nav.setup': 'Подешавање пријемника', 'nav.faq': 'ЧПП', 'common.buy': 'Купи сада', 'common.close': 'Затвори',
      'hero.title': 'RTK корекције високе прецизности за регионалну TruePoint мрежу.', 'hero.primary': 'Покрени годишњу претплату', 'hero.secondary': 'Погледај покривеност',
      'coverage.exclusive': 'Ексклузивне територије дистрибуције', 'coverage.online': 'Територије online продаје', 'pricing.title': 'Јасни претплатнички пакети.', 'pricing.choose': 'Изабери пакет', 'pricing.beforeTax': 'без пореза', 'setup.title': 'Смернице за подешавање GNSS пријемника.', 'faq.title': 'Честа питања.', 'footer.admin': 'Админ панел', 'checkout.country': 'Земља услуге'
    },
    bs: {
      'language.label': 'Jezik', 'nav.coverage': 'Pokrivenost', 'nav.capabilities': 'Mogućnosti', 'nav.pricing': 'Cijene', 'nav.setup': 'Podešavanje prijemnika', 'nav.faq': 'FAQ', 'common.buy': 'Kupi sada', 'common.close': 'Zatvori',
      'hero.title': 'RTK korekcije visoke preciznosti za regionalnu TruePoint mrežu.', 'hero.primary': 'Pokreni godišnju pretplatu', 'hero.secondary': 'Pogledaj pokrivenost',
      'coverage.exclusive': 'Ekskluzivne teritorije distribucije', 'coverage.online': 'Teritorije online prodaje', 'pricing.title': 'Jasni pretplatnički paketi.', 'pricing.choose': 'Odaberi paket', 'pricing.beforeTax': 'prije poreza', 'setup.title': 'Upute za podešavanje GNSS prijemnika.', 'faq.title': 'Česta pitanja.', 'footer.admin': 'Admin panel', 'checkout.country': 'Zemlja usluge'
    },
    hr: {
      'language.label': 'Jezik', 'nav.coverage': 'Pokrivenost', 'nav.capabilities': 'Mogućnosti', 'nav.pricing': 'Cijene', 'nav.setup': 'Postavljanje prijamnika', 'nav.faq': 'FAQ', 'common.buy': 'Kupi sada', 'common.close': 'Zatvori',
      'hero.title': 'RTK korekcije visoke preciznosti za regionalnu TruePoint mrežu.', 'hero.primary': 'Pokreni godišnju pretplatu', 'hero.secondary': 'Prikaži pokrivenost',
      'coverage.exclusive': 'Ekskluzivna distribucijska područja', 'coverage.online': 'Područja online prodaje', 'pricing.title': 'Jasne pretplatničke razine.', 'pricing.choose': 'Odaberi paket', 'pricing.beforeTax': 'prije poreza', 'setup.title': 'Upute za konfiguraciju GNSS prijamnika.', 'faq.title': 'Česta pitanja.', 'footer.admin': 'Administracija', 'checkout.country': 'Država usluge'
    },
    ro: {
      'language.label': 'Limbă', 'nav.coverage': 'Acoperire', 'nav.capabilities': 'Capabilități', 'nav.pricing': 'Prețuri', 'nav.setup': 'Configurare receptor', 'nav.faq': 'FAQ', 'common.buy': 'Cumpără acum', 'common.close': 'Închide',
      'hero.title': 'Corecții RTK regionale TruePoint cu precizie de topografie.', 'hero.body': 'Abonați-vă online la corecții NTRIP multi-frecvență, bazate pe cloud, susținute de tehnologia BDSTAR și distribuite de Topomarket IKE.', 'hero.primary': 'Începe abonamentul anual', 'hero.secondary': 'Vezi acoperirea',
      'coverage.title': 'Acoperire construită pe drepturi comerciale regionale.', 'coverage.exclusive': 'Teritorii cu distribuție exclusivă', 'coverage.online': 'Teritorii cu drepturi de vânzare online',
      'pricing.title': 'Niveluri transparente de abonament.', 'pricing.choose': 'Alege planul', 'pricing.best': 'Cea mai bună valoare', 'pricing.beforeTax': 'fără taxe',
      'setup.title': 'Ghiduri de configurare pentru receptoare GNSS.', 'faq.title': 'Întrebări frecvente.', 'footer.admin': 'Panou administrare', 'checkout.title': 'Finalizează abonamentul', 'checkout.country': 'Țara serviciului'
    },
    et: {
      'language.label': 'Keel', 'nav.coverage': 'Katvus', 'nav.capabilities': 'Võimekus', 'nav.pricing': 'Hinnad', 'nav.setup': 'Vastuvõtja seadistus', 'nav.faq': 'KKK', 'common.buy': 'Osta kohe', 'common.close': 'Sulge',
      'hero.title': 'Kõrgtäpsed RTK parandused TruePointi piirkondlikule võrgule.', 'hero.primary': 'Alusta aastast tellimust', 'hero.secondary': 'Vaata katvust',
      'coverage.exclusive': 'Eksklusiivsed levitusterritooriumid', 'coverage.online': 'Online-müügi territooriumid', 'pricing.title': 'Selged tellimustasemed.', 'pricing.choose': 'Vali plaan', 'pricing.beforeTax': 'ilma maksuta', 'setup.title': 'GNSS-vastuvõtjate seadistusjuhised.', 'faq.title': 'Korduma kippuvad küsimused.', 'footer.admin': 'Adminipaneel', 'checkout.country': 'Teenuse riik'
    },
    lv: {
      'language.label': 'Valoda', 'nav.coverage': 'Pārklājums', 'nav.capabilities': 'Iespējas', 'nav.pricing': 'Cenas', 'nav.setup': 'Uztvērēja iestatīšana', 'nav.faq': 'BUJ', 'common.buy': 'Pirkt tagad', 'common.close': 'Aizvērt',
      'hero.title': 'Augstas precizitātes RTK korekcijas TruePoint reģionālajam tīklam.', 'hero.primary': 'Sākt gada abonementu', 'hero.secondary': 'Skatīt pārklājumu',
      'coverage.exclusive': 'Ekskluzīvas izplatīšanas teritorijas', 'coverage.online': 'Tiešsaistes pārdošanas teritorijas', 'pricing.title': 'Caurspīdīgi abonementu līmeņi.', 'pricing.choose': 'Izvēlēties plānu', 'pricing.beforeTax': 'bez nodokļiem', 'setup.title': 'GNSS uztvērēju konfigurācijas vadlīnijas.', 'faq.title': 'Biežāk uzdotie jautājumi.', 'footer.admin': 'Administrācijas panelis', 'checkout.country': 'Pakalpojuma valsts'
    },
    lt: {
      'language.label': 'Kalba', 'nav.coverage': 'Aprėptis', 'nav.capabilities': 'Galimybės', 'nav.pricing': 'Kainos', 'nav.setup': 'Imtuvo nustatymas', 'nav.faq': 'DUK', 'common.buy': 'Pirkti dabar', 'common.close': 'Uždaryti',
      'hero.title': 'Aukšto tikslumo RTK pataisos regioniniam TruePoint tinklui.', 'hero.primary': 'Pradėti metinę prenumeratą', 'hero.secondary': 'Peržiūrėti aprėptį',
      'coverage.exclusive': 'Išskirtinės platinimo teritorijos', 'coverage.online': 'Internetinės prekybos teritorijos', 'pricing.title': 'Skaidrūs prenumeratos lygiai.', 'pricing.choose': 'Pasirinkti planą', 'pricing.beforeTax': 'be mokesčių', 'setup.title': 'GNSS imtuvų konfigūravimo gairės.', 'faq.title': 'Dažnai užduodami klausimai.', 'footer.admin': 'Administravimo skydelis', 'checkout.country': 'Paslaugos šalis'
    },
    hu: {
      'language.label': 'Nyelv', 'nav.coverage': 'Lefedettség', 'nav.capabilities': 'Képességek', 'nav.pricing': 'Árak', 'nav.setup': 'Vevő beállítása', 'nav.faq': 'GYIK', 'common.buy': 'Vásárlás', 'common.close': 'Bezárás',
      'hero.title': 'Nagy pontosságú RTK korrekciók a regionális TruePoint hálózathoz.', 'hero.primary': 'Éves előfizetés indítása', 'hero.secondary': 'Lefedettség megtekintése',
      'coverage.exclusive': 'Exkluzív disztribúciós területek', 'coverage.online': 'Online értékesítési területek', 'pricing.title': 'Átlátható előfizetési csomagok.', 'pricing.choose': 'Csomag kiválasztása', 'pricing.beforeTax': 'adó nélkül', 'setup.title': 'GNSS vevők konfigurációs útmutatója.', 'faq.title': 'Gyakran ismételt kérdések.', 'footer.admin': 'Admin panel', 'checkout.country': 'Szolgáltatási ország'
    }
  };

  const countryNames = {
    en: {},
    el: { CY: 'Κύπρος', MK: 'Βόρεια Μακεδονία', ME: 'Μαυροβούνιο', BA: 'Βοσνία και Ερζεγοβίνη', HR: 'Κροατία', RO: 'Ρουμανία', MD: 'Μολδαβία', EE: 'Εσθονία', LV: 'Λετονία', LT: 'Λιθουανία', RS: 'Σερβία', HU: 'Ουγγαρία', TR: 'Τουρκία' },
    tr: { CY: 'Kıbrıs', MK: 'Kuzey Makedonya', ME: 'Karadağ', BA: 'Bosna-Hersek', HR: 'Hırvatistan', RO: 'Romanya', MD: 'Moldova', EE: 'Estonya', LV: 'Letonya', LT: 'Litvanya', RS: 'Sırbistan', HU: 'Macaristan', TR: 'Türkiye' },
    ro: { CY: 'Cipru', MK: 'Macedonia de Nord', ME: 'Muntenegru', BA: 'Bosnia și Herțegovina', HR: 'Croația', RO: 'România', MD: 'Moldova', EE: 'Estonia', LV: 'Letonia', LT: 'Lituania', RS: 'Serbia', HU: 'Ungaria', TR: 'Turcia' },
    hu: { CY: 'Ciprus', MK: 'Észak-Macedónia', ME: 'Montenegró', BA: 'Bosznia-Hercegovina', HR: 'Horvátország', RO: 'Románia', MD: 'Moldova', EE: 'Észtország', LV: 'Lettország', LT: 'Litvánia', RS: 'Szerbia', HU: 'Magyarország', TR: 'Törökország' }
  };

  window.TruePointI18n = { en, localized, countryNames };
})();
