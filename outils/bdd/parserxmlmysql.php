<?php
/** coding: utf-8 */
/**parser
 v23092005 necessite simple xml et rien d'autre
 v07012008 mies des fonctions dans un ficher annexe
 v03022008 ajout des echeances
 ajout des types d'operations par comptes
 suppression de la table 'typecat'
 */
/**-------------
 importation dans mysql d'un fichier grisbi

TODO gestion des etats

 verifier les tiers
 prendre les bons numeros dans idgrsibi pour les tiers et les categories
 */
require_once 'functions.php';
$titre   = "parser";
$xmlfile = '../20040701.gsb';
/*----------------principal--------------------*/
$maxnivlog = 2;
$starttime = microtime(true);
register_shutdown_function(create_function('$s', 'echo (microtime(true)-$s);'), $starttime);
//on recupere les noms des tables
$db = new MySQLConnector('localhost', 'root', '', 'grisbi');
//on ouvre le fichier xml en simplexml
$xml = simplexml_load_file($xmlfile);
$version = (string) $xml->Generalites->Version_fichier;
$db->debogagesql = false;
//verification du numero de version
if ($version <> "0.5.0") {
    throw new Exception (" attention, votre fichier grisbi qui se trouve etre en version $version n'est pas en version 0.5.0.et ce logiciel n'importe que cette version pour l'instant.");
}
//attention l'ordre des tables a une importance.on doit effacer les tables dependantes avant.

foreach (array('generalite', 'ope', 'echeance', 'rapp', 'moyen', 'compte', 'scat', 'cat', 'exercice', 'sib', 'ib', 'banque', 'titre', 'devise', 'tiers') as $r) {
        $db->q("delete from $r;");
}
aff('tables effacés');
aff('-----------------------------------------------------------');
ob_implicit_flush(true);
aff("debut de l'import");
//gestion des devises
$nbdevises = 0;
$nbdevisesmax = (int) $xml->Devises->Generalites->Nb_devises;

foreach ($xml->Devises->Detail_des_devises->Devise as $devise) {
    $nbdevises++;
    $q_devises = array();//id de la devise meme remarque que les autres id
    $q_devises['id'] = (int) $devise['No'] + 1;// nom de la devise
    $q_devises['nom'] = $db->ins($devise['Nom']);//tx de change entre la devise principale et cette devise
    $tx = util::fr2uk($devise['Change']);
    if ($tx === 0.0){
        $q_devises['dernier_tx_de_change'] = 1;
    } else {
        $q_devises['dernier_tx_de_change'] = $tx;
    }
    $d = util::datefr2date($devise->Date_dernier_change);
    $q_devises['date_dernier_change'] = ($d === NULL)?util::now():$d;
    $q_devises['isocode'] = $db->ins($devise['IsoCode']);//code iso
    //  $q_devises['passe_euro'] = $db->ins($devise['Passage_euro']);    //est ce que cette monnaie est passe e l'euro
    //  $q_devises['Rapport_entre_devises'] = $db->ins($devise['Rapport_entre_devises']);    //rapport avec une devise non principale
    //cf "Rapport_entre_devises"
    //gerer la possibilite que la devise ne soit pas 0
    //$q_devises['devise_de_reference'] = ($devise['Devise_en_rapport'] == 0) ? $q_devises['id'] : $devise['Devise_en_rapport'] + 1;    //date de derniere date de change
    //verification
    if ($nbdevises > $nbdevisesmax) {
        throw new Exception  ('probleme de coherence à la devise "'.(string) $devise['Nom'].'" car il y a plus de devises que le max devise');
    }

    $db->insert('devise', $q_devises);
}//end foreach
unset ($q_devises);
echo $nbdevises.' devises inserées'.N;
aff('-----------------------------------------------------------');

//on s'occupe du volet global
$general = array('id' => 1);

//$general['Version_fichier'] = (string) $xml->Generalites->Version_fichier;

//actuellement la seule version existante est 0.5.0
// $general['Version_grisbi'] = (string) $xml->Generalites->Version_grisbi;//meme remarque que precedemente
// $general['Fichier_ouvert'] = (string) $xml->Generalites->Fichier_ouvert;//1 si le fichier declare deja ouvert par gribsi
// $general['Backup'] = (string) $xml->Generalites->Backup;

$general['Titre'] = $db->ins((string) $xml->Generalites->Titre);

// $general['Adresse_commune'] = (string) $xml->Generalites->Adresse_commune;
// $general['Adresse_secondaire'] = (string) $xml->Generalites->Adresse_secondaire;

$general['Utilise_exercices'] = (int) $xml->Generalites->Utilise_exercices;
$general['Utilise_IB'] = (int) $xml->Generalites->Utilise_IB;
//0: n'utilise pas les imp et les simp
//1: les utilises
$general['Utilise_PC'] = (int) $xml->Generalites->Utilise_PC;
//0: n'utilise pas les pieces comptables
//1: les utilises
//$general['Utilise_info_BG'] = (int) $xml->Generalites->Utilise_info_BG;
$general['devise_generale_id'] = $db->ins((int) $xml->Generalites->Numero_devise_totaux_tiers + 1);

// $general['Type_affichage_des_echeances'] = (string) $xml->Generalites->Type_affichage_des_echeances;
// $general['Affichage_echeances_perso_nb_libre'] = (string) $xml->Generalites->Affichage_echeances_perso_nb_libre;
// $general['Type_affichage_perso_echeances'] = (string) $xml->Generalites->Type_affichage_perso_echeances;
// $general['Chemin_logo'] = (string) $xml->Generalites->Chemin_logo;
// //chemin absolu du logo qui s'affiche dans grisbi
// $general['Affichage_opes'] = (string) $xml->Generalites->Affichage_opes;
// $general['Rapport_largeur_col'] = (string) $xml->Generalites->Rapport_largeur_col;
// $general['Ligne_aff_une_ligne'] = (string) $xml->Generalites->Ligne_aff_une_ligne;
// $general['Lignes_aff_deux_lignes'] = (string) $xml->Generalites->Lignes_aff_deux_lignes;
// $general['Lignes_aff_trois_lignes'] = (string) $xml->Generalites->Lignes_aff_trois_lignes;

$db->insert('generalite', $general);
unset($general);
aff("generalités ok");
aff('-----------------------------------------------------------');
//extraction des données des tiers
$nbtiers = 0;
$nbtiersmax = (int) $xml->Tiers->Generalites->Nb_tiers;
$db->total = true;
foreach ($xml->xpath('//Tiers') as $tiers) {
    if ((string) $tiers['No'] <> "") {
    //il y a le node maitre qui n'a rien.de plus verification qu'il y a index existant
        $nbtiers++;
        $q_tiers = array();
        $q_tiers['id'] = (int) $tiers['No'] + 1;
        //numero d'index du tiers.attention les numeros peuvent ne pas se suivre en cas de suppression de tiers
        //car les numeros des tiers supprimes ne sont pas reutilises
        $q_tiers['nom'] = $db->ins($tiers['Nom']);
        //nom du tiers
        $q_tiers['notes'] = $db->ins($tiers['Informations']);
        // commentaires
        //verification
        if ($nbtiers > $nbtiersmax) {
            throw new Exception ('probleme de coherence au tiers Ne '.$tiers['No'].' : "'.(string) $tiers['Nom'].'" car il y a plus de tiers que le max tiers');
        }
        $db->insert('tiers', $q_tiers);
    }
    if ($nbtiers % 50 === 0) {
        aff($nbtiers.' tiers inserés');
    }
}
$db->save('tiers');
aff($nbtiers.' tiers inserés');
aff('-----------------------------------------------------------');

//insertion des categories
$nbcategories = 0;
$nbcategoriesmax = (int) $xml->Categories->Generalites->Nb_categories;
$q_cat = array();
$nb_tot_scat = 0;
foreach ($xml->xpath('//Categorie') as $categorie) {
    $nbcategories++;
    $q_cat['id'] = $db->ins($categorie['No']) + 1;
    $q_cat['nom'] = $db->ins($categorie['Nom']);
    switch ((int) $categorie['Type']) {
        case 0:
            $q_cat['typecat'] = 'd';
            break;
        case 1:
            $q_cat['typecat'] = 'r';
            break;
        case 2:
            $q_cat['typecat'] = 'v';
            break;
        default:
            throw new Exception ("id de la categorie ".$q_cat['id']." inconnu");
            break;
    }
    //0 depenses
    //1 recettes
    //verification
    if ($nbcategories > $nbcategoriesmax) {
        echo $nbcategories.N.$nbcategoriesmax.N;
        throw new Exception ('probleme de coherence à la categorie "'.(string) $categorie['Nom'].'" car il y a plus de categories que le max categorie');
    }
    $db->insert('cat', $q_cat);
    unset($q_cat);
    //on s'occupe des sous categories
    $predicat = '//Categorie[@Nom = \''.$categorie['Nom'].'\']/Sous-categorie';
    $nbscat = 0;
    foreach ($xml->xpath($predicat) as $scat) {
        $nbscat++;
        $nb_tot_scat++;
        $q_scat['id'] = $nb_tot_scat + 1;
        $q_scat['nom'] = $db->ins($scat['Nom']);
        $q_scat['grisbi_id'] = (int) $scat['No'] + 1;
        $q_scat['cat_id'] = (int) $categorie['No'] + 1;
        //verification
        if ($nbscat > (int) $categorie['No_derniere_sous_cagegorie']) {
            throw new Exception ('probleme de coherence à la categorie "'.(string) $scat['Nom'].'" car il y a plus de categories que le max sous categorie');
        }
        //bdd
        $db->insert('scat', $q_scat);
    }
}//end foreach
aff($nbcategories.' categories insérés et '.$nb_tot_scat.' inseres');
aff('-----------------------------------------------------------');
//insertion des ibs
$nbibs = 0;
$nbibsmax = (int) $xml->Imputations->Generalites->Nb_imputations;
$q_ib = array();
$nb_tot_sib = 0;
foreach ($xml->xpath('//Imputation') as $ibs) {
    $nbibs++;
    $q_ib['id'] = (int) $ibs['No'] + 1;
    $q_ib['nom'] = $db->ins($ibs['Nom']);
    switch ((int) $ibs['Type']) {
        case 0:
            $q_ib['typeimp'] = 'd';
            break;
        case 1:
            $q_ib['typeimp'] = 'r';
            break;
        case 2:
            $q_ib['typeimp'] = 'v';
            break;
        default:
            throw new Exception ("id de la categorie ".$q_cat['id']." inconnu");
            break;
    }
    //0 depenses
    //1 recettes
    //verification
    if ($nbibs > $nbibsmax) {
        echo $nbibs.N.$nbibsmax.N;
        throw new Exception ('probleme de coherence à la ibs "'.(string) $ibs['Nom'].' '.$q_ib['id'].'" car il y a plus de ibs que le max ibs');
    }
    $db->insert('ib', $q_ib);
    unset($q_ib);
    //on s'occupe des sous ibs
    $predicat = '//Imputation[@Nom = \''.$ibs['Nom'].'\']/Sous-imputation';
    $nbsib = 0;
    foreach ($xml->xpath($predicat) as $sib) {
        $nbsib++;
        $nb_tot_sib++;
        $q_sib['id'] = $nb_tot_sib;
        $q_sib['nom'] = $db->ins($sib['Nom']);
        $q_sib['grisbi_id'] = (int) $sib['No'] + 1;
        $q_sib['ib_id'] = (int) $ibs['No'] + 1;
        //verification
        if ($nbsib > (int) $ibs['No_derniere_sous_imputation']) {
            throw new Exception ('probleme de coherence à la ibs "'.(string) $sib['Nom'].'" car il y a plus de ibs '.$nbsib.'que le max sous ibs'.(int) $ibs['No_derniere_sous_imputation'] );
        }
        //bdd
        $db->insert('sib', $q_sib);
    }
}//end foreach
aff($nbibs.' ibs et '.$nb_tot_sib.' sib insérés');
aff('-----------------------------------------------------------');

//gestion des banques
$nbbanques = 0;
$nbbanquesmax = (int) $xml->Banques->Generalites->Nb_banques;
foreach ($xml->xpath('//Detail_des_banques/Banque') as $banque) {
    $nbbanques++;

    $q_ban['id'] = (int) $banque['No'] + 1;
    $q_ban['nom'] = $db->ins($banque['Nom']);
    $q_ban['cib'] = $db->ins($banque['Code']);
    // $q_ban['adresse'] = $db->ins($banque['Adresse']);
    // $q_ban['tel'] = $db->ins($banque['Tel']);
    // $q_ban['mail'] = $db->ins($banque['Mail']);
    // $q_ban['web'] = $db->ins($banque['Web']);
    // $q_ban['nom_corres'] = $db->ins($banque['Nom_correspondant']);
    // $q_ban['tel_corres'] = $db->ins($banque['Tel_correspondant']);
    // $q_ban['fax_corres'] = $db->ins($banque['Fax_correspondant']);
    // $q_ban['mel_corres'] = $db->ins($banque['Mel_correspondant']);
    $q_ban['notes'] = $db->ins($banque['Remarques']);

    //verification
    if ($nbbanques > $nbbanquesmax) {
        throw new Exception ('probleme de coherence à la banque "'.(string) $banque['Nom'].'" car il y a plus de banques que le max banques');
    }
    $result = $db->insert('banque', $q_ban);
}//end foreach
aff($nbbanques.' banques insérées');
unset($q_ban);



aff('-----------------------------------------------------------');
//gestion des comptes
$nbcompte = 0;
foreach ($xml->xpath('//Compte/Details') as $compte) {
    $nbcompte++;
    $q_cpt['nom'] = $db->ins($compte->Nom);
    $q_cpt['id'] = (int) $compte->No_de_compte + 1;//pour eviter l'auto increment
    $q_cpt['titulaire'] = $db->ins($compte->Titulaire);
    $tb = (int) $compte->Type_de_compte;
    switch ($tb) {
    //0 = bancaire, 1 = espece, 2 = passif, 3 = actif
        case 0:
            $q_cpt['type'] = "b";
            break;
        case 1:
            $q_cpt['type'] = "e";
            break;
        case 2:
            $q_cpt['type'] = "p";
            break;
        case 3:
            $q_cpt['type'] = "a";
            break;
        default:
            throw new Exception ("type du compte ".$q_cpt['id']." inconnu");
            break;
    }
    $q_cpt['devise_id'] = (int) $compte->Devise + 1;
    $q_cpt['banque_id'] = $db->ins((int) $compte->Banque + 1);
    $q_cpt['guichet'] = (int) $compte->Guichet;
    $q_cpt['num_compte'] = $db->ins($compte->No_compte_banque);//numero du compte (rib)
    $q_cpt['cle_compte'] = (int) $compte->Cle_du_compte;
    $q_cpt['solde_init'] = util::fr2uk((string) $compte->Solde_initial);
    $q_cpt['solde_mini_voulu'] = util::fr2uk((string) $compte->Solde_mini_voulu);
    $q_cpt['solde_mini_autorise'] = util::fr2uk((string) $compte->Solde_mini_autorise);
    // $q_cpt['solde_courant'] = util::fr2uk((string) $compte->Solde_courant);
    $q_cpt['date_dernier_releve'] = util::datefr2date($compte->Date_dernier_releve);
    $q_cpt['solde_dernier_releve'] = util::fr2uk((string) $compte->Solde_dernier_releve);
    $q_cpt['cloture'] = (int) $compte->Compte_cloture; // si = 1 => cloture
    // $q_cpt['Affichage_r'] = $compte->Affichage_r;

    $q_cpt['notes'] = $db->ins($compte->Commentaires);

    // $q_cpt['adresse_du_titulaire'] = $db->ins($compte->Adresse_du_titulaire);
    // //nombre_de_types : nombre des types differents par comptes
    // $q_cpt['type_defaut_debit'] = $db->ins($compte->Type_defaut_debit);
    // $q_cpt['type_defaut_credit'] = $db->ins($compte->Type_defaut_credit);
    // $q_cpt['tri_par_type'] = $db->ins($compte->Tri_par_type);// si = 1 => tri en fonction des types, si 0 => des dates
    // $q_cpt['neutres_inclus'] = $db->ins($compte->Neutres_inclus);
    // $q_cpt['ordre_du_tri'] = $db->ins($compte->Ordre_du_tri);//contient la liste des types dans l'ordre du tri du compte
    $result = $db->insert('compte', $q_cpt);
    unset($q_cpt);
}//end foreach
aff($nbcompte.' comptes insérés');

aff(N.'-----------------------------------------------------------'.N);
//gestion des types de paiments
$nb_tot_sib = 0;
foreach ($xml->xpath('//Type') as $type) {
    $nb_tot_sib++;
    $q_type['id'] = $nb_tot_sib;
    $q_type['grisbi_id'] = (int) $type['No'] + 1;
    $q_type['nom'] = $db->ins($type['Nom']);
    $q_type['signe'] = (int) $type['Signe'] + 1;
    //0 pour les virements
    //1 pour les depenses
    //2 pour les recettes
    $q_type['affiche_numero'] = $db->ins($type['Affiche_entree']);
    $q_type['num_auto'] = $db->ins($type['Numerotation_auto']);
    $q_type['num_en_cours'] = (int) $type['No_en_cours'];
    $q_type['compte_id'] = (int) implode('', $type->xpath('../../Details/No_de_compte')) + 1;//implode car xpath renvoie un tableau
    $result = $db->insert('moyen', $q_type);
}
aff($nb_tot_sib.' moyens insérées');
aff('-----------------------------------------------------------');
//gestion des rapprochements

$nbrapps = 0;
foreach ($xml->xpath('//Rapprochement') as $rapp) {
    $nbrapps++;
    $id = (int) $rapp['No'];
    $q_rapp['id'] = $id + 1;
    $q_rapp['nom'] = $db->ins($rapp['Nom']);
    //but trouver les operation et donc le compte qui se rapportent a ce rapprochement
    $xpath = "//Operation[@R = '".$id."']";
    $ope = $xml->xpath($xpath);
    if (!empty($ope)) {
        $ope = $ope[0];
        $idcompte = $ope->xpath('../../Details/No_de_compte');//recuperation du no du compte
        $q_rapp['compte_id'] = (int) $idcompte[0] + 1;
    }
    $result = $db->insert('rapp', $q_rapp);
    unset($q_rapp);
}
aff($nbrapps.' rapps insérées');
aff('-----------------------------------------------------------');
//gestion des exercices
$nbexo = 0;
$nbexomax = (int) $xml->Exercices->Generalites->No_dernier_exercice;
foreach ($xml->xpath('//Exercice') as $exo) {
    $nbexo++;
    $q_exo['id'] = (int) $exo['No'] + 1;
    $q_exo['date_debut'] = util::datefr2date($exo['Date_debut']);
    if ($q_exo['date_debut'] == null) {
        throw new InvalidArgumentException("la date de debut de l'exercice n° '".$q_exo['id']."' est invalide");
    }
    $q_exo['date_fin'] = util::datefr2date($exo['Date_fin']);
    $q_exo['nom'] = $db->ins($exo['Nom'], true);
    // $q_exo['nom'] = (int) $exo['Visible'], true);
    if ($nbexo > $nbexomax) {
        throw new Exception ('probleme de coherence à l\'exercice n° "'.(string) $exo['No'].'" car il y a plus d\'exercice que le max exo');
    }
    $result = $db->insert('exercice', $q_exo);
    unset($q_exo);
}
aff($nbexo.' exercices insérées');

//gestion des operations
aff('-----------------------------------------------------------');
$nbope = 0;
$nbopemax = (int) $xml->Generalites->Numero_derniere_operation;
$ope_exist = array();
$db->total = true;
foreach ($xml->xpath('//Operation') as $ope) {
    $nbope++;
    $q_ope['id'] = (int) $ope['No'] + 1;//numero de l'operation
    $ope_exist[] = $q_ope['id'];
    $catexist = 0;
    $idcompte = $ope->xpath('../../Details/No_de_compte');//recuperation du no du compte
    $q_ope['compte_id'] = (int) $idcompte[0] + 1;
    $q_ope['date'] = util::datefr2date($ope['D'], true); //date de l'operation
    $q_ope['date_val'] = util::datefr2time($ope['Db'], true);//date de valeur
    $q_ope['montant'] = util::fr2uk($ope['M']);//montant
    $q_ope['devise_id'] = (int) $ope['De'] + 1;//devise utilisé
    //Rdc//TODO est ce vraiment utile?
    //Tc taux de change utilisé dans l'operation de change//TODO
    //Fc frais de change utilisés dans l'operations//TODO
    $q_ope['tiers_id'] = $db->ins((int) $ope['T'] + 1);//tiers
    $q_ope['cat_id'] = $db->ins((int) $ope['C'] + 1);//categorie
    $q_ope['scat_id'] = $db->ins((int) $ope['Sc'] + 1);//souscat
    $q_ope['notes'] = $db->ins($ope['N']);//note
    $q_ope['moyen_id'] = $db->ins((int) $ope['Ty'] + 1);//type de paiment de l'operation
    $moyen = $db->ins($ope['Ct'] + 1);
    $q_ope['numcheque'] = ($moyen === NULL)?'':$moyen;//type de paiment de l'operation
    $pointage = (int) $ope['P'];
    switch ($pointage) { //0 = rien, 1 = pointée, 2 = rapprochée,
        case 0:
            $q_ope['pointe'] = "na";
            break;
        case 1:
            $q_ope['pointe'] = "p";
            break;
        case 2:
            $q_ope['pointe'] = "r";
            break;
        default:
            throw new Exception ("type du pointage de l'ope ".$q_ope['id']." inconnu");
            break;
    }
    $q_ope['rapp_id'] = $db->ins((int) $ope['R'] + 1);//numero rapprochement
    $q_ope['exercice_id'] = $db->ins((int) $ope['E'] + 1);// exercice de l'ope
    $q_ope['ib_id'] = $db->ins((int) $ope['I'] + 1);//numero de l'imputation budgetaire
    $q_ope['sib_id'] = $db->ins((int) $ope['Si'] + 1);//numero de la sous imputation budgetaire
    // $q_ope['idpcompt'] = $db->ins($ope['Pc'] + 1);//no_piece_comptable
    if (((int) $ope['Ro']) + 1!=1) {
        $q_ope['jumelle_id'] = $ope['Ro'] + 1;//transaction jumelle
    }else{
        $q_ope['jumelle_id'] = NULL;
    }
    if ((int)$ope['Va']!==0) {
        $q_ope['mere_id'] = $ope['Va'] + 1;//id de l'operation mere pour les operations ventiles
    }
    $q_ope['is_mere']=(int)$ope['Ov'];
    // $q_ope['infobq'] = $ope['Ibg'];//idbanque
    //verification
    if ($nbope > $nbopemax) {
        throw new Exception ('probleme de coherence à l\'operation n° "'.(string) $ope['No'].'" car il y a plus d\'ope que le max ope');
    }
    $result = $db->insert('ope', $q_ope);
    unset($q_ope);
    if ($nbope % 200 == 0) echo $nbope.' opes inserés'.N;
}//end foreach
$db->save('ope');
aff($nbope.' ope insérées');
/*
//gestion des rapprochements
aff('-----------------------------------------------------------');
//gestion des echeances
$nbecheances = 0;
$nbecheancesmax = (int) $xml->Echeances->Generalites->Nb_echeances;
foreach ($xml->xpath('//Echeance') as $ech) {
    $nbecheances++;
    $q_ech['id'] = $ech['No'] + 1;
    $q_ech['date_ech'] = util::datefr2date($ech['Date']);
    $q_ech['compte_id'] = $ech['Compte'] + 1;
    $q_ech['montant'] = util::fr2uk($ech['Montant']);
    $q_ech['devise_id'] = $ech['Devise'] + 1;
    $q_ech['tiers_id'] = $db->ins((int) $ech['Tiers'] + 1);
    $q_ech['cat_id'] = $db->ins((int) $ech['Categorie'] + 1);
    $q_ech['scat_id'] = $db->ins((int) $ech['Sous-categorie'] + 1);
    $q_ech['compte_virement_id'] = $db->ins((int) $ech['Virement_compte'] + 1);
    $q_ech['moyen_id'] = $db->ins((int) $ech['Type'] + 1);
    $q_ech['moyen_virement_id'] = $db->ins((int) $ech['Contenu_du_type'] + 1);
    $q_ech['exercice_id'] = $db->ins((int) $ech['Exercice'] + 1);// je l'enleve car c'est pas ca.//TODO
    $q_ech['ib_id'] = $db->ins((int) $ech['Imputation'] + 1);
    $q_ech['sib_id'] = $db->ins((int) $ech['Sous-imputation'] + 1);
    $q_ech['notes'] = $db->ins($ech['Notes']);
    $q_ech['inscription_automatique'] = (int) $ech['Automatique'];
    $q_ech['periodicite'] = (int) $ech['Periodicite'];
    $q_ech['intervalle'] = (int) $ech['Intervalle_periodicite'];
    $q_ech['periode_perso'] = (int) $ech['Periodicite_personnalisee'];
    $q_ech['date_limite'] = util::datefr2date($ech['Date_limite']);
    if ($nbecheances > $nbecheancesmax) {
        throw new Exception('probleme de coherence à l\'imputation "'.(string) $nbecheances['Nom'].'" car il y a plus d\' imputations que le max imputation');
    }
    $result = $db->insert('echeance', $q_ech);
}//end foreach
aff($nbecheances." échéances");
//gestion des etats
