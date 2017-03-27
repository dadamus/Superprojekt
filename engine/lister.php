<?php
require "../config.php";

function writeLine($string) {
    echo "<p>$string</p>";
}

function pushClient($id, $name) {
    global $data_src, $directories, $user_name;
    for ($i = 0; $i < count($directories); $i++) {
        $e_dir = explode("-", $directories[$i]);
        $min = intval($e_dir[0]);
        $max = intval($e_dir[1]);
        if ($min <= $id && $id <= $max) {
            $src = $data_src . $directories[$i] . "/" . $id . "_" . $name;
            mkdir($src, 0777);
            chown($src, $user_name);
            mkdir($src . "/PROJEKTY", 0777);
            chown($src . "/PROJEKTY", $user_name);
            return true;
        }
    }
    return false;
}

function makeDirectory($id, $name) {
    global $data_src, $directories, $user_name;
    foreach (glob($data_src . "*", GLOB_ONLYDIR) as $directory) {
        array_push($directories, end((explode("/", $directory))));
    }

    if (pushClient($id, $name) == false) {
        $min = $id;
        if ($min > 1) {
            $max = $min + 49;
        } else {
            $max = 49;
        }
        $directory = $min . "-" . $max;
        if (mkdir($data_src . $directory, 0777) == false) {
            die($data_src . $directory);
        }
        chown($data_src . $directory, $user_name);
        mkdir($data_src . $directory . "/" . $id . "_" . $name, 0777);
        chown($data_src . $directory . "/" . $id . "_" . $name, $user_name);
        mkdir($data_src . $directory . "/" . $id . "_" . $name . "/PROJEKTY", 0777);
        chown($data_src . $directory . "/" . $id . "_" . $name . "/PROJEKTY", $user_name);
    }
}

writeLine("Inicjalizacja");

echo '<html><body>';

$data_string = "75_DAS_AG:93_Basiaga_Piotr_OPTIGLAS:44_AIFO:9_Tymbark:81_Maksymilian Murzyn:23_Biel Wojciech:148_MARKOM:66_MailABL:48_Śliwa:17_Rokstal:85_Katowiczak:82_Lttermann:159_Hubert Jawień:61_Marian:149_Anna_Blue:119_Paweł_Węgrzyn:116_RST:14_Verte:31_MetChem:39_Konsorcjum:137_Z_Przebierała:26_Rak:120_Hutmet:47_StalMet:54_Kowal:59_Talar:40_FraKro:129_Zakład Grabo:68_Grzesiek Sobczyk:96_OSP_JODŁOWNIK:52_Garaz_G33A:78_Medite:33_Mroz:1_Pojedyńcze zlecenia:99_Fach:100_Marcin_Nowak_BANK:60_BABRON:135_EXPERT:123_Konieczny:72_MIA_SHOP_CONCEPT:41_Schody:91_Pawel_Termion:20_MigSystem:35_Kowalski:12_GPT:161_STAKO:84_Stalkwas:144_KUCZMA:90_Szymon_Dutka:134_Spaw-Mech:65_JARO:29_DoniceKonrad:113_Montherm:145_airportmszcz:139_Smart-TG:97_Kazimierz_Kuflewski_ALSAT:25_DoniceStalowe:160_Ogrodzenia:104_Madros:21_Konrad:56_Garaże Szwajcaria:92_Wiesław_Matuszczyk:74_Elsen:130_Noworolnik Kamil:37_[empty37]:16_Roboty:24_Tokarz Mieczysław:67_Sebastian:86_Skraw-Mech:106_Altim:42_BOMBRAM:73_Radosław Kowalczyk:162_KATCON:112_Ozdoby_rdza:83_Bramsteel:114_Kelmet:152_Leier:38_SportsCar:103-Hejmo:98_RADMOR:150_Culinary Solutions:143_Dohmeyer:95_Marcin Kowalski:45_Amada:165_ŁukaszStanowski:57_Komin wojcik:107_KurnikKONSTAL:50_AKPO:49_Hydrosprzęt:153_Kosmider:146_Piotr Jakubowski:163_MarcinŁomzik:111_Stanisław-Gocal:22_Mariusz:124_FNS:8_Barierki:6_OZDOBY:141_ART-WIN:2_Bomstal:128_Hubert_Kowalczyk:70_PDM Technics:3_GDDKiA:142_NOVART:121_Profis.pl:58_Piotrek ELEKTRON:77_Blaszak:10_OSP Szczyrzyc:64_[empty]:0_PRZYKLAD:46_Bomstal_slowacja:156_Artur_Artpiw:88_Bonarek.Wojciech:126_HumanOffice:151_Didex_Stal:102_Edesa:53_Stradowski:89_Adam_Nowak:36_Knap:155_ALSTAL:4_Lamafer:118_AMZ- Kutno:131_BEST-POL:164_Maritom:105_Lorac:79_PROFILE STALSERWIS:32_pthsteel:157_Paweł_Sroka:43_Zygmunt:117_DV INDUSTRIESERVICE:80_PARTNERSTAL:115_Wojciech Zarzeka:132_Tomasz Kohla:15_Kordas:71_Marcin Pazdan:101_FRANCUZ:147_AluSystemPlus:167_Józef_Gocal:34_Wamech:13_EEC:76_Konsorcjum:18_SPE:62_GABEX:63_[empty]:51_Fakro:28_maciek:27_ENERIDEA:7_Dubiel Vitrum:133_Franczyk:158_Czech:125_BefraElectronic:122_Dziedzic_Stanisław:140_SMART Technology:55_Kubas:11_Wanado:138_Joniec:110_Andrzej Skowronek:30_Bartol:19_Polmar:94_Gumitex:166_Drzwi z klasą:154_Lynsky:87_Stalem.pl:5_MultiSter_Henryk_Bukowiec:136_EMIL:69_Borys:127_FRANKONIA";
    
writeLine("<b>Slicing</b>");

$data = explode(":", $data_string);

writeLine("<b>Sortowanie</b>");
$date = date("Y-m-d H:i:s");
$clients = array();
$directories = array();

for ($i = 0; $i < count($data); $i ++) {

    $client = explode("_", $data[$i]);
    $id = $client[0];
    $name = $client[1];

    $clients[$id] = $name;
}

writeLine("<b>Zapis</b>");
for ($i = 0; $i < count($clients); $i++) {
    if ($i == 0) {
        continue;
    }
    $name = $clients[$i];
    $query = $db->prepare("INSERT INTO `clients` (`name`, `date`) VALUES ('$name', '$date')");
    $query->execute();
    makeDirectory($i, $name);
    writeLine("$name dodany");
}

writeLine("Koniec");
?>
</body>
</html>