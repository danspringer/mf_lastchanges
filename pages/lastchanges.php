<?php
$username = rex_request('mf_lastchanges_username', 'string');

if ($username == "") {
	$username = rex_request('list', 'string');
}
//echo $username;
$db_table = rex::getTablePrefix()."user";
$sql = rex_sql::factory();
$sql->setDebug(false); //Ausgabe Query
$sql->setQuery("SELECT * FROM $db_table ORDER BY `id` ASC");

$anzahl = 50 // Anzahl Listenergebnisse
?>

<div class="panel panel-edit">
    
        <header class="panel-heading"><div class="panel-title">Nur Änderungen von Nutzer anzeigen</div></header>
        
                    <div class="panel-body">
                
<form id="rex-addon-editmode" action="index.php?page=mf_lastchanges/lastchanges&func=filter" method="post">
<fieldset>
<dl class="rex-form-group form-group"><dt><label class="control-label" for="mf_lastchanges_username">Username</label></dt><dd><select id="mf_lastchanges_username" class="form-control" type="text" name="mf_lastchanges_username" value="">
<option value="*">Alle</option>

<?php
$userarray = array();
foreach($sql as $user)
{	
	$selected = '';
	
	if ($user->getValue("login") == $username) {
	 $selected = " selected";
	}
	
	echo '<option value="'.$user->getValue("login").'"'.$selected.'>'.$user->getValue("name").' ['.$user->getValue("login").']</option>';
	array_push($userarray, $user->getValue('login'));
}
?>
</select></dd></dl>



<div class="rex-form-panel-footer"><div class="btn-toolbar"><button id="mf_lastchanges_username_filter" type="submit" name="mf_lastchanges_username_filter" class="btn btn-save rex-form-aligned" value="1">Anzeigen</button></div></div></fieldset>
</form>
</div>
</div>


<div class="panel panel-edit">
    
        <header class="panel-heading"><div class="panel-title"><i class="fa fa-search"></i> Durchsuchen Sie die angezeigten Ergebnisse</div></header>
        
                    <div class="panel-body">
<div class="form-group yform-element">
<input type="search" class="live-search-box form-control" placeholder="Z.B. Suchbegriff oder Datum (Format: dd.mm.yyyy) eingeben">
</div>
</div>
</div>

<?php

$func = rex_request('func', 'string');

// Normale Ansicht ohne Filter
if ($func == '' && $username == '') {
	//echo '0';
	$list = rex_list::factory("SELECT id, name, updatedate, updateuser  FROM `".rex::getTablePrefix()."article` ORDER BY `updatedate` DESC", $anzahl);
	}
	
// Nach Filternwendung und Paginierungs-Klick
if ($func == '' && $username != '' && in_array($username, $userarray) ) {
	//echo '1';
	$list = rex_list::factory("SELECT id, name, updatedate, updateuser  FROM `".rex::getTablePrefix()."article` WHERE updateuser = '$username' ORDER BY `updatedate` DESC", $anzahl, $username);
	}
	
// Nach Filternwendung und Paginierungs-Klick
if ($func == '' && $username != '' && !in_array($username, $userarray) ) {
	//echo '2';
	$list = rex_list::factory("SELECT id, name, updatedate, updateuser  FROM `".rex::getTablePrefix()."article` ORDER BY `updatedate` DESC", $anzahl);
	}

// wenn nach einem User gefiltert wird
if ($func == 'filter' ) {
	// Wenn alle ausgewählt wurde
	if ($username == '*') {
	//echo '3';
		$list = rex_list::factory("SELECT id, name, updatedate, updateuser  FROM `".rex::getTablePrefix()."article` ORDER BY `updatedate` DESC", $anzahl);
	}
	// Wenn username nciht leer UND nicht alle ist
	if ($username != '' && $username != '*') {
	//echo '4';
		$list = rex_list::factory("SELECT id, name, updatedate, updateuser FROM `".rex::getTablePrefix()."article` WHERE updateuser = '$username' ORDER BY `updatedate` DESC", $anzahl, $username);
		}
	}	
	
	$list->addTableAttribute('class', 'table-striped');
	$list->setNoRowsMessage($this->i18n('mf_lastchanges_norowsmessage'));
	$list->setColumnParams('name', ['page' => 'content/edit', 'article_id'=> '###id###', 'mode' => 'edit']);
	$list->setColumnFormat('updatedate', 'date','d.m.Y - H:m:s');
	$list->setColumnLabel('id', 'ID');
	$list->setColumnLabel('name', 'Artikelname');
	$list->setColumnLabel('updatedate', 'Letztes Änderungsdatum');
	$list->setColumnLabel('updateuser', 'Benutzer');
	//$list->setColumnSortable('name');
	//$list->setColumnSortable('name');
	//$list->setColumnSortable('id');
	//$list->setColumnSortable('updateuser');
	//$list->setColumnSortable('updatedate');
	//$list->removeColumn('id');
	
	$content = $list->get();
	
	$fragment = new rex_fragment();
	$fragment->setVar('content', $content, false);
	$content = $fragment->parse('core/page/section.php');
	
	echo $content;
?>

<script>
			$('.table-striped tbody tr').each(function(){
			$(this).attr('data-search-term', $(this).text().toLowerCase());
			});
			
			
			$('.live-search-box').on('keyup', function(){
				
			var searchTerm = $(this).val().toLowerCase();
			var searchTermOriginal = $(this).val();

				$('.table-striped tr').each(function(){
					
					if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
						$(this).show();
						//updateAnzahl ++;
						//$('.anzahl-live').text(updateAnzahl);
					} else {
						$(this).hide();
						//updateAnzahl--;
						//$('.anzahl-live').text(updateAnzahl);
					}
				}); // EoF each
			
			});
				 
</script>