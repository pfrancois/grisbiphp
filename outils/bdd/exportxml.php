<?php /* coding: utf-8 */ 
//mise � jour le samedi 19 janvier 2008 � 15:00
require_once 'include/functions.php';
$db= new MySQLConnector( 'localhost', 'root', 'mdp','test') ;

$xmlinitial="<?xml version=\"1.0\"?><Grisbi></Grisbi>";

$xml=simplexml_load_string($xmlinitial);
$xml->AddChild('Generalites');
$tab=$db->tab('select * from generalite');
$tab=$tab[0];
if ($tab['Version_fichier']<>'0.5.0') {throw new Exception ('attention seul la version 0.5.0 est g�r�e');}
$xml->Generalites->AddChild('Version_fichier','0.5.0');//seulement cette version est g�ree
$xml->Generalites->AddChild('Version_grisbi',$tab['Version_grisbi']);
$xml->Generalites->AddChild('Fichier_ouvert',0);
$xml->Generalites->AddChild('Backup',$tab['Backup']);
$xml->Generalites->AddChild('Titre',unins($tab['Titre']));
$xml->Generalites->AddChild('Adresse_commune',unins($tab['Adresse_commune']));
$xml->Generalites->AddChild('Adresse_secondaire',unins($tab['Adresse_secondaire']));
$xml->Generalites->AddChild('Utilise_exercices',$tab['Utilise_exercices']);
$xml->Generalites->AddChild('Utilise_IB',$tab['Utilise_IB']);
$xml->Generalites->AddChild('Utilise_PC',$tab['Utilise_PC']);
$xml->Generalites->AddChild('Utilise_info_BG',$tab['Utilise_info_BG']);
$xml->Generalites->AddChild('Numero_devise_totaux_tiers',1);//TODO
$xml->Generalites->AddChild('Numero_devise_totaux_categ',1);//TODO
$xml->Generalites->AddChild('Numero_devise_totaux_ib',1);//TODO
$xml->Generalites->AddChild('Type_affichage_des_echeances',$tab['Type_affichage_des_echeances']);
$xml->Generalites->AddChild('Affichage_echeances_perso_nb_libre',$tab['Affichage_echeances_perso_nb_libre']);
$xml->Generalites->AddChild('Type_affichage_perso_echeances',$tab['Affichage_echeances_perso_nb_libre']);
$num_derniere_ope=$db->s('SELECT id FROM `ope` order by `id` desc');
if ($num_derniere_ope==NULL) {$num_derniere_ope=0;}
$xml->Generalites->AddChild('Numero_derniere_operation',$num_derniere_ope);
$xml->Generalites->AddChild('Echelle_date_import',2);
$xml->Generalites->AddChild('Utilise_logo',1);
//comme on les a class� par id decroissant, le premier est donc le dernier. donc on prend l'id du dernier et on a le nombre d'operations
$xml->Generalites->AddChild('Chemin_logo',$tab['Chemin_logo']);
$xml->Generalites->AddChild('Affichage_opes',$tab['Affichage_opes']);
$xml->Generalites->AddChild('Rapport_largeur_col',$tab['Rapport_largeur_col']);
$xml->Generalites->AddChild('Ligne_aff_une_ligne',$tab['Ligne_aff_une_ligne']);
$xml->Generalites->AddChild('Lignes_aff_deux_lignes',$tab['Lignes_aff_deux_lignes']);
$xml->Generalites->AddChild('Lignes_aff_trois_lignes',$tab['Lignes_aff_trois_lignes']);

//au sujet des comptes
$xml->AddChild('Comptes');
$xml->Comptes->AddChild('Generalites');


/* TODO
se rappeler de l'ordre des comptes.
le compte courant est par defaut le premier ce qui n'est pas necessairement le cas.
*/
//nombre de comptes
$sql='SELECT id FROM `compte`';
$result=$db->q($sql);
$nbcomptes=mysql_num_rows($result);
$ligne="";
while ($row = mysql_fetch_row($result)) {
   $ligne=$ligne.$row[0].'-';
}
$contenu=substr($ligne,0,-1);
$xml->Comptes->Generalites->AddChild('Ordre_des_comptes',$contenu);
$xml->Comptes->Generalites->AddChild('Compte_courant','0');

//on s'occupe de chacun descomptes en particulier
$resultcomptes=$db->q('select id from compte');
//on ne met pas de for car il peut y avoir des trous dans la numerotation
while ($r = mysql_fetch_array($resultcomptes)) {
	$n=(int)$r['id'];
	$xml->Comptes->AddChild('Compte');
	$xml->Comptes->Compte[$n]->AddChild('Details');
	$xml->Comptes->Compte[$n]->Details->AddChild('Nom',unins($db->s("SELECT nom FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Id_compte');
	$xml->Comptes->Compte[$n]->Details->AddChild('No_de_compte',$db->s("SELECT id FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Titulaire',unins($db->s("SELECT titulaire FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Type_de_compte',$db->s("SELECT `idtype` FROM `compte` WHERE id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Nb_operations',$db->s("SELECT count(id) FROM `ope` where idcompte=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Devise',$db->s("SELECT iddevise FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Banque',unins($db->s("SELECT idbanque FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Guichet',$db->s("SELECT guichet FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('No_compte_banque',$db->s("SELECT Num_compte FROM `compte` where id=$n"));
	$q=$db->s("SELECT cle_compte FROM `compte` where id=$n");
	if ($q<>0){
		$xml->Comptes->Compte[$n]->Details->AddChild('Cle_du_compte',$q);
	} else{
		$xml->Comptes->Compte[$n]->Details->AddChild('Cle_du_compte','');
	}
	$xml->Comptes->Compte[$n]->Details->AddChild('Solde_initial',uk2fr(sprintf('%f',($db->s("SELECT Solde_init FROM `compte` where id=$n")))));
	$xml->Comptes->Compte[$n]->Details->AddChild('Solde_mini_voulu',uk2fr(sprintf('%f',$db->s("SELECT Solde_mini_voulu FROM `compte` where id=$n"))));
	$xml->Comptes->Compte[$n]->Details->AddChild('Solde_mini_autorise',uk2fr(sprintf('%f',$db->s("SELECT solde_mini_autorise FROM `compte` where id=$n"))));
	$xml->Comptes->Compte[$n]->Details->AddChild('Solde_courant',uk2fr(sprintf('%f',$db->s("SELECT solde_courant FROM `compte` where id=$n"))));
	$q=date2xml($db->s("SELECT date_dernier_releve FROM `compte` where id=$n"));
	if ($q<>""){
		$xml->Comptes->Compte[$n]->Details->AddChild('Date_dernier_releve',$q);
	}else{
		$xml->Comptes->Compte[$n]->Details->AddChild('Date_dernier_releve');
	}
	$contenu=uk2fr(sprintf("%01.6f",$db->s("SELECT solde_dernier_releve FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Solde_dernier_releve',$contenu);
	$xml->Comptes->Compte[$n]->Details->AddChild('Dernier_no_de_rapprochement',$db->s("SELECT dernier_numero_de_rapprochement FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Compte_cloture',$db->s("SELECT compte_cloture FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Affichage_r',$db->s("SELECT affichage_r FROM `compte` where id=$n"));
	
	$xml->Comptes->Compte[$n]->Details->AddChild('Nb_lignes_ope',$db->s("SELECT nb_lignes_ope FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Commentaires',unins($db->s("SELECT notes FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Adresse_du_titulaire',unins($db->s("SELECT adresse_du_titulaire FROM `compte` where id=$n")));
	$xml->Comptes->Compte[$n]->Details->AddChild('Nombre_de_types',$db->s("select count(id) from types where cpt=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Type_defaut_debit',$db->s("SELECT type_defaut_debit FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Type_defaut_credit',$db->s("SELECT type_defaut_credit FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Tri_par_type',$db->s("SELECT tri_par_type FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Neutres_inclus',$db->s("SELECT neutres_inclus FROM `compte` where id=$n"));
	$xml->Comptes->Compte[$n]->Details->AddChild('Ordre_du_tri',$db->s("SELECT ordre_du_tri FROM `compte` where id=$n"));
	
	//gestion des types
	$xml->Comptes->Compte[$n]->AddChild('Detail_de_Types');
	$tabtype=$db->tab('select * from types where cpt='.$n);
	foreach($tabtype as $type){
		$temp=$xml->Comptes->Compte[$n]->Detail_de_Types->AddChild('Type');
		$temp->AddAttribute('No',$type['idgrisbi']);
		$temp->AddAttribute('Nom',unins($type['nom']));
		$temp->AddAttribute('Signe',$type['signe']);
		$temp->AddAttribute('Affiche_entree',$type['affiche_entree']);
		$temp->AddAttribute('Numerotation_auto',$type['num_auto']);
		$temp->AddAttribute('No_en_cours',$type['num_en_cours']);
	}
	
	//gestion des operations du compte
	$xml->Comptes->Compte[$n]->AddChild('Detail_des_operations');
	unset($result);
	$sql="SELECT * FROM `ope` WHERE `idcompte` =$n";
	$result=$db->q($sql);
	while ($row = mysql_fetch_assoc($result)) {
		$temp=$xml->Comptes->Compte[$n]->Detail_des_operations->AddChild('Operation');
		$temp->AddAttribute('No',$row['id']);
		$temp->AddAttribute('D',date2xml($row['date_ope']));
		$temp->AddAttribute('Db',(date2xml($row['date_val']))?date2xml($row['date_val']):'0/0/0');
		$temp->AddAttribute('M',uk2fr(sprintf('%f',(string)$row['montant'])));
		$temp->AddAttribute('De',$row['iddevise']);
		$temp->AddAttribute('Rdc','0');//TODO
		$temp->AddAttribute('Tc','0,0000000');//TODO
		$temp->AddAttribute('Fc','0,0000000');//TODO
		$temp->AddAttribute('T',$row['idtiers']);
		$cat=$row['idcat'];
		$scat=$row['idscat'];
		$comptevirement=$row['idcomptevirement'];
		$idtransactionvirement=$row['idjumelle'];
		if ($cat=='999') {
			$cat=0;
			$scat=0;
		}
		if ($cat==998){
			$cat=0;
			$scat=0;
		}
		$temp->AddAttribute('C',$cat);
		$temp->AddAttribute('Sc',$scat);
		$temp->AddAttribute('Ov',$row['opeventilemere']);
		$temp->AddAttribute('N',$row['note']);
		$temp->AddAttribute('Ty',$row['idtypeope']);
		$temp->AddAttribute('Ct',$row['numcheque']);
		$temp->AddAttribute('P',$row['pointe']);
		$temp->AddAttribute('A','0');//TODO
		$temp->AddAttribute('R',$row['idrappro']);
		$temp->AddAttribute('E',$row['idexercice']);
		$temp->AddAttribute('I',$row['idib']);
		$temp->AddAttribute('Si',$row['idsib']);
		$temp->AddAttribute('Pc',($row['idpcompt']<>0)?$row['idpcompt']:' ');
		$temp->AddAttribute('Ibg',$row['infobq']);
		$temp->AddAttribute('Ro',$idtransactionvirement);
		$temp->AddAttribute('Rc',$comptevirement);
		$temp->AddAttribute('Va',$row['idmere']);
	}
}
//echeances
$xml->AddChild('Echeances');
$xml->Echeances->AddChild('Generalites');
$xml->Echeances->Generalites->AddChild('Nb_echeances',0);//TODO
$xml->Echeances->Generalites->AddChild('No_derniere_echeance',0);//TODO
$xml->Echeances->AddChild('Detail_des_echeances',0);//TODO

//TODO
//tiers
$xml->AddChild('Tiers');
$xml->Tiers->AddChild('Generalites');
$xml->Tiers->Generalites->AddChild('Nb_tiers',$db->s('select count(id) from tiers'));
$xml->Tiers->Generalites->AddChild('No_dernier_tiers',$db->s('SELECT id FROM `tiers` order by `id` desc'));
$sql="SELECT * FROM `tiers`";
$result=$db->q($sql);
$xml->Tiers->AddChild('Detail_des_tiers');
while ($row = mysql_fetch_assoc($result)) {
	$temp=$xml->Tiers->Detail_des_tiers->AddChild('Tiers');
	$temp->AddAttribute('No',$row['id']);
	$temp->AddAttribute('Nom',$row['nom']);
	$temp->AddAttribute('Informations',$row['information']);
	$temp->AddAttribute('Liaison',0);//TODO
}

//categories
$xml->AddChild('Categories');
$xml->Categories->AddChild('Generalites');
$xml->Categories->Generalites->AddChild('Nb_categories',((int)$db->s('select count(id) from cat')));//on enleve les categories virement et ventilee
$q=$db->s('SELECT id FROM `cat` order by `id` desc');
//gerer les fausses categories
$xml->Categories->Generalites->AddChild('No_derniere_categorie',$q);
$sql="SELECT * FROM `cat`";
$result=$db->q($sql);
$xml->Categories->AddChild('Detail_des_categories');
while ($row = mysql_fetch_assoc($result)) {
	if ($row['id']<998){
		$temp=$xml->Categories->Detail_des_categories->AddChild('Categorie');
		$temp->AddAttribute('No',$row['id']);
		$temp->AddAttribute('Nom',unins($row['nom']));
		$temp->AddAttribute('Type',$row['idtypecat']);
		$q=$db->s('SELECT idgrisbi FROM `scat` where idcat='.$row['id'].' order by `idgrisbi` desc');
		$temp->AddAttribute('No_derniere_sous_cagegorie',($q==' ')?'0':$q);
		$sql="SELECT * FROM `scat`where idcat=".$row['id'];
		$result2=$db->q($sql);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$temp2=$temp->AddChild('Sous-categorie');
			$temp2->AddAttribute('No',$row2['idgrisbi']);
			$temp2->AddAttribute('Nom',unins($row2['nom']));
		}
	}
}

//imputations
$xml->AddChild('Imputations');
$xml->Imputations->AddChild('Generalites');
$xml->Imputations->Generalites->AddChild('Nb_imputations',$db->s('select count(id) from imp'));
$xml->Imputations->Generalites->AddChild('No_derniere_imputation',$db->s('SELECT id FROM `imp` order by `id` desc'));
$sql="SELECT * FROM `imp`";
$result=$db->q($sql);
$xml->Imputations->AddChild('Detail_des_imputations');
while ($row = mysql_fetch_assoc($result)) {
	$temp=$xml->Imputations->Detail_des_imputations->AddChild('Imputation');
	$temp->AddAttribute('No',$row['id']);
	$temp->AddAttribute('Nom',$row['nom']);
	$temp->AddAttribute('Type',$row['idtypeimp']);
	$q=$db->s('SELECT idgrisbi FROM `simp` where idimp='.$row['id'].' order by `idgrisbi` desc');
	$temp->AddAttribute('No_derniere_sous_imputation',($q=='')?'0':$q);
	$sql="SELECT * FROM `simp`where idimp=".$row['id'];
	$result2=$db->q($sql);
	while ($row = mysql_fetch_assoc($result2)) {
		$temp2=$temp->AddChild('Sous-imputation');
		$temp2->AddAttribute('No',$row['idgrisbi']);
		$temp2->AddAttribute('Nom',unins($row['nom']));
	}
}

//devises
$xml->AddChild('Devises');
$xml->Devises->AddChild('Generalites');
$xml->Devises->Generalites->AddChild('Nb_devises',$db->s('select count(id) from devise'));
$xml->Devises->Generalites->AddChild('No_derniere_devise',$db->s('SELECT id FROM `devise` order by `id` desc'));
$sql="SELECT * FROM `devise`";
$result=$db->q($sql);
$xml->Devises->AddChild('Detail_des_devises');
while ($row = mysql_fetch_assoc($result)) {
	$temp=$xml->Devises->Detail_des_devises->AddChild('Devise');
	$temp->AddAttribute('No',$row['id']);
	$temp->AddAttribute('Nom',unins($row['nom']));
	$temp->AddAttribute('Code',unins($row['isocode']));
	$temp->AddAttribute('IsoCode','');//TODO
	$temp->AddAttribute('Passage_euro',$row['passe_euro']);
	$temp->AddAttribute('Date_dernier_change',date2xml($row['date_dernier_maj']));
	$temp->AddAttribute('Rapport_entre_devises',$row['Rapport_entre_devises']);
	$temp->AddAttribute('Devise_en_rapport',$row['Devise_en_rapport']);
	$temp->AddAttribute('Change',uk2fr(sprintf('%f',(string)$row['tx_de_change'])));
}

//banques
$xml->AddChild('Banques');
$xml->Banques->AddChild('Generalites');
$xml->Banques->Generalites->AddChild('Nb_banques',$db->s('select count(id) from banque'));
$xml->Banques->Generalites->AddChild('No_derniere_banque',$db->s('SELECT id FROM `banque` order by `id` desc'));
$sql="SELECT * FROM `banque`";
$result=$db->q($sql);
$xml->Banques->AddChild('Detail_des_banques');
while ($row = mysql_fetch_assoc($result)) {
	$temp=$xml->Banques->Detail_des_banques->AddChild('Banque');
	$temp->AddAttribute('No',$row['id']);
	$temp->AddAttribute('Nom',unins($row['nom']));
	$temp->AddAttribute('Code',$row['code']);
	$temp->AddAttribute('Adresse',unins($row['adresse']));
	$temp->AddAttribute('Tel',$row['tel']);
	$temp->AddAttribute('Mail',$row['mail']);
	$temp->AddAttribute('Web',$row['web']);
	$temp->AddAttribute('Nom_correspondant',unins($row['nom_corres']));
	$temp->AddAttribute('Fax_correspondant',$row['fax_corres']);
	$temp->AddAttribute('Tel_correspondant',$row['tel_corres']);
	$temp->AddAttribute('Mail_correspondant',$row['mel_corres']);
	$temp->AddAttribute('Remarques',unins($row['notes']));
}

//Exercices

//Rapprochements
$xml->AddChild('Rapprochements');
$xml->Rapprochements->AddChild('Detail_des_rapprochements');
//Etats
$xml->AddChild('Etats'); 
$xml->Etats->AddChild('Generalites');

//enregistrement du fichier sous temp.gsb
    if (file_exists("temp.gsb")) {unlink("temp.gsb");}
    $file=fopen("temp.gsb","wb");
	$data=$xml->AsXML();
  	$achercher=array('<',' />',' xmlns=""',"\n <?xml"," <","<Backup></Backup>");
	$aremplacer=array("\n <",'/>','',"<?xml","<","<Backup/>");
	$data=str_replace($achercher,$aremplacer,$data);
	$data=str_replace("\n</","</",$data);
	$achercher=array('</Generalites>',"\n\n");
	$aremplacer=array("\n </Generalites>","\n");
	$data=str_replace($achercher,$aremplacer,$data);
	//$data=preg_replace('|<(.*)>0</.*>|','<\1/>',$data);
	highlight_string($data);
    fwrite($file,$data);
    fclose($file);
    chmod('temp.gsb',0777);
    chown('temp.gsb','francois');
	echo date('r');
?>