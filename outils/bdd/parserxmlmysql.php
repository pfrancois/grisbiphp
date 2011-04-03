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
$xmlfile = '../20040701.gsb';
/*----------------principal--------------------*/
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

foreach (array('generalite', 'ope', 'echeance', 'rapp', 'moyen', 'compte', 'scat', 'cat', 'exercice', 'sib', 'ib', 'banque', 'titre', 'devise', 'tiers') as $objet) {
        $db->q("delete from $objet;");
}
aff('tables effacés');
aff('-----------------------------------------------------------');
ob_implicit_flush(true);
aff("debut de l'import");
//gestion des devises
$nb = 0;
$nbmax = (int) $xml->Devises->Generalites->Nb_devises;

foreach ($xml->Devises->Detail_des_devises->Devise as $objet) {
    $q=array();
    $nb++;
    $q['id'] = (int) $objet['No'] + 1;// nom de la devise
    $q['nom'] = $db->ins($objet['Nom']);//tx de change entre la devise principale et cette devise
    $tx = util::fr2uk($objet['Change']);
    if ($tx === 0.0){
        $q['dernier_tx_de_change'] = 1;
    } else {
        $q['dernier_tx_de_change'] = $tx;
    }
    $date = util::datefr2date($objet->Date_dernier_change);
    $q['date_dernier_change'] = ($date === NULL)?util::now():$date;
    $q['isocode'] = $db->ins($objet['IsoCode']);//code iso
    //  $q['passe_euro'] = $db->ins($objet['Passage_euro']);    //est ce que cette monnaie est passe e l'euro
    //  $q['Rapport_entre_devises'] = $db->ins($objet['Rapport_entre_devises']);    //rapport avec une devise non principale
    //cf "Rapport_entre_devises"
    //gerer la possibilite que la devise ne soit pas 0
    //$q['devise_de_reference'] = ($objet['Devise_en_rapport'] == 0) ? $q['id'] : $objet['Devise_en_rapport'] + 1;    //date de derniere date de change
    //verification
    if ($nb > $nbmax) {
        throw new Exception  ('probleme de coherence à la devise "'.(string) $objet['Nom'].'" car il y a plus de devises que le max devise');
    }

    $db->insert('devise', $q);
}//end foreach
aff($nb.' devises inserées');
aff('-----------------------------------------------------------');

//on s'occupe du volet global
$q = array('id' => 1);

//$general['Version_fichier'] = (string) $xml->Generalites->Version_fichier;

//actuellement la seule version existante est 0.5.0
// $general['Version_grisbi'] = (string) $xml->Generalites->Version_grisbi;//meme remarque que precedemente
// $general['Fichier_ouvert'] = (string) $xml->Generalites->Fichier_ouvert;//1 si le fichier declare deja ouvert par gribsi
// $general['Backup'] = (string) $xml->Generalites->Backup;

$q['Titre'] = $db->ins((string) $xml->Generalites->Titre);

// $q['Adresse_commune'] = (string) $xml->Generalites->Adresse_commune;
// $q['Adresse_secondaire'] = (string) $xml->Generalites->Adresse_secondaire;

$q['Utilise_exercices'] = (int) $xml->Generalites->Utilise_exercices;
$q['Utilise_IB'] = (int) $xml->Generalites->Utilise_IB;
//0: n'utilise pas les imp et les simp
//1: les utilises
$q['Utilise_PC'] = (int) $xml->Generalites->Utilise_PC;
//0: n'utilise pas les pieces comptables
//1: les utilises
//$q['Utilise_info_BG'] = (int) $xml->Generalites->Utilise_info_BG;
$q['devise_generale_id'] = $db->ins((int) $xml->Generalites->Numero_devise_totaux_tiers + 1);

// $q['Type_affichage_des_echeances'] = (string) $xml->Generalites->Type_affichage_des_echeances;
// $q['Affichage_echeances_perso_nb_libre'] = (string) $xml->Generalites->Affichage_echeances_perso_nb_libre;
// $q['Type_affichage_perso_echeances'] = (string) $xml->Generalites->Type_affichage_perso_echeances;
// $q['Chemin_logo'] = (string) $xml->Generalites->Chemin_logo;
// //chemin absolu du logo qui s'affiche dans grisbi
// $q['Affichage_opes'] = (string) $xml->Generalites->Affichage_opes;
// $q['Rapport_largeur_col'] = (string) $xml->Generalites->Rapport_largeur_col;
// $q['Ligne_aff_une_ligne'] = (string) $xml->Generalites->Ligne_aff_une_ligne;
// $q['Lignes_aff_deux_lignes'] = (string) $xml->Generalites->Lignes_aff_deux_lignes;
// $q['Lignes_aff_trois_lignes'] = (string) $xml->Generalites->Lignes_aff_trois_lignes;

$db->insert('generalite', $q);
aff("generalités ok");
aff('-----------------------------------------------------------');
//extraction des données des tiers
$nb = 0;
$nbmax = (int) $xml->Tiers->Generalites->Nb_tiers;
$db->total = true;
$liste=$xml->xpath('//Tiers');
foreach ( $liste as $objet) {
    $q=array();
    if ((string) $objet['No'] <> "") {
    //il y a le node maitre qui n'a rien.de plus verification qu'il y a index existant
        $nb++;
        $q['id'] = (int) $objet['No'] + 1;
        //numero d'index du tiers.attention les numeros peuvent ne pas se suivre en cas de suppression de tiers
        //car les numeros des tiers supprimes ne sont pas reutilises
        $q['nom'] = $db->ins($objet['Nom']);
        //nom du tiers
        $q['notes'] = $db->ins($objet['Informations']);
        // commentaires
        //verification
        if ($nb > $nbmax) {
            throw new Exception ('probleme de coherence au tiers Ne '.$objet['No'].' : "'.(string) $objet['Nom'].'" car il y a plus de tiers que le max tiers');
        }
        $db->insert('tiers', $q);
    }
    if ($nb % 50 === 0) {
        aff($nb.' tiers inserés');
    }
}
$db->save('tiers');
aff($nb.' tiers inserés');
$nb_tot_sous=$db->s('SELECT MAX(id) FROM titre');
if (is_null($nb_tot_sous)){
    $nb_tot_sous=0;
}
aff('-----------------------------------------------------------');
$nb=0;
foreach ( $liste as $objet) {
    $q=array();
    if ((string) $objet['Nom']!==""){
        $nom=stristr((string) $objet['Nom'],"titre_");
    }else {
        $nom=FALSE;
    }
    //si c'est pas false c'est que vraisemeblablement un titre
    if ($nom!=FALSE){
        $nb++;
        $q['nom']=substr($nom,6);
        $q['isin']=stristr((string)$objet['Informations'],'@',true);
        $q['type']=stristr((string)$objet['Informations'],'@');
        if ($q['isin'] == FALSE){
            if($q['type']!=FALSE){
                $nb_tot_sous++;
                $q['isin']="XX".$nb_tot_sous;
            } else {
                $q['isin']=(string)$objet['Informations'];
            }
        }
        if ($q['type'] == FALSE){
            $q['type']='XXX';
        } else {
            $q['type']=substr($q['type'],1);
            switch ($q['type']){
                case 'action':
                    $q['type']='ACT';
                    break;
                case 'opcvm':
                    $q['type']='OPC';
                    break;
                case 'csl':
                    $q['type']='CSL';
                    break;
                case 'obligation':
                    $q['type']='OBL';
                    break;
                default:
                    $q['type']='XXX';
            }
        }
        $q['tiers_id']=(int)$objet['No']+1;
        $db->insert('titre',$q);
    }
}
aff($nb.' titres inserés');

aff('-----------------------------------------------------------');
//insertion des categories
$nb = 0;
$nbmax = (int) $xml->Categories->Generalites->Nb_categories;
$nb_tot_sous = 0;
foreach ($xml->xpath('//Categorie') as $objet) {
    $q=array();
    $nb++;
    $q['id'] = $db->ins($objet['No']) + 1;
    $q['nom'] = $db->ins($objet['Nom']);
    switch ((int) $objet['Type']) {
        case 0:
            $q['typecat'] = 'd';
            break;
        case 1:
            $q['typecat'] = 'r';
            break;
        case 2:
            $q['typecat'] = 'v';
            break;
        default:
            throw new Exception ("id de la categorie ".$q['id']." inconnu");
            break;
    }
    //0 depenses
    //1 recettes
    //verification
    if ($nb > $nbmax) {
        aff($nb);
        aff($nbmax);
        throw new Exception ('probleme de coherence à la categorie "'.(string) $objet['Nom'].'" car il y a plus de categories que le max categorie');
    }
    $db->insert('cat', $q);
    //on s'occupe des sous categories
    $predicat = '//Categorie[@Nom = \''.$objet['Nom'].'\']/Sous-categorie';
    $nb_sous = 0;
    foreach ($xml->xpath($predicat) as $sous_liste) {
        $sous=array();
        $nb_sous++;
        $nb_tot_sous++;
       $sous['id'] = $nb_tot_sous + 1;
       $sous['nom'] = $db->ins($sous_liste['Nom']);
       $sous['grisbi_id'] = (int) $sous_liste['No'] + 1;
       $sous['cat_id'] = (int) $objet['No'] + 1;
        //verification
        if ($nb_sous > (int) $objet['No_derniere_sous_cagegorie']) {
            throw new Exception ('probleme de coherence à la categorie "'.(string) $q['id'].'" car il y a plus de categories que le max sous categorie');
        }
        //bdd
        $db->insert('scat',$sous);
    }
}//end foreach
aff($nb.' categories insérés et '.$nb_tot_sous.' sous-catégories inserés');
aff('-----------------------------------------------------------');
//insertion des ibs
$nb = 0;
$nbmax = (int) $xml->Imputations->Generalites->Nb_imputations;

$nb_tot_sous = 0;
foreach ($xml->xpath('//Imputation') as $liste) {
    $q = array();
    $nb++;
    $q['id'] = (int) $liste['No'] + 1;
    $q['nom'] = $db->ins($liste['Nom']);
    switch ((int) $liste['Type']) {
        case 0:
            $q['typeimp'] = 'd';
            break;
        case 1:
            $q['typeimp'] = 'r';
            break;
        case 2:
            $q['typeimp'] = 'v';
            break;
        default:
            throw new Exception ("id de la categorie ".$q['id']." inconnu");
            break;
    }
    //0 depenses
    //1 recettes
    //verification
    if ($nb > $nbmax) {
        aff($nb);
        aff($nbmax);
        throw new Exception ('probleme de coherence à la ibs "'.$q['Nom'].' '.$q['id'].'" car il y a plus de ibs que le max ibs');
    }
    $db->insert('ib', $q);
    //on s'occupe des sous ibs
    $predicat = '//Imputation[@Nom = \''.$q['nom'].'\']/Sous-imputation';
    $nb_sous = 0;
    foreach ($xml->xpath($predicat) as $sous_liste) {
        $sous=array();
        $nb_sous++;
        $nb_tot_sous++;
        $sous['id'] = $nb_tot_sous;
        $sous['nom'] = $db->ins($sous_liste['Nom']);
        $sous['grisbi_id'] = (int) $sous_liste['No'] + 1;
        $sous['ib_id'] = (int) $liste['No'] + 1;
        //verification
        if ($nb_sous > (int) $liste['No_derniere_sous_imputation']) {
            throw new Exception ('probleme de coherence à la sous-ib "'.$sous['Nom'].'" de l\'ib '.$q['id'].'car il y a plus de sous_ib '.$nb_sous.'que le max sous_ibs'.(int) $liste['No_derniere_sous_imputation'] );
        }
        //bdd
        $db->insert('sib', $sous);
    }
}//end foreach
aff($nb.' ibs et '.$nb_tot_sous.' sib insérés');
aff('-----------------------------------------------------------');
//gestion des banques
$nb = 0;
$nbmax = (int) $xml->Banques->Generalites->Nb_banques;
foreach ($xml->xpath('//Detail_des_banques/Banque') as $objet) {
    $nb++;
    $q=array();
    $q['id'] = (int) $objet['No'] + 1;
    $q['nom'] = $db->ins($objet['Nom']);
    $q['cib'] = $db->ins($objet['Code']);
    // $q['adresse'] = $db->ins($objet['Adresse']);
    // $q['tel'] = $db->ins($objet['Tel']);
    // $q['mail'] = $db->ins($objet['Mail']);
    // $q['web'] = $db->ins($objet['Web']);
    // $q['nom_corres'] = $db->ins($objet['Nom_correspondant']);
    // $q['tel_corres'] = $db->ins($objet['Tel_correspondant']);
    // $q['fax_corres'] = $db->ins($objet['Fax_correspondant']);
    // $q['mel_corres'] = $db->ins($objet['Mel_correspondant']);
    $q['notes'] = $db->ins($objet['Remarques']);

    //verification
    if ($nb > $nbmax) {
        throw new Exception ('probleme de coherence à la banque "'.(string) $objet['Nom'].'" car il y a plus de banques que le max banques');
    }
    $db->insert('banque', $q);
}//end foreach
aff($nb.' banques insérées');



aff('-----------------------------------------------------------');
//gestion des comptes
$nb = 0;
foreach ($xml->xpath('//Compte/Details') as $objet) {
    $q=array();
    $nb++;
    $q['nom'] = $db->ins($objet->Nom);
    $q['id'] = (int) $objet->No_de_compte + 1;//pour eviter l'auto increment
    $q['titulaire'] = $db->ins($objet->Titulaire);
    $tb = (int) $objet->Type_de_compte;
    switch ($tb) {
    //0 = bancaire, 1 = espece, 2 = passif, 3 = actif
        case 0:
            $q['type'] = "b";
            break;
        case 1:
            $q['type'] = "e";
            break;
        case 2:
            $q['type'] = "p";
            break;
        case 3:
            $q['type'] = "a";
            break;
        default:
            throw new Exception ("type du compte ".$q['id']." inconnu");
            break;
    }
    $q['devise_id'] = (int) $objet->Devise + 1;
    $q['banque_id'] = $db->ins((int) $objet->Banque + 1);
    $q['guichet'] = (int) $objet->Guichet;
    $q['num_compte'] = $db->ins($objet->No_compte_banque);//numero du compte (rib)
    $q['cle_compte'] = (int) $objet->Cle_du_compte;
    $q['solde_init'] = util::fr2uk((string) $objet->Solde_initial);
    $q['solde_mini_voulu'] = util::fr2uk((string) $objet->Solde_mini_voulu);
    $q['solde_mini_autorise'] = util::fr2uk((string) $objet->Solde_mini_autorise);
    // $q['solde_courant'] = util::fr2uk((string) $objet->Solde_courant);
    $q['date_dernier_releve'] = util::datefr2date($objet->Date_dernier_releve);
    $q['solde_dernier_releve'] = util::fr2uk((string) $objet->Solde_dernier_releve);
    $q['cloture'] = (int) $objet->Compte_cloture; // si = 1 => cloture
    // $q['Affichage_r'] = $objet->Affichage_r;

    $q['notes'] = $db->ins($objet->Commentaires);

    // $q['adresse_du_titulaire'] = $db->ins($objet->Adresse_du_titulaire);
    // //nombre_de_types : nombre des types differents par comptes
    // $q['type_defaut_debit'] = $db->ins($objet->Type_defaut_debit);
    // $q['type_defaut_credit'] = $db->ins($objet->Type_defaut_credit);
    // $q['tri_par_type'] = $db->ins($objet->Tri_par_type);// si = 1 => tri en fonction des types, si 0 => des dates
    // $q['neutres_inclus'] = $db->ins($objet->Neutres_inclus);
    // $q['ordre_du_tri'] = $db->ins($objet->Ordre_du_tri);//contient la liste des types dans l'ordre du tri du compte
    $db->insert('compte', $q);
}//end foreach
aff($nb.' comptes insérés');
aff('-----------------------------------------------------------');
//gestion des types de paiments
$nb_tot_sous = 0;
foreach ($xml->xpath('//Type') as $type) {
    $q=array();
    $nb_tot_sous++;
    $q['id'] = $nb_tot_sous;
    $q['grisbi_id'] = (int) $type['No'] + 1;
    $q['nom'] = $db->ins($type['Nom']);
    $q['signe'] = (int) $type['Signe'] + 1;
    //0 pour les virements
    //1 pour les depenses
    //2 pour les recettes
    $q['affiche_numero'] = $db->ins($type['Affiche_entree']);
    $q['num_auto'] = $db->ins($type['Numerotation_auto']);
    $q['num_en_cours'] = (int) $type['No_en_cours'];
    $q['compte_id'] = (int) implode('', $type->xpath('../../Details/No_de_compte')) + 1;//implode car xpath renvoie un tableau
    $db->insert('moyen', $q);
}
aff($nb_tot_sous.' moyens insérées');
aff('-----------------------------------------------------------');
//gestion des rapprochements

$nb = 0;
foreach ($xml->xpath('//Rapprochement') as $rapp) {
    $q=array();
    $nb++;
    $id = (int) $rapp['No'];
    $q['id'] = $id + 1;
    $q['nom'] = $db->ins($rapp['Nom']);
    //but trouver les operation et donc le compte qui se rapportent a ce rapprochement
    $predicat = "//Operation[@R = '".$id."']";
    $ope = $xml->xpath($predicat);
    if (!empty($ope)) {
        $ope = $ope[0];
        $idcompte = $ope->xpath('../../Details/No_de_compte');//recuperation du no du compte
        $q['compte_id'] = (int) $idcompte[0] + 1;
    }
    $db->insert('rapp', $q);
}
aff($nb.' rapps insérées');
aff('-----------------------------------------------------------');
//gestion des exercices
$nb = 0;
$nbmax = (int) $xml->Exercices->Generalites->No_dernier_exercice;
foreach ($xml->xpath('//Exercice') as $objet) {
    $q=array();
    $nb++;
    $q['id'] = (int) $objet['No'] + 1;
    $q['date_debut'] = util::datefr2date($objet['Date_debut']);
    if ($q['date_debut'] == null) {
        throw new InvalidArgumentException("la date de debut de l'exercice n° '".$q['id']."' est invalide");
    }
    $q_exo['date_fin'] = util::datefr2date($objet['Date_fin']);
    $q_exo['nom'] = $db->ins($objet['Nom'], true);
    // $q_exo['nom'] = (int) $objet['Visible'], true);
    if ($nb > $nbmax) {
        throw new Exception ('probleme de coherence à l\'exercice n° "'.(string) $objet['No'].'" car il y a plus d\'exercice que le max exo');
    }
    $db->insert('exercice', $q);
}
aff($nb.' exercices insérées');

//gestion des operations
aff('-----------------------------------------------------------');
$nb = 0;
$nbmax = (int) $xml->Generalites->Numero_derniere_operation;
//$ope_exist = array();
$db->total = true;
foreach ($xml->xpath('//Operation') as $ope) {
    $q=array();
    $nb++;
    $q['id'] = (int) $ope['No'] + 1;//numero de l'operation
    $ope_exist[] = $q['id'];
    $catexist = 0;
    $idcompte = $ope->xpath('../../Details/No_de_compte');//recuperation du no du compte
    $q['compte_id'] = (int) $idcompte[0] + 1;
    $q['date'] = util::datefr2date($ope['D'], true); //date de l'operation
    $q['date_val'] = util::datefr2time($ope['Db'], true);//date de valeur
    $q['montant'] = util::fr2uk($ope['M']);//montant
    $q['devise_id'] = (int) $ope['De'] + 1;//devise utilisé
    //Rdc//TODO est ce vraiment utile?
    //Tc taux de change utilisé dans l'operation de change//TODO
    //Fc frais de change utilisés dans l'operations//TODO
    $q['tiers_id'] = $db->ins((int) $ope['T'] + 1);//tiers
    $q['cat_id'] = $db->ins((int) $ope['C'] + 1);//categorie
    $q['scat_id'] = $db->ins((int) $ope['Sc'] + 1);//souscat
    $q['notes'] = $db->ins($ope['N']);//note
    $q['moyen_id'] = $db->ins((int) $ope['Ty'] + 1);//type de paiment de l'operation
    $moyen = $db->ins($ope['Ct'] + 1);
    $q['numcheque'] = ($moyen === NULL)?'':$moyen;//type de paiment de l'operation
    $pointage = (int) $ope['P'];
    switch ($pointage) { //0 = rien, 1 = pointée, 2 = rapprochée,
        case 0:
            $q['pointe'] = "na";
            break;
        case 1:
            $q['pointe'] = "p";
            break;
        case 2:
            $q['pointe'] = "r";
            break;
        default:
            throw new Exception ("type du pointage de l'ope ".$q['id']." inconnu");
            break;
    }
    $q['rapp_id'] = $db->ins((int) $ope['R'] + 1);//numero rapprochement
    $q['exercice_id'] = $db->ins((int) $ope['E'] + 1);// exercice de l'ope
    $q['ib_id'] = $db->ins((int) $ope['I'] + 1);//numero de l'imputation budgetaire
    $q['sib_id'] = $db->ins((int) $ope['Si'] + 1);//numero de la sous imputation budgetaire
    // $q['idpcompt'] = $db->ins($ope['Pc'] + 1);//no_piece_comptable
    if (((int) $ope['Ro']) + 1!=1) {
        $q['jumelle_id'] = $ope['Ro'] + 1;//transaction jumelle
    }else{
        $q['jumelle_id'] = NULL;
    }
    if ((int)$ope['Va']!==0) {
        $q['mere_id'] = $ope['Va'] + 1;//id de l'operation mere pour les operations ventiles
    }
    $q['is_mere']=(int)$ope['Ov'];
    // $q['infobq'] = $ope['Ibg'];//idbanque
    //verification
    if ($nb > $nbmax) {
        throw new Exception ('probleme de coherence à l\'operation n° "'.(string) $ope['No'].'" car il y a plus d\'ope que le max ope');
    }
    $db->insert('ope', $q);
    if ($nb % 200 == 0) aff($nb.' opes inserés');
}//end foreach
$db->save('ope');
aff($nb.' ope insérées');

//gestion des rapprochements
aff('-----------------------------------------------------------');
//gestion des echeances
$nb = 0;
$nbmax = (int) $xml->Echeances->Generalites->Nb_echeances;
foreach ($xml->xpath('//Echeance') as $objet) {
    $nb++;
    $q=array();
    $q['id'] = $objet['No'] + 1;
    $q['date_ech'] = util::datefr2date($objet['Date']);
    $q['compte_id'] = $objet['Compte'] + 1;
    $q['montant'] = util::fr2uk($objet['Montant']);
    $q['devise_id'] = $objet['Devise'] + 1;
    $q['tiers_id'] = $db->ins((int) $objet['Tiers'] + 1);
    $q['cat_id'] = $db->ins((int) $objet['Categorie'] + 1);
    $q['scat_id'] = $db->ins((int) $objet['Sous-categorie'] + 1);
    $q['compte_virement_id'] = $db->ins((int) $objet['Virement_compte'] + 1);
    $q['moyen_id'] = $db->ins((int) $objet['Type'] + 1);
    $q['moyen_virement_id'] = $db->ins((int) $objet['Contenu_du_type'] + 1);
    $q['exercice_id'] = $db->ins((int) $objet['Exercice'] + 1);// je l'enleve car c'est pas ca.//TODO
    $q['ib_id'] = $db->ins((int) $objet['Imputation'] + 1);
    $q['sib_id'] = $db->ins((int) $objet['Sous-imputation'] + 1);
    $q['notes'] = $db->ins($objet['Notes']);
    $q['inscription_automatique'] = (int) $objet['Automatique'];
    $q['periodicite'] = (int) $objet['Periodicite'];
    $q['intervalle'] = (int) $objet['Intervalle_periodicite'];
    $q['periode_perso'] = (int) $objet['Periodicite_personnalisee'];
    $q['date_limite'] = util::datefr2date($objet['Date_limite']);
    if ($nb > $nbmax) {
        throw new Exception('probleme de coherence à l\'imputation "'.(string) $nb['Nom'].'" car il y a plus d\' imputations que le max imputation');
    }
    $db->insert('echeance', $q);
}//end foreach
aff($nb." échéances");
//gestion des etats
