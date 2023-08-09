<?php

// Aggiungi il filtro 'bulk_actions-edit-product'
add_filter('bulk_actions-edit-product', 'my_custom_bulk_action');
function my_custom_bulk_action($actions)
{
    
    // Aggiungi una falsa azione come titolo della generazione del PDF
    $actions['catalog_pdf_disabled'] = '&#8615; Catalogo PDF';
    
    // Aggiungi un'azione personalizzata per il salvataggio dei prodotti selezionati
    $actions['save_selected_products'] = 'Salva i prodotti selezionati';
  
    // Aggiungi un'azione personalizzata per eliminare il file generato
    $actions['delete_selected_products_file'] = 'Elimina il file dei prodotti selezionati';

    return $actions;
}



// Aggiungi l'azione personalizzata 'save_selected_products'
add_action('admin_action_save_selected_products', 'my_save_selected_products_action');
function my_save_selected_products_action()
{
    // Verifica che l'azione sia stata richiamata correttamente
    if (isset($_REQUEST['post'])) {
        $selected_products = $_REQUEST['post'];

        // Percorso del file temporaneo
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/selected_products.txt';

        // Ottieni il contenuto corrente del file, se esiste
        if (file_exists($file_path)) {
            $current_content = file_get_contents($file_path);
        } else {
            $current_content = '';
        }

        // Rimuovi eventuali spazi bianchi
        $current_content = trim($current_content);

        // Unisci i prodotti esistenti con i nuovi prodotti selezionati
        $all_products = array_merge(explode(',', $current_content), $selected_products);

        // Rimuovi eventuali duplicati e filtra i valori vuoti
        $unique_products = array_filter(array_unique($all_products));

        // Crea una stringa con i prodotti separati da virgola
        $updated_content = implode(',', $unique_products);

        // Salva il contenuto aggiornato nel file temporaneo
        file_put_contents($file_path, $updated_content);

        // Notifica all'utente il salvataggio dei prodotti selezionati
        $download_url = $upload_dir['baseurl'] . '/selected_products.txt';
        wp_redirect(admin_url('edit.php?post_type=product&success_save=true'));
        exit;
    }
}


// Aggiungi l'azione personalizzata 'delete_selected_products_file'
add_action('admin_action_delete_selected_products_file', 'my_delete_selected_products_file_action');

function my_delete_selected_products_file_action()
{
    // Percorso del file temporaneo
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/selected_products.txt';

    // Verifica se il file esiste e elimina il file
    if (file_exists($file_path)) {
        unlink($file_path);
        wp_redirect(admin_url('edit.php?post_type=product&success_delete=true'));
        exit;
    } else {
        wp_redirect(admin_url('edit.php?post_type=product&error_delete=true'));
        exit;
    }
}




# Add Admin Notice




// Aggiungi l'azione 'admin_notices' per visualizzare gli avvisi
add_action('admin_notices', 'my_custom_admin_notices');

function my_custom_admin_notices() {
	$screen = get_current_screen();
	if ($screen->base == 'edit' && $screen->post_type == 'product') {
		
		// Verifica se è presente il parametro 'success' nell'URL per il salvataggio dei prodotti
		if (isset($_GET['success_save']) && $_GET['success_save'] === 'true') { $upload_dir = wp_upload_dir();
			echo '<div class="notice notice-success is-dismissible">
			<p>I prodotti selezionati sono stati salvati con successo.</p>
			</div>';
			}
		
		// Verifica se è presente il parametro 'success_delete' nell'URL per l'eliminazione del file
		if (isset($_GET['success_delete']) && $_GET['success_delete'] === 'true') {
			echo '<div class="notice notice-success is-dismissible">
			<p>Il file dei prodotti selezionati è stato eliminato con successo.</p>
			</div>';
		}
		
		// Verifica se è presente il parametro 'error_delete' nell'URL per l'eliminazione del file
		if (isset($_GET['error_delete']) && $_GET['error_delete'] === 'true') {
			echo '<div class="notice notice-error is-dismissible">
			<p>Non esiste alcun file in quanto già eliminato.</p>
			</div>';
		}
		
		
		// Verifica se il file esiste per mostrare il conteggio dei prodotti
		$upload_dir = wp_upload_dir();
		$file_path = $upload_dir['basedir'] . '/selected_products.txt';
		
		if (file_exists($file_path)) {
			$content = file_get_contents($file_path);
			$product_count = substr_count($content, ',') + 1; // Aggiungi 1 per l'ultimo prodotto
			echo '<div class="notice notice-info">
			<p>Il file per generare il catalogo in PDF contiene ' . $product_count . ' prodotti.</p>
			</div>';
		}
	}
}




// Aggiunge l'azione "in_admin_footer" e la funzione "my_custom_admin_page" ad essa
add_action('in_admin_footer', 'my_custom_admin_page');
function my_custom_admin_page() {  
  // Verifica se è la pagina corretta
  $screen = get_current_screen();
  if ($screen->base == 'edit' && $screen->post_type == 'product') {
    ?>
    <script type='text/javascript'>
      jQuery(document).ready(function() { 
        // Trova l'opzione desiderata e aggiungi l'attributo "disabled" e lo stile CSS
        jQuery('option[value="catalog_pdf_disabled"]').attr("disabled", "disabled").css({
          "background-color": "#CCCCCC",  // Imposta lo sfondo grigio
          "color": "#FF0000"  // Imposta il colore del testo rosso
        });
      });
    </script>
    <?php
  }
}
