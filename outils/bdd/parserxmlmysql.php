<?php /* coding: utf-8 */
/*parser
 v23092005 necessite simple xml et rien d'autre
 v07012008 mies des fonctions dans un ficher annexe
 v03022008 ajout des echeances
 ajout des types d'operations par comptes
 suppression de la table 'typecat'
 */
/*-------------
 importation dans mysql d'un fichier grisbi

TODO types de paiments
TODO gestion des rapprochements
TODO gestion des etats
TODO gestion des exercices

 verifier les tiers
 prendre les bons numeros dans idgrsibi pour les tiers et les categories
 */
require_once 'include/functions.php';
$titre="parser";


/*----------------principal--------------------*/
if (isset($_GET['xml'])) {
	echo '<PRE>';
	$xmlfile=$_GET['xml'];
	$maxnivlog=2;
	aff("on debute");
	$starttime=start_timing();
	//on recupere les noms des tables
	$db= new MySQLConnector( 'localhost', 'root', 'mdp','test');

	$db->efface_tables();
	aff('tables effac�s');

	//on ouvre le fichier xml en simplexml
	$xml = simplexml_load_file($xmlfile);
	$version=(string) $xml->Generalites->Version_fichier;
	//verification du numero de version
	if ($version<>"0.5.0") {
		throw new Exception (" attention, votre fichier grisbi qui se trouve etre en version $version n'est pas en version 0.5.0. et ce logiciel n'importe que cette version pour l'instant.");
	}

	//on s'occupe du volet global
	aff("debut de l'import");
	$general=array();
	$general['Version_fichier']=(string)$xml->Generalites->Version_fichier;
	#actuellement la seule version existante est 0.5.0
	$general['Version_grisbi']=(string)$xml->Generalites->Version_grisbi;
	#meme remarque que precedement�
	$general['Fichier_ouvert']=(string)$xml->Generalites->Fichier_ouvert;
	#1 si le fichier declar� d�ja ouvert par gribsi
	$general['Backup']=(string)$xml->Generalites->Backup;
	$general['Titre']=(string)$xml->Generalites->Titre;
	$general['Adresse_commune']=(string)$xml->Generalites->Adresse_commune;
	$general['Adresse_secondaire']=(string)$xml->Generalites->Adresse_secondaire;
	$general['Utilise_exercices']=(string)$xml->Generalites->Utilise_exercices;
	$general['Utilise_IB']=(string)$xml->Generalites->Utilise_IB;
	#0: n'utilise pas les imp et les simp
	#1: les utilises
	$general['Utilise_PC']=(string)$xml->Generalites->Utilise_PC;
	#0: n'utilise pas les pieces comptables
	#1: les utilises
	$general['Utilise_info_BG']=(string)$xml->Generalites->Utilise_info_BG;
	$general['Numero_devise_totaux_tiers']=(string)$xml->Generalites->Numero_devise_totaux_tiers;
	$general['Type_affichage_des_echeances']=(string)$xml->Generalites->Type_affichage_des_echeances;
	$general['Affichage_echeances_perso_nb_libre']=(string)$xml->Generalites->Affichage_echeances_perso_nb_libre;
	$general['Type_affichage_perso_echeances']=(string)$xml->Generalites->Type_affichage_perso_echeances;
	$general['Chemin_logo']=(string)$xml->Generalites->Chemin_logo;
	#chemin absolu du logo qui s'affiche dans grisbi
	$general['Affichage_opes']=(string)$xml->Generalites->Affichage_opes;
	$general['Rapport_largeur_col']=(string)$xml->Generalites->Rapport_largeur_col;
	$general['Ligne_aff_une_ligne']=(string)$xml->Generalites->Ligne_aff_une_ligne;
	$general['Lignes_aff_deux_lignes']=(string)$xml->Generalites->Lignes_aff_deux_lignes;
	$general['Lignes_aff_trois_lignes']=(string)$xml->Generalites->Lignes_aff_trois_lignes;
	$db->insert('generalite',$general);
	unset($general);
	aff("generalit�s ok");

	//extraction des donn�es des tiers
	$nbtiers=0;
	$nbtiersmax=(int)$xml->Tiers->Generalites->Nb_tiers;
	foreach ($xml->xpath('//Tiers') as $tiers){
		if ((string) $tiers['No']<>""){ //il y a le node maitre qui n'a rien. de plus verification qu'il y a index existant
			$nbtiers+=1;
			if ($db->debogagesql) aff($nbtiers);
			$q_tiers=array();
			$q_tiers['id']=ins($tiers['No']);
			#numero d'index du tiers. attention les numeros peuvent ne pas se suivre en cas de suppression de tiers
			#car les numeros des tiers supprim�s ne sont pas reutilis�s
			$q_tiers['nom']=ins($tiers['Nom']);
			#nom du tiers
			$q_tiers['information']=ins($tiers['Informations']);
			# commentaires
			//verification
			if ($nbtiers>$nbtiersmax) {
				throw new Exception ('probleme de coherence au tiers N� '.$tiers['No'].' : "'.(string)$tiers['Nom']. '" car il y a plus de tiers que le max tiers');
			}
			$db->insert('tiers',$q_tiers);
		}
	}
	unset($q_tiers);
	aff($nbtiers.' tiers ins�res');
	if ($db->debogagesql) echo(N.'-----------------------------------------'.N);

	//gestion des devises
	$nbdevises=0;
	$nbdevisesmax=(int)$xml->Devises->Generalites->Nb_devises;
	foreach ($xml->Devises->Detail_des_devises->Devise as $devise){
		$nbdevises+=1;
		$q_devises=array();
		$q_devises['id']=ins($devise['No']);
		#id de la devise meme remarque que les autres id
		$q_devises['nom']=ins($devise['Nom']);
		# nom de la devise
		$q_devises['tx_de_change']='"'.fr2uk($devise['Change']).'"';
		#tx de change entre la devise principale et cette devise
		$q_devises['isocode']=ins($devise['Code']);
		#code iso
		$q_devises['passe_euro']=ins($devise['Passage_euro']);
		#est ce que cette monnaie est pass� � l'euro
		$q_devises['Rapport_entre_devises']=ins($devise['Rapport_entre_devises']);
		#rapport avec une devise non principale
		$q_devises['Devise_en_rapport']=ins($devise['Devise_en_rapport']);
		#cf "Rapport_entre_devises"
		$q_devises['date_dernier_maj']=xml2date($devise['Date_dernier_change']);
		#date de derniere date de change
		//verification
		if ($nbdevises>$nbdevisesmax) {
			throw new Exception  ('probleme de coherence � la devise "'.(string)$devise['Nom']. '" car il y a plus de devises que le max devise');
		}
		$db->insert('devise',$q_devises);
	}
	unset ($q_devises);
	echo $nbdevises.' devises ins�r�s'.N;
	if ($db->debogagesql) echo(N.'-----------------------------------------'.N);
	 

	//insertion des categories
	$nbcategories=0;
	$nbcategoriesmax=(int)$xml->Categories->Generalites->Nb_categories;
	$q_cat=array();
	foreach ($xml->xpath('//Categorie') as $categorie){
		$nbcategories+=1;
		$q_cat['id']=ins($categorie['No']);
		$q_cat['nom']=ins($categorie['Nom']);
		$q_cat['idtypecat']=$categorie['Type'];
		#0 depenses
		#1 recettes
		//verification
		if ($nbcategories>$nbcategoriesmax) {
			echo $nbcategories.N.$nbcategoriesmax.N;
			throw new Extension ('probleme de coherence � la categorie "'.(string)$categorie['Nom']. '" car il y a plus de categories que le max categorie');
		}
		$db->insert('cat',$q_cat);
		unset($q_cat);
		//on s'occupe des sous categories
		$predicat='//Categorie[@Nom=\''.$categorie['Nom'].'\']/Sous-categorie';
		$nbscat=0;
		foreach ($xml->xpath($predicat) as $scat){
			$nbscat+=1;
			$q_scat['nom']=ins($scat['Nom']);
			$q_scat['idgrisbi']=ins($scat['No']);
			$q_scat['idcat']=ins($categorie['No']);
			//verification
			if ($nbscat>(int) $categorie['No_derniere_sous_cagegorie']) {
				throw new Exception ('probleme de coherence � la categorie "'.(string)$scat['Nom']. '" car il y a plus de categories que le max sous categorie');
			}
			//bdd
			$db->insert('scat',$q_scat);
		}
	}
	echo $nbcategories.' categories ins�r�s'.N;
	//il faut rajouter la categorie virement et la categorie op ventilee
	$db->insert('cat',array('id'=>999,'nom'=>'op�ration ventil�e','idtypecat'=>'v'));
	$db->insert('cat',array('id'=>998,'nom'=>'virement','idtypecat'=>"v"));
	$db->insert('scat',array('id'=>999,'nom'=>'op�ration ventil�e','idcat'=>"v"));
	$db->insert('scat',array('id'=>998,'nom'=>'virement','idcat'=>"v"));

	//gestion des banques
	$nbbanques=0;
	$nbbanquesmax=(int)$xml->Banques->Generalites->Nb_banques;
	foreach ($xml->xpath('//Detail_des_banques/Banque') as $banque){
		$nbbanques+=1;

		$q_ban['id']=ins($banque['No']);
		$q_ban['nom']=ins($banque['Nom']);
		$q_ban['code']=ins($banque['Code']);
		$q_ban['adresse']=ins($banque['Adresse']);
		$q_ban['tel']=ins($banque['Tel']);
		$q_ban['mail']=ins($banque['Mail']);
		$q_ban['web']=ins($banque['Web']);
		$q_ban['nom_corres']=ins($banque['Nom_correspondant']);
		$q_ban['tel_corres']=ins($banque['Tel_correspondant']);
		$q_ban['fax_corres']=ins($banque['Fax_correspondant']);
		$q_ban['mel_corres']=ins($banque['Mel_correspondant']);
		$q_ban['notes']=ins($banque['Remarques']);

		//verification
		if ($nbbanques>$nbbanquesmax) {
			throw new Exception ('probleme de coherence � la banque "'.(string)$banque['Nom']. '" car il y a plus de banques que le max banques');
		}
		$result=$db->insert('banque',$q_ban);

	}
	echo $nbbanques.' banques ins�r�es'.N;
	unset($q_ban);


	//gestion des types de paiments
	$nbcompte=0;
	foreach ($xml->xpath('//Type') as $type){
		$q_type['idgrisbi']=ins($type['No']);
		$q_type['nom']=ins($type['Nom']);
		$q_type['signe']=ins($type['Signe']);
		#0 pour les virements
		#1 pour les depenses
		#2 pour les recettes
		$q_type['affiche_entree']=ins($type['Affiche_entree']);
		$q_type['num_auto']=ins($type['Numerotation_auto']);
		$q_type['num_en_cours']=ins($type['No_en_cours']);
		$q_type['cpt']=(int)implode('',$type->xpath('../../Details/No_de_compte'));
		$result=$db->insert('types',$q_type);
	}
	//gestion des comptes
	foreach ($xml->xpath('//Compte/Details') as $compte){
		$nbcompte+=1;
		$q_cpt['nom']=ins($compte->Nom);
		$q_cpt['id']=ins($compte->No_de_compte);
		$q_cpt['titulaire']=ins($compte->Titulaire);
		$q_cpt['idtype']=ins($compte->Type_de_compte); #0 = bancaire, 1 = esp�ce, 2 = passif, 3= actif
		#Nb_operations: recence le nombre d'ope
		$q_cpt['iddevise']=ins($compte->Devise);
		$q_cpt['idbanque']=ins($compte->Banque);
		##code interbancaire de la bq
		$q_cpt['guichet']=ins($compte->Guichet);
		#code guichet
		$q_cpt['num_compte']=ins($compte->No_compte_banque);
		#numero du compte (rib)
		$q_cpt['cle_compte']=ins($compte->Cle_du_compte);
		$q_cpt['solde_init']=fr2uk((string)$compte->Solde_initial);
		$q_cpt['solde_mini_voulu']=fr2uk((string)$compte->Solde_mini_voulu);
		$q_cpt['solde_mini_autorise']=fr2uk((string)$compte->Solde_mini_autorise);
		$q_cpt['solde_courant']=fr2uk((string)$compte->Solde_courant);
		$q_cpt['date_dernier_releve']=ins(xml2date($compte->Date_dernier_releve));
		$q_cpt['solde_dernier_releve']=fr2uk((string)$compte->Solde_dernier_releve);
		$q_cpt['dernier_numero_de_rapprochement']=ins($compte->Dernier_no_de_rapprochement);
		$q_cpt['compte_cloture']=ins($compte->compte_cloture); # si = 1 => clotur�
		$q_cpt['Affichage_r']=$compte->Affichage_r;
		$q_cpt['Nb_lignes_ope']=$compte->Nb_lignes_ope;
		$q_cpt['notes']=ins($compte->Commentaires);
		$q_cpt['adresse_du_titulaire']=ins($compte->Adresse_du_titulaire);
		#nombre_de_types : nombre des types differents par comptes
		$q_cpt['type_defaut_debit']=ins($compte->Type_defaut_debit);
		$q_cpt['type_defaut_credit']=ins($compte->Type_defaut_credit);
		$q_cpt['tri_par_type']=ins($compte->Tri_par_type);# si = 1 => tri en fonction des types, si 0 => des dates
		$q_cpt['neutres_inclus']=ins($compte->Neutres_inclus);
		$q_cpt['ordre_du_tri']=ins($compte->Ordre_du_tri);#contient la liste des types dans l'ordre du tri du compte
		$result=$db->insert('compte',$q_cpt);
		unset($q_cpt);
	}
	echo $nbcompte.' comptes ins�r�es'.N;

	//gestion des operations
	$nbope=0;
	$nbopemax=(int)$xml->Generalites->Numero_derniere_operation;
	foreach ($xml->xpath('//Operation') as $ope){
		$nbope+=1;
		$estopeventilemere=$ope['Ov'];
		$transactionvirement=$ope['Ro'];//transaction jumelle
		$comptevirement=$ope['Rc'];
		$num_operation_mere=$ope['Va'];//attention c'est un operation ventil�e, si ce n'en n'est pas une, c'est egale � zero
		$cat=$ope['C'];//categorie de l'operation
		if ($cat=='0') {
			if ($transactionvirement<>0 || $comptevirement <>0) {
				$cat=998;
				//echo("vir:$date:$montant:$comptevirement");
			}elseif ($estopeventilemere=1){
				$cat=999;
				//echo("op�ration ventil�e le $date de $montant");
			}
		}
		$q_ope['id']=ins($ope['No']);#numero de l'operation
		$q_ope['idgrisbi']=ins($ope['Id']); # utilis� lors d'import ofx pour �viter les doublons
		$catexist=0;
		$idcompte=$ope->xpath('../../Details/No_de_compte');//recuperation du no du compte
		$q_ope['idcompte']=ins($idcompte[0]);
		$q_ope['date_ope']=ins(xml2date($ope['D']));#date de l'operation
		$q_ope['date_val']=ins(xml2date($ope['Db']));#date de valeur
		$q_ope['montant']=fr2uk($ope['M']);#montant
		$q_ope['iddevise']=ins($ope['De']);#devise utilis�
		#Rdc//TODO
		#Tc taux de change utilis� dans l'operation de change//TODO
		#Fc frais de change utilis�s dans l'operations//TODO
		$q_ope['idtiers']=ins($ope['T']);#tiers
		$q_ope['idcat']=ins($cat);#categorie
		$q_ope['idscat']=ins($ope['Sc']);#souscat
		$q_ope['opeventilemere']=ins($estopeventilemere);# 1 si opventilemere
		$q_ope['note']=ins($ope['N']);#note
		$q_ope['idtypeope']=ins($ope['Ty']);#type de paiment de l'operation
		$q_ope['numcheque']=ins($ope['Ct']);#ce peut �tre un no de ch�que, de virement
		$q_ope['pointe']=ins($ope['P']);#0=rien, 1=point�e, 2=rapproch�e, 3=T
		#A  0=manuel, 1=automatique TODO
		$q_ope['idrappro']=ins($ope['R']);#numero rapprochement
		$q_ope['idexercice']=ins($ope['E']);# exercice de l'op�
		$q_ope['idib']=ins($ope['I']);#numero de l'imputation budgetaire
		$q_ope['idsib']=ins($ope['Si']);#numero de la sous imputation budgetaire
		$q_ope['idpcompt']=ins($ope['Pc']);#no_piece_comptable
		$q_ope['idjumelle']=ins($ope['Ro']);#transaction jumelle
		$q_ope['idcomptevirement']=ins($ope['Rc']);#relation_no_compte  -1 si compte supprim�
		$q_ope['idmere']=ins($num_operation_mere);#id de l'operation mere pour les operations ventiles
		$q_ope['infobq']=ins($ope['Ibg']);#idbanque
		//verification
		if ($nbope>$nbopemax) {
			throw new Exception ('probleme de coherence � l\'operation n� "'.(string)$ope['No']. '" car il y a plus d\'ope que le max ope');
		}
		$result=$db->insert('ope',$q_ope);
	}
	echo $nbope.' ope ins�r�es'.N;

	//gestion des rapprochements

	//gestion des echeances
	$nbecheances=0;
	$nbecheancesmax=(int)$xml->Echeances->Generalites->Nb_echeances;
	foreach ($xml->xpath('//Echeance') as $ech){
		$nbecheances++;
		$q_ech['id']=ins($ech['No']);
		$q_ech['date_ech']=xml2date($ech['Date']);
		$q_ech['compte']=ins($ech['Compte']);
		$q_ech['montant']=fr2uk($ech['Montant']);
		$q_ech['devise']=ins($ech['Devise']);
		$q_ech['tiers']=ins($ech['Tiers']);
		$q_ech['cat']=ins($ech['Categorie']);
		$q_ech['scat']=ins($ech['Sous-categorie']);
		$q_ech['vir']=ins($ech['Virement_compte']);
		$q_ech['type']=ins($ech['Type']);
		$q_ech['ctype']=ins($ech['Contenu_du_type']);
		$q_ech['exercice']=ins($ech['Exercice']);
		$q_ech['imp']=ins($ech['Imputation']);
		$q_ech['simp']=ins($ech['Sous-imputation']);
		$q_ech['notes']=ins($ech['Notes']);
		$q_ech['auto']=ins($ech['Automatique']);
		$q_ech['periodicite']=ins($ech['Periodicite']);
		$q_ech['intervalle']=ins($ech['Intervalle_periodicite']);
		$q_ech['periode_perso']=ins($ech['Periodicite_personnalisee']);
		$q_ech['date_limite']=xml2date($ech['Date_limite']);
		if ($nbecheances>$nbecheancesmax) {
			throw new Exception('probleme de coherence � l\'imputation "'.(string)$nbecheances['Nom']. '" car il y a plus d\' imputations que le max imputation');
		}
		$result=$db->insert('ech',$q_ech);
	}
	aff($nbecheances." �ch�ances");
	//gestion des etats

	//gestion des exercices

	//gestion des imputations
	$nbimputations=0;
	$nbimputationsmax=(int)$xml->Imputations->Generalites->Nb_imputations;
	foreach ($xml->xpath('//Imputation') as $imputation){
		$nbimputations++;
		$q_imp['id']=ins($imputation['No']);
		$q_imp['nom']=ins($imputation['Nom']);
		$q_imp['idtypeimp']=ins($imputation['Type']);
		//verification
		if ($nbimputations>$nbimputationsmax) {
			echo $nbimputations.N.$nbimputationsmax.N;
			die ('probleme de coherence � l\'imputation "'.(string)$imputation['Nom']. '" car il y a plus d\' imputations que le max imputation');
		}
		$result=$db->insert('imp',$q_imp);
		//on s'occupe des sous imputations
		$predicat='//Imputation[@Nom=\''.$imputation['Nom'].'\']/Sous-imputation';
		$nbsimp=0;
		foreach ($xml->xpath($predicat) as $simp){
			$nbsimp+=1;
			$q_simp['nom']=ins($simp['Nom']);
			$q_simp['idgrisbi']=ins($simp['No']);
			$q_simp['idimp']=ins($imputation['No']);
			//verification
			//TODO pas de verification afaire avec count en xpath
			//bdd
			$result=$db->insert('simp',$q_simp);
		}
	}
	aff($nbimputations.' imputations');
	aff(print_timing($starttime));
}
else {
	//affiche le nom des xml et des gsb avec un
	echo "selectionner le fichier à importer \n";
	$rep=scandir(getcwd());
	echo ('<ul>');
	foreach ($rep as $fic) {
		$fic=trim($fic);
		if ((strripos($fic,'.xml')) or (strripos($fic,'.gsb'))) {
			echo ('<li><A href="parserxmlmysql.php?xml='.$fic.'"> '.$fic.' </A> </li>');
		}
	}
	echo ('</ul>');
}

?>